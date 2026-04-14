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
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 text-lg">Balance Sheet</h3>
                    <p class="text-sm text-gray-500">As of {{ $asOf->format('F d, Y') }}</p>
                    @if(!$data['balanced'])
                        <p class="mt-1 text-sm text-red-600 font-semibold">Warning: Balance sheet does not balance.</p>
                    @endif
                </div>
                <div class="px-6 py-5 grid md:grid-cols-2 gap-8">
                    <!-- Assets -->
                    <div>
                        <h4 class="font-semibold text-gray-700 border-b pb-1 mb-2">Assets</h4>
                        <table class="min-w-full">
                            <tbody>
                                @forelse($data['assets'] as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-1 text-sm text-gray-500 w-20">{{ $row['account_number'] }}</td>
                                        <td class="py-1 text-sm">
                                            <a href="{{ route('ledger.show', $row['account_id']) }}" class="text-indigo-600 hover:underline">{{ $row['account_name'] }}</a>
                                        </td>
                                        <td class="py-1 text-right font-mono text-sm w-32">${{ number_format($row['amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-2 text-sm text-gray-500">No assets.</td></tr>
                                @endforelse
                                <tr class="border-t-2 border-gray-900 font-bold">
                                    <td colspan="2" class="py-2 text-right text-gray-700">Total Assets</td>
                                    <td class="py-2 text-right font-mono">${{ number_format($data['total_assets'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Liabilities + Equity -->
                    <div>
                        <h4 class="font-semibold text-gray-700 border-b pb-1 mb-2">Liabilities</h4>
                        <table class="min-w-full mb-4">
                            <tbody>
                                @forelse($data['liabilities'] as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-1 text-sm text-gray-500 w-20">{{ $row['account_number'] }}</td>
                                        <td class="py-1 text-sm">
                                            <a href="{{ route('ledger.show', $row['account_id']) }}" class="text-indigo-600 hover:underline">{{ $row['account_name'] }}</a>
                                        </td>
                                        <td class="py-1 text-right font-mono text-sm w-32">${{ number_format($row['amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-2 text-sm text-gray-500">No liabilities.</td></tr>
                                @endforelse
                                <tr class="border-t font-semibold">
                                    <td colspan="2" class="py-1 text-right">Total Liabilities</td>
                                    <td class="py-1 text-right font-mono">${{ number_format($data['total_liabilities'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 class="font-semibold text-gray-700 border-b pb-1 mb-2">Equity</h4>
                        <table class="min-w-full mb-4">
                            <tbody>
                                @forelse($data['equity'] as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-1 text-sm text-gray-500 w-20">{{ $row['account_number'] }}</td>
                                        <td class="py-1 text-sm">
                                            <a href="{{ route('ledger.show', $row['account_id']) }}" class="text-indigo-600 hover:underline">{{ $row['account_name'] }}</a>
                                        </td>
                                        <td class="py-1 text-right font-mono text-sm w-32">${{ number_format($row['amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-2 text-sm text-gray-500">No equity accounts.</td></tr>
                                @endforelse
                                <tr>
                                    <td colspan="2" class="py-1 text-sm text-gray-500 italic">Current year net income</td>
                                    <td class="py-1 text-right font-mono text-sm">${{ number_format($data['ytd_net_income'], 2) }}</td>
                                </tr>
                                <tr class="border-t font-semibold">
                                    <td colspan="2" class="py-1 text-right">Total Equity</td>
                                    <td class="py-1 text-right font-mono">${{ number_format($data['total_equity'] + $data['ytd_net_income'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="border-t-2 border-gray-900 pt-2 flex justify-between font-bold">
                            <span>Total Liabilities + Equity</span>
                            <span class="font-mono">${{ number_format($data['total_liabilities_and_equity'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
