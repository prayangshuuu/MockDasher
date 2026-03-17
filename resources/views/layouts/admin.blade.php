<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[var(--color-bg)] font-sans antialiased text-[var(--color-text)]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - MockDasher</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden flex flex-col">

    <!-- Top Navbar -->
    <nav class="bg-[var(--color-bg)] border-b border-[var(--color-divider)] h-[64px] flex items-center justify-between px-[24px] z-20 shrink-0 relative">
        <div class="flex items-center gap-[16px]">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-[16px]">
                <div class="w-[32px] h-[32px] rounded-[var(--radius-base)] shrink-0 bg-[var(--color-primary)] flex items-center justify-center text-[var(--color-white)] font-bold text-[18px]">M</div>
                <span class="font-bold text-[24px] tracking-tight hidden sm:block">MockDasher <span class="text-[14px] font-normal text-[var(--color-text)] opacity-70 ml-[8px]">CMS</span></span>
            </a>
        </div>
        
        <div class="flex items-center gap-[24px]">
            <div class="flex items-center gap-[16px]">
                <div class="h-[32px] w-[32px] rounded-[var(--radius-base)] bg-[var(--color-bg)] border border-[var(--color-divider)] flex items-center justify-center text-[14px] font-bold text-[var(--color-primary)]">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <span class="text-[14px] font-medium hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
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
        <aside class="w-[256px] bg-[var(--color-bg)] border-r border-[var(--color-divider)] flex-shrink-0 flex flex-col overflow-y-auto z-10 hidden md:flex">
            <nav class="flex-1 px-[16px] py-[24px] space-y-[8px]">
                @php
                    $navItems = [
                        ['route' => 'admin.dashboard', 'icon' => 'fa-home', 'label' => 'Dashboard', 'pattern' => 'admin.dashboard'],
                        ['route' => 'admin.tests.index', 'icon' => 'fa-file-alt', 'label' => 'Tests', 'pattern' => 'admin.tests.*'],
                        ['route' => 'admin.users.index', 'icon' => 'fa-users', 'label' => 'Users', 'pattern' => 'admin.users.*'],
                        ['route' => 'admin.results.index', 'icon' => 'fa-chart-bar', 'label' => 'Results', 'pattern' => 'admin.results.*'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="flex items-center px-[16px] py-[16px] rounded-[var(--radius-base)] text-[14px] font-medium transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-[var(--color-primary)] text-[var(--color-white)]' : 'text-[var(--color-text)] hover:opacity-80' }}">
                        <i class="fas {{ $item['icon'] }} w-[24px] mr-[16px] {{ request()->routeIs($item['pattern']) ? 'text-[var(--color-white)]' : 'text-[var(--color-text)] opacity-60' }}"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-[var(--color-bg)] p-[16px] md:p-[32px]">
            <div class="max-w-7xl mx-auto w-full">
                <!-- Header -->
                <div class="mb-[32px] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-[16px]">
                    <div>
                        <h1 class="text-[36px] font-bold tracking-tight text-[var(--color-text)]">@yield('header', 'Dashboard')</h1>
                        @if(View::hasSection('subheader'))
                            <p class="mt-[8px] text-[14px] text-[var(--color-text)] opacity-70">@yield('subheader')</p>
                        @endif
                    </div>
                    <div>
                        @yield('header_actions')
                    </div>
                </div>

                <!-- Global Flash Messages -->
                @if(session('success'))
                    <x-alert variant="success" class="mb-[24px] flex justify-between items-center" id="admin-flash-success">
                        <div class="flex items-center gap-[16px]">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </x-alert>
                @endif
                @if(session('error'))
                    <x-alert variant="error" class="mb-[24px] flex justify-between items-center" id="admin-flash-error">
                        <div class="flex items-center gap-[16px]">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </x-alert>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
    <script>
        setTimeout(function() {
            document.querySelectorAll('#admin-flash-success, #admin-flash-error').forEach(function(el) {
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
