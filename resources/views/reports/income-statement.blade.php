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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 text-lg">Income Statement</h3>
                    <p class="text-sm text-gray-500">{{ $from->format('F d, Y') }} &ndash; {{ $to->format('F d, Y') }}</p>
                </div>
                <div class="px-6 py-5">
                    <h4 class="font-semibold text-gray-700 border-b pb-1 mb-2">Revenue</h4>
                    <table class="min-w-full mb-5">
                        <tbody>
                            @forelse($data['revenues'] as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-1 text-sm text-gray-500 w-24">{{ $row['account_number'] }}</td>
                                    <td class="py-1 text-sm">
                                        <a href="{{ route('ledger.show', $row['account_id']) }}" class="text-indigo-600 hover:underline">{{ $row['account_name'] }}</a>
                                    </td>
                                    <td class="py-1 text-right font-mono text-sm w-40">${{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-2 text-sm text-gray-500">No revenue activity.</td></tr>
                            @endforelse
                            <tr class="border-t font-bold">
                                <td colspan="2" class="py-2 text-right text-gray-700">Total Revenue</td>
                                <td class="py-2 text-right font-mono">${{ number_format($data['total_revenue'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4 class="font-semibold text-gray-700 border-b pb-1 mb-2">Expenses</h4>
                    <table class="min-w-full mb-5">
                        <tbody>
                            @forelse($data['expenses'] as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-1 text-sm text-gray-500 w-24">{{ $row['account_number'] }}</td>
                                    <td class="py-1 text-sm">
                                        <a href="{{ route('ledger.show', $row['account_id']) }}" class="text-indigo-600 hover:underline">{{ $row['account_name'] }}</a>
                                    </td>
                                    <td class="py-1 text-right font-mono text-sm w-40">${{ number_format($row['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-2 text-sm text-gray-500">No expense activity.</td></tr>
                            @endforelse
                            <tr class="border-t font-bold">
                                <td colspan="2" class="py-2 text-right text-gray-700">Total Expenses</td>
                                <td class="py-2 text-right font-mono">${{ number_format($data['total_expenses'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="border-t-2 border-gray-900 pt-3 flex justify-between font-bold text-lg">
                        <span>Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</span>
                        <span class="font-mono {{ $data['net_income'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            ${{ number_format(abs($data['net_income']), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
