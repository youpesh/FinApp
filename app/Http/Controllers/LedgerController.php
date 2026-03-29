<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        // View summary of all accounts
        $accounts = Account::orderBy('account_number')->get();

        // Calculate current balance for each dynamically based on approved entries
        foreach ($accounts as $account) {
            $approvedLinesQuery = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', function($q) {
                    $q->where('status', 'approved');
                });
            
            $debits = (clone $approvedLinesQuery)->where('type', 'debit')->sum('amount');
            $credits = (clone $approvedLinesQuery)->where('type', 'credit')->sum('amount');

            $balance = $account->initial_balance;
            if (strtolower($account->normal_side) === 'debit') {
                $balance += $debits - $credits;
            } else {
                $balance += $credits - $debits;
            }

            $account->computed_balance = $balance;
            $account->activity_count = $approvedLinesQuery->count();
        }

        return view('ledger.index', compact('accounts'));
    }

    public function show(Request $request, Account $account)
    {
        $query = JournalEntryLine::with(['journalEntry'])
            ->where('account_id', $account->id)
            ->whereHas('journalEntry', function($q) {
                $q->where('status', 'approved');
            });

        if ($request->filled('date_from')) {
            $query->whereHas('journalEntry', function($q) use ($request) {
                $q->whereDate('date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('journalEntry', function($q) use ($request) {
                $q->whereDate('date', '<=', $request->date_to);
            });
        }

        if ($request->filled('amount')) {
            $query->where('amount', $request->amount);
        }

        // Sort chronologically
        $lines = $query->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                       ->orderBy('journal_entries.date', 'asc')
                       ->orderBy('journal_entry_lines.id', 'asc')
                       ->select('journal_entry_lines.*')
                       ->get();

        // Compute running balance
        $runningBalance = $account->initial_balance ?? 0;
        $isDebitNormal = strtolower($account->normal_side) === 'debit';

        // Note: Running balance strictly requires computing from the VERY BEGINNING.
        // If filters are applied, the opening balance for the view should be the balance BEFORE the date_from.
        $openingBalance = $account->initial_balance ?? 0;
        
        if ($request->filled('date_from')) {
            $priorLines = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', function($q) use ($request) {
                    $q->where('status', 'approved')
                      ->whereDate('date', '<', $request->date_from);
                })->get();
                
            foreach ($priorLines as $pl) {
                if ($isDebitNormal) {
                    $openingBalance += $pl->type === 'debit' ? $pl->amount : -$pl->amount;
                } else {
                    $openingBalance += $pl->type === 'credit' ? $pl->amount : -$pl->amount;
                }
            }
        }

        $runningBalance = $openingBalance;

        // Apply running balance to the visible lines
        foreach ($lines as $line) {
            if ($isDebitNormal) {
                $runningBalance += $line->type === 'debit' ? $line->amount : -$line->amount;
            } else {
                $runningBalance += $line->type === 'credit' ? $line->amount : -$line->amount;
            }
            $line->running_balance = $runningBalance;
        }

        return view('ledger.show', compact('account', 'lines', 'openingBalance'));
    }
}
