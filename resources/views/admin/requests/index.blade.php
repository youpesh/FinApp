<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Access Requests') }}
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

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse($requests as $req)
                                <tr>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $req->first_name }} {{ $req->last_name }}</td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $req->email }}</td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium flex space-x-2">
                                        <!-- Approve Form -->
                                        <form method="POST" action="{{ route('admin.requests.approve', $req) }}" class="inline">
                                            @csrf
                                            <select name="role" class="text-xs border-gray-300 rounded-md mr-2" required>
                                                <option value="accountant">Accountant</option>
                                                <option value="manager">Manager</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                            <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                        </form>

                                        <span class="text-gray-300">|</span>

                                        <!-- Deny Form with prompt for reason -->
                                        <form method="POST" action="{{ route('admin.requests.deny', $req) }}" class="inline" onsubmit="return submitDenyForm(this);">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" class="rejection_reason_input">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Deny</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No pending requests found.</td>
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
                return false; // Cancel form submission
            }
            form.querySelector('.rejection_reason_input').value = reason;
            return true;
        }
    </script>
</x-app-layout>