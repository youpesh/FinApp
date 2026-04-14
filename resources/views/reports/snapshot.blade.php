<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Reports', 'url' => route('reports.index')],
        ['label' => 'Snapshot'],
    ]">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ $snapshot->title }}</h2>
            <span class="text-sm text-gray-500">Saved {{ $snapshot->generated_at->format('M d, Y g:ia') }} by {{ $snapshot->generator->full_name ?? 'Unknown' }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-end">
                <a href="{{ route('reports.pdf', ['type' => $snapshot->type] + $snapshot->parameters) }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Download PDF (current data)
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 mb-4">This is a saved snapshot. Numbers reflect data as of {{ $snapshot->generated_at->format('M d, Y g:ia') }}.</p>

                @php $data = $snapshot->payload; @endphp

                @if($snapshot->type === 'trial_balance')
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data['rows'] as $row)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $row['account_number'] }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $row['account_name'] }}</td>
                                    <td class="px-4 py-2 text-right font-mono text-sm">{{ $row['debit'] > 0 ? '$' . number_format($row['debit'], 2) : '' }}</td>
                                    <td class="px-4 py-2 text-right font-mono text-sm">{{ $row['credit'] > 0 ? '$' . number_format($row['credit'], 2) : '' }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-bold">
                                <td colspan="2" class="px-4 py-2 text-right">Totals:</td>
                                <td class="px-4 py-2 text-right font-mono">${{ number_format($data['total_debits'], 2) }}</td>
                                <td class="px-4 py-2 text-right font-mono">${{ number_format($data['total_credits'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                @elseif($snapshot->type === 'income_statement')
                    <div class="space-y-3">
                        <h4 class="font-semibold">Revenue</h4>
                        <ul class="text-sm">
                            @foreach($data['revenues'] as $r)
                                <li class="flex justify-between"><span>{{ $r['account_number'] }} {{ $r['account_name'] }}</span><span class="font-mono">${{ number_format($r['amount'], 2) }}</span></li>
                            @endforeach
                            <li class="flex justify-between font-bold border-t pt-1"><span>Total Revenue</span><span class="font-mono">${{ number_format($data['total_revenue'], 2) }}</span></li>
                        </ul>
                        <h4 class="font-semibold">Expenses</h4>
                        <ul class="text-sm">
                            @foreach($data['expenses'] as $r)
                                <li class="flex justify-between"><span>{{ $r['account_number'] }} {{ $r['account_name'] }}</span><span class="font-mono">${{ number_format($r['amount'], 2) }}</span></li>
                            @endforeach
                            <li class="flex justify-between font-bold border-t pt-1"><span>Total Expenses</span><span class="font-mono">${{ number_format($data['total_expenses'], 2) }}</span></li>
                        </ul>
                        <div class="flex justify-between font-bold border-t-2 pt-2 text-lg"><span>Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</span><span class="font-mono">${{ number_format(abs($data['net_income']), 2) }}</span></div>
                    </div>
                @elseif($snapshot->type === 'balance_sheet')
                    <div class="grid md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <h4 class="font-semibold border-b pb-1 mb-2">Assets</h4>
                            @foreach($data['assets'] as $r)
                                <div class="flex justify-between py-0.5"><span>{{ $r['account_name'] }}</span><span class="font-mono">${{ number_format($r['amount'], 2) }}</span></div>
                            @endforeach
                            <div class="flex justify-between font-bold border-t-2 pt-1 mt-1"><span>Total Assets</span><span class="font-mono">${{ number_format($data['total_assets'], 2) }}</span></div>
                        </div>
                        <div>
                            <h4 class="font-semibold border-b pb-1 mb-2">Liabilities</h4>
                            @foreach($data['liabilities'] as $r)
                                <div class="flex justify-between py-0.5"><span>{{ $r['account_name'] }}</span><span class="font-mono">${{ number_format($r['amount'], 2) }}</span></div>
                            @endforeach
                            <div class="flex justify-between font-semibold border-t pt-1 mt-1"><span>Total Liabilities</span><span class="font-mono">${{ number_format($data['total_liabilities'], 2) }}</span></div>
                            <h4 class="font-semibold border-b pb-1 mb-2 mt-4">Equity</h4>
                            @foreach($data['equity'] as $r)
                                <div class="flex justify-between py-0.5"><span>{{ $r['account_name'] }}</span><span class="font-mono">${{ number_format($r['amount'], 2) }}</span></div>
                            @endforeach
                            <div class="flex justify-between italic text-gray-500 py-0.5"><span>Current year net income</span><span class="font-mono">${{ number_format($data['ytd_net_income'], 2) }}</span></div>
                            <div class="flex justify-between font-bold border-t-2 pt-1 mt-1"><span>Total L + E</span><span class="font-mono">${{ number_format($data['total_liabilities_and_equity'], 2) }}</span></div>
                        </div>
                    </div>
                @elseif($snapshot->type === 'retained_earnings')
                    <table class="min-w-full text-sm">
                        <tr><td class="py-2">Retained Earnings, beginning of period</td><td class="py-2 text-right font-mono">${{ number_format($data['opening_balance'], 2) }}</td></tr>
                        <tr><td class="py-2">Add: Net {{ $data['net_income'] >= 0 ? 'Income' : 'Loss' }}</td><td class="py-2 text-right font-mono">${{ number_format(abs($data['net_income']), 2) }}</td></tr>
                        <tr><td class="py-2">Less: Distributions</td><td class="py-2 text-right font-mono">${{ number_format($data['distributions'], 2) }}</td></tr>
                        <tr class="font-bold border-t-2"><td class="py-2">Retained Earnings, end of period</td><td class="py-2 text-right font-mono">${{ number_format($data['ending_balance'], 2) }}</td></tr>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
