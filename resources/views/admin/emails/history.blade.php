<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Email History for {{ $user->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-gray-600">Showing all emails sent to <strong>{{ $user->email }}</strong>
                        </p>
                        <a href="{{ route('admin.emails.create', $user) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700">
                            + Compose New Email
                        </a>
                    </div>

                    @if($emails->isEmpty())
                        <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-lg">
                            No emails have been sent to this user yet.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($emails as $email)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-sm">{{ $email->subject }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Sent by: {{ $email->sender->full_name ?? 'System' }}
                                                &nbsp;&bull;&nbsp;
                                                {{ $email->sent_at->format('M d, Y g:i A') }}
                                            </p>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $email->sent_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="mt-3 text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded p-3">
                                        {{ $email->body }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">←
                            Back to Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>