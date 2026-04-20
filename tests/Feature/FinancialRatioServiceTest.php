<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\User;
use App\Services\FinancialRatioService;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialRatioServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected FinancialRatioService $service;

    protected Account $cash;
    protected Account $receivable;
    protected Account $prepaid;
    protected Account $equipment;
    protected Account $accountsPayable;
    protected Account $notesPayable;
    protected Account $commonStock;
    protected Account $retainedEarnings;
    protected Account $revenue;
    protected Account $expense;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->service = new FinancialRatioService(new FinancialReportService());

        $this->cash = $this->makeAccount(10100, 'Cash', 'debit', 'asset', 'Current Assets');
        $this->receivable = $this->makeAccount(10200, 'Accounts Receivable', 'debit', 'asset', 'Current Assets');
        $this->prepaid = $this->makeAccount(10400, 'Prepaid Insurance', 'debit', 'asset', 'Current Assets');
        $this->equipment = $this->makeAccount(15100, 'Equipment', 'debit', 'asset', 'Fixed Assets');
        $this->accountsPayable = $this->makeAccount(20100, 'Accounts Payable', 'credit', 'liability', 'Current Liabilities');
        $this->notesPayable = $this->makeAccount(20400, 'Notes Payable', 'credit', 'liability', 'Long-Term Liabilities');
        $this->commonStock = $this->makeAccount(30100, 'Common Stock', 'credit', 'equity', 'Equity');
        $this->retainedEarnings = $this->makeAccount(30200, 'Retained Earnings', 'credit', 'equity', 'Equity', 'RE');
        $this->revenue = $this->makeAccount(40100, 'Service Revenue', 'credit', 'revenue', 'Operating Revenue', 'IS');
        $this->expense = $this->makeAccount(50100, 'Rent Expense', 'debit', 'expense', 'Operating Expenses', 'IS');
    }

    private function makeAccount(int $number, string $name, string $side, string $category, string $subcategory, string $statement = 'BS', float $initial = 0.0): Account
    {
        return Account::create([
            'account_number' => $number,
            'account_name' => $name,
            'normal_side' => $side,
            'account_category' => $category,
            'account_subcategory' => $subcategory,
            'statement' => $statement,
            'initial_balance' => $initial,
            'balance' => $initial,
            'order' => $number,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);
    }

    private function postEntry(string $date, int $debitAccountId, int $creditAccountId, float $amount): void
    {
        $entry = JournalEntry::create([
            'reference_id' => 'JE-TEST-' . uniqid(),
            'date' => $date,
            'description' => 'test',
            'status' => 'approved',
            'created_by' => $this->admin->id,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $debitAccountId, 'type' => 'debit', 'amount' => $amount]);
        JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $creditAccountId, 'type' => 'credit', 'amount' => $amount]);
    }

    public function test_current_ratio_is_current_assets_over_current_liabilities(): void
    {
        // Opening balances via a single capitalization entry
        $this->postEntry('2026-01-01', $this->cash->id, $this->commonStock->id, 40000);
        $this->postEntry('2026-01-01', $this->receivable->id, $this->revenue->id, 10000);
        $this->postEntry('2026-01-01', $this->prepaid->id, $this->cash->id, 2000);
        $this->postEntry('2026-01-15', $this->expense->id, $this->accountsPayable->id, 5000);

        $r = $this->service->compute(Carbon::parse('2026-06-30'));

        // CA = 38000 cash + 10000 AR + 2000 prepaid = 50000
        // CL = 5000 AP
        // Current Ratio = 10.0
        $current = collect($r['liquidity'])->firstWhere('label', 'Current Ratio');
        $this->assertEqualsWithDelta(10.0, $current['value'], 0.01);
        $this->assertSame('green', $current['status']);
    }

    public function test_quick_ratio_excludes_prepaids(): void
    {
        $this->postEntry('2026-01-01', $this->cash->id, $this->commonStock->id, 10000);
        $this->postEntry('2026-01-01', $this->receivable->id, $this->revenue->id, 5000);
        $this->postEntry('2026-01-01', $this->prepaid->id, $this->cash->id, 3000); // prepaid should NOT count
        $this->postEntry('2026-01-15', $this->expense->id, $this->accountsPayable->id, 10000);

        $r = $this->service->compute(Carbon::parse('2026-06-30'));

        $quick = collect($r['liquidity'])->firstWhere('label', 'Quick Ratio');
        // Quick Assets = cash 7000 + AR 5000 = 12000; CL = 10000 → 1.20
        $this->assertEqualsWithDelta(1.20, $quick['value'], 0.01);
        $this->assertSame('green', $quick['status']);
    }

    public function test_working_capital_is_red_when_negative(): void
    {
        $this->postEntry('2026-01-01', $this->cash->id, $this->commonStock->id, 1000);
        $this->postEntry('2026-01-15', $this->expense->id, $this->accountsPayable->id, 5000);

        $r = $this->service->compute(Carbon::parse('2026-06-30'));

        $wc = collect($r['liquidity'])->firstWhere('label', 'Working Capital');
        $this->assertLessThan(0, $wc['value']);
        $this->assertSame('red', $wc['status']);
    }

    public function test_net_profit_margin_bands(): void
    {
        // Revenue 10000, expenses 500 → margin 95% → green
        $this->postEntry('2026-02-01', $this->cash->id, $this->revenue->id, 10000);
        $this->postEntry('2026-02-10', $this->expense->id, $this->cash->id, 500);

        $r = $this->service->compute(Carbon::parse('2026-06-30'));

        $npm = collect($r['profitability'])->firstWhere('label', 'Net Profit Margin');
        $this->assertEqualsWithDelta(0.95, $npm['value'], 0.01);
        $this->assertSame('green', $npm['status']);
    }

    public function test_net_profit_margin_red_when_loss(): void
    {
        $this->postEntry('2026-02-01', $this->cash->id, $this->revenue->id, 1000);
        $this->postEntry('2026-02-10', $this->expense->id, $this->cash->id, 5000);

        $r = $this->service->compute(Carbon::parse('2026-06-30'));

        $npm = collect($r['profitability'])->firstWhere('label', 'Net Profit Margin');
        $this->assertLessThan(0, $npm['value']);
        $this->assertSame('red', $npm['status']);
    }

    public function test_zero_denominator_produces_na_and_gray(): void
    {
        // No entries at all → revenue=0, liabilities=0
        $r = $this->service->compute(Carbon::parse('2026-06-30'));

        $current = collect($r['liquidity'])->firstWhere('label', 'Current Ratio');
        $this->assertNull($current['value']);
        $this->assertSame('N/A', $current['display']);
        $this->assertSame('gray', $current['status']);
    }

    public function test_debt_to_equity_classifies_negative_equity_as_red(): void
    {
        // Build a scenario with positive liabilities but negative equity (accumulated losses).
        // Start with $1000 equity, then expense $5000 funded by AP.
        $this->postEntry('2026-01-01', $this->cash->id, $this->commonStock->id, 1000);
        $this->postEntry('2026-01-15', $this->expense->id, $this->accountsPayable->id, 5000);

        $r = $this->service->compute(Carbon::parse('2026-06-30'));

        $de = collect($r['leverage'])->firstWhere('label', 'Debt-to-Equity');
        // Equity = 1000 CS + (-5000 NI) = -4000; D/E = 5000 / -4000 = -1.25
        $this->assertLessThan(0, $de['value']);
        $this->assertSame('red', $de['status']);
    }

    public function test_compute_returns_expected_groups_and_as_of_date(): void
    {
        $asOf = Carbon::parse('2026-03-15');
        $r = $this->service->compute($asOf);

        $this->assertArrayHasKey('liquidity', $r);
        $this->assertArrayHasKey('profitability', $r);
        $this->assertArrayHasKey('leverage', $r);
        $this->assertSame('2026-03-15', $r['as_of']);
        $this->assertCount(4, $r['liquidity']);
        $this->assertCount(3, $r['profitability']);
        $this->assertCount(2, $r['leverage']);
    }
}
