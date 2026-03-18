<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[var(--color-bg-secondary)] font-sans antialiased text-[var(--color-text-primary)]">
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
    <nav class="bg-[var(--color-bg-primary)] border-b border-[var(--color-divider)] h-[64px] flex items-center justify-between px-[24px] z-20 shrink-0 relative">
        <div class="flex items-center gap-[16px]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-[16px]">
                <div class="w-[32px] h-[32px] rounded-[var(--radius-base)] shrink-0 bg-[var(--color-primary)] flex items-center justify-center text-[var(--color-white)] font-bold text-[18px]">M</div>
                <span class="font-bold text-[24px] tracking-tight hidden sm:block text-[var(--color-text-primary)]">MockDasher</span>
            </a>
        </div>
        
        <div class="flex items-center gap-[24px]">
            <div class="flex items-center gap-[16px]">
                @if(auth()->check() && auth()->user()->profile_photo_path)
                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="Profile" class="h-[32px] w-[32px] rounded-[var(--radius-base)] object-cover border border-[var(--color-divider)]">
                @else
                    <div class="h-[32px] w-[32px] rounded-[var(--radius-base)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] flex items-center justify-center text-[14px] font-bold text-[var(--color-primary)]">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif
                <a href="{{ route('profile.show') }}" class="text-[14px] font-medium hidden sm:block text-[var(--color-text-primary)] hover:opacity-80 transition-opacity">{{ auth()->user()->name ?? 'Profile' }}</a>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-[14px] font-medium text-[var(--color-error)] hover:opacity-80 transition-opacity">
                    <i class="fas fa-sign-out-alt mr-[8px]"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-[256px] bg-[var(--color-bg-secondary)] border-r border-[var(--color-divider)] flex-shrink-0 flex flex-col overflow-y-auto z-10 hidden md:flex">
            <nav class="flex-1 px-[16px] py-[24px] space-y-[8px]">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'icon' => 'fa-home', 'label' => 'Dashboard', 'pattern' => 'dashboard'],
                        ['route' => 'user.history.index', 'icon' => 'fa-history', 'label' => 'History & Results', 'pattern' => 'user.history.*'],
                        ['route' => 'profile.show', 'icon' => 'fa-cog', 'label' => 'Settings', 'pattern' => 'profile.show'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="flex items-center px-[16px] py-[8px] rounded-[var(--radius-base)] text-[14px] font-medium transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-[var(--color-primary)] text-[var(--color-white)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-black/5' }}">
                        <i class="fas {{ $item['icon'] }} w-[24px] mr-[8px] {{ request()->routeIs($item['pattern']) ? 'text-[var(--color-white)]' : 'text-[var(--color-text-secondary)]' }}"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-[var(--color-bg-secondary)] p-[16px] md:p-[32px]">
            <div class="max-w-7xl mx-auto w-full">
                <!-- Global Flash Messages -->
                @if(session('success'))
                    <x-alert variant="success" class="mb-[24px] flex justify-between items-center" id="user-flash-success">
                        <div class="flex items-center gap-[16px]">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </x-alert>
                @endif
                @if(session('error'))
                    <x-alert variant="error" class="mb-[24px] flex justify-between items-center" id="user-flash-error">
                        <div class="flex items-center gap-[16px]">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </x-alert>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
            
            <!-- Simple Footer -->
            <footer class="mt-[48px] py-[24px] border-t border-[var(--color-divider)] text-[14px] text-[var(--color-text-secondary)] flex justify-between items-center max-w-7xl mx-auto px-[16px] md:px-0">
                <div>&copy; {{ date('Y') }} MockDasher.</div>
                <div class="flex gap-[16px]">
                    <span class="cursor-default" title="Coming soon">Terms</span>
                    <span class="cursor-default" title="Coming soon">Privacy</span>
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
    <!-- Toast Notifications -->
    <x-toast-container />
</body>
</html>
