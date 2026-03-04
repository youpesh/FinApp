<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800  leading-tight">
            {{ __('Expired Passwords Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-red-700  flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Users with Expired Passwords
                            </h3>
                            <p class="text-sm text-gray-500  mt-1">
                                These users must reset their passwords before their next login.
                            </p>
                        </div>
                        <a href="{{ route('admin.reports.users') }}"
                            class="inline-flex items-center gap-1 text-indigo-600  hover:text-indigo-900  text-sm transition">
                            ← Back to User Report
                        </a>
                    </div>

                    @if($users->isEmpty())
                        <div class="text-center py-12 text-green-700  bg-green-50  rounded-lg">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            No users have expired passwords.
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200 ">
                            <table class="min-w-full divide-y divide-gray-200 ">
                                <thead class="bg-red-50 ">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Username</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Full Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Role</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Password Expired</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Days Overdue</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white  divide-y divide-gray-200 ">
                                    @foreach($users as $user)
                                        <tr class="bg-red-50/50  hover:bg-red-50  transition-colors">
                                            <td class="px-4 py-3 text-sm font-mono text-gray-700 ">{{ $user->username }}</td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900 ">{{ $user->full_name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600 ">{{ $user->email }}</td>
                                            <td class="px-4 py-3 text-sm capitalize text-gray-700 ">{{ $user->role }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @php $sc = $user->status === 'active' ? 'green' : 'yellow'; @endphp
                                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $sc }}-100 text-{{ $sc }}-800 {{ $sc }}-900/40 {{ $sc }}-300 capitalize">
                                                    {{ $user->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-red-600  font-semibold">
                                                {{ $user->password_expires_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-red-700  font-bold">
                                                {{ abs($user->password_expires_at->diffInDays(now())) }} days
                                            </td>
                                            <td class="px-4 py-3 text-sm flex items-center gap-3">
                                                <a href="{{ route('admin.emails.create', $user) }}"
                                                    class="inline-flex items-center gap-1 text-indigo-600  hover:text-indigo-900  transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    Email
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="inline-flex items-center gap-1 text-gray-500  hover:text-gray-800  transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-4 text-sm text-gray-500 ">Total: {{ $users->count() }} user(s) with expired passwords.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>