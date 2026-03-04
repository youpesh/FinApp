<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name"
                :value="old('first_name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name"
                :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Security Question -->
        <div class="mt-4">
            <x-input-label for="security_question" :value="__('Security Question')" />
            <select id="security_question" name="security_question"
                class="block mt-1 w-full border-gray-300    focus:border-indigo-500  focus:ring-indigo-500  rounded-md shadow-sm"
                required>
                <option value="">{{ __('Select a security question') }}</option>
                <option value="What is your mother's maiden name?" {{ old('security_question') == "What is your mother's maiden name?" ? 'selected' : '' }}>What is your mother's maiden name?</option>
                <option value="What was the name of your first pet?" {{ old('security_question') == 'What was the name of your first pet?' ? 'selected' : '' }}>What was the name of your first pet?</option>
                <option value="What city were you born in?" {{ old('security_question') == 'What city were you born in?' ? 'selected' : '' }}>What city were you born in?</option>
                <option value="What is the name of your favorite teacher?" {{ old('security_question') == 'What is the name of your favorite teacher?' ? 'selected' : '' }}>What is the name of your favorite teacher?
                </option>
                <option value="What was the make of your first car?" {{ old('security_question') == 'What was the make of your first car?' ? 'selected' : '' }}>What was the make of your first car?</option>
            </select>
            <x-input-error :messages="$errors->get('security_question')" class="mt-2" />
        </div>

        <!-- Security Answer -->
        <div class="mt-4">
            <x-input-label for="security_answer" :value="__('Security Answer')" />
            <x-text-input id="security_answer" class="block mt-1 w-full" type="text" name="security_answer"
                :value="old('security_answer')" required />
            <x-input-error :messages="$errors->get('security_answer')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600  hover:text-gray-900  rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 "
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>