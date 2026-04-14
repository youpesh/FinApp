@extends('reports.pdf._layout')

@section('content')
    <table>
        <tr>
            <td>Retained Earnings, beginning of period</td>
            <td class="text-right font-mono" style="width: 25%;">${{ number_format($data['opening_balance'], 2) }}</td>
        </tr>
        <tr>
            <td>Add: Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</td>
            <td class="text-right font-mono">{{ $data['net_income'] < 0 ? '(' : '' }}${{ number_format(abs($data['net_income']), 2) }}{{ $data['net_income'] < 0 ? ')' : '' }}</td>
        </tr>
        <tr>
            <td>Less: Distributions</td>
            <td class="text-right font-mono">{{ $data['distributions'] > 0 ? '(' : '' }}${{ number_format($data['distributions'], 2) }}{{ $data['distributions'] > 0 ? ')' : '' }}</td>
        </tr>
        <tr class="totals">
            <td><strong>Retained Earnings, end of period</strong></td>
            <td class="text-right font-mono">${{ number_format($data['ending_balance'], 2) }}</td>
        </tr>
    </table>
@endsection
