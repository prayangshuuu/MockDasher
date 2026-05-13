<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Exam') — {{ config('app.name', 'MockDasher') }}</title>

    {{-- Material Symbols --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Vite: Tailwind v4 CSS + App JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="h-full overflow-hidden bg-[var(--color-bg-secondary)] font-sans text-[var(--color-text-primary)] antialiased flex flex-col">

    {{-- ═══════════════════════════════════════════════════════════════════════
         TOP BAR — Flat, distraction-free
         ═══════════════════════════════════════════════════════════════════════ --}}
    <header class="z-50 flex h-14 shrink-0 items-center justify-between bg-[var(--color-bg-primary)] px-4 sm:px-6 lg:px-8">

        {{-- Left: Brand + Test Info --}}
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex size-8 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[var(--color-primary)] text-white">
                <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1">menu_book</span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-primary)] leading-none">@yield('test_type', 'Exam')</p>
                <h1 class="truncate text-sm font-bold text-[var(--color-text-primary)]">@yield('test_title', 'Mock Test')</h1>
            </div>
        </div>

        {{-- Center: Timer (injected by child views) --}}
        <div class="flex items-center justify-center">
            @yield('timer_area')
        </div>

        {{-- Right: Actions + Candidate --}}
        <div class="flex items-center gap-4">
            @yield('top_right_actions')

            <div class="hidden items-center gap-3 border-l border-[var(--color-divider)] pl-4 sm:flex">
                <div class="text-right">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] leading-none">Candidate</p>
                    <p class="text-xs font-bold text-[var(--color-text-primary)]">{{ auth()->user()->name }}</p>
                </div>
                <div class="flex size-8 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-primary)_12%,transparent)] text-xs font-bold text-[var(--color-primary)] overflow-hidden">
                    @if(auth()->user()->profile_photo_path)
                        <img class="h-full w-full object-cover" src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════════════════════════════════
         MAIN CONTENT — Full remaining height
         ═══════════════════════════════════════════════════════════════════════ --}}
    <main class="relative flex flex-1 flex-col overflow-hidden">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
