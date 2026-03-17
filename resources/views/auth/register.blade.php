<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>MockDasher - Create Your Account</title>
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
                        "primary": "#4F46E5",
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
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
    <div class="flex-1 flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-[480px] space-y-8">
            <div class="flex flex-col items-center text-center">
                <a href="{{ url('/') }}" class="flex items-center gap-2 mb-8 group cursor-pointer hover:opacity-90 transition-opacity">
                    <div class="size-10 bg-primary rounded-lg flex items-center justify-center text-white">
                        <span class="material-symbols-outlined text-2xl">neurology</span>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">MockDasher</h2>
                </a>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Create your account</h1>
                <p class="mt-2 text-slate-600 dark:text-slate-400">Join thousands of test-takers worldwide</p>
            </div>

            <div class="bg-white dark:bg-slate-900 p-8 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none {{ $errors->has('name') ? 'text-red-500' : '' }}" for="name">Full Name</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">person</span>
                            <input class="flex h-12 w-full rounded-lg border {{ $errors->has('name') ? 'border-red-500 focus-visible:ring-red-500' : 'border-slate-200 dark:border-slate-800 focus-visible:ring-primary' }} bg-transparent px-10 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe" type="text"/>
                        </div>
                        @error('name')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none {{ $errors->has('email') ? 'text-red-500' : '' }}" for="email">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">mail</span>
                            <input class="flex h-12 w-full rounded-lg border {{ $errors->has('email') ? 'border-red-500 focus-visible:ring-red-500' : 'border-slate-200 dark:border-slate-800 focus-visible:ring-primary' }} bg-transparent px-10 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@example.com" type="email"/>
                        </div>
                        @error('email')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Fields visually separated but logically grouped for registration -->
                    <div class="space-y-5" x-data="{ showPassword: false }">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none {{ $errors->has('password') ? 'text-red-500' : '' }}" for="password">Password</label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl z-10 pointer-events-none">lock</span>
                                <input class="flex h-12 w-full rounded-lg border {{ $errors->has('password') ? 'border-red-500 focus-visible:ring-red-500' : 'border-slate-200 dark:border-slate-800 focus-visible:ring-primary' }} bg-transparent px-10 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2" id="password" name="password" required autocomplete="new-password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" />
                                <button @click="showPassword = !showPassword" class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none" type="button" tabindex="-1">
                                    <span class="material-symbols-outlined text-[20px]" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none" for="password_confirmation">Confirm Password</label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl z-10 pointer-events-none">lock_reset</span>
                                <input class="flex h-12 w-full rounded-lg border border-slate-200 dark:border-slate-800 bg-transparent px-10 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 py-2">
                        <input class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer" id="terms" name="terms" type="checkbox" required />
                        <label class="text-sm text-slate-600 dark:text-slate-400 leading-none cursor-pointer" for="terms">
                            I agree to the <a class="text-primary hover:underline font-medium" href="#">Terms of Service</a> and <a class="text-primary hover:underline font-medium" href="#">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button class="w-full flex items-center justify-center h-12 px-4 py-2 bg-gradient-to-r from-primary to-[#6366f1] text-white text-sm font-semibold rounded-lg shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity active:scale-[0.98]" type="submit">
                        Get Started
                    </button>
                </form>

                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t border-slate-200 dark:border-slate-800"></span>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white dark:bg-slate-900 px-2 text-slate-500">Or continue with</span>
                    </div>
                </div>

                <button class="w-full flex items-center justify-center gap-3 h-12 px-4 py-2 border border-slate-200 dark:border-slate-800 rounded-lg text-sm font-medium bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <svg class="h-5 w-5" viewbox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                    </svg>
                    Sign up with Google
                </button>
            </div>

            <p class="text-center text-sm text-slate-600 dark:text-slate-400">
                Already have an account? 
                <a class="font-semibold text-primary hover:text-primary/80 transition-colors" href="{{ route('login') }}">Sign In</a>
            </p>
        </div>
    </div>

    <footer class="py-8 px-4 border-t border-slate-200 dark:border-slate-800 mt-auto">
        <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500 uppercase tracking-widest">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">copyright</span>
                2024 MockDasher Inc.
            </div>
            <div class="flex gap-6">
                <a class="hover:text-primary transition-colors" href="#">Privacy</a>
                <a class="hover:text-primary transition-colors" href="#">Terms</a>
                <a class="hover:text-primary transition-colors" href="#">Help</a>
            </div>
        </div>
    </footer>
</body>
</html>
