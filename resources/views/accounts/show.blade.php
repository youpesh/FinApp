<x-app-layout :breadcrumbs="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Chart of Accounts', 'url' => route('accounts.index')],
    ['label' => $account->account_name],
]">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounts.index') }}" title="Back to Chart of Accounts"
                    class="text-gray-400 hover:text-gray-600  transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-bold text-xl text-gray-800  leading-tight">
                    {{ $account->account_name }}
                    <span class="text-sm font-normal text-gray-500 ">#{{ $account->account_number }}</span>
                </h2>
                @if(!$account->is_active)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Inactive</span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('accounts.event-log', $account) }}" title="View the event log for this account"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600  hover:text-gray-800  border border-gray-300  rounded-md hover:bg-gray-50  transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Event Log
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('accounts.edit', $account) }}" title="Edit this account"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="font-medium text-sm text-green-600 bg-green-50 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="font-medium text-sm text-red-600 bg-red-50 px-4 py-3 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Account Details Card --}}
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800  mb-4">Account Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-8">
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Account Number</dt>
                            <dd class="mt-1 text-sm font-mono text-gray-900 ">{{ $account->account_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Account Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 ">{{ $account->account_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900  capitalize">{{ $account->account_category }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Subcategory</dt>
                            <dd class="mt-1 text-sm text-gray-900 ">{{ $account->account_subcategory }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Normal Side</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-0.5 rounded text-xs font-medium capitalize
                                    {{ $account->normal_side === 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $account->normal_side }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Statement</dt>
                            <dd class="mt-1 text-sm text-gray-900 ">
                                @switch($account->statement)
                                    @case('BS') Balance Sheet @break
                                    @case('IS') Income Statement @break
                                    @case('RE') Retained Earnings @break
                                @endswitch
                                ({{ $account->statement }})
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Order</dt>
                            <dd class="mt-1 text-sm font-mono text-gray-900 ">{{ str_pad($account->order, 2, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Status</dt>
                            <dd class="mt-1">
                                @if($account->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900 ">{{ $account->creator->full_name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500  uppercase">Date Added</dt>
                            <dd class="mt-1 text-sm text-gray-900 ">{{ $account->created_at->format('M j, Y g:i A') }}</dd>
                        </div>
                    </div>

                    @if($account->account_description)
                        <div class="mt-6 pt-4 border-t border-gray-200 ">
                            <dt class="text-xs font-medium text-gray-500  uppercase">Description</dt>
                            <dd class="mt-1 text-sm text-gray-700 ">{{ $account->account_description }}</dd>
                        </div>
                    @endif

                    @if($account->comment)
                        <div class="mt-4">
                            <dt class="text-xs font-medium text-gray-500  uppercase">Comment</dt>
                            <dd class="mt-1 text-sm text-gray-700 ">{{ $account->comment }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Financial Summary --}}
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800  mb-4">Financial Summary</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50  rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500  uppercase font-medium">Initial Balance</p>
                            <p class="text-lg font-bold text-gray-800  font-mono mt-1">${{ $account->formatted_initial_balance }}</p>
                        </div>
                        <div class="bg-blue-50  rounded-lg p-4 text-center">
                            <p class="text-xs text-blue-600  uppercase font-medium">Debit</p>
                            <p class="text-lg font-bold text-blue-800  font-mono mt-1">${{ $account->formatted_debit }}</p>
                        </div>
                        <div class="bg-purple-50  rounded-lg p-4 text-center">
                            <p class="text-xs text-purple-600  uppercase font-medium">Credit</p>
                            <p class="text-lg font-bold text-purple-800  font-mono mt-1">${{ $account->formatted_credit }}</p>
                        </div>
                        <div class="bg-indigo-50  rounded-lg p-4 text-center">
                            <p class="text-xs text-indigo-600  uppercase font-medium">Balance</p>
                            <p class="text-xl font-bold text-indigo-800  font-mono mt-1">${{ $account->formatted_balance }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions: View Ledger, Email Manager --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Account Actions</h3>
                        <p class="text-sm text-gray-500">View the ledger for this account or contact a manager/administrator.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('ledger.show', $account) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition">
                            View Ledger &rarr;
                        </a>
                        <button type="button" onclick="document.getElementById('account-email-modal').classList.remove('hidden')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
                            Email Manager/Admin
                        </button>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                    <ul class="list-disc pl-5 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>

    {{-- Email modal --}}
    <div id="account-email-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Email about <span class="text-indigo-600">{{ $account->account_name }} (#{{ $account->account_number }})</span>
            </h3>
            <form method="POST" action="{{ route('accounts.email', $account) }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recipient (Manager or Admin)</label>
                    <select name="recipient_email" required
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        <option value="">Select recipient...</option>
                        @foreach($recipients as $r)
                            <option value="{{ $r->email }}" {{ old('recipient_email') === $r->email ? 'selected' : '' }}>
                                {{ $r->first_name }} {{ $r->last_name }} — {{ $r->email }} ({{ $r->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" name="subject" required
                        value="{{ old('subject', 'Question about account ' . $account->account_number . ' — ' . $account->account_name) }}"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="body" rows="5" required
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">{{ old('body') }}</textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('account-email-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Send</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
