<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\FinancialReport;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\User;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected FinancialReportService $service;

    protected Account $cash;       // asset
    protected Account $payable;    // liability
    protected Account $equity;     // equity (RE)
    protected Account $revenue;    // revenue
    protected Account $expense;    // expense

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->manager = User::factory()->create(['role' => 'manager', 'status' => 'active']);
        $this->service = new FinancialReportService();

        $this->cash = Account::create(['account_number' => 101, 'account_name' => 'Cash', 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current', 'statement' => 'BS', 'order' => 1, 'is_active' => true, 'created_by' => $this->admin->id]);
        $this->payable = Account::create(['account_number' => 201, 'account_name' => 'Accounts Payable', 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Current', 'statement' => 'BS', 'order' => 2, 'is_active' => true, 'created_by' => $this->admin->id]);
        $this->equity = Account::create(['account_number' => 301, 'account_name' => 'Retained Earnings', 'normal_side' => 'credit', 'account_category' => 'equity', 'account_subcategory' => 'Equity', 'statement' => 'RE', 'order' => 3, 'is_active' => true, 'created_by' => $this->admin->id]);
        $this->revenue = Account::create(['account_number' => 401, 'account_name' => 'Service Revenue', 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Operating', 'statement' => 'IS', 'order' => 4, 'is_active' => true, 'created_by' => $this->admin->id]);
        $this->expense = Account::create(['account_number' => 501, 'account_name' => 'Rent Expense', 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating', 'statement' => 'IS', 'order' => 5, 'is_active' => true, 'created_by' => $this->admin->id]);
    }

    /** Post an approved journal entry with two legs. */
    private function postEntry(string $date, int $debitAccountId, int $creditAccountId, float $amount, bool $approved = true): JournalEntry
    {
        $entry = JournalEntry::create([
            'reference_id' => 'JE-TEST-' . uniqid(),
            'date' => $date,
            'description' => 'test',
            'status' => $approved ? 'approved' : 'pending',
            'created_by' => $this->admin->id,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $debitAccountId, 'type' => 'debit', 'amount' => $amount]);
        JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $creditAccountId, 'type' => 'credit', 'amount' => $amount]);
        return $entry;
    }

    public function test_trial_balance_has_equal_debits_and_credits(): void
    {
        $this->postEntry('2026-01-15', $this->cash->id, $this->revenue->id, 1000.00);
        $this->postEntry('2026-01-20', $this->expense->id, $this->cash->id, 300.00);

        $tb = $this->service->trialBalance(Carbon::parse('2026-12-31'));

        $this->assertTrue($tb['balanced'], 'Trial balance debits must equal credits');
        $this->assertEqualsWithDelta($tb['total_debits'], $tb['total_credits'], 0.01);
    }

    public function test_pending_entries_do_not_appear_in_trial_balance(): void
    {
        $this->postEntry('2026-01-15', $this->cash->id, $this->revenue->id, 1000.00, approved: false);

        $tb = $this->service->trialBalance(Carbon::parse('2026-12-31'));

        $this->assertSame([], $tb['rows']);
    }

    public function test_income_statement_net_income_equals_revenue_minus_expense(): void
    {
        $this->postEntry('2026-02-01', $this->cash->id, $this->revenue->id, 5000.00);
        $this->postEntry('2026-02-10', $this->expense->id, $this->cash->id, 1200.00);

        $is = $this->service->incomeStatement(Carbon::parse('2026-01-01'), Carbon::parse('2026-12-31'));

        $this->assertEqualsWithDelta(5000.00, $is['total_revenue'], 0.01);
        $this->assertEqualsWithDelta(1200.00, $is['total_expenses'], 0.01);
        $this->assertEqualsWithDelta(3800.00, $is['net_income'], 0.01);
    }

    public function test_balance_sheet_balances(): void
    {
        $this->postEntry(Carbon::now()->format('Y-m-d'), $this->cash->id, $this->revenue->id, 2500.00);
        $this->postEntry(Carbon::now()->format('Y-m-d'), $this->expense->id, $this->cash->id, 400.00);
        $this->postEntry(Carbon::now()->format('Y-m-d'), $this->cash->id, $this->payable->id, 750.00);

        $bs = $this->service->balanceSheet(Carbon::now());

        $this->assertTrue($bs['balanced'], 'Balance sheet must balance (A = L + E + YTD NI)');
    }

    public function test_retained_earnings_equals_net_income_when_no_distributions(): void
    {
        $this->postEntry('2026-03-01', $this->cash->id, $this->revenue->id, 8000.00);
        $this->postEntry('2026-03-15', $this->expense->id, $this->cash->id, 2000.00);

        $re = $this->service->retainedEarnings(Carbon::parse('2026-01-01'), Carbon::parse('2026-12-31'));

        $this->assertEqualsWithDelta(6000.00, $re['net_income'], 0.01);
        $this->assertEqualsWithDelta(0.00, $re['distributions'], 0.01);
        $this->assertEqualsWithDelta($re['opening_balance'] + 6000.00, $re['ending_balance'], 0.01);
    }

    public function test_save_report_persists_snapshot(): void
    {
        $this->postEntry('2026-04-01', $this->cash->id, $this->revenue->id, 100.00);

        $response = $this->actingAs($this->manager)->post(route('reports.save'), [
            'type' => 'trial_balance',
            'as_of' => '2026-04-30',
        ]);

        $response->assertRedirect();
        $this->assertSame(1, FinancialReport::count());
        $this->assertSame('trial_balance', FinancialReport::first()->type);
    }

    public function test_report_email_requires_manager_or_admin_recipient(): void
    {
        $accountant = User::factory()->create(['role' => 'accountant', 'status' => 'active']);

        $response = $this->actingAs($this->manager)->post(route('reports.email'), [
            'type' => 'trial_balance',
            'as_of' => '2026-04-30',
            'recipient_email' => $accountant->email,
            'subject' => 'TB',
            'body' => 'hi',
        ]);

        $response->assertSessionHasErrors('recipient_email');
    }
}
