<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Send Email to {{ $user->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div
                        class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <span class="font-medium">To:</span> {{ $user->full_name }}
                            — <span class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            <span class="font-medium">Username:</span> {{ $user->username }}
                            &nbsp;|&nbsp;
                            <span class="font-medium">Role:</span> {{ ucfirst($user->role) }}
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

                        <div class="mb-6">
                            <x-input-label for="body" :value="__('Message')" />
                            <textarea id="body" name="body" rows="8"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required placeholder="Type your message here...">{{ old('body') }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>
                                <svg class="w-4 h-4 mr-1.5 -ml-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                {{ __('Send Email') }}
                            </x-primary-button>
                            <a href="{{ route('admin.users.index') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition">Cancel</a>
                            <a href="{{ route('admin.emails.history', $user) }}"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 ml-auto transition">
                                View Email History →
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>