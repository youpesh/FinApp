<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Send Email to {{ $user->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border">
                        <p class="text-sm text-gray-600">
                            <strong>To:</strong> {{ $user->full_name }} — <span
                                class="font-mono text-xs">{{ $user->email }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            <strong>Username:</strong> {{ $user->username }} | <strong>Role:</strong>
                            {{ ucfirst($user->role) }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.emails.send', $user) }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="subject" :value="__('Subject')" />
                            <x-text-input id="subject" class="block mt-1 w-full" type="text" name="subject"
                                :value="old('subject')" required autofocus
                                placeholder="e.g., Password Expiry Reminder" />
                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="body" :value="__('Message')" />
                            <textarea id="body" name="body" rows="8"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required placeholder="Type your message here...">{{ old('body') }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Send Email') }}</x-primary-button>
                            <a href="{{ route('admin.users.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                            <a href="{{ route('admin.emails.history', $user) }}"
                                class="text-sm text-indigo-600 hover:text-indigo-900 ml-auto">View Email History →</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>