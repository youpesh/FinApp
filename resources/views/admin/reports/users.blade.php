<x-app-layout>
    <x-slot name="title">User Report</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">User Report</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Full listing of all system users and their
                    current status.</p>
            </div>
            <a href="{{ route('admin.reports.expired-passwords') }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-red-600 dark:text-red-400 hover:underline">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z"
                        clip-rule="evenodd" />
                </svg>
                Expired Passwords Report →
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Username</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Password Expires</th>
                        <th class="px-6 py-3 text-center">Failed Attempts</th>
                        <th class="px-6 py-3">Last Login</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors {{ $user->trashed() ? 'opacity-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 text-xs font-semibold flex-shrink-0">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $user->full_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $user->username }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleColors = ['administrator' => 'purple', 'manager' => 'blue', 'accountant' => 'indigo'];
                                    $rc = $roleColors[$user->role] ?? 'gray';
                                @endphp
                                <span
                                    class="bg-{{ $rc }}-100 text-{{ $rc }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-{{ $rc }}-900 dark:text-{{ $rc }}-300 capitalize">{{ $user->role }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->trashed())
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Deleted</span>
                                @else
                                    @php
                                        $statusColors = ['active' => 'green', 'inactive' => 'gray', 'suspended' => 'red', 'pending' => 'yellow'];
                                        $sc = $statusColors[$user->status] ?? 'gray';
                                    @endphp
                                    <span
                                        class="bg-{{ $sc }}-100 text-{{ $sc }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-{{ $sc }}-900 dark:text-{{ $sc }}-300 capitalize">{{ $user->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($user->password_expires_at)
                                    @if($user->password_expires_at->isPast())
                                        <span class="text-red-600 dark:text-red-400 font-semibold">Expired
                                            {{ $user->password_expires_at->diffForHumans() }}</span>
                                    @elseif($user->password_expires_at->diffInDays() <= 7)
                                        <span
                                            class="text-yellow-600 dark:text-yellow-400 font-medium">{{ $user->password_expires_at->format('M d, Y') }}
                                            ⚠</span>
                                    @else
                                        <span
                                            class="text-gray-600 dark:text-gray-400">{{ $user->password_expires_at->format('M d, Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->failed_login_attempts > 0)
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ $user->failed_login_attempts }}</span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>