<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Notifications'],
    ]">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Notifications') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('notifications.index') }}"
                    class="px-3 py-1.5 text-sm rounded-md {{ !request('filter') ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">All</a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                    class="px-3 py-1.5 text-sm rounded-md {{ request('filter') === 'unread' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">Unread</a>
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit"
                        class="px-3 py-1.5 text-sm rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Mark all read</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <ul class="divide-y divide-gray-200">
                    @forelse($notifications as $notification)
                        <li class="p-5 {{ $notification->read_at ? '' : 'bg-indigo-50/50' }} hover:bg-gray-50">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    @if(!$notification->read_at)
                                        <span class="w-2 h-2 rounded-full bg-indigo-600 inline-block"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ $notification->title }}</p>
                                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $notification->message }}</p>
                                    <p class="mt-2 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex-shrink-0 flex gap-2">
                                    @if($notification->action_url)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            <button type="submit"
                                                class="text-sm text-indigo-600 hover:underline">View &rarr;</button>
                                        </form>
                                    @elseif(!$notification->read_at)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs text-gray-500 hover:text-gray-700">Mark read</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-8 text-center text-sm text-gray-500">No notifications yet.</li>
                    @endforelse
                </ul>
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
