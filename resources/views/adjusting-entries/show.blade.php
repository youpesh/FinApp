<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Adjusting Entries', 'url' => route('adjusting-entries.index')],
        ['label' => $journalEntry->reference_id],
    ]">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('adjusting-entries.index') }}" class="text-gray-500 hover:text-gray-700">&larr; Back</a>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    {{ __('Adjusting Entry') }}: {{ $journalEntry->reference_id }}
                </h2>

                @if($journalEntry->status === 'approved')
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 ml-4">Approved</span>
                @elseif($journalEntry->status === 'rejected')
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 ml-4">Rejected</span>
                @else
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 ml-4">Pending</span>
                @endif
            </div>

            @if(Auth::user()->isManager() && $journalEntry->status === 'pending')
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                        class="bg-red-50 text-red-700 px-4 py-2 border border-red-200 rounded-md hover:bg-red-100 transition shadow-sm font-medium text-sm">
                        Reject Entry
                    </button>
                    <form method="POST" action="{{ route('manager.adjusting-entries.approve', $journalEntry) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition shadow-sm font-medium text-sm">
                            Approve & Post to Ledger
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                    <p class="text-red-700 font-bold mb-1">Error:</p>
                    <ul class="list-disc pl-5 text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($journalEntry->status === 'rejected')
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                    <h3 class="text-sm font-medium text-red-800">Rejected by {{ $journalEntry->approver->full_name ?? 'Manager' }}</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p><strong>Reason:</strong> {{ $journalEntry->rejection_reason }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                        <div class="flex justify-between border-b pb-4 mb-4">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Adjusting Transaction</h3>
                                <p class="text-gray-500 text-sm mt-1">Date: {{ $journalEntry->date->format('F d, Y') }}</p>
                                @if($journalEntry->submitted_at)
                                    <p class="text-gray-500 text-xs">Submitted: {{ $journalEntry->submitted_at->format('F d, Y g:ia') }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Created by</p>
                                <p class="font-medium text-indigo-600">{{ $journalEntry->creator->full_name ?? 'Unknown' }}</p>
                            </div>
                        </div>

                        <p class="text-gray-700 mb-6 bg-gray-50 p-4 rounded-md border border-gray-100">
                            {{ $journalEntry->description }}
                        </p>

                        <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">Entry Lines</h4>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Account</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Memo</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-32">Debit</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-32">Credit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @php
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                    @endphp
                                    @foreach($journalEntry->lines as $line)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <a href="{{ route('ledger.show', $line->account_id) }}"
                                                    class="text-sm font-medium text-indigo-600 hover:underline">
                                                    {{ $line->account->account_number }} - {{ $line->account->account_name }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $line->description ?? '-' }}</td>
                                            @if($line->type === 'debit')
                                                @php $totalDebit += $line->amount; @endphp
                                                <td class="px-6 py-4 text-right text-sm font-mono text-gray-900">${{ number_format($line->amount, 2) }}</td>
                                                <td class="px-6 py-4 text-right text-sm font-mono text-gray-300">-</td>
                                            @else
                                                @php $totalCredit += $line->amount; @endphp
                                                <td class="px-6 py-4 text-right text-sm font-mono text-gray-300">-</td>
                                                <td class="px-6 py-4 text-right text-sm font-mono text-gray-900">${{ number_format($line->amount, 2) }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold border-t-2 border-gray-300">
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-right text-gray-700">Totals:</td>
                                        <td class="px-6 py-4 text-right text-gray-900 border-b-4 border-double border-gray-900">${{ number_format($totalDebit, 2) }}</td>
                                        <td class="px-6 py-4 text-right text-gray-900 border-b-4 border-double border-gray-900">${{ number_format($totalCredit, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 text-lg mb-4 border-b pb-2 flex items-center justify-between">
                            <span>Source Documents</span>
                            <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">{{ $journalEntry->attachments->count() }}</span>
                        </h3>

                        @if($journalEntry->attachments->count() > 0)
                            <ul class="space-y-3">
                                @foreach($journalEntry->attachments as $attachment)
                                    <li class="flex items-start bg-gray-50 p-3 rounded-md border border-gray-200 hover:bg-gray-100 transition">
                                        <div class="flex-1 overflow-hidden">
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                                class="text-sm font-medium text-indigo-600 truncate block hover:underline"
                                                title="{{ $attachment->original_name }}">
                                                {{ $attachment->original_name }}
                                            </a>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($attachment->size / 1024, 1) }} KB</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-6 text-gray-500 text-sm">No attachments provided.</div>
                        @endif
                    </div>
                </div>
            </div>

            @if($journalEntry->status === 'approved')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Posted to Ledger</h3>
                    <p class="text-sm text-gray-600 mb-4">This adjusting entry has been posted and is reflected in the ledger and financial statements.</p>
                    <a href="{{ route('ledger.index') }}" class="text-indigo-600 font-medium hover:underline">
                        View in General Ledger &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if(Auth::user()->isManager() && $journalEntry->status === 'pending')
        <div id="reject-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                <h3 class="text-lg leading-6 font-medium text-gray-900 text-center mt-4">Reject Adjusting Entry</h3>
                <div class="mt-2 px-2 text-sm text-gray-500 text-center">
                    <p>Are you sure you want to reject this entry? You must provide a reason.</p>
                </div>

                <form method="POST" action="{{ route('manager.adjusting-entries.reject', $journalEntry) }}" class="mt-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason *</label>
                        <textarea name="rejection_reason" rows="3" required
                            class="border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm w-full"
                            placeholder="e.g. Wrong account used for accrual..."></textarea>
                    </div>

                    <div class="flex justify-between items-center mt-5 gap-3">
                        <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')"
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 w-full">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 w-full">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
