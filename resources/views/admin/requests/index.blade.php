<x-app-layout :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Access Requests'],
    ]">
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800  leading-tight">
            {{ __('Pending Access Requests') }}
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

                    <div class="overflow-x-auto rounded-lg border border-gray-200 ">
                        <table class="min-w-full divide-y divide-gray-200 ">
                            <thead class="bg-gray-50 ">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Name</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Requested At</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white  divide-y divide-gray-200 ">
                                @forelse($requests as $req)
                                    <tr class="hover:bg-gray-50  transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
                                            {{ $req->first_name }} {{ $req->last_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 ">
                                            {{ $req->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
                                            {{ $req->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                <!-- Approve Form -->
                                                <form method="POST" action="{{ route('admin.requests.approve', $req) }}"
                                                    class="inline flex items-center gap-2">
                                                    @csrf
                                                    <select name="role" class="text-xs border-gray-300    rounded-md"
                                                        required>
                                                        <option value="accountant">Accountant</option>
                                                        <option value="manager">Manager</option>
                                                        <option value="admin">Admin</option>
                                                    </select>
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 text-green-600  hover:text-green-900  font-medium transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>

                                                <span class="text-gray-300 ">|</span>

                                                <!-- Deny Form -->
                                                <form method="POST" action="{{ route('admin.requests.deny', $req) }}"
                                                    class="inline" onsubmit="return submitDenyForm(this);">
                                                    @csrf
                                                    <input type="hidden" name="rejection_reason"
                                                        class="rejection_reason_input">
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 text-red-600  hover:text-red-900  font-medium transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Deny
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 ">No
                                            pending requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitDenyForm(form) {
            const reason = prompt("Please enter the reason for rejection:");
            if (reason === null || reason.trim() === "") {
                return false;
            }
            form.querySelector('.rejection_reason_input').value = reason;
            return true;
        }
    </script>
</x-app-layout>