<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Attachment;
use App\Models\ErrorMessage;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdjustingEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalEntry::with(['creator', 'approver', 'lines.account'])
            ->adjusting()
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
            $query->where(function ($outer) use ($search) {
                $outer->whereHas('lines', function ($q) use ($search) {
                    $q->where('amount', 'like', "%{$search}%")
                      ->orWhereHas('account', function ($q2) use ($search) {
                          $q2->where('account_name', 'like', "%{$search}%");
                      });
                })->orWhere('reference_id', 'like', "%{$search}%");
            });
        }

        $entries = $query->paginate(15)->withQueryString();

        return view('adjusting-entries.index', compact('entries'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->orderBy('account_number')->get();
        return view('adjusting-entries.create', compact('accounts'));
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

        // Required: at least one debit and one credit
        if (!$lines->contains('type', 'debit') || !$lines->contains('type', 'credit')) {
            throw ValidationException::withMessages([
                'lines' => ErrorMessage::getByCode('MIN_DEBIT_CREDIT')
                    ?? 'Each transaction must have at least one debit and one credit.',
            ]);
        }

        // Required: debits must come before credits
        $foundCredit = false;
        foreach ($lines as $line) {
            if ($line['type'] === 'credit') {
                $foundCredit = true;
            } elseif ($line['type'] === 'debit' && $foundCredit) {
                throw ValidationException::withMessages([
                    'lines' => ErrorMessage::getByCode('DEBITS_BEFORE_CREDITS')
                        ?? 'Debits must be entered before credits.',
                ]);
            }
        }

        // Required: totals must balance
        $totalDebits = $lines->where('type', 'debit')->sum('amount');
        $totalCredits = $lines->where('type', 'credit')->sum('amount');
        if (round((float) $totalDebits, 2) !== round((float) $totalCredits, 2)) {
            throw ValidationException::withMessages([
                'lines' => ErrorMessage::getByCode('DEBIT_CREDIT_MISMATCH')
                    ?? 'Total debits must equal total credits.',
            ]);
        }

        $entry = DB::transaction(function () use ($request, $lines) {
            $year = date('Y', strtotime($request->date));
            $count = JournalEntry::adjusting()->whereYear('date', $year)->count() + 1;
            $referenceId = 'AJE-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $entry = JournalEntry::create([
                'reference_id' => $referenceId,
                'date' => $request->date,
                'description' => $request->description,
                'is_adjusting' => true,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'submitted_at' => now(),
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

        // Notify managers (outside transaction so DB state is committed before mail sends)
        NotificationService::notifyManagers([
            'type' => 'adjusting_entry_submitted',
            'title' => "New adjusting entry awaiting approval: {$entry->reference_id}",
            'message' => sprintf(
                "%s submitted adjusting journal entry %s on %s for \"%s\". Please review and approve or reject.",
                auth()->user()->full_name,
                $entry->reference_id,
                $entry->date->format('M d, Y'),
                $entry->description,
            ),
            'action_url' => route('adjusting-entries.show', $entry),
            'data' => [
                'entry_id' => $entry->id,
                'reference_id' => $entry->reference_id,
            ],
        ]);

        return redirect()
            ->route('adjusting-entries.index')
            ->with('success', "Adjusting entry {$entry->reference_id} submitted for manager approval.");
    }

    public function show(JournalEntry $adjustingEntry)
    {
        abort_unless($adjustingEntry->is_adjusting, 404);

        $adjustingEntry->load(['lines.account', 'attachments', 'creator', 'approver']);

        return view('adjusting-entries.show', ['journalEntry' => $adjustingEntry]);
    }
}
