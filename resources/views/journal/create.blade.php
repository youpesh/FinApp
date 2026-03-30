<x-app-layout>
    @php
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Journal Entries', 'url' => route('journal-entries.index')],
            ['label' => 'Create Entry'],
        ];
    @endphp
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('journal-entries.index') }}" class="text-gray-500 hover:text-gray-700">
                &larr; Back
            </a>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Create Journal Entry') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-8 text-gray-900">

                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">There were problems with your submission
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('journal-entries.store') }}" method="POST" enctype="multipart/form-data"
                        x-data="journalForm()">
                        @csrf

                        <!-- Header Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Date</label>
                                <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Source Documents
                                    (Optional)</label>
                                <input type="file" name="attachments[]" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png"
                                    class="border border-gray-300 rounded-md block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-xs text-gray-500 mt-1">Accepted: PDF, DOC, XLS, CSV, JPG, PNG (Max 5MB
                                    per file)</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Description / Reason</label>
                                <textarea name="description" rows="3" required
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Dynamic Table Lines -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Entry Lines</h3>

                            <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                                Account</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-48">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-48">
                                                Amount</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                                Memo</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-16">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        <template x-for="(line, index) in lines" :key="line.id">
                                            <tr :class="{'bg-red-50': line.error}">
                                                <td class="px-4 py-3">
                                                    <select x-model="line.account_id"
                                                        :name="'lines['+index+'][account_id]'" required
                                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                                        <option value="">Select Account</option>
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}">
                                                                {{ $account->account_number }} - {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <select x-model="line.type" :name="'lines['+index+'][type]'"
                                                        @change="validateSequence()" required
                                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                                        <option value="debit">Debit</option>
                                                        <option value="credit">Credit</option>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <span class="text-gray-500 sm:text-sm">$</span>
                                                        </div>
                                                        <input type="number" step="0.01" min="0.01" required
                                                            x-model="line.amount" :name="'lines['+index+'][amount]'"
                                                            class="pl-7 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text" x-model="line.description"
                                                        :name="'lines['+index+'][description]'"
                                                        placeholder="Optional reference..."
                                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeLine(line.id)"
                                                        class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-md"
                                                        x-show="lines.length > 2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="5" class="px-4 py-3 border-b flex items-center space-x-2">
                                                <button type="button" @click="addLine('debit')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-indigo-200 text-sm font-medium rounded text-indigo-700 bg-indigo-50 hover:bg-indigo-100 mr-2">
                                                    + Add Debit Line
                                                </button>
                                                <button type="button" @click="addLine('credit')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-amber-200 text-sm font-medium rounded text-amber-700 bg-amber-50 hover:bg-amber-100">
                                                    + Add Credit Line
                                                </button>

                                                <div class="ml-auto text-sm text-red-600 font-semibold italic flex-grow text-right pr-4"
                                                    x-show="sequenceError">
                                                    Error: Debits must come before credits!
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="px-6 py-4 text-right font-bold text-gray-700">Totals:
                                            </td>
                                            <td class="px-4 py-4 font-mono font-bold w-48 text-left"
                                                :class="isBalanced ? 'text-green-600' : 'text-red-600'">
                                                <div class="flex flex-col">
                                                    <span>Dr: $<span x-text="formatTotal(totalDebits)"></span></span>
                                                    <span>Cr: $<span x-text="formatTotal(totalCredits)"></span></span>
                                                </div>
                                            </td>
                                            <td colspan="2" class="px-4 py-4 text-left font-bold"
                                                :class="isBalanced ? 'text-green-600' : 'text-red-600'">
                                                <span x-show="isBalanced" class="flex items-center">
                                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Balanced
                                                </span>
                                                <span x-show="!isBalanced" class="flex items-center">
                                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    Out of balance by $<span
                                                        x-text="formatTotal(Math.abs(totalDebits - totalCredits))"></span>
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="flex justify-end pt-5 border-t border-gray-200">
                            <a href="{{ route('journal-entries.index') }}"
                                class="py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                                Cancel
                            </a>
                            <button type="button" @click="resetForm"
                                class="py-2 px-4 border border-red-300 text-red-700 rounded-md text-sm font-medium hover:bg-red-50 mr-3">
                                Clear Data
                            </button>
                            <button type="submit" :disabled="!isBalanced || sequenceError || lines.length < 2"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                Submit for Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('journalForm', () => ({
                    uuid: 2, // Used for unique IDs on rows since we start with 2
                    lines: [
                        { id: 1, account_id: '', type: 'debit', amount: '', description: '', error: false },
                        { id: 2, account_id: '', type: 'credit', amount: '', description: '', error: false }
                    ],
                    sequenceError: false,

                    get totalDebits() {
                        return this.lines.filter(l => l.type === 'debit')
                            .reduce((sum, l) => sum + (parseFloat(l.amount) || 0), 0);
                    },

                    get totalCredits() {
                        return this.lines.filter(l => l.type === 'credit')
                            .reduce((sum, l) => sum + (parseFloat(l.amount) || 0), 0);
                    },

                    get isBalanced() {
                        let d = Math.round(this.totalDebits * 100);
                        let c = Math.round(this.totalCredits * 100);
                        return d === c && d > 0;
                    },

                    validateSequence() {
                        let foundCredit = false;
                        this.sequenceError = false;
                        this.lines.forEach(line => {
                            line.error = false;
                            if (line.type === 'credit') {
                                foundCredit = true;
                            } else if (line.type === 'debit' && foundCredit) {
                                this.sequenceError = true;
                                line.error = true;
                            }
                        });
                    },

                    addLine(type) {
                        this.uuid++;
                        this.lines.push({
                            id: this.uuid,
                            account_id: '',
                            type: type,
                            amount: '',
                            description: '',
                            error: false
                        });
                        this.validateSequence();
                    },

                    removeLine(id) {
                        if (this.lines.length <= 2) return;
                        this.lines = this.lines.filter(l => l.id !== id);
                        this.validateSequence();
                    },

                    resetForm() {
                        if (confirm('Are you sure you want to clear all lines?')) {
                            this.uuid = 2;
                            this.lines = [
                                { id: 1, account_id: '', type: 'debit', amount: '', description: '', error: false },
                                { id: 2, account_id: '', type: 'credit', amount: '', description: '', error: false }
                            ];
                            // Also clear the standard html inputs inside the form using standard DOM
                            document.querySelector('textarea[name="description"]').value = '';
                            document.querySelector('input[type="file"]').value = '';
                        }
                    },

                    formatTotal(val) {
                        return (parseFloat(val) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>