<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Finance - Accounting Software</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body
    class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex p-6 lg:p-8 items-center justify-center min-h-screen flex-col font-sans antialiased">
    <header class="w-full lg:max-w-5xl max-w-sm text-sm mb-12">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-block px-5 py-2 bg-indigo-600 text-white dark:bg-indigo-500 rounded-md font-medium transition hover:bg-indigo-700">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-5 py-2 font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-block px-5 py-2 bg-indigo-600 text-white dark:bg-indigo-500 rounded-md font-medium transition hover:bg-indigo-700 shadow-sm">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <main class="flex w-full flex-col items-center text-center max-w-2xl px-4 flex-grow justify-center">
        <div class="mb-8">
            <img src="{{ asset('images/logo.png') }}" class="h-24 w-auto mx-auto" alt="Smart Finance Logo">
        </div>
        <h1 class="text-4xl lg:text-5xl font-semibold tracking-tight mb-4 text-gray-900 dark:text-white">Welcome to
            Smart Finance</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed mb-10 max-w-xl">
            Modern, secure, and intuitive web-based accounting software designed to streamline your financial workflows
            and reporting.
        </p>

        @guest
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:justify-center">
                <a href="{{ route('login') }}"
                    class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 transition">
                    Sign In
                </a>
                <a href="{{ route('register') }}"
                    class="px-8 py-3 bg-white text-gray-900 dark:bg-gray-800 dark:text-white border border-gray-200 dark:border-gray-700 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    Request Access
                </a>
            </div>
        @endguest
    </main>

    <footer class="mt-auto pt-16 pb-4 text-sm text-gray-500 dark:text-gray-400">
        &copy; {{ date('Y') }} Smart Finance. All rights reserved.
    </footer>
</body>

</html>