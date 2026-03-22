<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.show', $account) }}" title="Back to account detail"
                class="text-gray-400 hover:text-gray-600  transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800  leading-tight">Edit Account: {{ $account->account_name }}</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

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

                    <form id="update-account-form" method="POST" action="{{ route('accounts.update', $account) }}"
                        class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Account Name --}}
                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700 ">Account Name
                                    *</label>
                                <input type="text" name="account_name" id="account_name"
                                    value="{{ old('account_name', $account->account_name) }}"
                                    title="Unique name for this account"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_name') border-red-500 @enderror"
                                    required>
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div>
                                <label for="account_category" class="block text-sm font-medium text-gray-700 ">Category
                                    *</label>
                                <select name="account_category" id="account_category" title="The account type"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_category') border-red-500 @enderror"
                                    required>
                                    @foreach(['asset', 'liability', 'equity', 'revenue', 'expense'] as $cat)
                                        <option value="{{ $cat }}" {{ old('account_category', $account->account_category) === $cat ? 'selected' : '' }}>
                                            {{ ucfirst($cat) }}
                                        </option>
                                    @endforeach
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
                                    value="{{ old('account_number', $account->account_number) }}"
                                    title="Whole number starting with the category digit"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_number') border-red-500 @enderror"
                                    step="1" min="1" required>
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Subcategory --}}
                            <div>
                                <label for="account_subcategory"
                                    class="block text-sm font-medium text-gray-700 ">Subcategory *</label>
                                <input type="text" name="account_subcategory" id="account_subcategory"
                                    value="{{ old('account_subcategory', $account->account_subcategory) }}"
                                    title="Subcategory such as Current Assets, Fixed Assets, etc."
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
                                    title="The normal balance side for this account"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('normal_side') border-red-500 @enderror"
                                    required>
                                    <option value="debit" {{ old('normal_side', $account->normal_side) === 'debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="credit" {{ old('normal_side', $account->normal_side) === 'credit' ? 'selected' : '' }}>Credit</option>
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
                                    title="Which financial statement this account belongs to"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('statement') border-red-500 @enderror"
                                    required>
                                    <option value="BS" {{ old('statement', $account->statement) === 'BS' ? 'selected' : '' }}>Balance Sheet (BS)</option>
                                    <option value="IS" {{ old('statement', $account->statement) === 'IS' ? 'selected' : '' }}>Income Statement (IS)</option>
                                    <option value="RE" {{ old('statement', $account->statement) === 'RE' ? 'selected' : '' }}>Retained Earnings (RE)</option>
                                </select>
                                @error('statement')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Order --}}
                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 ">Order *</label>
                                <input type="number" name="order" id="order" value="{{ old('order', $account->order) }}"
                                    title="Display order within the chart of accounts"
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
                                    value="{{ old('initial_balance', $account->initial_balance) }}"
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
                                <input type="number" name="debit" id="debit" value="{{ old('debit', $account->debit) }}"
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
                                <input type="number" name="credit" id="credit"
                                    value="{{ old('credit', $account->credit) }}" title="Current credit amount"
                                    class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('credit') border-red-500 @enderror"
                                    step="0.01" min="0" required>
                                @error('credit')
                                    <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Balance --}}
                            <div>
                                <label for="balance" class="block text-sm font-medium text-gray-700 ">Balance *</label>
                                <input type="number" name="balance" id="balance"
                                    value="{{ old('balance', $account->balance) }}" title="Current account balance"
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
                                class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('account_description') border-red-500 @enderror">{{ old('account_description', $account->account_description) }}</textarea>
                            @error('account_description')
                                <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Comment --}}
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 ">Comment</label>
                            <textarea name="comment" id="comment" rows="2"
                                title="Optional comment or note about this account"
                                class="mt-1 block w-full border-gray-300  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm @error('comment') border-red-500 @enderror">{{ old('comment', $account->comment) }}</textarea>
                            @error('comment')
                                <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                            @enderror
                        </div>
                    </form>

                    {{-- Actions Container --}}
                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                        {{-- Deactivate Form --}}
                        @if($account->is_active && (float) $account->balance == 0)
                            <form method="POST" action="{{ route('accounts.deactivate', $account) }}"
                                onsubmit="return confirm('Are you sure you want to deactivate this account?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="Deactivate this account (only allowed when balance is zero)"
                                    class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 border border-red-300 rounded-md hover:bg-red-50 transition">
                                    Deactivate Account
                                </button>
                            </form>
                        @else
                            <div></div>
                        @endif

                        <div class="flex gap-3">
                            <a href="{{ route('accounts.show', $account) }}" title="Cancel and return to account detail"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition">
                                Cancel
                            </a>
                            <button type="submit" form="update-account-form" title="Save changes to this account"
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 transition">
                                Update Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>