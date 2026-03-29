<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ErrorMessage;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager;
    protected $accountant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->manager = User::factory()->create(['role' => 'manager', 'status' => 'active']);
        $this->accountant = User::factory()->create(['role' => 'accountant', 'status' => 'active']);

        // Seed necessary errors
        ErrorMessage::create(['code' => 'MIN_DEBIT_CREDIT', 'message' => 'Each transaction must have at least one debit and one credit.', 'severity' => 'error']);
        ErrorMessage::create(['code' => 'DEBITS_BEFORE_CREDITS', 'message' => 'Debits must be entered before credits.', 'severity' => 'error']);
        ErrorMessage::create(['code' => 'DEBIT_CREDIT_MISMATCH', 'message' => 'Total debits must equal total credits.', 'severity' => 'error']);
    }

    public function test_accountants_can_create_balanced_journal_entries()
    {
        $cash = Account::create(['account_number' => 101, 'account_name' => 'Cash', 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Cash Equivalent', 'statement' => 'BS', 'order' => 1, 'is_active' => true, 'created_by' => $this->admin->id]);
        $revenue = Account::create(['account_number' => 401, 'account_name' => 'Service Revenue', 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Operating Revenue', 'statement' => 'IS', 'order' => 2, 'is_active' => true, 'created_by' => $this->admin->id]);

        $response = $this->actingAs($this->accountant)->post(route('journal-entries.store'), [
            'date' => now()->format('Y-m-d'),
            'description' => 'Test earning revenue',
            'lines' => [
                ['account_id' => $cash->id, 'type' => 'debit', 'amount' => 1500.50],
                ['account_id' => $revenue->id, 'type' => 'credit', 'amount' => 1500.50],
            ]
        ]);

        $response->assertRedirect(route('journal-entries.index'));
        $this->assertDatabaseHas('journal_entries', ['description' => 'Test earning revenue', 'status' => 'pending']);
        $this->assertDatabaseCount('journal_entry_lines', 2);
    }

    public function test_fails_when_unbalanced()
    {
        $cash = Account::create(['account_number' => 101, 'account_name' => 'Cash', 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Cash Equivalent', 'statement' => 'BS', 'order' => 1, 'is_active' => true, 'created_by' => $this->admin->id]);
        $revenue = Account::create(['account_number' => 401, 'account_name' => 'Service Revenue', 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Operating Revenue', 'statement' => 'IS', 'order' => 2, 'is_active' => true, 'created_by' => $this->admin->id]);

        $response = $this->actingAs($this->accountant)->post(route('journal-entries.store'), [
            'date' => now()->format('Y-m-d'),
            'description' => 'Unbalanced',
            'lines' => [
                ['account_id' => $cash->id, 'type' => 'debit', 'amount' => 1500.50],
                ['account_id' => $revenue->id, 'type' => 'credit', 'amount' => 1000.00], // Mismatch
            ]
        ]);

        $response->assertSessionHasErrors(['lines']);
        $this->assertEquals(0, JournalEntry::count());
        $this->assertTrue(str_contains(session('errors')->first('lines'), 'Total debits must equal total credits.'));
    }

    public function test_fails_when_credits_before_debits()
    {
        $cash = Account::create(['account_number' => 101, 'account_name' => 'Cash', 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Cash Equivalent', 'statement' => 'BS', 'order' => 1, 'is_active' => true, 'created_by' => $this->admin->id]);
        $revenue = Account::create(['account_number' => 401, 'account_name' => 'Service Revenue', 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Operating Revenue', 'statement' => 'IS', 'order' => 2, 'is_active' => true, 'created_by' => $this->admin->id]);

        $response = $this->actingAs($this->accountant)->post(route('journal-entries.store'), [
            'date' => now()->format('Y-m-d'),
            'description' => 'Credit first',
            'lines' => [
                ['account_id' => $revenue->id, 'type' => 'credit', 'amount' => 1500.00], // Credit first
                ['account_id' => $cash->id, 'type' => 'debit', 'amount' => 1500.00],
            ]
        ]);

        $response->assertSessionHasErrors(['lines']);
        $this->assertTrue(str_contains(session('errors')->first('lines'), 'Debits must be entered before credits.'));
    }

    public function test_manager_can_approve_entry()
    {
        $entry = JournalEntry::create([
            'reference_id' => 'JE-2023-0001',
            'date' => now(),
            'description' => 'Pending Entry',
            'status' => 'pending',
            'created_by' => $this->accountant->id,
        ]);

        $response = $this->actingAs($this->manager)->post(route('manager.journal-entries.approve', $entry));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'status' => 'approved',
            'approved_by' => $this->manager->id
        ]);
    }

    public function test_accountants_cannot_approve()
    {
        $entry = JournalEntry::create([
            'reference_id' => 'JE-2023-0001',
            'date' => now(),
            'description' => 'Pending Entry',
            'status' => 'pending',
            'created_by' => $this->accountant->id,
        ]);

        $response = $this->actingAs($this->accountant)->post(route('manager.journal-entries.approve', $entry));
        
        $response->assertForbidden(); // Role middleware blocks them
    }
}
