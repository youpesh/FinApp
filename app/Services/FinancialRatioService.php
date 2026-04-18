<?php

namespace App\Services;

use App\Models\Account;
use Carbon\Carbon;

/**
 * Computes standard financial ratios from approved journal activity.
 * Wraps FinancialReportService for balance / income figures and classifies
 * each ratio into a green / yellow / red health band.
 */
class FinancialRatioService
{
    public function __construct(private FinancialReportService $reports)
    {
    }

    /**
     * @return array{
     *   as_of: string,
     *   liquidity: array<int, array<string, mixed>>,
     *   profitability: array<int, array<string, mixed>>,
     *   leverage: array<int, array<string, mixed>>,
     * }
     */
    public function compute(?Carbon $asOf = null): array
    {
        $asOf = $asOf ?: Carbon::today();
        $yearStart = $asOf->copy()->startOfYear();

        $bs = $this->reports->balanceSheet($asOf);
        $is = $this->reports->incomeStatement($yearStart, $asOf);

        $currentAssets = $this->sumSubcategory('asset', 'Current Assets', $asOf);
        $currentLiabilities = $this->sumSubcategory('liability', 'Current Liabilities', $asOf);
        $cash = $this->sumByAccountNumbers([10100], $asOf);
        $receivables = $this->sumByAccountNumbers([10200], $asOf);
        $quickAssets = $cash + $receivables;

        $totalAssets = (float) $bs['total_assets'];
        $totalLiabilities = (float) $bs['total_liabilities'];
        // Equity on BS excludes YTD net income by default; include it for ratios.
        $totalEquity = (float) $bs['total_equity'] + (float) $bs['ytd_net_income'];
        $revenue = (float) $is['total_revenue'];
        $netIncome = (float) $is['net_income'];

        $currentRatio = $this->safeDivide($currentAssets, $currentLiabilities);
        $quickRatio = $this->safeDivide($quickAssets, $currentLiabilities);
        $cashRatio = $this->safeDivide($cash, $currentLiabilities);
        $workingCapital = $currentAssets - $currentLiabilities;
        $debtToEquity = $this->safeDivide($totalLiabilities, $totalEquity);
        $debtRatio = $this->safeDivide($totalLiabilities, $totalAssets);
        $netMargin = $this->safeDivide($netIncome, $revenue);
        $roa = $this->safeDivide($netIncome, $totalAssets);
        $roe = $this->safeDivide($netIncome, $totalEquity);

        return [
            'as_of' => $asOf->toDateString(),
            'liquidity' => [
                $this->ratio(
                    label: 'Current Ratio',
                    value: $currentRatio,
                    display: $this->formatRatio($currentRatio),
                    status: $this->bandHigherBetter($currentRatio, 2.0, 1.0),
                    subtitle: sprintf('$%s current assets per $1 of current liabilities', number_format($currentRatio ?? 0, 2)),
                    formula: 'Current Assets ÷ Current Liabilities',
                ),
                $this->ratio(
                    label: 'Quick Ratio',
                    value: $quickRatio,
                    display: $this->formatRatio($quickRatio),
                    status: $this->bandHigherBetter($quickRatio, 1.0, 0.5),
                    subtitle: 'Excludes inventory and prepaids',
                    formula: '(Cash + Receivables) ÷ Current Liabilities',
                ),
                $this->ratio(
                    label: 'Cash Ratio',
                    value: $cashRatio,
                    display: $this->formatRatio($cashRatio),
                    status: $this->bandHigherBetter($cashRatio, 0.5, 0.2),
                    subtitle: 'Cash-only short-term coverage',
                    formula: 'Cash ÷ Current Liabilities',
                ),
                $this->ratio(
                    label: 'Working Capital',
                    value: $workingCapital,
                    display: $this->formatCurrency($workingCapital),
                    status: $this->bandWorkingCapital($workingCapital, $currentLiabilities),
                    subtitle: 'Short-term cushion above liabilities',
                    formula: 'Current Assets − Current Liabilities',
                ),
            ],
            'profitability' => [
                $this->ratio(
                    label: 'Net Profit Margin',
                    value: $netMargin,
                    display: $this->formatPercent($netMargin),
                    status: $this->bandMargin($netMargin),
                    subtitle: 'Profit earned per $1 of revenue',
                    formula: 'Net Income ÷ Revenue',
                ),
                $this->ratio(
                    label: 'Return on Assets',
                    value: $roa,
                    display: $this->formatPercent($roa),
                    status: $this->bandHigherBetter($roa, 0.05, 0.01),
                    subtitle: 'Efficiency of asset utilization',
                    formula: 'Net Income ÷ Total Assets',
                ),
                $this->ratio(
                    label: 'Return on Equity',
                    value: $roe,
                    display: $this->formatPercent($roe),
                    status: $this->bandHigherBetter($roe, 0.15, 0.05),
                    subtitle: 'Return delivered to owners',
                    formula: 'Net Income ÷ Total Equity',
                ),
            ],
            'leverage' => [
                $this->ratio(
                    label: 'Debt-to-Equity',
                    value: $debtToEquity,
                    display: $this->formatRatio($debtToEquity),
                    status: $this->bandLowerBetter($debtToEquity, 1.0, 2.0),
                    subtitle: 'Creditor vs owner financing mix',
                    formula: 'Total Liabilities ÷ Total Equity',
                ),
                $this->ratio(
                    label: 'Debt Ratio',
                    value: $debtRatio,
                    display: $this->formatPercent($debtRatio),
                    status: $this->bandLowerBetter($debtRatio, 0.5, 0.7),
                    subtitle: 'Share of assets financed by debt',
                    formula: 'Total Liabilities ÷ Total Assets',
                ),
            ],
        ];
    }

