@extends('reports.pdf._layout')

@section('content')
    @php
        $fmt = fn($v) => $v < 0 ? '($' . number_format(abs($v), 2) . ')' : '$' . number_format($v, 2);
        $hasYtd = abs($data['ytd_net_income']) >= 0.005;
        $assetGroups = $data['asset_groups'] ?? [];
        $liabilityGroups = $data['liability_groups'] ?? [];
        $equityGroups = $data['equity_groups'] ?? [];
    @endphp

    <div class="total-amount-header">Total Amount</div>

    {{-- Assets --}}
    <div class="section-label">Assets</div>
    <table>
        @foreach($assetGroups as $group)
            <tr class="group-row">
                <td class="indent-1" style="font-weight: bold;">{{ $group['subcategory'] }}</td>
                <td class="text-right font-mono" style="width: 25%; font-weight: bold;">{{ $fmt($group['subtotal']) }}</td>
            </tr>
            @foreach($group['rows'] as $row)
                <tr class="group-row">
                    <td class="indent-2">{{ $row['account_name'] }}</td>
                    <td class="text-right font-mono">{{ $fmt($row['amount']) }}</td>
                </tr>
            @endforeach
        @endforeach
        <tr class="grand-total">
            <td class="indent-1">Total Assets</td>
            <td class="text-right font-mono double-rule" style="width: 25%;">{{ $fmt($data['total_assets']) }}</td>
        </tr>
    </table>

    {{-- Equity & Liabilities --}}
    <div class="section-label">Equity &amp; Liabilities</div>
    <table>
        @foreach($equityGroups as $eqGroup)
            @php
                $isLastEquityGroup = $loop->last;
                $subtotalWithNI = $eqGroup['subtotal'] + ($isLastEquityGroup && $hasYtd ? $data['ytd_net_income'] : 0);
            @endphp
            <tr class="group-row">
                <td class="indent-1" style="font-weight: bold;">{{ $eqGroup['subcategory'] }}</td>
                <td class="text-right font-mono" style="width: 25%; font-weight: bold;">{{ $fmt($subtotalWithNI) }}</td>
            </tr>
            @foreach($eqGroup['rows'] as $row)
                <tr class="group-row">
                    <td class="indent-2">{{ $row['account_name'] }}</td>
                    <td class="text-right font-mono">{{ $fmt($row['amount']) }}</td>
                </tr>
            @endforeach
            @if($isLastEquityGroup && $hasYtd)
                <tr class="group-row">
                    <td class="indent-2" style="font-style: italic; color: #555;">Income Estimation</td>
                    <td class="text-right font-mono">{{ $fmt($data['ytd_net_income']) }}</td>
                </tr>
            @endif
        @endforeach

        @foreach($liabilityGroups as $group)
            <tr class="group-row">
                <td class="indent-1" style="font-weight: bold;">{{ $group['subcategory'] }}</td>
                <td class="text-right font-mono" style="width: 25%; font-weight: bold;">{{ $fmt($group['subtotal']) }}</td>
            </tr>
            @foreach($group['rows'] as $row)
                <tr class="group-row">
                    <td class="indent-2">{{ $row['account_name'] }}</td>
                    <td class="text-right font-mono">{{ $fmt($row['amount']) }}</td>
                </tr>
            @endforeach
        @endforeach

        <tr class="grand-total">
            <td class="indent-1">Total Equity &amp; Liabilities</td>
            <td class="text-right font-mono double-rule" style="width: 25%;">{{ $fmt($data['total_liabilities_and_equity']) }}</td>
        </tr>
    </table>

    @if(!$data['balanced'])
        <p style="color:#b91c1c; margin-top:8px;">Warning: balance sheet does not balance (differs by ${{ number_format(abs($data['total_assets'] - $data['total_liabilities_and_equity']), 2) }}).</p>
    @endif
@endsection
