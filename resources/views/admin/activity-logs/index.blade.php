<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Activity Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6">System-Wide Activity Audit Trail
                    </h3>

                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Time</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Event</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Description</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Performed By</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Subject</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Changed Properties</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $log->created_at->format('M d, Y g:i A') }}<br>
                                            <span
                                                class="text-gray-400 dark:text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $eventColors = ['created' => 'green', 'updated' => 'blue', 'deleted' => 'red'];
                                                $color = $eventColors[$log->event] ?? 'gray';
                                            @endphp
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900/40 dark:text-{{ $color }}-300 capitalize">
                                                {{ $log->event }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $log->description }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($log->causer)
                                                <span
                                                    class="font-mono text-xs text-gray-700 dark:text-gray-300">{{ $log->causer->username }}</span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">System</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                        </td>
                                        <td class="px-4 py-3 text-xs">
                                            @if(!empty($log->properties['old']) || !empty($log->properties['attributes']))
                                                @foreach($log->properties['attributes'] ?? [] as $key => $value)
                                                    @if(isset($log->properties['old'][$key]))
                                                        <div>
                                                            <span class="text-gray-500 dark:text-gray-400">{{ $key }}:</span>
                                                            <span
                                                                class="text-red-500 line-through">{{ Str::limit((string) $log->properties['old'][$key], 20) }}</span>
                                                            →
                                                            <span
                                                                class="text-green-600 dark:text-green-400">{{ Str::limit((string) $value, 20) }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No
                                            activity logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>