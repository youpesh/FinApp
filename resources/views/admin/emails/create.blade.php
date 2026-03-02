<x-app-layout>
    <x-slot name="title">Send Email</x-slot>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Send Internal Email</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Message will be logged in the system.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        {{-- Recipient Card --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-5 flex items-center gap-4">
            <div
                class="h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 font-bold text-sm flex-shrink-0">
                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->full_name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }} · <span class="capitalize">{{ $user->role }}</span>
                </p>
            </div>
            <a href="{{ route('admin.emails.history', $user) }}"
                class="text-xs text-primary-600 dark:text-primary-400 hover:underline">View History →</a>
        </div>

        {{-- Compose Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('admin.emails.send', $user) }}" class="space-y-5">
                @csrf
                <div>
                    <label for="subject"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required autofocus
                        placeholder="e.g., Password Expiry Reminder"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                    @error('subject')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="body"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Message</label>
                    <textarea id="body" name="body" rows="8" required placeholder="Type your message here..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">{{ old('body') }}</textarea>
                    @error('body')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-6 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send Email
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>