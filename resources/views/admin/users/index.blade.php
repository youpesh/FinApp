<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="flex space-x-2">
                            <select name="role"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager
                                </option>
                                <option value="accountant" {{ request('role') === 'accountant' ? 'selected' : '' }}>
                                    Accountant</option>
                            </select>

                            <select name="status"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
                            <a href="{{ route('admin.reports.users') }}"
                                class="inline-flex items-center px-3 py-2 bg-indigo-100 border border-transparent rounded-md text-xs text-indigo-700 font-semibold hover:bg-indigo-200">
                                📊 Reports
                            </a>
                            <a href="{{ route('admin.activity-logs.index') }}"
                                class="inline-flex items-center px-3 py-2 bg-yellow-100 border border-transparent rounded-md text-xs text-yellow-800 font-semibold hover:bg-yellow-200">
                                📋 Activity Log
                            </a>
                            <a href="{{ route('admin.users.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                + Add User
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Username</th>
                                    <th
                                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Name</th>
                                    <th
                                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ $user->username }}</td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ $user->full_name }}</td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 capitalize">
                                            {{ $user->role }}</td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 capitalize">
                                            {{ $user->status }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                            <a href="{{ route('admin.emails.create', $user) }}"
                                                class="text-gray-500 hover:text-gray-800">✉ Email</a>
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