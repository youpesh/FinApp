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

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                @include('reports._heading', [
                    'statement' => 'Trial Balance',
                    'period' => 'As of ' . $asOf->format('F jS, Y'),
                ])

                <div class="px-8 py-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-gray-700">
                                <th class="text-left font-semibold pb-2 border-b border-gray-300">Account</th>
                                <th class="text-right font-semibold pb-2 border-b border-gray-300 w-32">Debit</th>
                                <th class="text-right font-semibold pb-2 border-b border-gray-300 w-32">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['rows'] as $row)
                                <tr>
                                    <td class="py-1.5">
                                        <a href="{{ route('ledger.show', $row['account_id']) }}" class="text-indigo-600 hover:underline">{{ $row['account_number'] }} - {{ $row['account_name'] }}</a>
                                    </td>
                                    <td class="py-1.5 text-right font-mono text-gray-900">{{ $row['debit'] > 0 ? '$' . number_format($row['debit'], 2) : '' }}</td>
                                    <td class="py-1.5 text-right font-mono text-gray-900">{{ $row['credit'] > 0 ? '$' . number_format($row['credit'], 2) : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-center text-gray-500">No balances to show.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold">
                                <td class="pt-4 text-gray-900">Total</td>
                                <td class="pt-4 text-right font-mono text-gray-900" style="border-top: 1px solid #111; border-bottom: 3px double #111; padding-bottom: 2px;">${{ number_format($data['total_debits'], 2) }}</td>
                                <td class="pt-4 text-right font-mono text-gray-900" style="border-top: 1px solid #111; border-bottom: 3px double #111; padding-bottom: 2px;">${{ number_format($data['total_credits'], 2) }}</td>
                            </tr>
                            @if(!$data['balanced'])
                                <tr><td colspan="3" class="pt-2 text-right text-sm text-red-600">Out of balance by ${{ number_format(abs($data['total_debits'] - $data['total_credits']), 2) }}</td></tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
