<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Services\NotificationService;
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
                'rejection_reason' => null,
            ]);
            // The ledger pulls dynamically from approved journal entries,
            // so approving effectively "posts" to the ledger.
        });

        // Notify the creator so they see the outcome
        if ($journalEntry->creator) {
            NotificationService::notifyUser($journalEntry->creator, [
                'type' => $journalEntry->is_adjusting ? 'adjusting_entry_approved' : 'journal_entry_approved',
                'title' => "Entry approved: {$journalEntry->reference_id}",
                'message' => sprintf(
                    '%s approved your %s %s. It has been posted to the ledger.',
                    auth()->user()->full_name,
                    $journalEntry->is_adjusting ? 'adjusting entry' : 'journal entry',
                    $journalEntry->reference_id,
                ),
                'action_url' => $this->entryUrl($journalEntry),
                'data' => ['entry_id' => $journalEntry->id],
            ]);
        }

        return redirect()
            ->route($journalEntry->is_adjusting ? 'adjusting-entries.show' : 'journal-entries.show', $journalEntry)
            ->with('success', ($journalEntry->is_adjusting ? 'Adjusting entry' : 'Journal entry')
                . ' approved and posted to the ledger successfully.');
    }

    public function reject(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'pending') {
            return back()->with('error', 'Only pending entries can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $journalEntry->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($journalEntry->creator) {
            NotificationService::notifyUser($journalEntry->creator, [
                'type' => $journalEntry->is_adjusting ? 'adjusting_entry_rejected' : 'journal_entry_rejected',
                'title' => "Entry rejected: {$journalEntry->reference_id}",
                'message' => sprintf(
                    "%s rejected your %s %s.\nReason: %s",
                    auth()->user()->full_name,
                    $journalEntry->is_adjusting ? 'adjusting entry' : 'journal entry',
                    $journalEntry->reference_id,
                    $request->rejection_reason,
                ),
                'action_url' => $this->entryUrl($journalEntry),
                'data' => ['entry_id' => $journalEntry->id],
            ]);
        }

        return redirect()
            ->route($journalEntry->is_adjusting ? 'adjusting-entries.show' : 'journal-entries.show', $journalEntry)
            ->with('success', ($journalEntry->is_adjusting ? 'Adjusting entry' : 'Journal entry') . ' has been rejected.');
    }

    private function entryUrl(JournalEntry $entry): string
    {
        return $entry->is_adjusting
            ? route('adjusting-entries.show', $entry)
            : route('journal-entries.show', $entry);
    }
}
