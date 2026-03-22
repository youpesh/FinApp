<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.show', $account) }}" title="Back to account detail"
                class="text-gray-400 hover:text-gray-600  transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800  leading-tight">
                Event Log: {{ $account->account_name }}
                <span class="text-sm font-normal text-gray-500 ">#{{ $account->account_number }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @forelse($events as $event)
                <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        {{-- Event header --}}
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase
                                    @switch($event->event_type)
                                        @case('created') bg-green-100 text-green-800 @break
                                        @case('updated') bg-blue-100 text-blue-800 @break
                                        @case('deactivated') bg-red-100 text-red-800 @break
                                        @case('activated') bg-emerald-100 text-emerald-800 @break
                                    @endswitch">
                                    {{ $event->event_type }}
                                </span>
                                <span class="text-sm text-gray-600 ">
                                    by <strong>{{ $event->user->full_name ?? 'System' }}</strong>
                                    ({{ $event->user->username ?? '-' }})
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 ">
                                <span class="font-mono">ID: {{ $event->id }}</span>
                                &middot;
                                {{ $event->created_at->format('M j, Y g:i:s A') }}
                            </div>
                        </div>

                        {{-- Before / After comparison --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Before Image --}}
                            <div class="rounded-lg border border-gray-200  p-3">
                                <h4 class="text-xs font-semibold text-gray-500  uppercase mb-2">Before</h4>
                                @if($event->before_image)
                                    <dl class="space-y-1">
                                        @foreach($event->before_image as $key => $value)
                                            @php
                                                $afterVal  = $event->after_image[$key] ?? null;
                                                $changed   = $afterVal !== $value;
                                            @endphp
                                            <div class="flex justify-between text-sm {{ $changed ? 'bg-red-50  rounded px-1' : '' }}">
                                                <dt class="text-gray-500  capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                                                <dd class="font-mono text-gray-800  {{ $changed ? 'font-semibold' : '' }}">
                                                    {{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}
                                                </dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                @else
                                    <p class="text-sm text-gray-400  italic">No previous data (new account)</p>
                                @endif
                            </div>

                            {{-- After Image --}}
                            <div class="rounded-lg border border-gray-200  p-3">
                                <h4 class="text-xs font-semibold text-gray-500  uppercase mb-2">After</h4>
                                @if($event->after_image)
                                    <dl class="space-y-1">
                                        @foreach($event->after_image as $key => $value)
                                            @php
                                                $beforeVal = $event->before_image[$key] ?? null;
                                                $changed   = $beforeVal !== $value;
                                            @endphp
                                            <div class="flex justify-between text-sm {{ $changed ? 'bg-green-50  rounded px-1' : '' }}">
                                                <dt class="text-gray-500  capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                                                <dd class="font-mono text-gray-800  {{ $changed ? 'font-semibold' : '' }}">
                                                    {{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}
                                                </dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                @else
                                    <p class="text-sm text-gray-400  italic">No data</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-center text-gray-400 ">
                        No event log entries for this account.
                    </div>
                </div>
            @endforelse

            <div>
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
