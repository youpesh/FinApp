<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Users'],
    ]">
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800  leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600  bg-green-50  px-4 py-3 rounded-md">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600  bg-red-50  px-4 py-3 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex flex-wrap justify-between items-center mb-6 gap-2">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="flex space-x-2">
                            <select name="role"
                                class="border-gray-300    focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager
                                </option>
                                <option value="accountant" {{ request('role') === 'accountant' ? 'selected' : '' }}>
                                    Accountant</option>
                            </select>

                            <select name="status"
                                class="border-gray-300    focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>
                                    Suspended</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                                </option>
                            </select>

                            <x-primary-button type="submit">Filter</x-primary-button>
                        </form>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.users.create') }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-800  border border-transparent rounded-md font-semibold text-xs text-white  uppercase tracking-widest hover:bg-gray-700  transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add User
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200 ">
                        <table class="min-w-full divide-y divide-gray-200 ">
                            <thead class="bg-gray-50 ">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Username</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Name</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white  divide-y divide-gray-200 ">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50  transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-700 ">
                                            {{ $user->username }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900  font-medium">
                                            {{ $user->full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 ">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $roleClasses = [
                                                    'admin' => 'bg-purple-100 text-purple-800  ',
                                                    'manager' => 'bg-blue-100 text-blue-800  ',
                                                    'accountant' => 'bg-teal-100 text-teal-800  '
                                                ];
                                                $rc = $roleClasses[$user->role] ?? 'bg-gray-100 text-gray-800  ';
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $rc }} capitalize">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'active' => 'bg-green-100 text-green-800  ',
                                                    'inactive' => 'bg-gray-100 text-gray-800  ',
                                                    'suspended' => 'bg-red-100 text-red-800  ',
                                                    'pending' => 'bg-yellow-100 text-yellow-800  '
                                                ];
                                                $sc = $statusClasses[$user->status] ?? 'bg-gray-100 text-gray-800  ';
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }} capitalize">
                                                {{ $user->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-3">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="inline-flex items-center gap-1 text-indigo-600  hover:text-indigo-900  transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <a href="{{ route('admin.emails.create', $user) }}"
                                                class="inline-flex items-center gap-1 text-gray-500  hover:text-gray-800  transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                Email
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
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