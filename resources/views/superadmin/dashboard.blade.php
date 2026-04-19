<x-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Welcome Header -->
            <div class="mb-10">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Super Admin Dashboard</h1>
                <p class="text-lg text-gray-600">Manage users, system access, and overall application administration</p>
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

            <!-- Key Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Total Users Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Users</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                        </div>
                        <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a3 3 0 003-3v-2a3 3 0 00-3-3H6a3 3 0 00-3 3v2a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Verified Users Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Verified Users</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['verified'] }}</p>
                        </div>
                        <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Approvals Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Pending Approval</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Admin Users Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Admin/Manager Users</p>
                            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['admins'] }}</p>
                        </div>
                        <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Pending Approvals Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pending User Approvals
                            </h2>
                        </div>

                        <div class="p-6">
                            @if ($pendingUsers->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($pendingUsers as $user)
                                        <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Registered: {{ $user->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="button"
                                                    class="open-approve-modal px-3 py-1 bg-green-500 text-white text-sm font-medium rounded hover:bg-green-600 transition"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}"
                                                    data-user-email="{{ $user->email }}"
                                                    data-user-registered="{{ $user->created_at->format('M d, Y H:i') }}"
                                                    data-user-group="{{ $user->user_group ?? 'General' }}"
                                                    data-user-role="{{ $user->is_superadmin ? 'superadmin' : ($user->is_manager ? 'manager' : ($user->is_resource_officer ? 'resource_officer' : 'general')) }}">
                                                    Approve
                                                </button>
                                                <form method="POST" action="{{ route('superadmin.user.reject', $user) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white text-sm font-medium rounded hover:bg-red-600 transition">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-6">
                                    <a href="{{ route('superadmin.user-management', ['status' => 'pending']) }}" class="inline-block px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-medium">
                                        View All Pending ({{ $stats['pending'] }})
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-gray-600 font-medium">All users have been approved</p>
                                    <p class="text-gray-500 text-sm">No pending approvals at this time</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Approval Modal -->
                <div id="approve-user-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4 py-6">
                    <div class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-900/5">
                        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">Approve User Account</h3>
                                <p class="text-sm text-slate-500">Review details and assign a role before approving.</p>
                            </div>
                            <button type="button" id="close-approve-modal" class="rounded-full bg-slate-100 p-2 text-slate-600 hover:bg-slate-200">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form id="approve-user-form" method="POST" action="" class="space-y-6 px-6 py-6">
                            @csrf
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Name</label>
                                    <p id="approve-user-name" class="mt-1 text-sm text-slate-900"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Email</label>
                                    <p id="approve-user-email" class="mt-1 text-sm text-slate-900"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Registered</label>
                                    <p id="approve-user-registered" class="mt-1 text-sm text-slate-900"></p>
                                </div>
                                <div>
                                    <label for="approve-user-group" class="block text-sm font-medium text-slate-700">User Group</label>
                                    <select id="approve-user-group" name="user_group" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                                        <option value="General">General</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="APP">APP</option>
                                        <option value="Mechanical">Mechanical</option>
                                        <option value="Electrical">Electrical</option>
                                        <option value="Operations">Operations</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <p class="text-sm font-medium text-slate-700">Assign Role</p>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm hover:border-slate-300">
                                        <input type="radio" name="role" value="general" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500" checked>
                                        <span>General User</span>
                                    </label>
                                    <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm hover:border-slate-300">
                                        <input type="radio" name="role" value="manager" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                        <span>Manager</span>
                                    </label>
                                    <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm hover:border-slate-300">
                                        <input type="radio" name="role" value="resource_officer" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                        <span>Resource Officer</span>
                                    </label>
                                    <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm hover:border-slate-300">
                                        <input type="radio" name="role" value="superadmin" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                        <span>Super Admin</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 justify-end border-t border-slate-200 pt-4">
                                <button type="button" id="cancel-approve-user" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    Cancel
                                </button>
                                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                    Confirm Approval
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const modal = document.getElementById('approve-user-modal');
                        const closeModalButtons = [
                            document.getElementById('close-approve-modal'),
                            document.getElementById('cancel-approve-user'),
                        ];
                        const form = document.getElementById('approve-user-form');
                        const nameField = document.getElementById('approve-user-name');
                        const emailField = document.getElementById('approve-user-email');
                        const registeredField = document.getElementById('approve-user-registered');
                        const groupSelect = document.getElementById('approve-user-group');
                        const roleRadios = Array.from(form.querySelectorAll('input[name="role"]'));
                        const actionRouteBase = '{{ url('superadmin/users') }}';

                        function openModal(user) {
                            form.action = `${actionRouteBase}/${user.id}/approve`;
                            nameField.textContent = user.name;
                            emailField.textContent = user.email;
                            registeredField.textContent = user.registered;
                            groupSelect.value = user.group ?? 'General';
                            roleRadios.forEach(radio => {
                                radio.checked = radio.value === (user.role || 'general');
                            });
                            modal.classList.remove('hidden');
                        }

                        function closeModal() {
                            modal.classList.add('hidden');
                        }

                        document.querySelectorAll('.open-approve-modal').forEach(button => {
                            button.addEventListener('click', () => {
                                openModal({
                                    id: button.dataset.userId,
                                    name: button.dataset.userName,
                                    email: button.dataset.userEmail,
                                    registered: button.dataset.userRegistered,
                                    group: button.dataset.userGroup,
                                    role: button.dataset.userRole,
                                });
                            });
                        });

                        closeModalButtons.forEach(button => {
                            button.addEventListener('click', closeModal);
                        });

                        modal.addEventListener('click', (event) => {
                            if (event.target === modal) {
                                closeModal();
                            }
                        });
                    });
                </script>

                <!-- Quick Actions Panel -->
                <div class="space-y-6">
                    <!-- Management Section -->
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
                            <h2 class="text-lg font-bold text-white">Quick Actions</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('superadmin.user-management') }}" class="block w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition font-medium text-center border border-blue-200">
                                👥 Manage All Users
                            </a>
                            <a href="{{ route('superadmin.user-management', ['status' => 'pending']) }}" class="block w-full px-4 py-3 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition font-medium text-center border border-yellow-200">
                                ⏳ Pending Approvals
                            </a>
                            <a href="{{ route('superadmin.user-management', ['status' => 'verified']) }}" class="block w-full px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition font-medium text-center border border-green-200">
                                ✓ Verified Users
                            </a>
                            <a href="{{ route('manager') }}" class="block w-full px-4 py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition font-medium text-center border border-purple-200">
                                📋 Manager Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                            <h2 class="text-lg font-bold text-white">System Status</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Application</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Running
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Authentication</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Active
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Verification System</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Active
                                </span>
                            </div>
                            <hr class="my-3">
                            <p class="text-xs text-gray-500">Last updated: {{ now()->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users Section -->
            @if ($recentUsers->count() > 0)
                <div class="mt-10 bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Recently Registered Users
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Registered</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($recentUsers as $user)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if ($user->is_verified)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Verified</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <a href="{{ route('superadmin.user-management') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            View all users →
                        </a>
                    </div>
                </div>
            @endif

            <!-- Footer Info -->
            <div class="mt-10 p-6 bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl border border-slate-200">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Super Admin Resources</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li>
                        <strong>User Verification System:</strong> New users cannot login until approved via this dashboard.
                    </li>
                    <li>
                        <strong>Pending Approvals:</strong> Users with "Pending Approval" status are waiting for your verification.
                    </li>
                    <li>
                        <strong>Account Deactivation:</strong> You can deactivate verified users to prevent them from logging in.
                    </li>
                    <li>
                        <strong>System Access:</strong> Super admins (those with is_manager flag) can access all management features.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-layout>
