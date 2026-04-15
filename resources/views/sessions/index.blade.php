<x-layout>
    <div class="py-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Active Sessions</h1>
        <p class="text-slate-600 mb-8">Manage your active sessions across devices</p>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if ($sessions->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-slate-700">Device</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-700">IP Address</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-700">Last Activity</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-700">Login Time</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-700">Expires In</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-700">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessions as $session)
                                <tr class="border-b border-gray-200 hover:bg-slate-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if ($session->session_id === $currentSessionId)
                                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                                                <span class="font-medium text-slate-800">{{ $session->getDeviceName() }} <span class="text-xs text-green-600 ml-1">(Current)</span></span>
                                            @else
                                                <span class="text-slate-700">{{ $session->getDeviceName() }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $session->ip_address ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $session->last_activity?->diffForHumans() ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $session->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $minutesLeft = $session->minutesRemaining();
                                        @endphp
                                        @if ($minutesLeft && $minutesLeft > 0)
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $minutesLeft < 5 ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $minutesLeft }} min
                                            </span>
                                        @else
                                            <span class="text-slate-500 text-xs">Expired</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($session->session_id !== $currentSessionId)
                                            <form action="{{ route('sessions.revoke', $session->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition text-xs font-medium">
                                                    Logout
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 text-xs">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($sessions->count() > 1)
                <div class="mt-6">
                    <form action="{{ route('sessions.revoke-all-others') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-medium text-sm" onclick="return confirm('This will logout all other sessions. Continue?')">
                            Logout All Other Sessions
                        </button>
                    </form>
                </div>
            @endif

        @else
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <p class="text-slate-600 text-lg">No active sessions found</p>
            </div>
        @endif

        <!-- Session Info Card -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
                <h3 class="font-semibold text-blue-900 mb-3">Session Info</h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li><strong>Auto-logout:</strong> 30 minutes of inactivity</li>
                    <li><strong>Session timeout:</strong> Security feature to protect your account</li>
                    <li><strong>Multiple devices:</strong> You can login on multiple devices</li>
                </ul>
            </div>
            <div class="bg-purple-50 rounded-xl border border-purple-200 p-6">
                <h3 class="font-semibold text-purple-900 mb-3">Tips</h3>
                <ul class="space-y-2 text-sm text-purple-800">
                    <li>✓ Logout unused sessions regularly</li>
                    <li>✓ Review unfamiliar devices immediately</li>
                    <li>✓ Use "Logout All Other Sessions" after security concerns</li>
                </ul>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ auth()->user()->is_superadmin ? '/superadmin' : (auth()->user()->is_manager ? '/manager' : '/users') }}" class="inline-block px-4 py-2 rounded-lg bg-slate-200 text-slate-700 hover:bg-slate-300 transition font-medium">
                ← Back
            </a>
        </div>
    </div>
</x-layout>
