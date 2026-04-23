<x-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 px-4">
        <div class="w-full max-w-md">
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white">Create Account 🚀</h2>
                    <p class="text-gray-300 mt-2">Join our inventory management system</p>
                </div>

                <!-- Status Message -->
                @if (session('status'))
                    <div class="mb-4 p-3 bg-green-500/20 border border-green-400/30 text-green-300 rounded-lg text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-500/20 border border-red-400/30 text-red-300 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm text-gray-300 mb-1">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                               placeholder="Enter your full name">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm text-gray-300 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                               placeholder="you@example.com">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm text-gray-300 mb-1">Password</label>
                        <div class="flex items-center gap-2">
                            <input id="password" type="password" name="password" required
                                   class="flex-1 px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                   placeholder="Create a password">
                            <button type="button" onclick="togglePasswordVisibility('password')" class="px-4 py-3 text-blue-300 hover:text-blue-200 focus:outline-none transition font-semibold text-sm whitespace-nowrap bg-white">
                                <span id="toggle-password">Show</span>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm text-gray-300 mb-1">Confirm Password</label>
                        <div class="flex items-center gap-2">
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   class="flex-1 px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                   placeholder="Confirm your password">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="px-4 py-3 text-blue-300 hover:text-blue-200 focus:outline-none transition font-semibold text-sm whitespace-nowrap bg-white">
                                <span id="toggle-password_confirmation">Show</span>
                            </button>
                        </div>
                    </div>

                    <!-- Button -->
                    <button type="submit"
                            class="w-full py-3 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold hover:opacity-90 transition-all shadow-lg">
                        Create Account
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center">
                    <div class="flex-grow h-px bg-white/20"></div>
                    <span class="px-3 text-gray-400 text-sm">or</span>
                    <div class="flex-grow h-px bg-white/20"></div>
                </div>

                <!-- Login Link -->
                <p class="text-center text-sm text-gray-400">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium">
                        Sign in here
                    </a>
                </p>
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
