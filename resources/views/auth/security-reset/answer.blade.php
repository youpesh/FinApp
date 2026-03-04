<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 ">
        {{ __('Please answer the security question below to verify your identity.') }}
    </div>

    <form method="POST" action="{{ route('password.security.verify-answer') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Security Question (display only) -->
        <div>
            <x-input-label :value="__('Security Question')" />
            <p
                class="mt-1 text-sm font-medium text-gray-900  bg-gray-50  rounded-md p-3">
                {{ $security_question }}
            </p>
        </div>

        <!-- Security Answer -->
        <div class="mt-4">
            <x-input-label for="security_answer" :value="__('Your Answer')" />
            <x-text-input id="security_answer" class="block mt-1 w-full" type="text" name="security_answer" required
                autofocus />
            <x-input-error :messages="$errors->get('security_answer')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600  hover:text-gray-900  rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 "
                href="{{ route('password.security.identify') }}">
                {{ __('Start Over') }}
            </a>

            <x-primary-button>
                {{ __('Verify Answer') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>