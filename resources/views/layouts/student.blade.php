<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MockDasher') }} — @yield('title', 'Dashboard')</title>

    {{-- Material Symbols (icon font) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Vite: Tailwind v4 CSS + App JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body
    x-data="{ sidebarOpen: false }"
    class="bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] font-sans antialiased min-h-screen"
>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MOBILE BACKDROP
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div
    x-cloak
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-[60] bg-black/30 backdrop-blur-sm lg:hidden"
></div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     SIDEBAR
     ═══════════════════════════════════════════════════════════════════════════ --}}
<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-[70] flex w-64 flex-col glass-sidebar transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen"
>
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-6 py-6">
        <div class="flex size-9 items-center justify-center rounded-[var(--radius-base)] bg-[var(--color-primary)] text-white">
            <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">bolt</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-[var(--color-text-primary)]">MockDasher</span>
        {{-- Mobile close --}}
        <button @click="sidebarOpen = false" class="ml-auto lg:hidden p-1 rounded-[var(--radius-xs)] text-[var(--color-text-secondary)] hover:bg-[var(--color-bg-secondary)] transition-colors">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-2">
        <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)]">Main</p>

        @php
            $navItems = [
                ['label' => 'Dashboard',    'icon' => 'dashboard',    'route' => 'dashboard',            'match' => 'dashboard'],
                ['label' => 'My Exams',     'icon' => 'quiz',         'route' => 'user.history.index',   'match' => 'user.history.*'],
                ['label' => 'Results',      'icon' => 'analytics',    'route' => 'user.history.index',   'match' => 'user.results.*'],
            ];
        @endphp

        @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               class="group flex items-center gap-3 rounded-[var(--radius-base)] px-3 py-2.5 text-sm font-medium transition-colors
                      {{ $isActive
                          ? 'bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)]'
                          : 'text-[var(--color-text-secondary)] hover:bg-[var(--color-bg-secondary)] hover:text-[var(--color-text-primary)]' }}">
                <div class="flex size-8 items-center justify-center rounded-[var(--radius-xs)]
                            {{ $isActive
                                ? 'bg-[var(--color-primary)] text-white'
                                : 'bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] group-hover:text-[var(--color-text-primary)]' }}">
                    <span class="material-symbols-outlined text-[18px]" @if($isActive) style="font-variation-settings:'FILL' 1" @endif>{{ $item['icon'] }}</span>
                </div>
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- Divider --}}
        <div class="my-3 border-t border-[var(--color-divider)]"></div>
        <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)]">Account</p>

        @php $settingsActive = request()->routeIs('profile.show'); @endphp
        <a href="{{ route('profile.show') }}"
           class="group flex items-center gap-3 rounded-[var(--radius-base)] px-3 py-2.5 text-sm font-medium transition-colors
                  {{ $settingsActive
                      ? 'bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)]'
                      : 'text-[var(--color-text-secondary)] hover:bg-[var(--color-bg-secondary)] hover:text-[var(--color-text-primary)]' }}">
            <div class="flex size-8 items-center justify-center rounded-[var(--radius-xs)]
                        {{ $settingsActive
                            ? 'bg-[var(--color-primary)] text-white'
                            : 'bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] group-hover:text-[var(--color-text-primary)]' }}">
                <span class="material-symbols-outlined text-[18px]" @if($settingsActive) style="font-variation-settings:'FILL' 1" @endif>settings</span>
            </div>
            Settings
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="group flex w-full items-center gap-3 rounded-[var(--radius-base)] px-3 py-2.5 text-sm font-medium text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-bg-secondary)] hover:text-[var(--color-error)]">
                <div class="flex size-8 items-center justify-center rounded-[var(--radius-xs)] bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] group-hover:text-[var(--color-error)]">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                </div>
                Logout
            </button>
        </form>
    </nav>

    {{-- Sidebar Footer: User Capsule --}}
    <div class="border-t border-[var(--color-divider)] px-4 py-4">
        <div class="flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-primary)_12%,transparent)] text-sm font-bold text-[var(--color-primary)] overflow-hidden">
                @if(auth()->user()->profile_photo_path)
                    <img class="h-full w-full object-cover" src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-[var(--color-text-primary)]">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-[var(--color-text-secondary)]">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MAIN CONTENT COLUMN
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="flex min-w-0 flex-1 flex-col">

    {{-- ─── Top Header Bar ─── --}}
    <header class="sticky top-0 z-40 flex items-center justify-between bg-[var(--color-bg-primary)] px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            {{-- Mobile hamburger --}}
            <button
                @click="sidebarOpen = true"
                class="rounded-[var(--radius-xs)] p-2 text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-bg-secondary)] lg:hidden"
            >
                <span class="material-symbols-outlined text-xl">menu</span>
            </button>

            {{-- Breadcrumbs / Page title --}}
            <div class="min-w-0">
                @hasSection('breadcrumbs')
                    @yield('breadcrumbs')
                @else
                    <h1 class="truncate text-lg font-bold tracking-tight text-[var(--color-text-primary)]">@yield('title', 'Dashboard')</h1>
                @endif
            </div>
        </div>

        {{-- Right side: profile dropdown --}}
        <div x-data="{ open: false }" class="relative flex items-center gap-3">
            <button
                @click="open = !open"
                @click.outside="open = false"
                class="flex items-center gap-2.5 rounded-[var(--radius-base)] px-2 py-1.5 transition-colors hover:bg-[var(--color-bg-secondary)]"
            >
                <div class="flex size-8 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-primary)_12%,transparent)] text-xs font-bold text-[var(--color-primary)] overflow-hidden">
                    @if(auth()->user()->profile_photo_path)
                        <img class="h-full w-full object-cover" src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="hidden text-left sm:block">
                    <p class="text-sm font-semibold leading-none text-[var(--color-text-primary)]">{{ auth()->user()->name }}</p>
                    <p class="mt-0.5 text-xs text-[var(--color-text-secondary)]">Student</p>
                </div>
                <span class="material-symbols-outlined text-[18px] text-[var(--color-text-secondary)] transition-transform" :class="open && 'rotate-180'">expand_more</span>
            </button>

            {{-- Dropdown --}}
            <div
                x-cloak
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="absolute right-0 top-full mt-2 w-52 rounded-[var(--radius-lg)] border border-[var(--color-divider)] bg-[var(--color-bg-primary)] py-1"
            >
                <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-bg-secondary)] hover:text-[var(--color-text-primary)]">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                    My Profile
                </a>
                <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-bg-secondary)] hover:text-[var(--color-text-primary)]">
                    <span class="material-symbols-outlined text-[18px]">settings</span>
                    Settings
                </a>
                <div class="my-1 border-t border-[var(--color-divider)]"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-[var(--color-error)] transition-colors hover:bg-[var(--color-bg-secondary)]">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- ─── Flash Messages ─── --}}
    <div class="px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mt-4 flex items-center gap-3 rounded-[var(--radius-base)] border border-[color-mix(in_srgb,var(--color-success)_25%,transparent)] bg-[color-mix(in_srgb,var(--color-success)_6%,transparent)] px-4 py-3 text-sm font-medium text-[var(--color-success)]">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 flex items-center gap-3 rounded-[var(--radius-base)] border border-[color-mix(in_srgb,var(--color-error)_25%,transparent)] bg-[color-mix(in_srgb,var(--color-error)_6%,transparent)] px-4 py-3 text-sm font-medium text-[var(--color-error)]">
                <span class="material-symbols-outlined text-[20px]">error</span>
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- ─── Page Content ─── --}}
    <div class="flex-1 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @yield('content')
    </div>

    {{-- ─── Footer ─── --}}
    <footer class="px-4 py-6 text-center text-xs text-[var(--color-text-secondary)] sm:px-6 lg:px-8">
        &copy; {{ date('Y') }} {{ config('app.name', 'MockDasher') }}. All rights reserved.
    </footer>
</div>

@stack('scripts')
</body>
</html>
