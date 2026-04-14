@extends('reports.pdf._layout')

@section('content')
    <div class="section-title">Assets</div>
    <table>
        @foreach($data['assets'] as $row)
            <tr>
                <td style="width: 15%;">{{ $row['account_number'] }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td class="text-right font-mono" style="width: 20%;">${{ number_format($row['amount'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="totals">
            <td colspan="2" class="text-right">Total Assets</td>
            <td class="text-right font-mono">${{ number_format($data['total_assets'], 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Liabilities</div>
    <table>
        @foreach($data['liabilities'] as $row)
            <tr>
                <td style="width: 15%;">{{ $row['account_number'] }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td class="text-right font-mono" style="width: 20%;">${{ number_format($row['amount'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="totals">
            <td colspan="2" class="text-right">Total Liabilities</td>
            <td class="text-right font-mono">${{ number_format($data['total_liabilities'], 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Equity</div>
    <table>
        @foreach($data['equity'] as $row)
            <tr>
                <td style="width: 15%;">{{ $row['account_number'] }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td class="text-right font-mono" style="width: 20%;">${{ number_format($row['amount'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" class="text-right" style="color:#666;"><em>Current year net income</em></td>
            <td class="text-right font-mono">${{ number_format($data['ytd_net_income'], 2) }}</td>
        </tr>
        <tr class="totals">
            <td colspan="2" class="text-right">Total Equity</td>
            <td class="text-right font-mono">${{ number_format($data['total_equity'] + $data['ytd_net_income'], 2) }}</td>
        </tr>
    </table>

    <table style="margin-top: 14px;">
        <tr class="totals">
            <td><strong>Total Liabilities + Equity</strong></td>
            <td class="text-right font-mono">${{ number_format($data['total_liabilities_and_equity'], 2) }}</td>
        </tr>
    </table>
    @if(!$data['balanced'])
        <p style="color:#b91c1c; margin-top:8px;">Warning: balance sheet does not balance (Assets vs L+E differ by ${{ number_format(abs($data['total_assets'] - $data['total_liabilities_and_equity']), 2) }}).</p>
    @endif
@endsection
