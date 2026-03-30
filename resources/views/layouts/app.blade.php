<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Smart Finance') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Dark Mode Init — must run BEFORE styles load to prevent flicker -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 " x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- ─── Sidebar ──────────────────────────────────────────── -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900  flex flex-col transition-transform duration-300 ease-in-out
                   lg:relative lg:translate-x-0 lg:flex lg:flex-shrink-0">

            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-700/60">
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto" alt="Smart Finance Logo">
                </div>
                <span class="text-white font-bold text-lg tracking-tight">Smart Finance</span>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

                <!-- General -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                @if(Auth::user()->isAdmin())
                    <!-- Admin Section -->
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-widest">Admin</p>
                    </div>

                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Users
                    </a>

                    <a href="{{ route('admin.requests.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ request()->routeIs('admin.requests.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Access Requests
                    </a>

                    <a href="{{ route('admin.reports.users') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reports
                    </a>

                    <a href="{{ route('admin.activity-logs.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ request()->routeIs('admin.activity-logs.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Activity Log
                    </a>
                @endif

                @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-widest">Management</p>
                    </div>
                    <a href="{{ route('journal-entries.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ request()->routeIs('journal-entries.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Journal Entries
                    </a>
                @endif

                @if(Auth::user()->isAccountant() || Auth::user()->isAdmin() || Auth::user()->isManager())
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-widest">Accounting</p>
                    </div>
                    <a href="{{ route('accounts.index') }}" title="View and manage the chart of accounts"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ request()->routeIs('accounts.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Chart of Accounts
                    </a>

                    <a href="{{ route('ledger.index') }}" title="View general ledger for all accounts"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ request()->routeIs('ledger.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        General Ledger
                    </a>
                @endif

            </nav>

            <!-- User footer -->
            <div class="border-t border-gray-700/60 px-4 py-4">
                <div class="flex items-center gap-3">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                            class="w-8 h-8 rounded-full object-cover shrink-0" alt="Avatar">
                    @else
                        <div
                            class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->full_name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                    <div class="flex gap-1.5">
                        <a href="{{ route('profile.edit') }}" title="Profile"
                            class="p-1.5 rounded-md text-gray-500 hover:text-white hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Log out"
                                class="p-1.5 rounded-md text-gray-500 hover:text-white hover:bg-gray-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ─── Mobile overlay ───────────────────────────────────── -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- ─── Main content area ────────────────────────────────── -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            <!-- Top bar (mobile only) -->
            <header class="lg:hidden flex items-center gap-4 px-4 py-3 bg-white  border-b border-gray-200  shadow-sm">
                <button @click="sidebarOpen = true"
                    class="p-2 rounded-md text-gray-500  hover:text-gray-700  hover:bg-gray-100  transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="font-bold text-gray-800 ">Smart Finance</span>
            </header>

            <!-- Page header slot -->
            @isset($header)
                <div class="bg-white  border-b border-gray-200  px-6 py-4">
                    {{ $header }}
                </div>
            @endisset

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto bg-gray-100 ">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>

</html>