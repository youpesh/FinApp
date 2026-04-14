@php
    $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
    $recent = \App\Models\Notification::where('user_id', auth()->id())->orderByDesc('created_at')->limit(5)->get();
@endphp

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.outside="open = false" x-transition
        class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg border border-gray-200 z-50" style="display: none;">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-800">Notifications</h3>
            @if($unreadCount > 0)
                <span class="text-xs text-indigo-600 font-medium">{{ $unreadCount }} unread</span>
            @endif
        </div>
        <ul class="max-h-96 overflow-y-auto divide-y divide-gray-100">
            @forelse($recent as $n)
                <li class="{{ $n->read_at ? '' : 'bg-indigo-50/50' }} hover:bg-gray-50">
                    <a href="{{ $n->action_url ?: route('notifications.index') }}" class="block px-4 py-3">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $n->title }}</p>
                        <p class="text-xs text-gray-600 truncate">{{ $n->message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                    </a>
                </li>
            @empty
                <li class="px-4 py-6 text-center text-sm text-gray-500">No notifications</li>
            @endforelse
        </ul>
        <div class="px-4 py-2 border-t border-gray-200 text-center">
            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">View all</a>
        </div>
    </div>
</div>
