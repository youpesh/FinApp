<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * FinancialReportService — single source of truth for generating the four
 * financial reports from approved journal entry lines.
 *
 * Conventions:
 *  - Only lines on journal entries with status = 'approved' are included.
 *  - Debit-normal accounts: balance = initial + sum(debits) - sum(credits)
 *  - Credit-normal accounts: balance = initial + sum(credits) - sum(debits)
 */
class FinancialReportService
{
    /**
     * Trial Balance as of a given date.
     * Returns per-account balance in debit or credit column (whichever matches normal side).
     *
     * @return array{
     *   as_of: string,
     *   rows: array<int, array<string, mixed>>,
     *   total_debits: float,
     *   total_credits: float,
     *   balanced: bool
     * }
     */
    public function trialBalance(Carbon $asOf): array
    {
        $accounts = Account::active()->orderBy('account_number')->get();

        $rows = [];
        $totalDebits = 0.0;
        $totalCredits = 0.0;

        foreach ($accounts as $account) {
            $balance = $this->accountBalanceAsOf($account, $asOf);

            // Round to 2dp for display and totals
            $balance = round($balance, 2);

            if (abs($balance) < 0.005) {
                // Skip zero-balance rows for cleanliness (still include if initial != 0? keep simple: skip zero)
                continue;
            }

            $isDebitNormal = strtolower($account->normal_side) === 'debit';
            $debit = 0.0;
            $credit = 0.0;

            if ($balance >= 0) {
                // Balance is on its normal side
                if ($isDebitNormal) {
                    $debit = $balance;
                } else {
                    $credit = $balance;
                }
            } else {
                // Balance opposite its normal side (contra)
                if ($isDebitNormal) {
                    $credit = abs($balance);
                } else {
                    $debit = abs($balance);
                }
            }

            $totalDebits += $debit;
            $totalCredits += $credit;

            $rows[] = [
                'account_id' => $account->id,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'category' => $account->account_category,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        return [
            'as_of' => $asOf->toDateString(),
            'rows' => $rows,
            'total_debits' => round($totalDebits, 2),
            'total_credits' => round($totalCredits, 2),
            'balanced' => abs($totalDebits - $totalCredits) < 0.005,
        ];
    }

    /**
     * Income Statement for a period: Revenue - Expense = Net Income.
     */
    public function incomeStatement(Carbon $from, Carbon $to): array
    {
        $revenueAccounts = Account::active()->where('account_category', 'revenue')->orderBy('account_number')->get();
        $expenseAccounts = Account::active()->where('account_category', 'expense')->orderBy('account_number')->get();

        $revenueRows = [];
        $totalRevenue = 0.0;
        foreach ($revenueAccounts as $account) {
            $activity = $this->periodActivity($account, $from, $to);
            if (abs($activity) < 0.005) continue;
            $totalRevenue += $activity;
            $revenueRows[] = [
                'account_id' => $account->id,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'amount' => round($activity, 2),
            ];
        }

        $expenseRows = [];
        $totalExpenses = 0.0;
        foreach ($expenseAccounts as $account) {
            $activity = $this->periodActivity($account, $from, $to);
            if (abs($activity) < 0.005) continue;
            $totalExpenses += $activity;
            $expenseRows[] = [
                'account_id' => $account->id,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'amount' => round($activity, 2),
            ];
        }

        $netIncome = $totalRevenue - $totalExpenses;

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'revenues' => $revenueRows,
            'expenses' => $expenseRows,
            'total_revenue' => round($totalRevenue, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_income' => round($netIncome, 2),
        ];
    }

    /**
     * Balance Sheet as of date: Assets = Liabilities + Equity (+ period Net Income).
     */
    public function balanceSheet(Carbon $asOf): array
    {
        $assetAccounts = Account::active()->where('account_category', 'asset')->orderBy('account_number')->get();
        $liabilityAccounts = Account::active()->where('account_category', 'liability')->orderBy('account_number')->get();
        $equityAccounts = Account::active()->where('account_category', 'equity')->orderBy('account_number')->get();

        [$assetRows, $totalAssets] = $this->balancesFor($assetAccounts, $asOf);
        [$liabilityRows, $totalLiabilities] = $this->balancesFor($liabilityAccounts, $asOf);
        [$equityRows, $totalEquity] = $this->balancesFor($equityAccounts, $asOf);

        // Net income from start of year through $asOf is implicitly included in equity only if
        // closing entries have been made. To make BS balance before closing, add YTD net income
        // as a separate equity line.
        $yearStart = $asOf->copy()->startOfYear();
        $is = $this->incomeStatement($yearStart, $asOf);
        $ytdNetIncome = $is['net_income'];

        $totalEquityWithNI = $totalEquity + $ytdNetIncome;

        return [
            'as_of' => $asOf->toDateString(),
            'assets' => $assetRows,
            'liabilities' => $liabilityRows,
            'equity' => $equityRows,
            'total_assets' => round($totalAssets, 2),
            'total_liabilities' => round($totalLiabilities, 2),
            'total_equity' => round($totalEquity, 2),
            'ytd_net_income' => round($ytdNetIncome, 2),
            'total_liabilities_and_equity' => round($totalLiabilities + $totalEquityWithNI, 2),
            'balanced' => abs($totalAssets - ($totalLiabilities + $totalEquityWithNI)) < 0.005,
        ];
    }

    /**
     * Retained Earnings statement for a period.
     * Opening RE + Net Income - Distributions = Ending RE
     * RE accounts are equity accounts with `statement = 'RE'`.
     */
    public function retainedEarnings(Carbon $from, Carbon $to): array
    {
        $reAccounts = Account::active()
            ->where('account_category', 'equity')
            ->where('statement', 'RE')
            ->orderBy('account_number')
            ->get();

        // Opening balance: RE total as of (from - 1 day)
        $openingDate = $from->copy()->subDay();
        $openingBalance = 0.0;
        foreach ($reAccounts as $account) {
            $openingBalance += $this->accountBalanceAsOf($account, $openingDate);
        }

        // Net income for the period
        $is = $this->incomeStatement($from, $to);
        $netIncome = $is['net_income'];

        // Distributions: any debits against RE accounts during the period (e.g. dividends)
        $distributions = 0.0;
        foreach ($reAccounts as $account) {
            $debits = $this->sumApprovedLines($account, 'debit', $from, $to);
            $credits = $this->sumApprovedLines($account, 'credit', $from, $to);
            // For a credit-normal equity account, debits reduce the balance — treat as distributions.
            // Net credits are just closing entries that flow through net income, so don't double-count.
            if (strtolower($account->normal_side) === 'credit') {
                $distributions += $debits; // only raw debits, not net
            }
        }

        $endingBalance = $openingBalance + $netIncome - $distributions;

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'opening_balance' => round($openingBalance, 2),
            'net_income' => round($netIncome, 2),
            'distributions' => round($distributions, 2),
            'ending_balance' => round($endingBalance, 2),
        ];
    }

    // ── helpers ──────────────────────────────────────────────────────

    private function balancesFor(Collection $accounts, Carbon $asOf): array
    {
        $rows = [];
        $total = 0.0;
        foreach ($accounts as $account) {
            $balance = round($this->accountBalanceAsOf($account, $asOf), 2);
            if (abs($balance) < 0.005) continue;
            $total += $balance;
            $rows[] = [
                'account_id' => $account->id,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'amount' => $balance,
            ];
        }
        return [$rows, round($total, 2)];
    }

    /**
     * Compute an account's balance as of the given date, signed on its normal side.
     */
    public function accountBalanceAsOf(Account $account, Carbon $asOf): float
    {
        $debits = $this->sumApprovedLinesUpTo($account, 'debit', $asOf);
        $credits = $this->sumApprovedLinesUpTo($account, 'credit', $asOf);
        $initial = (float) $account->initial_balance;

        if (strtolower($account->normal_side) === 'debit') {
            return $initial + $debits - $credits;
        }
        return $initial + $credits - $debits;
    }

    /**
     * Period activity on its normal side (positive = increase).
     */
    public function periodActivity(Account $account, Carbon $from, Carbon $to): float
    {
        $debits = $this->sumApprovedLines($account, 'debit', $from, $to);
        $credits = $this->sumApprovedLines($account, 'credit', $from, $to);

        if (strtolower($account->normal_side) === 'debit') {
            return $debits - $credits;
        }
        return $credits - $debits;
    }

    private function sumApprovedLinesUpTo(Account $account, string $type, Carbon $asOf): float
    {
        return (float) JournalEntryLine::where('account_id', $account->id)
            ->where('type', $type)
            ->whereHas('journalEntry', function ($q) use ($asOf) {
                $q->where('status', 'approved')->whereDate('date', '<=', $asOf);
            })
            ->sum('amount');
    }

    private function sumApprovedLines(Account $account, string $type, Carbon $from, Carbon $to): float
    {
        return (float) JournalEntryLine::where('account_id', $account->id)
            ->where('type', $type)
            ->whereHas('journalEntry', function ($q) use ($from, $to) {
                $q->where('status', 'approved')
                  ->whereDate('date', '>=', $from)
                  ->whereDate('date', '<=', $to);
            })
            ->sum('amount');
    }
}
