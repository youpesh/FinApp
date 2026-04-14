@extends('reports.pdf._layout')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Account #</th>
                <th>Account</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $row)
                <tr>
                    <td>{{ $row['account_number'] }}</td>
                    <td>{{ $row['account_name'] }}</td>
                    <td class="text-right font-mono">{{ $row['debit'] > 0 ? '$' . number_format($row['debit'], 2) : '' }}</td>
                    <td class="text-right font-mono">{{ $row['credit'] > 0 ? '$' . number_format($row['credit'], 2) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="2" class="text-right">Totals:</td>
                <td class="text-right font-mono">${{ number_format($data['total_debits'], 2) }}</td>
                <td class="text-right font-mono">${{ number_format($data['total_credits'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @if(!$data['balanced'])
        <p style="color:#b91c1c; margin-top:8px;">Out of balance by ${{ number_format(abs($data['total_debits'] - $data['total_credits']), 2) }}</p>
    @endif
@endsection
