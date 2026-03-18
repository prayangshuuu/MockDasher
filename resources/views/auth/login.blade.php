<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sign in to MockDasher</title>
    <!-- Alpine.js before interacting with components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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

</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex items-center justify-center p-6">
<div class="w-full max-w-[480px]">
    <!-- Logo Section -->
    <div class="flex flex-col items-center mb-8">
        <a href="{{ url('/') }}" class="flex flex-col items-center hover:opacity-90 transition-opacity">
            <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white mb-4 shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined text-3xl">bolt</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">MockDasher</h1>
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-[0_20px_50px_rgba(80,72,229,0.05),0_1px_3px_rgba(0,0,0,0.02)] border border-slate-100 dark:border-slate-800 p-8 md:p-10">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Sign in to MockDasher</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Enter your details below to continue</p>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Field -->
            <div class="space-y-2">
                <label for="email" class="text-sm font-semibold text-slate-700 dark:text-slate-300 ml-1">Email</label>
                <div class="relative">
                    <input id="email" name="email" value="{{ old('email') }}" type="email" required autofocus class="w-full h-12 px-4 rounded-lg border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500/20 focus:border-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-primary/20 focus:border-primary' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition-all duration-200" placeholder="name@company.com" />
                </div>
                @error('email')
                    <p class="text-xs text-red-500 mt-1 ml-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="space-y-2" x-data="{ showPassword: false }">
                <div class="flex justify-between items-center ml-1">
                    <label for="password" class="text-sm font-semibold text-slate-700 dark:text-slate-300">Password</label>
                    @if (Route::has('password.request'))
                        <a class="text-xs font-medium text-primary hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password" class="w-full h-12 px-4 rounded-lg border {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500/20 focus:border-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-primary/20 focus:border-primary' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition-all duration-200" placeholder="••••••••" />
                    <button @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" type="button">
                        <span class="material-symbols-outlined text-[20px]" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-500 mt-1 ml-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center gap-2 ml-1">
                <input id="remember" name="remember" class="rounded border-slate-300 text-primary focus:ring-primary" type="checkbox" {{ old('remember') ? 'checked' : '' }}/>
                <label for="remember" class="text-sm text-slate-600 dark:text-slate-400 cursor-pointer">Keep me signed in</label>
            </div>

            <!-- Sign In Button -->
            <button class="w-full h-12 bg-gradient-to-r from-primary to-[#6366f1] hover:opacity-90 text-white font-semibold rounded-lg shadow-lg shadow-primary/20 transition-all active:scale-[0.98] mt-2" type="submit">
                Sign In
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-100 dark:border-slate-800"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white dark:bg-slate-900 px-4 text-slate-400">Or continue with</span>
            </div>
        </div>

        <!-- Social Button -->
        <button class="w-full h-12 flex items-center justify-center gap-3 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200 group">
            <svg class="size-5" viewbox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"></path>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
            </svg>
            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Google</span>
        </button>
    </div>

    <!-- Footer -->
    <p class="text-center mt-8 text-sm text-slate-500 dark:text-slate-400">
        Don't have an account? 
        <a class="text-primary font-semibold hover:underline" href="{{ route('register') }}">Create one</a>
    </p>

    <!-- Legal Links -->
    <div class="flex justify-center gap-6 mt-12 opacity-50">
        <span class="text-xs text-slate-500 cursor-default" title="Coming soon">Privacy Policy</span>
        <span class="text-xs text-slate-500 cursor-default" title="Coming soon">Terms of Service</span>
    </div>
</div>

</body>
</html>
