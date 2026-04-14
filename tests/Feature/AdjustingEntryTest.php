<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ErrorMessage;
use App\Models\JournalEntry;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdjustingEntryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $accountant;
    protected Account $cash;
    protected Account $revenue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->manager = User::factory()->create(['role' => 'manager', 'status' => 'active']);
        $this->accountant = User::factory()->create(['role' => 'accountant', 'status' => 'active']);

        ErrorMessage::create(['code' => 'MIN_DEBIT_CREDIT', 'message' => 'Each transaction must have at least one debit and one credit.', 'severity' => 'error']);
        ErrorMessage::create(['code' => 'DEBITS_BEFORE_CREDITS', 'message' => 'Debits must be entered before credits.', 'severity' => 'error']);
        ErrorMessage::create(['code' => 'DEBIT_CREDIT_MISMATCH', 'message' => 'Total debits must equal total credits.', 'severity' => 'error']);

        $this->cash = Account::create(['account_number' => 101, 'account_name' => 'Cash', 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Asset', 'statement' => 'BS', 'order' => 1, 'is_active' => true, 'created_by' => $this->admin->id]);
        $this->revenue = Account::create(['account_number' => 401, 'account_name' => 'Service Revenue', 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Operating', 'statement' => 'IS', 'order' => 2, 'is_active' => true, 'created_by' => $this->admin->id]);
    }

    public function test_accountant_can_submit_adjusting_entry_and_managers_are_notified(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->accountant)->post(route('adjusting-entries.store'), [
            'date' => now()->format('Y-m-d'),
            'description' => 'Accrued revenue adjustment',
            'lines' => [
                ['account_id' => $this->cash->id, 'type' => 'debit', 'amount' => 500.00],
                ['account_id' => $this->revenue->id, 'type' => 'credit', 'amount' => 500.00],
            ],
        ]);

        $response->assertRedirect(route('adjusting-entries.index'));

        $this->assertDatabaseHas('journal_entries', [
            'description' => 'Accrued revenue adjustment',
            'is_adjusting' => true,
            'status' => 'pending',
        ]);

        $entry = JournalEntry::adjusting()->first();
        $this->assertStringStartsWith('AJE-', $entry->reference_id);

        // Both admin and manager should get an in-app notification + email log
        $this->assertSame(2, Notification::where('type', 'adjusting_entry_submitted')->count());
        $this->assertDatabaseHas('email_logs', ['recipient' => $this->manager->email]);
        $this->assertDatabaseHas('email_logs', ['recipient' => $this->admin->email]);
    }

    public function test_adjusting_entries_do_not_appear_in_regular_journal_list(): void
    {
        JournalEntry::create([
            'reference_id' => 'AJE-2026-9901',
            'date' => now(),
            'description' => 'Adjusting',
            'is_adjusting' => true,
            'status' => 'approved',
            'created_by' => $this->accountant->id,
        ]);
        JournalEntry::create([
            'reference_id' => 'JE-2026-0001',
            'date' => now(),
            'description' => 'Regular',
            'is_adjusting' => false,
            'status' => 'approved',
            'created_by' => $this->accountant->id,
        ]);

        $response = $this->actingAs($this->accountant)->get(route('journal-entries.index'));
        $response->assertSee('JE-2026-0001');
        $response->assertDontSee('AJE-2026-9901');

        $response = $this->actingAs($this->accountant)->get(route('adjusting-entries.index'));
        $response->assertSee('AJE-2026-9901');
        $response->assertDontSee('JE-2026-0001');
    }

    public function test_manager_can_approve_adjusting_entry(): void
    {
        $entry = JournalEntry::create([
            'reference_id' => 'AJE-2026-0099',
            'date' => now(),
            'description' => 'Pending AJE',
            'is_adjusting' => true,
            'status' => 'pending',
            'created_by' => $this->accountant->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->post(route('manager.adjusting-entries.approve', $entry));

        $response->assertRedirect(route('adjusting-entries.show', $entry));
        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'status' => 'approved',
            'approved_by' => $this->manager->id,
        ]);

        // Creator is notified
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->accountant->id,
            'type' => 'adjusting_entry_approved',
        ]);
    }

    public function test_manager_can_reject_adjusting_entry_with_reason(): void
    {
        $entry = JournalEntry::create([
            'reference_id' => 'AJE-2026-0100',
            'date' => now(),
            'description' => 'Pending AJE',
            'is_adjusting' => true,
            'status' => 'pending',
            'created_by' => $this->accountant->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->post(route('manager.adjusting-entries.reject', $entry), [
                'rejection_reason' => 'Wrong account used for accrual.',
            ]);

        $response->assertRedirect(route('adjusting-entries.show', $entry));
        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'status' => 'rejected',
            'rejection_reason' => 'Wrong account used for accrual.',
        ]);
    }

    public function test_rejection_requires_a_reason(): void
    {
        $entry = JournalEntry::create([
            'reference_id' => 'AJE-2026-0101',
            'date' => now(),
            'description' => 'Pending AJE',
            'is_adjusting' => true,
            'status' => 'pending',
            'created_by' => $this->accountant->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->post(route('manager.adjusting-entries.reject', $entry), []);

        $response->assertSessionHasErrors('rejection_reason');
        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'status' => 'pending']);
    }

    public function test_accountants_cannot_approve_adjusting_entries(): void
    {
        $entry = JournalEntry::create([
            'reference_id' => 'AJE-2026-0102',
            'date' => now(),
            'description' => 'Pending',
            'is_adjusting' => true,
            'status' => 'pending',
            'created_by' => $this->accountant->id,
        ]);

        $this->actingAs($this->accountant)
            ->post(route('manager.adjusting-entries.approve', $entry))
            ->assertForbidden();
    }
}
