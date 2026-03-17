<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MockDasher Admin')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#5048e5",
                        "background-light": "#F8FAFC",
                        "background-dark": "#121121",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }
        .premium-shadow { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025); }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(8px); border: 1px solid rgba(241, 245, 249, 1); }
        .dark .glass-card { background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(30, 41, 59, 1); }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex antialiased">
    
    <!-- Fixed Side Navigation -->
    <aside class="fixed left-0 top-0 h-screen w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col z-50">
        <div class="p-6 flex items-center gap-3">
            <div class="size-9 bg-primary rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-2xl">auto_awesome</span>
            </div>
            <div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white leading-none">MockDasher</h1>
                <p class="text-xs font-medium text-primary uppercase tracking-wider mt-1">Admin Panel</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1">
            <x-nav-link-admin href="{{ route('admin.dashboard') }}" icon="dashboard" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link-admin>
            <x-nav-link-admin href="{{ route('admin.tests.index') }}" icon="description" :active="request()->routeIs('admin.tests.*')">Tests</x-nav-link-admin>
            <x-nav-link-admin href="{{ route('admin.users.index') }}" icon="group" :active="request()->routeIs('admin.users.*')">Students</x-nav-link-admin>
            <x-nav-link-admin href="{{ route('admin.results.index') }}" icon="bar_chart" :active="request()->routeIs('admin.results.*')">Reports</x-nav-link-admin>
            
            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Account</p>
                <x-nav-link-admin href="#" icon="settings">Settings</x-nav-link-admin>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-red-500 rounded-lg transition-colors group">
                        <span class="material-symbols-outlined">logout</span>
                        <span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </nav>

        <div class="p-4">
            <div class="bg-primary/5 rounded-xl p-4 border border-primary/10">
                <p class="text-xs font-semibold text-primary mb-1">System Health</p>
                <p class="text-[11px] text-slate-500 mb-3">All services are running normally.</p>
                <div class="flex items-center gap-2">
                    <div class="size-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">v2.4.0 Online</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 min-h-screen flex flex-col relative">
        <!-- Header / Top Bar -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                @yield('breadcrumbs')
            </div>

            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input type="text" placeholder="Search resources..." class="pl-10 pr-4 py-1.5 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm w-64 focus:ring-2 focus:ring-primary/20">
                </div>
                
                <button class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                </button>

                <div class="h-8 w-px bg-slate-200 dark:bg-slate-800 mx-1"></div>

                <div class="flex items-center gap-3 pl-2">
                    <div class="text-right hidden md:block">
                        <p class="text-xs font-bold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-black">Administrator</p>
                    </div>
                    <div class="size-9 bg-primary/10 rounded-full border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center text-primary font-black">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content -->
        <div class="p-8 flex-1">
            @if(session('success'))
                <div class="max-w-7xl mx-auto mb-6 bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 animate-in fade-in slide-in-from-top-4 duration-300">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
