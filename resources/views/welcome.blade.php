<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Finance - Accounting Software</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body
    class="bg-gray-50  text-gray-900  flex p-6 lg:p-8 items-center justify-center min-h-screen flex-col font-sans antialiased">
    <header class="w-full lg:max-w-5xl max-w-sm text-sm mb-12">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-block px-5 py-2 bg-indigo-600 text-white  rounded-md font-medium transition hover:bg-indigo-700">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-5 py-2 font-medium text-gray-600  hover:text-gray-900  transition">
                        Log in
                    </a>

                    @if (Route::has('request.access'))
                        <a href="{{ route('request.access') }}"
                            class="inline-flex justify-center rounded-lg text-sm font-semibold py-3 px-6 bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md active:scale-95 transition-all w-full sm:w-auto">
                            Request Access
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
        <h1 class="text-4xl lg:text-5xl font-semibold tracking-tight mb-4 text-gray-900 ">Welcome to
            Smart Finance</h1>
        <p class="text-lg text-gray-600  leading-relaxed mb-10 max-w-xl">
            Modern, secure, and intuitive web-based accounting software designed to streamline your financial workflows
            and reporting.
        </p>

        @guest
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:justify-center">
                <a href="{{ route('login') }}"
                    class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 transition">
                    Sign In
                </a>
                <a href="{{ route('request.access') }}"
                    class="px-8 py-3 bg-white text-gray-900   border border-gray-200  rounded-lg font-medium hover:bg-gray-50  transition shadow-sm">
                    Request Access
                </a>
            </div>
        @endguest
    </main>

    <footer class="mt-auto pt-16 pb-4 text-sm text-gray-500 ">
        &copy; {{ date('Y') }} Smart Finance. All rights reserved.
    </footer>
</body>

</html>