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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 text-lg">Statement of Retained Earnings</h3>
                    <p class="text-sm text-gray-500">{{ $from->format('F d, Y') }} &ndash; {{ $to->format('F d, Y') }}</p>
                </div>
                <table class="min-w-full">
                    <tbody>
                        <tr class="border-b">
                            <td class="px-6 py-3 text-sm font-medium text-gray-700">Retained Earnings, beginning of period</td>
                            <td class="px-6 py-3 text-right font-mono text-sm">${{ number_format($data['opening_balance'], 2) }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-6 py-3 text-sm font-medium text-gray-700">Add: Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</td>
                            <td class="px-6 py-3 text-right font-mono text-sm">{{ $data['net_income'] < 0 ? '(' : '' }}${{ number_format(abs($data['net_income']), 2) }}{{ $data['net_income'] < 0 ? ')' : '' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-6 py-3 text-sm font-medium text-gray-700">Less: Distributions</td>
                            <td class="px-6 py-3 text-right font-mono text-sm">{{ $data['distributions'] > 0 ? '(' : '' }}${{ number_format($data['distributions'], 2) }}{{ $data['distributions'] > 0 ? ')' : '' }}</td>
                        </tr>
                        <tr class="border-t-2 border-gray-900 font-bold">
                            <td class="px-6 py-4 text-gray-900">Retained Earnings, end of period</td>
                            <td class="px-6 py-4 text-right font-mono">${{ number_format($data['ending_balance'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
