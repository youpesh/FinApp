<x-app-layout>
    @php
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Journal Entries', 'url' => route('journal-entries.index')],
            ['label' => $journalEntry->reference_id],
        ];
    @endphp
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('journal-entries.index') }}" class="text-gray-500 hover:text-gray-700">
                    &larr; Back
                </a>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    {{ __('Journal Entry') }}: {{ $journalEntry->reference_id }}
                </h2>

                @if($journalEntry->status === 'approved')
                    <span
                        class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 ml-4">Approved</span>
                @elseif($journalEntry->status === 'rejected')
                    <span
                        class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 ml-4">Rejected</span>
                @else
                    <span
                        class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 ml-4">Pending</span>
                @endif
            </div>

            @if(Auth::user()->isManager() && $journalEntry->status === 'pending')
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                        class="bg-red-50 text-red-700 px-4 py-2 border border-red-200 rounded-md hover:bg-red-100 transition shadow-sm font-medium text-sm">
                        Reject Entry
                    </button>
                    <form method="POST" action="{{ route('manager.journal-entries.approve', $journalEntry) }}"
                        class="inline">
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
                    <p class="text-red-700 font-bold mb-1">Error resolving entry:</p>
                    <ul class="list-disc pl-5 text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($journalEntry->status === 'rejected')
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Rejected by
                                {{ $journalEntry->approver->full_name ?? 'Manager' }}</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p><strong>Reason:</strong> {{ $journalEntry->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Details -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                        <div class="flex justify-between border-b pb-4 mb-4">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Transaction Details</h3>
                                <p class="text-gray-500 text-sm mt-1">Date: {{ $journalEntry->date->format('F d, Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Created by</p>
                                <p class="font-medium text-indigo-600">
                                    {{ $journalEntry->creator->full_name ?? 'Unknown User' }}</p>
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
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Account</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Memo</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-32">
                                            Debit</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-32">
                                            Credit</th>
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
                                                <div class="text-sm font-medium text-indigo-600 hover:underline">
                                                    <a href="{{ route('accounts.show', $line->account_id) }}">{{ $line->account->account_number }}
                                                        - {{ $line->account->name }}</a>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $line->description ?? '-' }}
                                            </td>

                                            @if($line->type === 'debit')
                                                @php $totalDebit += $line->amount; @endphp
                                                <td class="px-6 py-4 text-right text-sm font-mono text-gray-900">
                                                    ${{ number_format($line->amount, 2) }}</td>
                                                <td class="px-6 py-4 text-right text-sm font-mono text-gray-300">-</td>
                                            @else
                                                @php $totalCredit += $line->amount; @endphp
                                                <td class="px-6 py-4 text-right text-sm font-mono text-gray-300">-</td>
                                                <td class="px-6 py-4 text-right text-sm font-mono text-gray-900">
                                                    ${{ number_format($line->amount, 2) }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold border-t-2 border-gray-300">
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-right text-gray-700">Totals:</td>
                                        <td
                                            class="px-6 py-4 text-right text-gray-900 border-b-4 border-double border-gray-900">
                                            ${{ number_format($totalDebit, 2) }}</td>
                                        <td
                                            class="px-6 py-4 text-right text-gray-900 border-b-4 border-double border-gray-900">
                                            ${{ number_format($totalCredit, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -> Attachments -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                        <h3
                            class="font-bold text-gray-800 text-lg mb-4 border-b pb-2 flex items-center justify-between">
                            <span>Source Documents</span>
                            <span
                                class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">{{ $journalEntry->attachments->count() }}</span>
                        </h3>

                        @if($journalEntry->attachments->count() > 0)
                            <ul class="space-y-3">
                                @foreach($journalEntry->attachments as $attachment)
                                    <li
                                        class="flex items-start bg-gray-50 p-3 rounded-md border border-gray-200 hover:bg-gray-100 transition">
                                        <div class="flex-shrink-0 pt-1">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1 overflow-hidden">
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                                class="text-sm font-medium text-indigo-600 truncate block hover:underline"
                                                title="{{ $attachment->original_name }}">
                                                {{ $attachment->original_name }}
                                            </a>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ number_format($attachment->size / 1024, 1) }} KB</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-6 text-gray-500 text-sm">
                                <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                No attachments provided.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PR Ledger Section placeholder (Appears only when Approved) -->
            @if($journalEntry->status === 'approved')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Ledger Post References</h3>
                    <p class="text-sm text-gray-600 mb-4">This transaction has been successfully posted to the General
                        Ledger.</p>
                    <a href="#" class="text-indigo-600 font-medium hover:underline flex items-center">
                        View in General Ledger
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    @if(Auth::user()->isManager() && $journalEntry->status === 'pending')
        <div id="reject-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 text-center mt-4">Reject Journal Entry</h3>
                    <div class="mt-2 px-2 text-sm text-gray-500 text-center">
                        <p>Are you sure you want to reject this entry? You must provide a reason.</p>
                    </div>

                    <form method="POST" action="{{ route('manager.journal-entries.reject', $journalEntry) }}" class="mt-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason *</label>
                            <textarea name="rejection_reason" rows="3" required
                                class="border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm w-full"
                                placeholder="e.g. Needs more details on invoice..."></textarea>
                        </div>

                        <div class="flex justify-between items-center mt-5 gap-3">
                            <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')"
                                class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 w-full transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 w-full transition shadow-sm font-medium">
                                Confirm Rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>