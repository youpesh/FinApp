@extends('reports.pdf._layout')

@section('content')
    @php
        $hasYtd = abs($data['ytd_net_income']) >= 0.005;
        $fmt = fn($v) => $v < 0 ? '(' . number_format(abs($v), 2) . ')' : number_format($v, 2);
    @endphp

    <table style="width:100%; border:0;">
        <tr style="vertical-align: top;">
            {{-- ASSETS column --}}
            <td style="width: 50%; padding-right: 18px; border:0;">
                @forelse($data['asset_groups'] ?? [] as $group)
                    <div style="font-style: italic; font-weight: bold; margin: 6px 0 2px 0;">{{ $group['subcategory'] }}</div>
                    <table style="width:100%; border-collapse: collapse;">
                        @foreach($group['rows'] as $row)
                            <tr>
                                <td style="padding: 1px 0 1px 14px; border:0;">{{ $row['account_name'] }}</td>
                                <td class="text-right font-mono" style="width: 25%; padding: 1px 0; border:0; {{ $loop->last ? 'border-top: 1px solid #111;' : '' }}">{{ $fmt($row["amount"]) }}</td>
                                <td class="text-right font-mono" style="width: 25%; padding: 1px 0 1px 6px; border:0;">{{ $loop->last ? $fmt($group["subtotal"]) : '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                @empty
                    <em>No assets.</em>
                @endforelse

                <table style="width:100%; margin-top: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="font-weight: bold; padding: 4px 0; border:0;">Total Assets</td>
                        <td class="text-right font-mono" style="width: 25%; font-weight: bold; padding: 4px 0; border-top: 1px solid #111; border-bottom: 3px double #111;">{{ number_format($data['total_assets'], 2) }}</td>
                    </tr>
                </table>
            </td>

            {{-- LIABILITIES + EQUITY column --}}
            <td style="width: 50%; padding-left: 18px; border:0;">
                @foreach($data['liability_groups'] ?? [] as $group)
                    <div style="font-style: italic; font-weight: bold; margin: 6px 0 2px 0;">{{ $group['subcategory'] }}</div>
                    <table style="width:100%; border-collapse: collapse;">
                        @foreach($group['rows'] as $row)
                            <tr>
                                <td style="padding: 1px 0 1px 14px; border:0;">{{ $row['account_name'] }}</td>
                                <td class="text-right font-mono" style="width: 25%; padding: 1px 0; border:0; {{ $loop->last ? 'border-top: 1px solid #111;' : '' }}">{{ $fmt($row["amount"]) }}</td>
                                <td class="text-right font-mono" style="width: 25%; padding: 1px 0 1px 6px; border:0;">{{ $loop->last ? $fmt($group["subtotal"]) : '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endforeach

                @foreach($data['equity_groups'] ?? [] as $eqGroup)
                    @php
                        $isLastEquityGroup = $loop->last;
                        $subtotalWithNI = $eqGroup['subtotal'] + ($isLastEquityGroup && $hasYtd ? $data['ytd_net_income'] : 0);
                    @endphp
                    <div style="font-style: italic; font-weight: bold; margin: 6px 0 2px 0;">{{ $eqGroup['subcategory'] }}</div>
                    <table style="width:100%; border-collapse: collapse;">
                        @foreach($eqGroup['rows'] as $row)
                            @php $isFinalRow = $loop->last && !($isLastEquityGroup && $hasYtd); @endphp
                            <tr>
                                <td style="padding: 1px 0 1px 14px; border:0;">{{ $row['account_name'] }}</td>
                                <td class="text-right font-mono" style="width: 25%; padding: 1px 0; border:0; {{ $isFinalRow ? 'border-top: 1px solid #111;' : '' }}">{{ $fmt($row["amount"]) }}</td>
                                <td class="text-right font-mono" style="width: 25%; padding: 1px 0 1px 6px; border:0;">{{ $isFinalRow ? $fmt($subtotalWithNI) : '' }}</td>
                            </tr>
                        @endforeach
                        @if($isLastEquityGroup && $hasYtd)
                            <tr>
                                <td style="padding: 1px 0 1px 14px; border:0; font-style: italic; color:#555;">Current year net income</td>
                                <td class="text-right font-mono" style="width: 25%; padding: 1px 0; border-top: 1px solid #111;">{{ $fmt($data["ytd_net_income"]) }}</td>
                                <td class="text-right font-mono" style="width: 25%; padding: 1px 0 1px 6px; border:0;">{{ $fmt($subtotalWithNI) }}</td>
                            </tr>
                        @endif
                    </table>
                @endforeach

                <table style="width:100%; margin-top: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="font-weight: bold; padding: 4px 0; border:0;">Total Equities</td>
                        <td class="text-right font-mono" style="width: 25%; font-weight: bold; padding: 4px 0; border-top: 1px solid #111; border-bottom: 3px double #111;">{{ number_format($data['total_liabilities_and_equity'], 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if(!$data['balanced'])
        <p style="color:#b91c1c; margin-top:8px;">Warning: balance sheet does not balance (Assets vs Equities differ by ${{ number_format(abs($data['total_assets'] - $data['total_liabilities_and_equity']), 2) }}).</p>
    @endif
@endsection
