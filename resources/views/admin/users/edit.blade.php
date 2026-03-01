<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User: ') }} {{ $user->username }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Edit User Form --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">User Details</h3>
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- First Name -->
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $user->first_name)" required autofocus />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <!-- Last Name -->
                        <div class="mt-4">
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $user->last_name)" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="accountant" {{ old('role', $user->role) === 'accountant' ? 'selected' : '' }}>Accountant</option>
                                <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                onchange="toggleSuspensionDates(this.value)">
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="pending" {{ old('status', $user->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Suspension Dates (visible when status = suspended) -->
                        <div id="suspension-dates" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg {{ old('status', $user->status) === 'suspended' ? '' : 'hidden' }}">
                            <p class="text-sm font-medium text-red-700 mb-3">⚠ Suspension Period</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="suspension_start_date" :value="__('Start Date')" />
                                    <x-text-input id="suspension_start_date" class="block mt-1 w-full" type="date"
                                        name="suspension_start_date"
                                        :value="old('suspension_start_date', $user->suspension_start_date?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('suspension_start_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="suspension_end_date" :value="__('End Date (optional)')" />
                                    <x-text-input id="suspension_end_date" class="block mt-1 w-full" type="date"
                                        name="suspension_end_date"
                                        :value="old('suspension_end_date', $user->suspension_end_date?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('suspension_end_date')" class="mt-2" />
                                </div>
                            </div>
                            <p class="text-xs text-red-500 mt-2">Leave end date blank for indefinite suspension.</p>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>{{ __('Update User') }}</x-primary-button>
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Actions Panel --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.emails.create', $user) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            ✉ Send Email
                        </a>
                        <a href="{{ route('admin.emails.history', $user) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Email History
                        </a>
                    </div>
                </div>
            </div>

            {{-- Delete User --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">{{ __('Delete User') }}</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Once the user is deleted, all of its resources and data will be permanently deleted.') }}
                            </p>
                        </header>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">
                                {{ __('Delete User') }}
                            </x-danger-button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSuspensionDates(status) {
            const datesDiv = document.getElementById('suspension-dates');
            if (status === 'suspended') {
                datesDiv.classList.remove('hidden');
            } else {
                datesDiv.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>