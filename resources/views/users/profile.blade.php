<x-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-10 bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl p-8 text-white shadow-lg">
                <div class="flex items-center gap-3 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h1 class="text-3xl font-bold">My Profile</h1>
                </div>
                <p class="text-blue-100">Manage your account information and security settings</p>
            </div>

            <!-- Success Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-700 rounded-lg flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Success!</p>
                        <p class="text-sm mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-semibold mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Profile Information Section -->
            <div class="bg-white rounded-2xl shadow-md p-8 mb-8 border border-blue-100">
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-1 h-6 bg-blue-600 rounded"></div>
                        <h2 class="text-xl font-bold text-gray-800">Profile Information</h2>
                    </div>
                    
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                class="w-full px-4 py-3 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white" 
                                placeholder="Enter your full name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white" 
                                placeholder="Enter your email address">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- User Group Display -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">User Group</label>
                            <div class="w-full px-4 py-3 border border-blue-300 rounded-lg bg-blue-50 text-gray-700 font-medium">
                                @php
                                    $groupDisplayNames = [
                                        'APP' => 'APP',
                                        'Engineering' => 'Engineering (Engg/INS)',
                                        'Mechanical' => 'Mechanical (ENGG/MEC)',
                                        'Operations' => 'Operations (OPTNS)',
                                    ];
                                    $displayName = $groupDisplayNames[$user->user_group] ?? ($user->user_group ?? 'Not assigned');
                                @endphp
                                {{ $displayName }}
                            </div>
                        </div>

                        <!-- User Roles -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Roles</label>
                            <div class="flex flex-wrap gap-2">
                                @if ($user->is_superadmin)
                                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full bg-blue-600 text-white">Super Admin</span>
                                @endif
                                @if ($user->is_manager)
                                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full bg-blue-500 text-white">Manager</span>
                                @endif
                                @if ($user->is_resource_officer)
                                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full bg-blue-400 text-white">Resource Officer</span>
                                @endif
                                @if (!$user->is_superadmin && !$user->is_manager && !$user->is_resource_officer)
                                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full bg-gray-300 text-gray-800">Regular User</span>
                                @endif
                            </div>
                        </div>

                        <!-- Update Button -->
                        <div class="pt-6 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 px-8 py-3 text-sm font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="bg-white rounded-2xl shadow-md p-8 border border-blue-100">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-1 h-6 bg-blue-600 rounded"></div>
                    <h2 class="text-xl font-bold text-gray-800">Change Password</h2>
                </div>
                
                <form action="{{ route('profile.change-password') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                        <div class="flex items-center gap-2">
                            <input type="password" id="current_password" name="current_password" 
                                class="flex-1 px-4 py-3 border @error('current_password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white" 
                                placeholder="Enter your current password">
                            <button type="button" onclick="togglePasswordVisibility('current_password')" class="px-4 py-3 text-blue-600 hover:text-blue-800 focus:outline-none transition font-semibold text-sm whitespace-nowrap">
                                <span id="toggle-current_password">Show</span>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <div class="flex items-center gap-2">
                            <input type="password" id="password" name="password" 
                                class="flex-1 px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white" 
                                placeholder="Enter your new password">
                            <button type="button" onclick="togglePasswordVisibility('password')" class="px-4 py-3 text-blue-600 hover:text-blue-800 focus:outline-none transition font-semibold text-sm whitespace-nowrap">
                                <span id="toggle-password">Show</span>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-600 bg-blue-50 rounded p-3 border border-blue-200">
                            <span class="font-semibold text-blue-900">Password Requirements:</span> Minimum 8 characters with uppercase, lowercase, numbers and special characters.
                        </p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                        <div class="flex items-center gap-2">
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                class="flex-1 px-4 py-3 border @error('password_confirmation') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white" 
                                placeholder="Confirm your new password">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="px-4 py-3 text-blue-600 hover:text-blue-800 focus:outline-none transition font-semibold text-sm whitespace-nowrap">
                                <span id="toggle-password_confirmation">Show</span>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Change Password Button -->
                    <div class="pt-6 border-t border-gray-200">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 px-8 py-3 text-sm font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Change Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Back Link -->
            <div class="mt-10">
                <a href="{{ route('users') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold transition gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            const toggleButton = document.getElementById('toggle-' + fieldId);
            
            if (input.type === 'password') {
                input.type = 'text';
                toggleButton.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggleButton.textContent = 'Show';
            }
        }
    </script>
</x-layout>
