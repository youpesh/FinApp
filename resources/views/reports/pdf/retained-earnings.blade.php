@extends('reports.pdf._layout')

@section('content')
    @php
        $fmt = fn($v) => $v < 0 ? '($' . number_format(abs($v), 2) . ')' : '$' . number_format($v, 2);
    @endphp

    <div class="total-amount-header">Total Amount</div>

    <table>
        <tr>
            <td>Beginning Balance</td>
            <td class="text-right font-mono" style="width: 30%;">{{ $fmt($data['opening_balance']) }}</td>
        </tr>
        <tr>
            <td>Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</td>
            <td class="text-right font-mono">{{ $fmt($data['net_income']) }}</td>
        </tr>
        <tr>
            <td>Less Drawings</td>
            <td class="text-right font-mono">{{ $fmt(-$data['distributions']) }}</td>
        </tr>
        <tr class="grand-total">
            <td>Ending Balance</td>
            <td class="text-right font-mono double-rule">{{ $fmt($data['ending_balance']) }}</td>
        </tr>
    </table>
@endsection
