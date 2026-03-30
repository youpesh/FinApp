<x-app-layout>
    @php
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Chart of Accounts', 'url' => route('accounts.index')],
            ['label' => 'Add Account'],
        ];
    @endphp
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.index') }}" title="Back to Chart of Accounts"
                class="text-gray-400 hover:text-gray-600  transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800  leading-tight">Add New Account</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50  border border-red-200  rounded-md p-4">
                            <h4 class="text-sm font-semibold text-red-800  mb-2">Please correct the following errors:</h4>
                            <ul class="list-disc list-inside text-sm text-red-600 ">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('accounts.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Account Name --}}
                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700 ">Account Name
                                    *</label>
                                <input type="text" name="account_name" id="account_name"
                                    value="{{ old('account_name') }}"
                                    title="Unique name for this account (e.g. Cash, Accounts Receivable)"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_name') border-red-500 @enderror"
                                    required>
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Account Category --}}
                            <div>
                                <label for="account_category" class="block text-sm font-medium text-gray-700 ">Category
                                    *</label>
                                <select name="account_category" id="account_category"
                                    title="The account type (Asset, Liability, Equity, Revenue, or Expense)"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_category') border-red-500 @enderror"
                                    required>
                                    <option value="">Select Category</option>
                                    <option value="asset" {{ old('account_category') === 'asset' ? 'selected' : '' }}>
                                        Asset</option>
                                    <option value="liability" {{ old('account_category') === 'liability' ? 'selected' : '' }}>Liability</option>
                                    <option value="equity" {{ old('account_category') === 'equity' ? 'selected' : '' }}>
                                        Equity</option>
                                    <option value="revenue" {{ old('account_category') === 'revenue' ? 'selected' : '' }}>
                                        Revenue</option>
                                    <option value="expense" {{ old('account_category') === 'expense' ? 'selected' : '' }}>
                                        Expense</option>
                                </select>
                                @error('account_category')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Account Number --}}
                            <div>
                                <label for="account_number" class="block text-sm font-medium text-gray-700 ">Account
                                    Number *</label>
                                <input type="number" name="account_number" id="account_number"
                                    value="{{ old('account_number') }}"
                                    title="Whole number starting with the category digit (1=Asset, 2=Liability, 3=Equity, 4=Revenue, 5=Expense)"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_number') border-red-500 @enderror"
                                    step="1" min="1" required>
                                <p class="mt-1 text-xs text-gray-400 " id="account_number_hint">Must start with:
                                    1=Asset, 2=Liability, 3=Equity, 4=Revenue, 5=Expense</p>
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Subcategory --}}
                            <div>
                                <label for="account_subcategory"
                                    class="block text-sm font-medium text-gray-700 ">Subcategory *</label>
                                <input type="text" name="account_subcategory" id="account_subcategory"
                                    value="{{ old('account_subcategory') }}"
                                    title="Subcategory such as Current Assets, Fixed Assets, Long-term Liabilities, etc."
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_subcategory') border-red-500 @enderror"
                                    required>
                                @error('account_subcategory')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Normal Side --}}
                            <div>
                                <label for="normal_side" class="block text-sm font-medium text-gray-700 ">Normal Side
                                    *</label>
                                <select name="normal_side" id="normal_side"
                                    title="The normal balance side for this account (Debit or Credit)"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('normal_side') border-red-500 @enderror"
                                    required>
                                    <option value="">Select Side</option>
                                    <option value="debit" {{ old('normal_side') === 'debit' ? 'selected' : '' }}>Debit
                                    </option>
                                    <option value="credit" {{ old('normal_side') === 'credit' ? 'selected' : '' }}>Credit
                                    </option>
                                </select>
                                @error('normal_side')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Statement --}}
                            <div>
                                <label for="statement" class="block text-sm font-medium text-gray-700 ">Statement
                                    *</label>
                                <select name="statement" id="statement"
                                    title="Which financial statement this account belongs to (IS=Income Statement, BS=Balance Sheet, RE=Retained Earnings)"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('statement') border-red-500 @enderror"
                                    required>
                                    <option value="">Select Statement</option>
                                    <option value="BS" {{ old('statement') === 'BS' ? 'selected' : '' }}>Balance Sheet
                                        (BS)</option>
                                    <option value="IS" {{ old('statement') === 'IS' ? 'selected' : '' }}>Income Statement
                                        (IS)</option>
                                    <option value="RE" {{ old('statement') === 'RE' ? 'selected' : '' }}>Retained Earnings
                                        (RE)</option>
                                </select>
                                @error('statement')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Order --}}
                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 ">Order *</label>
                                <input type="number" name="order" id="order" value="{{ old('order', 0) }}"
                                    title="Display order within the chart of accounts (e.g. Cash = 01)"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('order') border-red-500 @enderror"
                                    min="0" required>
                                @error('order')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Initial Balance --}}
                            <div>
                                <label for="initial_balance" class="block text-sm font-medium text-gray-700 ">Initial
                                    Balance *</label>
                                <input type="number" name="initial_balance" id="initial_balance"
                                    value="{{ old('initial_balance', '0.00') }}"
                                    title="The starting balance for this account"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('initial_balance') border-red-500 @enderror"
                                    step="0.01" min="0" required>
                                @error('initial_balance')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Debit --}}
                            <div>
                                <label for="debit" class="block text-sm font-medium text-gray-700 ">Debit *</label>
                                <input type="number" name="debit" id="debit" value="{{ old('debit', '0.00') }}"
                                    title="Current debit amount"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('debit') border-red-500 @enderror"
                                    step="0.01" min="0" required>
                                @error('debit')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Credit --}}
                            <div>
                                <label for="credit" class="block text-sm font-medium text-gray-700 ">Credit *</label>
                                <input type="number" name="credit" id="credit" value="{{ old('credit', '0.00') }}"
                                    title="Current credit amount"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('credit') border-red-500 @enderror"
                                    step="0.01" min="0" required>
                                @error('credit')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Balance --}}
                            <div>
                                <label for="balance" class="block text-sm font-medium text-gray-700 ">Balance *</label>
                                <input type="number" name="balance" id="balance" value="{{ old('balance', '0.00') }}"
                                    title="Current account balance"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('balance') border-red-500 @enderror"
                                    step="0.01" min="0" required>
                                @error('balance')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="account_description"
                                class="block text-sm font-medium text-gray-700 ">Description</label>
                            <textarea name="account_description" id="account_description" rows="3"
                                title="Optional description for this account"
                                class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_description') border-red-500 @enderror">{{ old('account_description') }}</textarea>
                            @error('account_description')
                                <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Comment --}}
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 ">Comment</label>
                            <textarea name="comment" id="comment" rows="2"
                                title="Optional comment or note about this account"
                                class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('comment') border-red-500 @enderror">{{ old('comment') }}</textarea>
                            @error('comment')
                                <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 ">
                            <a href="{{ route('accounts.index') }}" title="Cancel and return to Chart of Accounts"
                                class="px-4 py-2 text-sm font-medium text-gray-700  hover:text-gray-900  transition">
                                Cancel
                            </a>
                            <button type="submit" title="Save this new account to the chart of accounts"
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 transition">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Auto-set normal side & statement based on category --}}
    <script>
        document.getElementById('account_category')?.addEventListener('change', function () {
            const normalSideMap = { asset: 'debit', liability: 'credit', equity: 'credit', revenue: 'credit', expense: 'debit' };
            const statementMap = { asset: 'BS', liability: 'BS', equity: 'RE', revenue: 'IS', expense: 'IS' };
            const digitMap = { asset: 1, liability: 2, equity: 3, revenue: 4, expense: 5 };
            const cat = this.value;

            if (normalSideMap[cat]) {
                document.getElementById('normal_side').value = normalSideMap[cat];
            }
            if (statementMap[cat]) {
                document.getElementById('statement').value = statementMap[cat];
            }
            // Update hint
            const hint = document.getElementById('account_number_hint');
            if (hint && digitMap[cat]) {
                hint.textContent = `Account number must start with ${digitMap[cat]} for ${cat.charAt(0).toUpperCase() + cat.slice(1)} accounts.`;
            }
        });
    </script>
</x-app-layout>