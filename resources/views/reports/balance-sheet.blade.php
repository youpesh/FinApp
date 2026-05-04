<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Reports', 'url' => route('reports.index')],
        ['label' => 'Balance Sheet'],
    ]">
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Balance Sheet</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-5 rounded-lg border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('reports.balance-sheet') }}" class="flex items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">As of Date</label>
                        <input type="date" name="as_of" value="{{ $asOf->toDateString() }}" required
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Update</button>
                </form>
            </div>

            @include('reports._actions', [
                'type' => 'balance_sheet',
                'params' => ['as_of' => $asOf->toDateString()],
                'emailSubject' => 'Balance Sheet as of ' . $asOf->toFormattedDateString(),
            ])

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                @include('reports._heading', [
                    'statement' => 'Balance Sheet',
                    'period' => 'As of ' . $asOf->format('F d, Y'),
                ])
                @if(!$data['balanced'])
                    <p class="px-6 pt-2 text-sm text-red-600 font-semibold">Warning: Balance sheet does not balance.</p>
                @endif

                @php
                    $hasYtd = abs($data['ytd_net_income']) >= 0.005;
                    $equityGroups = $data['equity_groups'] ?? [];
                @endphp

                <div class="px-8 py-6 grid md:grid-cols-2 gap-12 font-serif text-gray-900">
                    {{-- ASSETS (left column of the T) --}}
                    <div>
                        @forelse($data['asset_groups'] ?? [] as $group)
                            <p class="italic font-semibold mb-1">{{ $group['subcategory'] }}</p>
                            <table class="w-full text-sm mb-4">
                                <colgroup>
                                    <col><col class="w-24"><col class="w-24">
                                </colgroup>
                                <tbody>
                                    @foreach($group['rows'] as $row)
                                        <tr>
                                            <td class="py-0.5 pl-4">
                                                <a href="{{ route('ledger.show', $row['account_id']) }}" class="hover:underline">{{ $row['account_name'] }}</a>
                                            </td>
                                            <td class="py-0.5 text-right font-mono {{ $loop->last ? 'border-t border-gray-900' : '' }}">{{ number_format($row['amount'], 2) }}</td>
                                            <td class="py-0.5 text-right font-mono">{{ $loop->last ? number_format($group['subtotal'], 2) : '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @empty
                            <p class="text-sm text-gray-500">No assets.</p>
                        @endforelse

                        <table class="w-full text-sm font-bold mt-2">
                            <colgroup><col><col class="w-24"></colgroup>
                            <tr>
                                <td class="pt-2">Total Assets</td>
                                <td class="pt-2 text-right font-mono" style="border-top: 1px solid #111; border-bottom: 3px double #111; padding-bottom: 2px;">{{ number_format($data['total_assets'], 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- LIABILITIES + EQUITY (right column of the T) --}}
                    <div>
                        @foreach($data['liability_groups'] ?? [] as $group)
                            <p class="italic font-semibold mb-1">{{ $group['subcategory'] }}</p>
                            <table class="w-full text-sm mb-4">
                                <colgroup>
                                    <col><col class="w-24"><col class="w-24">
                                </colgroup>
                                <tbody>
                                    @foreach($group['rows'] as $row)
                                        <tr>
                                            <td class="py-0.5 pl-4">
                                                <a href="{{ route('ledger.show', $row['account_id']) }}" class="hover:underline">{{ $row['account_name'] }}</a>
                                            </td>
                                            <td class="py-0.5 text-right font-mono {{ $loop->last ? 'border-t border-gray-900' : '' }}">{{ number_format($row['amount'], 2) }}</td>
                                            <td class="py-0.5 text-right font-mono">{{ $loop->last ? number_format($group['subtotal'], 2) : '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach

                        @foreach($equityGroups as $eqGroup)
                            @php
                                $isLastEquityGroup = $loop->last;
                                $rowsWithNI = $eqGroup['rows'];
                                $subtotalWithNI = $eqGroup['subtotal'] + ($isLastEquityGroup && $hasYtd ? $data['ytd_net_income'] : 0);
                            @endphp
                            <p class="italic font-semibold mb-1">{{ $eqGroup['subcategory'] }}</p>
                            <table class="w-full text-sm mb-4">
                                <colgroup>
                                    <col><col class="w-24"><col class="w-24">
                                </colgroup>
                                <tbody>
                                    @foreach($rowsWithNI as $row)
                                        <tr>
                                            <td class="py-0.5 pl-4">
                                                <a href="{{ route('ledger.show', $row['account_id']) }}" class="hover:underline">{{ $row['account_name'] }}</a>
                                            </td>
                                            @php $isFinalRow = $loop->last && !($isLastEquityGroup && $hasYtd); @endphp
                                            <td class="py-0.5 text-right font-mono {{ $isFinalRow ? 'border-t border-gray-900' : '' }}">{{ number_format($row['amount'], 2) }}</td>
                                            <td class="py-0.5 text-right font-mono">{{ $isFinalRow ? number_format($subtotalWithNI, 2) : '' }}</td>
                                        </tr>
                                    @endforeach
                                    @if($isLastEquityGroup && $hasYtd)
                                        <tr>
                                            <td class="py-0.5 pl-4 italic text-gray-600">Current year net income</td>
                                            <td class="py-0.5 text-right font-mono border-t border-gray-900">{{ number_format($data['ytd_net_income'], 2) }}</td>
                                            <td class="py-0.5 text-right font-mono">{{ number_format($subtotalWithNI, 2) }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @endforeach

                        <table class="w-full text-sm font-bold mt-2">
                            <colgroup><col><col class="w-24"></colgroup>
                            <tr>
                                <td class="pt-2">Total Equities</td>
                                <td class="pt-2 text-right font-mono" style="border-top: 1px solid #111; border-bottom: 3px double #111; padding-bottom: 2px;">{{ number_format($data['total_liabilities_and_equity'], 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
