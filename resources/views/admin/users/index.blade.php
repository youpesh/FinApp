<x-app-layout>
    <x-slot name="title">User Management</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">User Management</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage all system users, roles, and statuses.
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add User
            </a>
        </div>
    </x-slot>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
            $counts = [
                'Total' => $users->total(),
                'Active' => \App\Models\User::where('status', 'active')->count(),
                'Suspended' => \App\Models\User::where('status', 'suspended')->count(),
                'Pending' => \App\Models\User::where('status', 'pending')->count(),
            ];
            $colors = ['Total' => 'blue', 'Active' => 'green', 'Suspended' => 'red', 'Pending' => 'yellow'];
        @endphp
        @foreach($counts as $label => $count)
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-4">
                <div class="p-2 rounded-lg bg-{{ $colors[$label] }}-50 dark:bg-{{ $colors[$label] }}-900/20">
                    <span
                        class="text-{{ $colors[$label] }}-600 dark:text-{{ $colors[$label] }}-400 text-lg font-bold">{{ $count }}</span>
                </div>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $label }}</span>
            </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        {{-- Toolbar --}}
        <div
            class="p-4 flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2">
                <select name="role" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Roles</option>
                    <option value="administrator" {{ request('role') === 'administrator' ? 'selected' : '' }}>
                        Administrator</option>
                    <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="accountant" {{ request('role') === 'accountant' ? 'selected' : '' }}>Accountant
                    </option>
                </select>
                <select name="status" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </form>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.users') }}"
                    class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 gap-1.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                    </svg>
                    Reports
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">User</th>
                        <th scope="col" class="px-6 py-3">Username</th>
                        <th scope="col" class="px-6 py-3">Role</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Password Expires</th>
                        <th scope="col" class="px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 text-sm font-semibold flex-shrink-0">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $user->full_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-700 dark:text-gray-300">{{ $user->username }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $roleColors = ['administrator' => 'purple', 'manager' => 'blue', 'accountant' => 'indigo'];
                                    $rc = $roleColors[$user->role] ?? 'gray';
                                @endphp
                                <span
                                    class="bg-{{ $rc }}-100 text-{{ $rc }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-{{ $rc }}-900 dark:text-{{ $rc }}-300 capitalize">{{ $user->role }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = ['active' => 'green', 'inactive' => 'gray', 'suspended' => 'red', 'pending' => 'yellow'];
                                    $sc = $statusColors[$user->status] ?? 'gray';
                                @endphp
                                <span
                                    class="bg-{{ $sc }}-100 text-{{ $sc }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-{{ $sc }}-900 dark:text-{{ $sc }}-300 capitalize flex items-center gap-1 w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $sc }}-500"></span>
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->password_expires_at)
                                    @if($user->password_expires_at->isPast())
                                        <span class="text-red-600 dark:text-red-400 font-medium text-xs">Expired
                                            {{ $user->password_expires_at->diffForHumans() }}</span>
                                    @elseif($user->password_expires_at->diffInDays() <= 7)
                                        <span
                                            class="text-yellow-600 dark:text-yellow-400 font-medium text-xs">{{ $user->password_expires_at->diffForHumans() }}</span>
                                    @else
                                        <span
                                            class="text-gray-500 text-xs">{{ $user->password_expires_at->format('M d, Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="font-medium text-primary-600 dark:text-primary-500 hover:underline">Edit</a>
                                    <a href="{{ route('admin.emails.create', $user) }}"
                                        class="font-medium text-gray-500 dark:text-gray-400 hover:underline">Email</a>
                                    <a href="{{ route('admin.emails.history', $user) }}"
                                        class="font-medium text-gray-400 dark:text-gray-500 hover:underline text-xs">History</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">No users found.
                            </td>
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