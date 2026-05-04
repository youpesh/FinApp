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

            @php
                $fmt = fn($v) => $v < 0 ? '($' . number_format(abs($v), 2) . ')' : '$' . number_format($v, 2);
                $hasYtd = abs($data['ytd_net_income']) >= 0.005;
                $assetGroups = $data['asset_groups'] ?? [];
                $liabilityGroups = $data['liability_groups'] ?? [];
                $equityGroups = $data['equity_groups'] ?? [];
            @endphp

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                @include('reports._heading', [
                    'statement' => 'Balance Sheet',
                    'period' => 'As of ' . $asOf->format('F jS, Y'),
                ])

                @if(!$data['balanced'])
                    <p class="px-8 pt-3 text-sm text-red-600 font-semibold">Warning: Balance sheet does not balance.</p>
                @endif

                <div class="px-8 py-6 text-sm">
                    <div class="flex justify-end pb-2 border-b border-gray-300">
                        <span class="font-semibold text-gray-700">Total Amount</span>
                    </div>

                    {{-- Assets --}}
                    <h3 class="text-indigo-500 font-semibold mt-5 mb-2">Assets</h3>
                    @forelse($assetGroups as $group)
                        <div class="flex justify-between py-1 font-bold mt-2">
                            <span class="pl-2 text-gray-900">{{ $group['subcategory'] }}</span>
                            <span class="font-mono text-gray-900">{{ $fmt($group['subtotal']) }}</span>
                        </div>
                        @foreach($group['rows'] as $row)
                            <div class="flex justify-between py-1">
                                <a href="{{ route('ledger.show', $row['account_id']) }}" class="pl-8 text-gray-700 hover:underline">{{ $row['account_name'] }}</a>
                                <span class="font-mono text-gray-900">{{ $fmt($row['amount']) }}</span>
                            </div>
                        @endforeach
                    @empty
                        <p class="pl-8 py-2 text-gray-500">No assets.</p>
                    @endforelse
                    <div class="flex justify-between py-1 mt-3 font-bold">
                        <span class="pl-2 text-gray-900">Total Assets</span>
                        <span class="font-mono text-gray-900" style="border-top: 1px solid #111; border-bottom: 3px double #111; padding: 2px 0;">{{ $fmt($data['total_assets']) }}</span>
                    </div>

                    {{-- Equity & Liabilities --}}
                    <h3 class="text-indigo-500 font-semibold mt-7 mb-2">Equity &amp; Liabilities</h3>

                    @foreach($equityGroups as $eqGroup)
                        @php
                            $isLastEquityGroup = $loop->last;
                            $subtotalWithNI = $eqGroup['subtotal'] + ($isLastEquityGroup && $hasYtd ? $data['ytd_net_income'] : 0);
                        @endphp
                        <div class="flex justify-between py-1 font-bold mt-2">
                            <span class="pl-2 text-gray-900">{{ $eqGroup['subcategory'] }}</span>
                            <span class="font-mono text-gray-900">{{ $fmt($subtotalWithNI) }}</span>
                        </div>
                        @foreach($eqGroup['rows'] as $row)
                            <div class="flex justify-between py-1">
                                <a href="{{ route('ledger.show', $row['account_id']) }}" class="pl-8 text-gray-700 hover:underline">{{ $row['account_name'] }}</a>
                                <span class="font-mono text-gray-900">{{ $fmt($row['amount']) }}</span>
                            </div>
                        @endforeach
                        @if($isLastEquityGroup && $hasYtd)
                            <div class="flex justify-between py-1">
                                <span class="pl-8 text-gray-600 italic">Income Estimation</span>
                                <span class="font-mono text-gray-900">{{ $fmt($data['ytd_net_income']) }}</span>
                            </div>
                        @endif
                    @endforeach

                    @foreach($liabilityGroups as $group)
                        <div class="flex justify-between py-1 font-bold mt-2">
                            <span class="pl-2 text-gray-900">{{ $group['subcategory'] }}</span>
                            <span class="font-mono text-gray-900">{{ $fmt($group['subtotal']) }}</span>
                        </div>
                        @foreach($group['rows'] as $row)
                            <div class="flex justify-between py-1">
                                <a href="{{ route('ledger.show', $row['account_id']) }}" class="pl-8 text-gray-700 hover:underline">{{ $row['account_name'] }}</a>
                                <span class="font-mono text-gray-900">{{ $fmt($row['amount']) }}</span>
                            </div>
                        @endforeach
                    @endforeach

                    <div class="flex justify-between py-1 mt-3 font-bold">
                        <span class="pl-2 text-gray-900">Total Equity &amp; Liabilities</span>
                        <span class="font-mono text-gray-900" style="border-top: 1px solid #111; border-bottom: 3px double #111; padding: 2px 0;">{{ $fmt($data['total_liabilities_and_equity']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
