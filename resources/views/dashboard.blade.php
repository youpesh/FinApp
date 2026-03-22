<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800  leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900  flex items-center">
                    <div class="mr-4">
                        @if (Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile"
                                class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div
                                class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xl font-bold">
                                {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Welcome, {{ Auth::user()->full_name }}!</h3>
                        <p class="text-sm text-gray-500 capitalize">Role: {{ Auth::user()->role }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if (Auth::user()->isAdmin())
                    <div class="bg-white  p-6 rounded-lg shadow-sm border border-gray-100 ">
                        <h4 class="font-bold text-lg mb-3 text-gray-800 ">Admin Controls</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('admin.users.index') }}"
                                    class="flex items-center gap-2 text-indigo-600  hover:text-indigo-900  transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Manage Users</a></li>
                            <li><a href="{{ route('admin.requests.index') }}"
                                    class="flex items-center gap-2 text-indigo-600  hover:text-indigo-900  transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    Pending Access Requests</a></li>
                            <li><a href="{{ route('admin.reports.users') }}"
                                    class="flex items-center gap-2 text-indigo-600  hover:text-indigo-900  transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    User Report</a></li>
                            <li><a href="{{ route('admin.reports.expired-passwords') }}"
                                    class="flex items-center gap-2 text-red-600  hover:text-red-900  transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Expired Passwords Report</a></li>
                            <li><a href="{{ route('admin.activity-logs.index') }}"
                                    class="flex items-center gap-2 text-indigo-600  hover:text-indigo-900  transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Activity Log</a></li>
                        </ul>
                    </div>
                @endif

                @if (Auth::user()->isManager() || Auth::user()->isAdmin())
                    <div class="bg-white  p-6 rounded-lg shadow-sm border border-gray-100 ">
                        <h4 class="font-bold text-lg mb-3 text-gray-800 ">Manager Actions</h4>
                        <ul class="space-y-2">
                            <li class="text-gray-500 ">Review Journal Entries (Coming in Sprint 3)</li>
                            <li class="text-gray-500 ">Financial Reports (Coming in Sprint 4)</li>
                        </ul>
                    </div>
                @endif

                @if (Auth::user()->isAccountant() || Auth::user()->isAdmin() || Auth::user()->isManager())
                    <div class="bg-white  p-6 rounded-lg shadow-sm border border-gray-100 ">
                        <h4 class="font-bold text-lg mb-3 text-gray-800 ">Accounting</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('accounts.index') }}"
                                    class="flex items-center gap-2 text-indigo-600  hover:text-indigo-900  transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Chart of Accounts</a></li>
                            <li class="text-gray-500 ">Create Journal Entry (Coming in Sprint 3)</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>