    private function sumSubcategory(string $category, string $subcategory, Carbon $asOf): float
    {
        $accounts = Account::active()
            ->where('account_category', $category)
            ->where('account_subcategory', $subcategory)
            ->get();

        $total = 0.0;
        foreach ($accounts as $account) {
            $total += $this->reports->accountBalanceAsOf($account, $asOf);
        }
        return round($total, 2);
    }

    private function sumByAccountNumbers(array $numbers, Carbon $asOf): float
    {
        $accounts = Account::active()->whereIn('account_number', $numbers)->get();
        $total = 0.0;
        foreach ($accounts as $account) {
            $total += $this->reports->accountBalanceAsOf($account, $asOf);
        }
        return round($total, 2);
    }

    private function safeDivide(float $numerator, float $denominator): ?float
    {
        if (abs($denominator) < 0.005) {
            return null;
        }
        return $numerator / $denominator;
    }

    /**
     * Higher value is better. green ≥ $greenMin, yellow ≥ $yellowMin, else red.
     */
    private function bandHigherBetter(?float $v, float $greenMin, float $yellowMin): string
    {
        if ($v === null) return 'gray';
        if ($v >= $greenMin) return 'green';
        if ($v >= $yellowMin) return 'yellow';
        return 'red';
    }

    /**
     * Lower value is better. green ≤ $greenMax, yellow ≤ $yellowMax, else red.
     * A negative ratio means the denominator flipped sign (e.g. negative equity
     * for D/E) — that's an insolvency signal, not a low-leverage win, so red.
     */
    private function bandLowerBetter(?float $v, float $greenMax, float $yellowMax): string
    {
        if ($v === null) return 'gray';
        if ($v < 0) return 'red';
        if ($v <= $greenMax) return 'green';
        if ($v <= $yellowMax) return 'yellow';
        return 'red';
    }

    private function bandMargin(?float $v): string
    {
        if ($v === null) return 'gray';
        if ($v >= 0.10) return 'green';
        if ($v >= 0.0) return 'yellow';
        return 'red';
    }

    /**
     * Working capital: red if negative, yellow if positive but thin relative to liabilities, green otherwise.
     */
    private function bandWorkingCapital(float $wc, float $currentLiabilities): string
    {
        if ($wc < 0) return 'red';
        if ($currentLiabilities > 0 && $wc < $currentLiabilities) return 'yellow';
        return 'green';
    }

    private function formatRatio(?float $v): string
    {
        return $v === null ? 'N/A' : number_format($v, 2);
    }

    private function formatPercent(?float $v): string
    {
        return $v === null ? 'N/A' : number_format($v * 100, 1) . '%';
    }

    private function formatCurrency(float $v): string
    {
        $sign = $v < 0 ? '-' : '';
        return $sign . '$' . number_format(abs($v), 2);
    }

    private function ratio(
        string $label,
        ?float $value,
        string $display,
        string $status,
        string $subtitle,
        string $formula,
    ): array {
        return [
            'label' => $label,
            'value' => $value,
            'display' => $display,
            'status' => $status,
            'subtitle' => $subtitle,
            'formula' => $formula,
        ];
    }
}
