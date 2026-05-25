<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Confirm Password — MockDasher</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: { "primary": "#5048e5", "background-light": "#f8fafc" },
                    fontFamily: { "display": ["Inter", "sans-serif"] },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light min-h-screen flex items-center justify-center p-6 font-display text-slate-900">
<div class="w-full max-w-[480px] space-y-8">
    <div class="flex flex-col items-center text-center">
        <a href="{{ url('/') }}" class="flex items-center gap-2 mb-8 group cursor-pointer hover:opacity-90 transition-opacity">
            <img src="/storage/asset/logo.png" alt="MockDasher Logo" class="h-10" />
            <h2 class="text-2xl font-bold tracking-tight">MockDasher</h2>
        </a>
        <div class="size-16 bg-amber-50 rounded-full flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-3xl text-amber-600">shield_lock</span>
        </div>
        <h1 class="text-3xl font-bold tracking-tight">Confirm your password</h1>
        <p class="mt-2 text-slate-500 max-w-sm">This is a secure area. Please confirm your password before continuing.</p>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <form method="POST" action="{{ url('/user/confirm-password') }}" class="space-y-5">
            @csrf
            <div class="space-y-2">
                <label class="text-sm font-medium leading-none {{ $errors->has('password') ? 'text-red-500' : '' }}" for="password">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">lock</span>
                    <input class="flex h-12 w-full rounded-lg border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-200' }} bg-transparent px-10 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                           id="password" name="password" required autofocus type="password" placeholder="••••••••"/>
                </div>
                @error('password')
                    <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button class="w-full flex items-center justify-center h-12 px-4 py-2 bg-gradient-to-r from-primary to-[#6366f1] text-white text-sm font-semibold rounded-lg shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity active:scale-[0.98]" type="submit">
                Confirm Password
            </button>
        </form>
    </div>
</div>
</body>
</html>
