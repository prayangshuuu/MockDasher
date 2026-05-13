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
                        "xs": "0.375rem",
                        "sm": "0.5rem",
                        "DEFAULT": "0.75rem",
                        "md": "0.75rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "2xl": "2rem",
                        "3xl": "2.5rem",
                        "full": "9999px"
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
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }" class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden">
    <div class="flex min-h-screen">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[60] lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:sticky top-0 left-0 h-screen w-72 glass-sidebar flex flex-col z-[70] transition-transform duration-300 ease-in-out">
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


        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 md:px-8 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2 md:gap-4">
                    <!-- Mobile Menu Toggle -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-base transition-colors">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    @yield('breadcrumbs')
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative hidden sm:block">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                        <input type="text" placeholder="Search tests, modules..." class="pl-10 pr-4 py-1.5 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm w-64 focus:ring-2 focus:ring-primary/20">
                    </div>
                    
                    <button class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                    </button>

                    <div class="h-8 w-px bg-slate-200 dark:bg-slate-800 mx-1"></div>

                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 pl-2 hover:opacity-80 transition-opacity">
                        <div class="text-right hidden md:block">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-black">ID: {{ auth()->user()->id }}-{{ auth()->user()->isAdmin() ? 'ADMIN' : 'STUD' }}</p>
                        </div>
                        <div class="size-9 bg-primary/10 rounded-full border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center text-primary font-black overflow-hidden">
                            @if(auth()->user()->profile_photo_path)
                                <img class="w-full h-full object-cover" src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}"/>
                            @else
                                {{ substr(auth()->user()->name, 0, 1) }}
                            @endif
                        </div>
                    </a>
                </div>
            </header>

            <div class="p-4 md:p-8 max-w-7xl mx-auto w-full">
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
