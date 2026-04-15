<x-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">User Management - Super Admin</h1>
                <p class="text-gray-600">Approve or reject new user registrations and manage user accounts</p>
            </div>

            <!-- Status Alerts -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Filter Tabs -->
            <div class="mb-6 flex gap-4 border-b border-gray-200">
                <a href="{{ route('superadmin.user-management', ['status' => 'all']) }}"
                   class="px-4 py-3 font-medium {{ $status === 'all' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                    All Users
                </a>
                <a href="{{ route('superadmin.user-management', ['status' => 'pending']) }}"
                   class="px-4 py-3 font-medium {{ $status === 'pending' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                    Pending Approval
                </a>
                <a href="{{ route('superadmin.user-management', ['status' => 'verified']) }}"
                   class="px-4 py-3 font-medium {{ $status === 'verified' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                    Verified
                </a>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Registered</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($user->is_superadmin)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Super Admin</span>
                                        @elseif ($user->is_manager)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Manager</span>
                                        @elseif ($user->is_resource_officer)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Resource Officer</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">User</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($user->is_verified)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Verified</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        @if (!$user->is_verified)
                                            <!-- Pending User - Show Approve Button -->
                                            <form method="POST" action="{{ route('superadmin.user.approve', $user) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-500 text-white text-xs font-medium rounded hover:bg-green-600 transition">
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('superadmin.user.reject', $user) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition">
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            <!-- Verified User - Show Deactivate Button -->
                                            <form method="POST" action="{{ route('superadmin.user.deactivate', $user) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-orange-500 text-white text-xs font-medium rounded hover:bg-orange-600 transition" onclick="return confirm('Deactivate this user?')">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @endif

                                        @if ($user->is_verified)
                                            <!-- Option to Reactivate if needed -->
                                            <form method="POST" action="{{ route('superadmin.user.reactivate', $user) }}" class="inline-block" style="display:none;" id="reactivate-{{ $user->id }}">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-blue-500 text-white text-xs font-medium rounded hover:bg-blue-600 transition">
                                                    Reactivate
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No users found matching the selected status.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $users->appends(request()->query())->links() }}
            </div>

            <!-- Statistics -->
            <div class="mt-10 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ count($users) > 0 ? 'See list' : '0' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm font-medium">Verified</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $users->where('is_verified', true)->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm font-medium">Pending Approval</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $users->where('is_verified', false)->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm font-medium">Super Admins</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $users->where('is_superadmin', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
