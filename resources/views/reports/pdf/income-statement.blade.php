@extends('reports.pdf._layout')

@section('content')
    @php
        $fmt = fn($v) => $v < 0 ? '($' . number_format(abs($v), 2) . ')' : '$' . number_format($v, 2);
    @endphp

    <div class="total-amount-header">Total Amount</div>

    <div class="section-label">Revenue</div>
    <table>
        @foreach($data['revenues'] as $row)
            <tr>
                <td class="indent-1">{{ $row['account_name'] }}</td>
                <td class="text-right font-mono" style="width: 25%;">{{ $fmt($row['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="grand-total">
            <td class="indent-1">Revenue Total</td>
            <td class="text-right font-mono single-rule">{{ $fmt($data['total_revenue']) }}</td>
        </tr>
    </table>

    <div class="section-label">Expenses</div>
    <table>
        @foreach($data['expenses'] as $row)
            <tr>
                <td class="indent-1">{{ $row['account_name'] }}</td>
                <td class="text-right font-mono" style="width: 25%;">{{ $fmt($row['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="grand-total">
            <td class="indent-1">Expenses Total</td>
            <td class="text-right font-mono single-rule">{{ $fmt(-$data['total_expenses']) }}</td>
        </tr>
    </table>

    <div class="section-label">Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</div>
    <table>
        <tr class="grand-total">
            <td class="indent-1">Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }} Total</td>
            <td class="text-right font-mono double-rule" style="width: 25%;">{{ $fmt($data['net_income']) }}</td>
        </tr>
    </table>
@endsection
