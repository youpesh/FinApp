<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expired Passwords Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-red-700">⚠ Users with Expired Passwords</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                These users must reset their passwords before their next login.
                            </p>
                        </div>
                        <a href="{{ route('admin.reports.users') }}"
                            class="text-indigo-600 hover:text-indigo-900 text-sm">
                            ← Back to User Report
                        </a>
                    </div>

                    @if($users->isEmpty())
                        <div class="text-center py-12 text-green-700 bg-green-50 rounded-lg">
                            ✅ No users have expired passwords.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr class="bg-red-50">
                                        <th
                                            class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                            Username</th>
                                        <th
                                            class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                            Full Name</th>
                                        <th
                                            class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                            Email</th>
                                        <th
                                            class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                            Role</th>
                                        <th
                                            class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th
                                            class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                            Password Expired</th>
                                        <th
                                            class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                            Days Overdue</th>
                                        <th
                                            class="px-4 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr class="bg-red-50">
                                            <td class="px-4 py-3 text-sm font-mono">{{ $user->username }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $user->full_name }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $user->email }}</td>
                                            <td class="px-4 py-3 text-sm capitalize">{{ $user->role }}</td>
                                            <td class="px-4 py-3 text-sm capitalize">
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs
                                                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $user->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-red-600 font-semibold">
                                                {{ $user->password_expires_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-red-700 font-bold">
                                                {{ abs($user->password_expires_at->diffInDays(now())) }} days
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <a href="{{ route('admin.emails.create', $user) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-2">Email</a>
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="text-gray-600 hover:text-gray-900">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-4 text-sm text-gray-500">Total: {{ $users->count() }} user(s) with expired passwords.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>