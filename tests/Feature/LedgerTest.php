<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->create(['role' => 'manager', 'status' => 'active']);
    }

    public function test_ledger_calculates_running_balance_correctly()
    {
        // Setup Account
        $cash = Account::create([
            'account_number' => 101, 
            'account_name' => 'Cash', 
            'normal_side' => 'debit', 
            'account_category' => 'asset', 
            'account_subcategory' => 'Cash Equivalent',
            'statement' => 'BS',
            'order' => 1,
            'initial_balance' => 1000,
            'is_active' => true,
            'created_by' => $this->manager->id
        ]);

        // Entry 1: Received $500 cash 
        $je1 = JournalEntry::create([
            'reference_id' => 'JE-01',
            'date' => now()->subDays(2),
            'description' => 'Service provided',
            'status' => 'approved',
            'created_by' => $this->manager->id,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je1->id, 'account_id' => $cash->id, 'type' => 'debit', 'amount' => 500]);

        // Entry 2: Paid $200 cash 
        $je2 = JournalEntry::create([
            'reference_id' => 'JE-02',
            'date' => now()->subDays(1),
            'description' => 'Office supplies',
            'status' => 'approved',
            'created_by' => $this->manager->id,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je2->id, 'account_id' => $cash->id, 'type' => 'credit', 'amount' => 200]);

        $response = $this->actingAs($this->manager)->get(route('ledger.index'));
        $response->assertStatus(200);

        // Computed balance logic: 1000 (Initial) + 500 (Dr) - 200 (Cr) = 1300.
        $response->assertSee('$1,300.00');

        $detailResponse = $this->actingAs($this->manager)->get(route('ledger.show', $cash));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('$1,500.00'); // Running balance after first
        $detailResponse->assertSee('$1,300.00'); // Running balance after second
    }

    public function test_ledger_ignores_pending_entries()
    {
        $cash = Account::create([
            'account_number' => 101, 
            'account_name' => 'Cash', 
            'normal_side' => 'debit', 
            'account_category' => 'asset', 
            'account_subcategory' => 'Cash Equivalent',
            'statement' => 'BS',
            'order' => 1,
            'initial_balance' => 0,
            'is_active' => true,
            'created_by' => $this->manager->id
        ]);

        // Pending Entry
        $je1 = JournalEntry::create([
            'reference_id' => 'JE-01',
            'date' => now()->subDays(2),
            'description' => 'Pending service provided',
            'status' => 'pending',
            'created_by' => $this->manager->id,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je1->id, 'account_id' => $cash->id, 'type' => 'debit', 'amount' => 5000]);

        $response = $this->actingAs($this->manager)->get(route('ledger.index'));
        $response->assertStatus(200);

        // Since it's pending, balance should still be 0.
        $response->assertSee('$0.00');
    }
}
