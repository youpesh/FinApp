<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerApprovalController extends Controller
{
    public function approve(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'pending') {
            return back()->with('error', 'Only pending entries can be approved.');
        }

        DB::transaction(function () use ($journalEntry) {
            $journalEntry->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'rejection_reason' => null
            ]);

            // Note: the ledger pulls dynamically from approved journal entries,
            // so approving it effectively "posts" it to the ledger.
            // If the app architecture strictly separated ledger posting into a new table,
            // we would dispatch a job here. But based on our design, JournalEntry lines
            // act as the ledger source of truth when status is 'approved'.
        });

        return redirect()->route('journal-entries.show', $journalEntry)->with('success', 'Journal entry approved and posted to the ledger successfully.');
    }

    public function reject(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'pending') {
            return back()->with('error', 'Only pending entries can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $journalEntry->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->route('journal-entries.show', $journalEntry)->with('success', 'Journal entry has been rejected.');
    }
}
