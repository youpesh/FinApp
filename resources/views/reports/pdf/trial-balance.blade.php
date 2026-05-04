@extends('reports.pdf._layout')

@section('content')
    <table>
        <thead>
            <tr>
                <th style="border-bottom: 1px solid #444; padding-bottom: 4px;">Account</th>
                <th class="text-right" style="width: 18%; border-bottom: 1px solid #444; padding-bottom: 4px;">Debit</th>
                <th class="text-right" style="width: 18%; border-bottom: 1px solid #444; padding-bottom: 4px;">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $row)
                <tr>
                    <td>{{ $row['account_number'] }} - {{ $row['account_name'] }}</td>
                    <td class="text-right font-mono">{{ $row['debit'] > 0 ? '$' . number_format($row['debit'], 2) : '' }}</td>
                    <td class="text-right font-mono">{{ $row['credit'] > 0 ? '$' . number_format($row['credit'], 2) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td>Total</td>
                <td class="text-right font-mono double-rule">${{ number_format($data['total_debits'], 2) }}</td>
                <td class="text-right font-mono double-rule">${{ number_format($data['total_credits'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @if(!$data['balanced'])
        <p style="color:#b91c1c; margin-top:8px;">Out of balance by ${{ number_format(abs($data['total_debits'] - $data['total_credits']), 2) }}</p>
    @endif
@endsection
