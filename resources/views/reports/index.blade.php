<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Financial Reports'],
    ]">
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Financial Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Report selection tiles -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('reports.trial-balance') }}"
                    class="block p-6 bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                    <h3 class="font-bold text-gray-800 text-lg mb-2">Trial Balance</h3>
                    <p class="text-sm text-gray-500">All account balances on a given date; debits should equal credits.</p>
                </a>
                <a href="{{ route('reports.income-statement') }}"
                    class="block p-6 bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                    <h3 class="font-bold text-gray-800 text-lg mb-2">Income Statement</h3>
                    <p class="text-sm text-gray-500">Revenue, expenses, and net income for a period.</p>
                </a>
                <a href="{{ route('reports.balance-sheet') }}"
                    class="block p-6 bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                    <h3 class="font-bold text-gray-800 text-lg mb-2">Balance Sheet</h3>
                    <p class="text-sm text-gray-500">Assets, liabilities, and equity as of a given date.</p>
                </a>
                <a href="{{ route('reports.retained-earnings') }}"
                    class="block p-6 bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                    <h3 class="font-bold text-gray-800 text-lg mb-2">Retained Earnings</h3>
                    <p class="text-sm text-gray-500">Opening RE + net income − distributions = ending RE.</p>
                </a>
            </div>

            <!-- Saved snapshots -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 text-lg">Saved Snapshots</h3>
                    <p class="text-sm text-gray-500 mt-1">Point-in-time reports previously saved by you or other users.</p>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generated</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">By</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($snapshots as $snapshot)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $snapshot->title }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $types[$snapshot->type] ?? $snapshot->type }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $snapshot->generated_at->format('M d, Y g:ia') }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $snapshot->generator->full_name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3 text-right text-sm">
                                    <a href="{{ route('reports.snapshot.show', $snapshot) }}"
                                        class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No saved snapshots yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-3 border-t border-gray-200">{{ $snapshots->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
