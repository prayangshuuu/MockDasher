<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MockDasher') }} — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        :root {
            --shadow-soft: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-premium: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07);
            --shadow-lift: 0 25px 50px -12px rgb(0 0 0 / 0.15);
        }
        .font-display { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4F46E5",
                        "primary-hover": "#4338CA",
                        "background-light": "#F8FAFC",
                        "background-dark": "#0F172A",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1E293B",
                        "error": "#EF4444",
                        "success": "#10B981",
                    },
                    fontFamily: { "display": ["Inter", "sans-serif"] },
                    borderRadius: {
                        "xs": "0.375rem", "sm": "0.5rem", "base": "0.75rem", "DEFAULT": "0.75rem",
                        "md": "0.75rem", "lg": "1rem", "xl": "1.5rem", "2xl": "2rem",
                        "3xl": "2.5rem", "full": "9999px"
                    },
                    boxShadow: {
                        'soft': 'var(--shadow-soft)',
                        'premium': 'var(--shadow-premium)',
                        'lift': 'var(--shadow-lift)',
                    }
                },
            },
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body x-data="{ sidebarOpen: false }" class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased min-h-screen flex">

<div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-50 bg-black/30 backdrop-blur-sm lg:hidden"></div>

<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-[60] w-64 flex flex-col bg-surface-light dark:bg-surface-dark border-r border-slate-200 dark:border-slate-800 transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen"
>
    <div class="flex items-center gap-3 px-6 py-6 border-b border-slate-100 dark:border-slate-800/50">
        <a href="/" class="flex items-center gap-3">
            <img src="/storage/asset/logo.png" alt="MockDasher Logo" class="h-8" />
            <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white uppercase">{{ config('app.name', 'MockDasher') }}</span>
        </a>
        <button @click="sidebarOpen = false" class="ml-auto lg:hidden p-1 rounded-xs text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <img src="/storage/asset/icons/close-circle.svg" class="w-5 h-5 opacity-70" alt="Close" />
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
        <p class="mb-3 px-2 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400 dark:text-slate-500">Menu</p>
        @php
            $navItems = [
                ['label' => 'Dashboard',    'icon' => '/storage/asset/icons/overview.svg',  'route' => 'dashboard',          'match' => 'dashboard'],
                ['label' => 'Mock Tests',   'icon' => '/storage/asset/icons/library.svg',   'route' => 'user.tests.index',   'match' => 'user.tests.*'],
                ['label' => 'Test History', 'icon' => '/storage/asset/icons/history.svg',   'route' => 'user.history.index', 'match' => 'user.history.*'],
                ['label' => 'Settings',     'icon' => '/storage/asset/icons/settings.svg',  'route' => 'profile.show',       'match' => 'profile.*'],
            ];
        @endphp
        @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-300 {{ $isActive ? 'bg-primary text-white shadow-soft' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                <img src="{{ $item['icon'] }}" class="w-5 h-5 transition-all duration-300 {{ $isActive ? 'invert brightness-0' : 'opacity-50 group-hover:opacity-100' }}" alt="{{ $item['label'] }}" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-slate-200 dark:border-slate-800 px-6 py-5 bg-slate-50/50 dark:bg-slate-900/20">
        <div class="flex items-center gap-3">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 overflow-hidden border border-indigo-100 dark:border-indigo-800 shadow-sm">
                <img class="h-full w-full object-cover" src="{{ auth()->user()->getAvatarUrl() }}" alt="{{ auth()->user()->name }}">
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-[11px] font-black uppercase tracking-wider text-primary mt-0.5">Student</p>
            </div>
        </div>
    </div>
</aside>

<div class="flex min-w-0 flex-1 flex-col">
    <header class="sticky top-0 z-40 flex items-center justify-between border-b border-slate-200 dark:border-slate-800 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-lg px-4 py-3 sm:px-6 lg:px-8 h-20">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="rounded-xs p-2 text-slate-500 dark:text-slate-400 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden">
                <img src="/storage/asset/icons/menu.svg" class="w-6 h-6 opacity-70" alt="Menu" />
            </button>
            <div class="min-w-0">
                @hasSection('breadcrumbs')
                    @yield('breadcrumbs')
                @else
                    <h1 class="truncate text-lg font-bold tracking-tight text-slate-900 dark:text-white">@yield('title', 'Dashboard')</h1>
                @endif
            </div>
        </div>

        <div x-data="{ open: false }" class="relative flex items-center gap-3">
            <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 rounded-full px-2 py-1.5 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700">
                <div class="flex size-9 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 overflow-hidden border border-indigo-100 dark:border-indigo-800 shadow-sm">
                    <img class="h-full w-full object-cover" src="{{ auth()->user()->getAvatarUrl() }}" alt="{{ auth()->user()->name }}">
                </div>
                <div class="hidden text-left sm:block">
                    <p class="text-sm font-bold leading-none text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Student</p>
                </div>
                <img src="/storage/asset/icons/expand-more.svg" class="w-5 h-5 transition-transform opacity-50 ml-1" :class="open && 'rotate-180'" alt="v" />
            </button>
            <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" class="absolute right-0 top-full mt-3 w-56 rounded-2xl border border-slate-200 dark:border-slate-700 bg-surface-light dark:bg-surface-dark p-2 shadow-lift">
                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white rounded-xl">
                    <img src="/storage/asset/icons/settings.svg" class="w-5 h-5 opacity-50" alt="Settings" />
                    Settings
                </a>
                <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-bold text-error transition-colors hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl">
                        <img src="/storage/asset/icons/logout.svg" class="w-5 h-5 opacity-70" alt="Logout" /> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mt-6 flex items-center gap-3 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 px-6 py-4 text-sm font-bold text-emerald-800 dark:text-emerald-300 shadow-soft">
                <img src="/storage/asset/icons/check-circle.svg" class="w-5 h-5 shrink-0 dark:invert" alt="✓" />
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mt-6 flex items-center gap-3 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-6 py-4 text-sm font-bold text-red-800 dark:text-red-300 shadow-soft">
                <img src="/storage/asset/icons/info.svg" class="w-5 h-5 shrink-0 dark:invert" alt="!" />
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="flex-1 px-4 py-8 sm:px-6 lg:px-12 overflow-y-auto">
        @yield('content')
    </div>

    <footer class="border-t border-slate-200 dark:border-slate-800 px-6 py-8 text-center sm:text-left flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-medium text-slate-500 dark:text-slate-400 mt-auto">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'MockDasher') }}. All rights reserved.</p>
        <div class="flex gap-4">
            <span class="cursor-not-allowed hover:text-slate-800 dark:hover:text-slate-300 transition-colors" title="Coming soon">Terms</span>
            <span class="cursor-not-allowed hover:text-slate-800 dark:hover:text-slate-300 transition-colors" title="Coming soon">Privacy</span>
        </div>
    </footer>
</div>

@stack('scripts')
</body>
</html>
