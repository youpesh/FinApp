<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'General Ledger'],
    ]">
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('General Ledger') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Select Account</h3>

                    <form method="GET" action="{{ route('ledger.index') }}" class="flex items-end gap-3 mb-6">
                        <div class="flex-1 max-w-md">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Account name or number..."
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        </div>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Search</button>
                        @if(request('search'))
                            <a href="{{ route('ledger.index') }}"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Clear</a>
                        @endif
                    </form>

                    @if($accounts->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-8">No accounts match your search.</p>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($accounts as $account)
                            <a href="{{ route('ledger.show', $account) }}"
                                class="block border border-gray-200 rounded-lg p-4 hover:border-indigo-500 hover:shadow-md transition bg-gray-50 hover:bg-white group">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-indigo-600 transition">
                                            {{ $account->account_number }}
                                        </h4>
                                        <p class="text-sm font-medium text-gray-600 truncate max-w-[180px]">
                                            {{ $account->account_name }}
                                        </p>
                                    </div>
                                    <span
                                        class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded font-semibold">{{ $account->account_category }}</span>
                                </div>
                                <div class="flex justify-between items-end mt-4 pt-4 border-t border-gray-200">
                                    <span class="text-xs text-gray-500">{{ $account->activity_count }} transactions</span>
                                    <span
                                        class="font-mono font-bold {{ $account->computed_balance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                        ${{ number_format(abs($account->computed_balance), 2) }}
                                        <span
                                            class="text-xs text-gray-500 font-sans ml-1">({{ ucfirst($account->normal_side) }})</span>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>