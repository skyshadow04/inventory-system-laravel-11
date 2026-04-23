<!DOCTYPE html>
<html lang="en">

    <?php 
    
        $pathCSS = '<link href="https://inventory-system-app.infinityfreeapp.com/public/build/assets/app-mUIWfBr5.css" rel="stylesheet">';
        $pathJS = '<script src="https://inventory-system-app.infinityfreeapp.com/public/build/assets/app-BbzB21r_.js" defer></script>';
    
    ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <?php 
        // <link href="https://inventory-system-app.infinityfreeapp.com/public/build/assets/app-mUIWfBr5.css" rel="stylesheet">
		// <script src="https://inventory-system-app.infinityfreeapp.com/public/build/assets/app-BbzB21r_.js" defer></script>
        // Production assets from Vite build

        // Dev assets from Vite dev server

        //@vite(['resources/css/app.css', 'resources/js/app.js'])
        
        ?>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen">
        @php
            $sessionActive = false;
            if (Auth::check()) {
                $sessionActive = \App\Models\UserSession::where('user_id', auth()->id())
                    ->where('session_id', session()->getId())
                    ->where('expires_at', '>', now())
                    ->exists();
            }
        @endphp


        <div class="min-h-screen flex flex-col">
            <header class="bg-white/80 backdrop-blur border-b border-gray-200 shadow-sm sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between text-center">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white font-bold text-lg">I</div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">Inventory System</p>
                            <p class="text-xs text-slate-500">
                                @if (auth()->check())
                                    @if ($sessionActive)
                                        @if (auth()->user()->is_superadmin)
                                            Super Admin dashboard
                                        @elseif (auth()->user()->is_manager)
                                            Manager dashboard
                                        @else
                                            User dashboard
                                        @endif
                                    @else
                                        Session inactive
                                    @endif
                                @else
                                    Guest view
                                @endif
                            </p>
                        </div>
                        @if ($sessionActive)
                            <a href="{{ route('profile.show') }}" class="px-3 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition font-medium">Profile</a>
                            <a href="{{ route('sessions.index') }}" class="px-3 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition font-medium">Sessions</a>
                            @if (auth()->user()->is_superadmin)
                                <a href="{{ route('superadmin.dashboard') }}" class="px-3 py-2 rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200 transition font-medium">Super Admin</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="inline ml-4">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-medium shadow-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            <main class="flex-1 py-10 px-4 sm:px-6 lg:px-8">
                <div class="max-w-5xl mx-auto">
                    <div class="bg-white/90 border border-gray-200 shadow-xl rounded-2xl p-4 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>

            <footer class="py-4 text-center text-sm text-slate-500">
                Built with Laravel • 2026
            </footer>
        </div>

        <script>
            const menuButton = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');

            if (menuButton && menu) {
                menuButton.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!menu.contains(event.target) && !menuButton.contains(event.target)) {
                        menu.classList.add('hidden');
                    }
                });
            }
        </script>
    </body>
</html>