<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\ErrorMessage;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalEntry::with(['creator', 'approver', 'lines'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('lines', function ($q) use ($search) {
                $q->where('amount', 'like', "%{$search}%")
                  ->orWhereHas('account', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            })->orWhere('reference_id', 'like', "%{$search}%");
        }

        $entries = $query->paginate(15)->withQueryString();

        return view('journal.index', compact('entries'));
    }

    public function create()
    {
        // Only active accounts from the chart of accounts
        $accounts = Account::where('is_active', true)->orderBy('account_number')->get();
        return view('journal.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:1000',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
            'lines.*.description' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png|max:5120',
        ]);

        $lines = collect($request->lines);

        // Required Check 1: Min 1 Debit, Min 1 Credit
        $hasDebit = $lines->contains('type', 'debit');
        $hasCredit = $lines->contains('type', 'credit');
        if (!$hasDebit || !$hasCredit) {
            throw ValidationException::withMessages([
                'lines' => ErrorMessage::getByCode('MIN_DEBIT_CREDIT') ?? 'Each transaction must have at least one debit and one credit.'
            ]);
        }

        // Required Check 2: Debits MUST come before credits
        $foundCredit = false;
        foreach ($lines as $line) {
            if ($line['type'] === 'credit') {
                $foundCredit = true;
            } elseif ($line['type'] === 'debit' && $foundCredit) {
                throw ValidationException::withMessages([
                    'lines' => ErrorMessage::getByCode('DEBITS_BEFORE_CREDITS') ?? 'Debits must be entered before credits.'
                ]);
            }
        }

        // Required Check 3: Total Debits == Total Credits
        $totalDebits = $lines->where('type', 'debit')->sum('amount');
        $totalCredits = $lines->where('type', 'credit')->sum('amount');
        if (round((float) $totalDebits, 2) !== round((float) $totalCredits, 2)) {
            throw ValidationException::withMessages([
                'lines' => ErrorMessage::getByCode('DEBIT_CREDIT_MISMATCH') ?? 'Total debits must equal total credits.'
            ]);
        }

        $entry = DB::transaction(function () use ($request, $lines) {
            // Generate unique reference string eg: JE-2026-0001
            $year = date('Y', strtotime($request->date));
            $count = JournalEntry::whereYear('date', $year)->count() + 1;
            $referenceId = 'JE-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $entry = JournalEntry::create([
                'reference_id' => $referenceId,
                'date' => $request->date,
                'description' => $request->description,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'type' => $line['type'],
                    'amount' => $line['amount'],
                    'description' => $line['description'] ?? null,
                ]);
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    Attachment::create([
                        'journal_entry_id' => $entry->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }

            return $entry;
        });

        return redirect()->route('journal-entries.index')->with('success', 'Journal entry submitted for manager approval.');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.account', 'attachments', 'creator', 'approver']);
        return view('journal.show', compact('journalEntry'));
    }
}
