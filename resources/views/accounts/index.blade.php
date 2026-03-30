<x-app-layout>
    @php
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Chart of Accounts'],
        ];
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800  leading-tight">
                {{ __('Chart of Accounts') }}
            </h2>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('accounts.create') }}" title="Add a new account to the chart of accounts"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Account
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Status / Error messages --}}
            @if (session('status'))
                <div class="font-medium text-sm text-green-600 bg-green-50  px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="font-medium text-sm text-red-600 bg-red-50  px-4 py-3 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filter / Search Panel --}}
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <form method="GET" action="{{ route('accounts.index') }}"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">

                        {{-- Search --}}
                        <div class="lg:col-span-2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search by name or number…"
                                title="Search for accounts by name or account number"
                                class="w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        </div>

                        {{-- Category --}}
                        <select name="category" title="Filter accounts by category"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">All Categories</option>
                            @foreach(['asset', 'liability', 'equity', 'revenue', 'expense'] as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                    {{ ucfirst($cat) }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Subcategory --}}
                        <select name="subcategory" title="Filter accounts by subcategory"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">All Subcategories</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub }}" {{ request('subcategory') === $sub ? 'selected' : '' }}>
                                    {{ $sub }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Statement --}}
                        <select name="statement" title="Filter by financial statement type"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">All Statements</option>
                            <option value="BS" {{ request('statement') === 'BS' ? 'selected' : '' }}>Balance Sheet
                            </option>
                            <option value="IS" {{ request('statement') === 'IS' ? 'selected' : '' }}>Income Statement
                            </option>
                            <option value="RE" {{ request('statement') === 'RE' ? 'selected' : '' }}>Retained Earnings
                            </option>
                        </select>

                        {{-- Status --}}
                        <select name="status" title="Filter by active or inactive status"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>

                        {{-- Date Range --}}
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            title="Filter accounts added from this date" placeholder="From date"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">

                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            title="Filter accounts added up to this date" placeholder="To date"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">

                        {{-- Buttons --}}
                        <div class="flex gap-2">
                            <button type="submit" title="Apply the selected filters"
                                class="px-4 py-2 bg-gray-800  text-white  text-xs font-semibold uppercase tracking-widest rounded-md hover:bg-gray-700  transition">
                                Filter
                            </button>
                            <a href="{{ route('accounts.index') }}" title="Clear all filters and show all accounts"
                                class="px-4 py-2 bg-gray-200  text-gray-700  text-xs font-semibold uppercase tracking-widest rounded-md hover:bg-gray-300  transition">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Pop-up Calendar --}}
            <div class="bg-white  p-4 shadow-sm sm:rounded-lg" x-data="{ showCalendar: false }">
                <button @click="showCalendar = !showCalendar" title="Toggle calendar view"
                    class="inline-flex items-center gap-2 text-sm text-gray-600  hover:text-indigo-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span x-text="showCalendar ? 'Hide Calendar' : 'Show Calendar'"></span>
                </button>
                <div x-show="showCalendar" x-cloak class="mt-3">
                    <input type="date" id="popup-calendar" title="Select a date to navigate"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                        value="{{ now()->format('Y-m-d') }}">
                    <p class="text-xs text-gray-400 mt-1">Today: {{ now()->format('F j, Y') }}</p>
                </div>
            </div>

            {{-- Accounts Table --}}
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 ">
                        <thead class="bg-gray-50 ">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Number</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Name</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Subcategory</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Normal Side</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Statement</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Debit</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Credit</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Balance</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500  uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white  divide-y divide-gray-200 ">
                            @forelse($accounts as $account)
                                <tr class="hover:bg-gray-50  transition-colors cursor-pointer"
                                    onclick="window.location='{{ route('accounts.show', $account) }}'">
                                    <td class="px-4 py-3 whitespace-nowrap font-mono text-sm text-gray-700 ">
                                        {{ $account->account_number }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"
                                        onclick="event.stopPropagation()">
                                        <a href="{{ route('ledger.show', $account) }}"
                                            title="View General Ledger for {{ $account->account_name }}"
                                            class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                            {{ $account->account_name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600  capitalize">
                                        {{ $account->account_category }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 ">
                                        {{ $account->account_subcategory }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm capitalize">
                                        <span
                                            class="px-2 py-0.5 rounded text-xs font-medium
                                                    {{ $account->normal_side === 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $account->normal_side }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 ">
                                        {{ $account->statement }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-mono text-gray-700 ">
                                        ${{ $account->formatted_debit }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-mono text-gray-700 ">
                                        ${{ $account->formatted_credit }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right font-mono font-semibold text-gray-900 ">
                                        ${{ $account->formatted_balance }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        @if($account->is_active)
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800  ">Active</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600  ">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-8 text-center text-gray-400 ">
                                        No accounts found.
                                        {{ Auth::user()->isAdmin() ? 'Click "Add Account" to create the first account.' : '' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-gray-200 ">
                    {{ $accounts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>