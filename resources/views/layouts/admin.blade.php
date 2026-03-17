<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MockDasher Admin Panel')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#5048e5",
                        "background-light": "#f8fafc",
                        "background-dark": "#121121",
                        "surface": "#FFFFFF",
                        "border-subtle": "#E2E8F0"
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px"},
                },
            },
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased">

<div class="flex min-h-screen">
    <!-- Sidebar Navigation (Desktop) -->
    <aside class="hidden md:flex fixed inset-y-0 left-0 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 z-50 flex-col">
        <div class="p-6 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-primary to-indigo-400 flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-xl">bolt</span>
            </div>
            <div>
                <h1 class="font-bold text-lg leading-none tracking-tight">MockDasher</h1>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-semibold mt-1">Admin Console</p>
            </div>
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100' }}">
                <span class="material-symbols-outlined">dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('admin.tests.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.tests.*') ? 'bg-primary/10 text-primary' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100' }}">
                <span class="material-symbols-outlined">description</span>
                Tests
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100' }}">
                <span class="material-symbols-outlined">group</span>
                Users
            </a>
            <a href="{{ route('admin.results.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.results.*') ? 'bg-primary/10 text-primary' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100' }}">
                <span class="material-symbols-outlined">analytics</span>
                Results
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-red-600 font-medium transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                </button>
            </form>
        </nav>
        
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center font-bold text-primary shrink-0">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@mockdasher.com' }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Navigation (Mobile Only) -->
    <header class="md:hidden sticky top-0 z-30 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-border-subtle dark:border-slate-800 px-4 py-3 flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 hover:opacity-90 transition-opacity">
            <div class="size-8 rounded-lg bg-gradient-to-tr from-primary to-indigo-400 flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-lg">bolt</span>
            </div>
            <div>
                <h1 class="text-xs font-bold tracking-tight">MockDasher</h1>
            </div>
        </a>
        <div class="flex items-center gap-2">
            <button class="size-8 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-slate-600 dark:text-slate-400">notifications</span>
            </button>
            <div class="size-8 rounded-full bg-slate-200 overflow-hidden border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center text-primary font-bold text-xs">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 md:ml-64 min-h-screen flex flex-col pb-20 md:pb-0">
        <!-- Global Flash Messages -->
        <div class="px-8 mt-4 hidden md:block" id="desktop-flash">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-red-500">error</span>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- Content -->
        @yield('content')
    </main>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/90 dark:bg-background-dark/90 backdrop-blur-lg border-t border-border-subtle dark:border-slate-800 px-2 pb-6 pt-3 z-50">
        <div class="max-w-md mx-auto flex items-center justify-around">
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 group {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-slate-400 hover:text-primary transition-colors' }}">
                <span class="material-symbols-outlined text-2xl {{ request()->routeIs('admin.dashboard') ? 'font-variation-fill' : '' }}">home</span>
                <span class="text-[10px] font-bold">Home</span>
            </a>
            <a href="{{ route('admin.tests.index') }}" class="flex flex-col items-center gap-1 group {{ request()->routeIs('admin.tests.*') ? 'text-primary' : 'text-slate-400 hover:text-primary transition-colors' }}">
                <span class="material-symbols-outlined text-2xl {{ request()->routeIs('admin.tests.*') ? 'font-variation-fill' : '' }}">assignment</span>
                <span class="text-[10px] font-medium">Tests</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-1 group {{ request()->routeIs('admin.users.*') ? 'text-primary' : 'text-slate-400 hover:text-primary transition-colors' }}">
                <span class="material-symbols-outlined text-2xl {{ request()->routeIs('admin.users.*') ? 'font-variation-fill' : '' }}">group</span>
                <span class="text-[10px] font-medium">Users</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="flex flex-col items-center cursor-pointer">
                @csrf
                <button type="submit" class="flex flex-col items-center gap-1 group text-slate-400 hover:text-red-500 transition-colors">
                    <span class="material-symbols-outlined text-2xl">logout</span>
                    <span class="text-[10px] font-medium">Logout</span>
                </button>
            </form>
        </div>
    </nav>

</div>

@stack('scripts')
<script>
    setTimeout(function() {
        const flash = document.getElementById('desktop-flash');
        if (flash) {
            Array.from(flash.children).forEach(function(el) {
                el.style.transition = 'opacity 0.5s ease-out';
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 500);
            });
        }
    }, 4000);
</script>
</body>
</html>
