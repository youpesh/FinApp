<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Reports', 'url' => route('reports.index')],
        ['label' => 'Retained Earnings'],
    ]">
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Statement of Retained Earnings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-5 rounded-lg border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('reports.retained-earnings') }}" class="flex items-end gap-3">
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
                'type' => 'retained_earnings',
                'params' => ['date_from' => $from->toDateString(), 'date_to' => $to->toDateString()],
                'emailSubject' => 'Retained Earnings ' . $from->toFormattedDateString() . ' – ' . $to->toFormattedDateString(),
            ])

            @php
                $fmt = fn($v) => $v < 0 ? '($' . number_format(abs($v), 2) . ')' : '$' . number_format($v, 2);
            @endphp

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                @include('reports._heading', [
                    'statement' => 'Statement of Retained Earnings',
                    'period' => 'For the period ending ' . $to->format('F jS, Y'),
                ])

                <div class="px-8 py-6 text-sm">
                    <div class="flex justify-end pb-2 border-b border-gray-300 mb-4">
                        <span class="font-semibold text-gray-700">Total Amount</span>
                    </div>

                    <div class="flex justify-between py-1.5">
                        <span class="text-gray-700">Beginning Balance</span>
                        <span class="font-mono text-gray-900">{{ $fmt($data['opening_balance']) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-gray-700">Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</span>
                        <span class="font-mono text-gray-900">{{ $fmt($data['net_income']) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-gray-700">Less Drawings</span>
                        <span class="font-mono text-gray-900">{{ $fmt(-$data['distributions']) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 mt-2 font-bold">
                        <span class="text-gray-900">Ending Balance</span>
                        <span class="font-mono text-gray-900" style="border-top: 1px solid #111; border-bottom: 3px double #111; padding: 2px 0;">{{ $fmt($data['ending_balance']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
