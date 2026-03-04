<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 ">
        {{ __('To reset your password, please enter your email address and username below.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.security.verify-identity') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')"
                required />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600  hover:text-gray-900  rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 "
                href="{{ route('login') }}">
                {{ __('Back to Login') }}
            </a>

            <x-primary-button>
                {{ __('Continue') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>