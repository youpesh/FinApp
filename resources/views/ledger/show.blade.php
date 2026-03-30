<x-app-layout>
    @php
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'General Ledger', 'url' => route('ledger.index')],
            ['label' => $account->account_name],
        ];
    @endphp
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('ledger.index') }}" class="text-gray-500 hover:text-gray-700">
                &larr; Back to Ledger List
            </a>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Ledger') }}: {{ $account->account_number }} - {{ $account->account_name }}
            </h2>
            <span
                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 ml-auto">
                {{ $account->account_category }} (Normal: {{ ucfirst($account->normal_side) }})
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filters -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('ledger.show', $account) }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" step="0.01" name="amount" value="{{ request('amount') }}"
                            placeholder="Specific Amount"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    <div class="flex justify-end gap-2 ml-auto">
                        <a href="{{ route('ledger.show', $account) }}"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 text-sm font-medium">Clear</a>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Filter</button>
                    </div>
                </form>
            </div>

            <!-- Ledger Core -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Date</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Description
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Post Ref
                                    (PR)</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider bg-gray-700/50">
                                    Debit</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider bg-gray-700/50">
                                    Credit</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-indigo-300 bg-gray-900 border-l border-gray-700">
                                    Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">

                            <!-- Opening Balance Row if Filtered by Date -->
                            @if(request()->filled('date_from'))
                                <tr class="bg-yellow-50 font-medium">
                                    <td class="px-6 py-3 text-sm text-gray-500 border-b border-gray-200" colspan="5">
                                        Opening Balance (Prior to {{ request('date_from') }})
                                    </td>
                                    <td
                                        class="px-6 py-3 text-right text-sm font-mono text-gray-900 border-b border-gray-200 border-l bg-gray-50">
                                        ${{ number_format($openingBalance, 2) }}
                                    </td>
                                </tr>
                            @else
                                <tr class="bg-gray-50/50 font-medium text-gray-500 italic">
                                    <td class="px-6 py-3 text-sm border-b border-gray-200" colspan="5">
                                        Initial Account Balance
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm font-mono border-b border-gray-200 border-l">
                                        ${{ number_format($account->initial_balance ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endif

                            @forelse($lines as $line)
                                <tr class="hover:bg-indigo-50/30 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $line->journalEntry->date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-sm truncate"
                                        title="{{ $line->description ?? $line->journalEntry->description }}">
                                        {{ $line->description ?? $line->journalEntry->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('journal-entries.show', $line->journalEntry) }}"
                                            class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                            {{ $line->journalEntry->reference_id }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-mono bg-gray-50/30">
                                        {!! $line->type === 'debit' ? '$' . number_format($line->amount, 2) : '<span class="text-gray-300">-</span>' !!}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-mono bg-gray-50/30">
                                        {!! $line->type === 'credit' ? '$' . number_format($line->amount, 2) : '<span class="text-gray-300">-</span>' !!}
                                    </td>

                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-mono font-bold border-l border-gray-200 {{ $line->running_balance < 0 ? 'text-red-600 bg-red-50/30' : 'text-gray-900 bg-gray-50' }}">
                                        ${{ number_format(abs($line->running_balance), 2) }}
                                        @if($line->running_balance < 0)
                                            <span
                                                class="text-xs font-sans font-normal text-red-400 opacity-80">(Abnormal)</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No ledger entries found for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center text-sm text-gray-500 pb-8">
                End of ledger for account {{ $account->account_number }}
            </div>

        </div>
    </div>
</x-app-layout>