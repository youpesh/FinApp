<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex items-center">
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
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="font-bold text-lg mb-2">Admin Controls</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:underline">Manage
                                    Users</a></li>
                            <li><a href="{{ route('admin.requests.index') }}"
                                    class="text-indigo-600 hover:underline">Pending Access Requests</a></li>
                            <li><a href="{{ route('admin.reports.users') }}" class="text-indigo-600 hover:underline">📊 User
                                    Report</a></li>
                            <li><a href="{{ route('admin.reports.expired-passwords') }}"
                                    class="text-red-600 hover:underline">⚠ Expired Passwords Report</a></li>
                            <li><a href="{{ route('admin.activity-logs.index') }}"
                                    class="text-indigo-600 hover:underline">📋 Activity Log</a></li>
                        </ul>
                    </div>
                @endif

                @if (Auth::user()->isManager() || Auth::user()->isAdmin())
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="font-bold text-lg mb-2">Manager Actions</h4>
                        <ul class="space-y-2">
                            <li class="text-gray-500">Review Journal Entries (Coming in Sprint 3)</li>
                            <li class="text-gray-500">Financial Reports (Coming in Sprint 4)</li>
                        </ul>
                    </div>
                @endif

                @if (Auth::user()->isAccountant() || Auth::user()->isAdmin())
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="font-bold text-lg mb-2">Accountant Actions</h4>
                        <ul class="space-y-2">
                            <li class="text-gray-500">Chart of Accounts (Coming in Sprint 2)</li>
                            <li class="text-gray-500">Create Journal Entry (Coming in Sprint 3)</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>