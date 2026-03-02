<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Welcome back, {{ Auth::user()->first_name }}!
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 capitalize">{{ Auth::user()->role }} Account</p>
        </div>
    </x-slot>

    @if(Auth::user()->isAdmin())
        {{-- Admin stat cards --}}
        @php
            $totalUsers = \App\Models\User::count();
            $activeUsers = \App\Models\User::where('status', 'active')->count();
            $pendingRequests = \App\Models\User::where('status', 'pending')->count();
            $expiredPasswords = \App\Models\User::whereNotNull('password_expires_at')->where('password_expires_at', '<', now())->count();
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                    <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 18">
                            <path
                                d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Users</p>
                    <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $activeUsers }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Requests</p>
                    <div class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingRequests }}</p>
                @if($pendingRequests > 0)
                    <a href="{{ route('admin.requests.index') }}"
                        class="text-xs text-yellow-600 dark:text-yellow-400 hover:underline mt-1 block">Review now →</a>
                @endif
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Expired Passwords</p>
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $expiredPasswords }}</p>
                @if($expiredPasswords > 0)
                    <a href="{{ route('admin.reports.expired-passwords') }}"
                        class="text-xs text-red-600 dark:text-red-400 hover:underline mt-1 block">View report →</a>
                @endif
            </div>
        </div>
    @endif

    {{-- Quick Access Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @if (Auth::user()->isAdmin())
            <a href="{{ route('admin.users.index') }}"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:border-primary-300 hover:shadow-md dark:hover:border-primary-700 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl group-hover:bg-primary-100 transition">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 18">
                            <path
                                d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
                        </svg>
                    </div>
                    <div>
                        <p
                            class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">
                            Manage Users</p>
                        <p class="text-xs text-gray-400 mt-0.5">View, edit, and manage all accounts</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.reports.users') }}"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:border-primary-300 hover:shadow-md dark:hover:border-primary-700 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl group-hover:bg-blue-100 transition">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                        </svg>
                    </div>
                    <div>
                        <p
                            class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                            User Reports</p>
                        <p class="text-xs text-gray-400 mt-0.5">Analytics and password expiry tracking</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.activity-logs.index') }}"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:border-primary-300 hover:shadow-md dark:hover:border-primary-700 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl group-hover:bg-indigo-100 transition">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 0 1 0 2Z" />
                        </svg>
                    </div>
                    <div>
                        <p
                            class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                            Activity Log</p>
                        <p class="text-xs text-gray-400 mt-0.5">Audit trail of all system actions</p>
                    </div>
                </div>
            </a>
        @endif

        @if (Auth::user()->isManager() || Auth::user()->isAdmin())
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 opacity-60 cursor-not-allowed">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-500">Journal Entries</p>
                        <p class="text-xs text-gray-400 mt-0.5">Coming in Sprint 3</p>
                    </div>
                </div>
            </div>
        @endif

        @if (Auth::user()->isAccountant() || Auth::user()->isAdmin())
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 opacity-60 cursor-not-allowed">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-500">Chart of Accounts</p>
                        <p class="text-xs text-gray-400 mt-0.5">Coming in Sprint 2</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>