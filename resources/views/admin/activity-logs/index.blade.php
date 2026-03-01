<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">System-Wide Activity Audit Trail</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th
                                        class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                        Time</th>
                                    <th
                                        class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                        Event</th>
                                    <th
                                        class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                        Description</th>
                                    <th
                                        class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                        Performed By</th>
                                    <th
                                        class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                        Subject</th>
                                    <th
                                        class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                        Changed Properties</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                            {{ $log->created_at->format('M d, Y g:i A') }}<br>
                                            <span class="text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $eventColors = ['created' => 'green', 'updated' => 'blue', 'deleted' => 'red'];
                                                $color = $eventColors[$log->event] ?? 'gray';
                                            @endphp
                                            <span
                                                class="px-2 py-1 rounded-full text-xs bg-{{ $color }}-100 text-{{ $color }}-800 capitalize">
                                                {{ $log->event }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $log->description }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($log->causer)
                                                <span class="font-mono text-xs">{{ $log->causer->username }}</span>
                                            @else
                                                <span class="text-gray-400">System</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                        </td>
                                        <td class="px-4 py-3 text-xs">
                                            @if(!empty($log->properties['old']) || !empty($log->properties['attributes']))
                                                @foreach($log->properties['attributes'] ?? [] as $key => $value)
                                                    @if(isset($log->properties['old'][$key]))
                                                        <div>
                                                            <span class="text-gray-500">{{ $key }}:</span>
                                                            <span
                                                                class="text-red-500 line-through">{{ Str::limit((string) $log->properties['old'][$key], 20) }}</span>
                                                            →
                                                            <span class="text-green-600">{{ Str::limit((string) $value, 20) }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No activity logs found.
                                        </td>
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