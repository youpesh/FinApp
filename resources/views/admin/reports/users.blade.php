<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">All System Users</h3>
                        <a href="{{ route('admin.reports.expired-passwords') }}"
                            class="text-indigo-600 hover:text-indigo-900 text-sm">
                            → View Expired Passwords Report
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Username</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Full Name</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Password Expires</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Failed Attempts</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Last Login</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $user)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors {{ $user->trashed() ? 'opacity-60' : '' }}">
                                        <td class="px-4 py-3 text-sm font-mono text-gray-700 dark:text-gray-300">
                                            {{ $user->username }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $user->full_name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}
                                        </td>
                                        <td class="px-4 py-3 text-sm capitalize text-gray-700 dark:text-gray-300">
                                            {{ $user->role }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($user->trashed())
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Deleted</span>
                                            @elseif($user->status === 'active')
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Active</span>
                                            @elseif($user->status === 'suspended')
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Suspended</span>
                                            @elseif($user->status === 'inactive')
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">Inactive</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">{{ ucfirst($user->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($user->password_expires_at)
                                                @if($user->password_expires_at->isPast())
                                                    <span
                                                        class="text-red-600 font-semibold">{{ $user->password_expires_at->format('M d, Y') }}
                                                        (Expired)</span>
                                                @elseif($user->password_expires_at->diffInDays(now()) <= 3)
                                                    <span
                                                        class="text-yellow-600 font-semibold">{{ $user->password_expires_at->format('M d, Y') }}
                                                        (Expiring Soon)</span>
                                                @else
                                                    {{ $user->password_expires_at->format('M d, Y') }}
                                                @endif
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            @if($user->failed_login_attempts > 0)
                                                <span
                                                    class="text-red-600 font-semibold">{{ $user->failed_login_attempts }}</span>
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-4 text-center text-gray-500">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>