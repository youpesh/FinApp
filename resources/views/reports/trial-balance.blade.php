<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Reports', 'url' => route('reports.index')],
        ['label' => 'Trial Balance'],
    ]">
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Trial Balance</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-5 rounded-lg border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('reports.trial-balance') }}" class="flex items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">As of Date</label>
                        <input type="date" name="as_of" value="{{ $asOf->toDateString() }}" required
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Update</button>
                </form>
            </div>

            @include('reports._actions', [
                'type' => 'trial_balance',
                'params' => ['as_of' => $asOf->toDateString()],
                'emailSubject' => 'Trial Balance as of ' . $asOf->toFormattedDateString(),
            ])

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 text-lg">Trial Balance</h3>
                    <p class="text-sm text-gray-500">As of {{ $asOf->format('F d, Y') }}</p>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Debit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data['rows'] as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $row['account_number'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900">
                                    <a href="{{ route('ledger.show', $row['account_id']) }}" class="text-indigo-600 hover:underline">{{ $row['account_name'] }}</a>
                                </td>
                                <td class="px-6 py-3 text-right font-mono text-sm text-gray-900">{{ $row['debit'] > 0 ? '$' . number_format($row['debit'], 2) : '' }}</td>
                                <td class="px-6 py-3 text-right font-mono text-sm text-gray-900">{{ $row['credit'] > 0 ? '$' . number_format($row['credit'], 2) : '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">No balances to show.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right text-gray-700">Totals:</td>
                            <td class="px-6 py-4 text-right font-mono border-b-4 border-double border-gray-900">${{ number_format($data['total_debits'], 2) }}</td>
                            <td class="px-6 py-4 text-right font-mono border-b-4 border-double border-gray-900">${{ number_format($data['total_credits'], 2) }}</td>
                        </tr>
                        @if(!$data['balanced'])
                            <tr>
                                <td colspan="4" class="px-6 py-2 text-right text-sm text-red-600">
                                    Out of balance by ${{ number_format(abs($data['total_debits'] - $data['total_credits']), 2) }}
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
