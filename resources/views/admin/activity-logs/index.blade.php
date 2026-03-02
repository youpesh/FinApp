<x-app-layout>
    <x-slot name="title">Activity Log</x-slot>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Activity Log</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">System-wide audit trail of all user actions.</p>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Time</th>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Performed By</th>
                        <th class="px-6 py-3">Subject</th>
                        <th class="px-6 py-3">Changes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-medium text-gray-900 dark:text-white text-xs">
                                    {{ $log->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $log->created_at->format('g:i A') }} ·
                                    {{ $log->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $eventColors = ['created' => 'green', 'updated' => 'blue', 'deleted' => 'red', 'login' => 'purple', 'logout' => 'gray'];
                                    $color = $eventColors[$log->event] ?? 'gray';
                                @endphp
                                <span
                                    class="bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-{{ $color }}-900 dark:text-{{ $color }}-300 capitalize">{{ $log->event }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 max-w-xs">{{ $log->description }}</td>
                            <td class="px-6 py-4">
                                @if($log->causer)
                                    <span
                                        class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-gray-700 dark:text-gray-300">{{ $log->causer->username }}</span>
                                @else
                                    <span class="text-gray-400 text-xs italic">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-medium">{{ class_basename($log->subject_type) }}</span>
                                <span class="text-gray-400"> #{{ $log->subject_id }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs max-w-xs">
                                @if(!empty($log->properties['old']) || !empty($log->properties['attributes']))
                                    @foreach($log->properties['attributes'] ?? [] as $key => $value)
                                        @if(isset($log->properties['old'][$key]))
                                            <div class="mb-1">
                                                <span class="text-gray-500 font-medium">{{ $key }}:</span>
                                                <span
                                                    class="text-red-500 line-through ml-1">{{ Str::limit((string) $log->properties['old'][$key], 15) }}</span>
                                                <span class="text-gray-400 mx-1">→</span>
                                                <span
                                                    class="text-green-600 dark:text-green-400">{{ Str::limit((string) $value, 15) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-gray-400 dark:text-gray-500">No activity logs found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>