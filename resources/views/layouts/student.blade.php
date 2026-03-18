<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MockDasher') }} - @yield('title', 'Student Dashboard')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#5048e5",
                        "accent": "#7C3AED",
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
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                        'layered': '0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -4px rgba(0, 0, 0, 0.04)',
                    }
                },
            },
        }
    </script>
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased">
    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-72 glass-sidebar hidden lg:flex flex-col sticky top-0 h-screen z-50">
            <div class="p-8 flex items-center gap-3">
                <div class="size-10 indigo-gradient rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/30">
                    <span class="material-symbols-outlined text-2xl">bolt</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600">MockDasher</h1>
            </div>
            
            <nav class="flex-1 px-4 py-4 space-y-1 mt-2">
                <x-nav-link-admin href="{{ route('dashboard') }}" icon="dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link-admin>
                <x-nav-link-admin href="{{ route('user.history.index') }}" icon="history" :active="request()->routeIs('user.history.*')">My Test History</x-nav-link-admin>
                
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                    <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Account</p>
                    <x-nav-link-admin href="{{ route('profile.show') }}" icon="settings" :active="request()->routeIs('profile.show')">Settings</x-nav-link-admin>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-red-500 rounded-lg transition-colors group">
                            <span class="material-symbols-outlined">logout</span>
                            <span class="text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </nav>

            <div class="p-4 mx-4 mb-8 indigo-gradient rounded-2xl text-white shadow-xl shadow-primary/20">
                <p class="text-xs font-medium opacity-80 mb-1 uppercase tracking-wider">Premium Plan</p>
                <p class="text-sm font-bold mb-3 leading-tight">Unlock AI Speaking Feedback</p>
                <button class="w-full py-2 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-lg text-xs font-bold transition-all">
                    Upgrade Now
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Top Header -->
            <header class="h-20 bg-white/60 backdrop-blur-md border-b border-slate-200 px-8 flex items-center justify-between sticky top-0 z-40">
                <div class="flex-1 max-w-md">
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                        <input class="w-full pl-10 pr-4 py-2 bg-slate-100/50 border-transparent focus:border-primary/30 focus:bg-white focus:ring-4 focus:ring-primary/5 rounded-xl text-sm transition-all" placeholder="Search tests, modules..." type="text"/>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <button class="size-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-500 relative transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2.5 right-2.5 size-2 bg-red-500 border-2 border-white rounded-full"></span>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                    <div class="flex items-center gap-3 pl-2">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold leading-none">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-500 font-medium mt-1">ID: {{ auth()->user()->id }}-STUD</p>
                        </div>
                        <div class="size-10 rounded-xl bg-slate-200 overflow-hidden ring-2 ring-slate-100">
                            @if(auth()->user()->profile_photo_path)
                                <img class="w-full h-full object-cover" src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary text-white font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-8 max-w-7xl mx-auto w-full">
                @if(session('success'))
                    <div class="mb-8 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-8 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 flex items-center gap-3">
                        <span class="material-symbols-outlined">error</span>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="mt-auto py-8 px-8 text-center text-slate-400 text-xs font-medium">
                © {{ date('Y') }} MockDasher IELTS Simulation. Developed for premium academic excellence.
            </footer>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
