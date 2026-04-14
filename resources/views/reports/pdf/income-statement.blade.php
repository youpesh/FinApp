@extends('reports.pdf._layout')

@section('content')
    <div class="section-title">Revenue</div>
    <table>
        @foreach($data['revenues'] as $row)
            <tr>
                <td style="width: 15%;">{{ $row['account_number'] }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td class="text-right font-mono" style="width: 20%;">${{ number_format($row['amount'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="totals">
            <td colspan="2" class="text-right">Total Revenue</td>
            <td class="text-right font-mono">${{ number_format($data['total_revenue'], 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Expenses</div>
    <table>
        @foreach($data['expenses'] as $row)
            <tr>
                <td style="width: 15%;">{{ $row['account_number'] }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td class="text-right font-mono" style="width: 20%;">${{ number_format($row['amount'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="totals">
            <td colspan="2" class="text-right">Total Expenses</td>
            <td class="text-right font-mono">${{ number_format($data['total_expenses'], 2) }}</td>
        </tr>
    </table>

    <table style="margin-top: 14px;">
        <tr class="totals">
            <td><strong>Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</strong></td>
            <td class="text-right font-mono">${{ number_format(abs($data['net_income']), 2) }}</td>
        </tr>
    </table>
@endsection
