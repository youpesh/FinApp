<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Reports', 'url' => route('reports.index')],
        ['label' => 'Income Statement'],
    ]">
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Income Statement</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-5 rounded-lg border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('reports.income-statement') }}" class="flex items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                        <input type="date" name="date_from" value="{{ $from->toDateString() }}" required
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                        <input type="date" name="date_to" value="{{ $to->toDateString() }}" required
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Update</button>
                </form>
            </div>

            @include('reports._actions', [
                'type' => 'income_statement',
                'params' => ['date_from' => $from->toDateString(), 'date_to' => $to->toDateString()],
                'emailSubject' => 'Income Statement ' . $from->toFormattedDateString() . ' – ' . $to->toFormattedDateString(),
            ])

            @php
                $fmt = fn($v) => $v < 0 ? '($' . number_format(abs($v), 2) . ')' : '$' . number_format($v, 2);
            @endphp

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                @include('reports._heading', [
                    'statement' => 'Income Statement',
                    'period' => 'For the period ending ' . $to->format('F jS, Y'),
                ])

                <div class="px-8 py-6 text-sm">
                    <div class="flex justify-end pb-2 border-b border-gray-300">
                        <span class="font-semibold text-gray-700">Total Amount</span>
                    </div>

                    {{-- Revenue --}}
                    <h3 class="text-indigo-500 font-semibold mt-5 mb-1">Revenue</h3>
                    @forelse($data['revenues'] as $row)
                        <div class="flex justify-between py-1">
                            <a href="{{ route('ledger.show', $row['account_id']) }}" class="pl-6 text-gray-700 hover:underline">{{ $row['account_name'] }}</a>
                            <span class="font-mono text-gray-900">{{ $fmt($row['amount']) }}</span>
                        </div>
                    @empty
                        <p class="pl-6 py-2 text-gray-500">No revenue activity.</p>
                    @endforelse
                    <div class="flex justify-between py-1 mt-1 font-bold">
                        <span class="pl-6 text-gray-900">Revenue Total</span>
                        <span class="font-mono text-gray-900" style="border-top: 1px solid #111; padding-top: 2px;">{{ $fmt($data['total_revenue']) }}</span>
                    </div>

                    {{-- Expenses --}}
                    <h3 class="text-indigo-500 font-semibold mt-6 mb-1">Expenses</h3>
                    @forelse($data['expenses'] as $row)
                        <div class="flex justify-between py-1">
                            <a href="{{ route('ledger.show', $row['account_id']) }}" class="pl-6 text-gray-700 hover:underline">{{ $row['account_name'] }}</a>
                            <span class="font-mono text-gray-900">{{ $fmt($row['amount']) }}</span>
                        </div>
                    @empty
                        <p class="pl-6 py-2 text-gray-500">No expense activity.</p>
                    @endforelse
                    <div class="flex justify-between py-1 mt-1 font-bold">
                        <span class="pl-6 text-gray-900">Expenses Total</span>
                        <span class="font-mono text-gray-900" style="border-top: 1px solid #111; padding-top: 2px;">{{ $fmt(-$data['total_expenses']) }}</span>
                    </div>

                    {{-- Net Income --}}
                    <h3 class="text-indigo-500 font-semibold mt-6 mb-1">Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</h3>
                    <div class="flex justify-between py-1 mt-1 font-bold">
                        <span class="pl-6 text-gray-900">Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }} Total</span>
                        <span class="font-mono text-gray-900" style="border-top: 1px solid #111; border-bottom: 3px double #111; padding: 2px 0;">{{ $fmt($data['net_income']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
