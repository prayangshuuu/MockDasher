<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MockDasher') }}</title>

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

        .font-display {
            font-family: 'Inter', sans-serif;
        }

        .glass-nav {
            background-color: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #eee;
        }

        .dark .glass-nav {
            background-color: rgba(15, 23, 42, 0.5);
            border-bottom: 1px solid rgb(30, 41, 59);
        }

        .hero-gradient {
            background: linear-gradient(to right, #4F46E5, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .dot-pattern {
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 1rem 1rem;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-10deg);
        }
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
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "xs": "0.375rem",
                        "sm": "0.5rem",
                        "base": "0.75rem",
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased h-screen flex flex-col overflow-hidden"
    x-data="{ mobileMenuOpen: false }">

    <!-- Navbar -->
    <nav class="glass-nav flex items-center justify-between px-4 md:px-8 h-20 shrink-0 z-50">
        <a href="/" class="flex items-center gap-3">
            <img src="/storage/asset/logo.png" alt="MockDasher Logo" class="h-9" />
            <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white uppercase">MockDasher</span>
        </a>

        <!-- Desktop Nav -->
        <div class="hidden md:flex items-center gap-8">
            <div class="flex items-center gap-3">
                @if(auth()->check() && auth()->user()->profile_photo_path)
                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-sm">
                @else
                    <div class="h-10 w-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 flex items-center justify-center text-sm font-bold text-primary shadow-sm">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif
                <a href="{{ route('profile.show') }}" class="text-sm font-bold text-slate-900 dark:text-white hover:text-primary dark:hover:text-primary transition-colors">{{ auth()->user()->name ?? 'Profile' }}</a>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                    class="bg-white hover:bg-slate-50 dark:bg-surface-dark dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-full text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300 flex items-center gap-2">
                    <img src="/storage/asset/icons/logout.svg" class="w-4 h-4 opacity-70" alt="Logout" />
                    Logout
                </button>
            </form>
        </div>

        <!-- Mobile Nav Toggle -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-slate-900 dark:text-white">
            <img :src="mobileMenuOpen ? '/storage/asset/icons/close-circle.svg' : '/storage/asset/icons/menu.svg'" class="w-8 h-8 opacity-70" alt="Menu" />
        </button>
    </nav>

    <!-- Mobile Nav Menu -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
        class="md:hidden bg-white/90 dark:bg-slate-900/90 backdrop-blur-lg border-b border-slate-200 dark:border-slate-800 p-6 absolute top-20 left-0 right-0 shadow-xl z-40">
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3 mb-4">
                @if(auth()->check() && auth()->user()->profile_photo_path)
                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                @else
                    <div class="h-10 w-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 flex items-center justify-center text-sm font-bold text-primary">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif
                <a href="{{ route('profile.show') }}" class="text-lg font-bold text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Profile' }}</a>
            </div>
            <a class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined text-[20px]">dashboard</span> Dashboard
            </a>
            <a class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white" href="{{ route('user.history.index') }}">
                <span class="material-symbols-outlined text-[20px]">history</span> History
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="w-full bg-white hover:bg-slate-50 dark:bg-surface-dark dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 py-4 rounded-xl font-bold shadow-soft flex items-center justify-center gap-2">
                    <img src="/storage/asset/icons/logout.svg" class="w-5 h-5 opacity-70" alt="Logout" />
                    Logout
                </button>
            </form>
        </div>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-surface-light dark:bg-surface-dark border-r border-slate-200 dark:border-slate-800 flex-shrink-0 flex flex-col overflow-y-auto z-10 hidden md:flex">
            <nav class="flex-1 px-4 py-8 space-y-2">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'pattern' => 'dashboard'],
                        ['route' => 'user.history.index', 'icon' => 'history', 'label' => 'History & Results', 'pattern' => 'user.history.*'],
                        ['route' => 'profile.show', 'icon' => 'settings', 'label' => 'Settings', 'pattern' => 'profile.show'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 {{ request()->routeIs($item['pattern']) ? 'bg-primary text-white shadow-soft' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs($item['pattern']) ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-6 md:p-8 lg:p-12">
            <div class="max-w-7xl mx-auto w-full">
                <!-- Global Flash Messages -->
                @if(session('success'))
                    <div id="user-flash-success" class="mb-8 flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-6 py-4 rounded-xl shadow-soft font-medium">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div id="user-flash-error" class="mb-8 flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-6 py-4 rounded-xl shadow-soft font-medium">
                        <span class="material-symbols-outlined">error</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>

            <!-- Simple Footer -->
            <footer class="mt-16 pt-8 border-t border-slate-200 dark:border-slate-800 text-sm text-slate-500 dark:text-slate-400 flex justify-between items-center max-w-7xl mx-auto">
                <div>&copy; {{ date('Y') }} MockDasher Inc.</div>
                <div class="flex gap-6 font-medium">
                    <span class="cursor-not-allowed hover:text-slate-800 dark:hover:text-slate-300 transition-colors" title="Coming soon">Terms</span>
                    <span class="cursor-not-allowed hover:text-slate-800 dark:hover:text-slate-300 transition-colors" title="Coming soon">Privacy</span>
                </div>
            </footer>
        </main>
    </div>

    @stack('scripts')
    <script>
        setTimeout(function() {
            document.querySelectorAll('#user-flash-success, #user-flash-error').forEach(function(el) {
                el.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                setTimeout(function() { el.remove(); }, 500);
            });
        }, 4000);
    </script>
    <!-- Toast Notifications -->
    <x-toast-container />
</body>
</html>
