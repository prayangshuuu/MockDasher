<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[var(--color-dwimik-bg)] font-sans antialiased text-[var(--color-dwimik-text)]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MockDasher') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden flex flex-col">

    <!-- Top Navbar -->
    <nav class="bg-white border-b border-[var(--color-dwimik-divider)] shadow-sm h-16 flex items-center justify-between px-6 z-20 shrink-0 relative">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 rounded shrink-0 bg-[var(--color-dwimik-primary)] flex items-center justify-center text-white font-bold text-lg">M</div>
                <span class="font-bold text-xl tracking-tight hidden sm:block">MockDasher</span>
            </a>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3">
                @if(auth()->check() && auth()->user()->profile_photo_path)
                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="Profile" class="h-8 w-8 rounded-full object-cover border border-[var(--color-dwimik-divider)]">
                @else
                    <div class="h-8 w-8 rounded-full bg-blue-50 border border-[var(--color-dwimik-divider)] flex items-center justify-center text-sm font-bold text-[var(--color-dwimik-primary)]">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif
                <a href="{{ route('profile.show') }}" class="text-sm font-medium hidden sm:block hover:text-[var(--color-dwimik-primary)] transition-colors">{{ auth()->user()->name ?? 'Profile' }}</a>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-[var(--color-dwimik-error)] hover:opacity-80 transition-opacity">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-[var(--color-dwimik-divider)] flex-shrink-0 flex flex-col overflow-y-auto z-10 hidden md:flex">
            <nav class="flex-1 px-4 py-6 space-y-2">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'icon' => 'fa-home', 'label' => 'Dashboard', 'pattern' => 'dashboard'],
                        ['route' => 'user.history.index', 'icon' => 'fa-history', 'label' => 'History & Results', 'pattern' => 'user.history.*'],
                        ['route' => 'profile.show', 'icon' => 'fa-cog', 'label' => 'Settings', 'pattern' => 'profile.show'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="flex items-center px-4 py-3 rounded-[var(--radius-dwimik)] text-sm font-medium transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-[var(--color-dwimik-primary)] text-white shadow-sm' : 'text-[var(--color-dwimik-text)] hover:bg-[#F9F8F6]' }}">
                        <i class="fas {{ $item['icon'] }} w-5 mr-3 {{ request()->routeIs($item['pattern']) ? 'text-white' : 'text-gray-400' }}"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-[var(--color-dwimik-bg)] p-4 md:p-8">
            <div class="max-w-7xl mx-auto w-full">
                <!-- Global Flash Messages -->
                @if(session('success'))
                    <x-alert variant="success" class="mb-6 flex justify-between items-center" id="user-flash-success">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </x-alert>
                @endif
                @if(session('error'))
                    <x-alert variant="error" class="mb-6 flex justify-between items-center" id="user-flash-error">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </x-alert>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
            
            <!-- Simple Footer -->
            <footer class="mt-12 py-6 border-t border-[var(--color-dwimik-divider)] text-xs text-gray-500 flex justify-between items-center max-w-7xl mx-auto px-4 md:px-0">
                <div>&copy; {{ date('Y') }} MockDasher.</div>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-[var(--color-dwimik-primary)] transition-colors">Terms</a>
                    <a href="#" class="hover:text-[var(--color-dwimik-primary)] transition-colors">Privacy</a>
                </div>
            </footer>
        </main>
    </div>

    @stack('scripts')
    <script>
        setTimeout(function() {
            document.querySelectorAll('#user-flash-success, #user-flash-error').forEach(function(el) {
                el.style.transition = 'opacity 0.5s ease-out';
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 500);
            });
        }, 4000);
    </script>
</body>
</html>
