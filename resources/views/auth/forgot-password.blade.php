<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Forgot Password — MockDasher</title>
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
            <div class="size-10 bg-primary rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-2xl">bolt</span>
            </div>
            <h2 class="text-2xl font-bold tracking-tight">MockDasher</h2>
        </a>
        <h1 class="text-3xl font-bold tracking-tight">Forgot your password?</h1>
        <p class="mt-2 text-slate-500 max-w-sm">No worries! Enter the email address linked to your account and we'll send you a reset link.</p>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700 font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div class="space-y-2">
                <label class="text-sm font-medium leading-none {{ $errors->has('email') ? 'text-red-500' : '' }}" for="email">Email Address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">mail</span>
                    <input class="flex h-12 w-full rounded-lg border {{ $errors->has('email') ? 'border-red-500 focus-visible:ring-red-500' : 'border-slate-200 focus-visible:ring-primary' }} bg-transparent px-10 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                           id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="name@example.com" type="email"/>
                </div>
                @error('email')
                    <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button class="w-full flex items-center justify-center h-12 px-4 py-2 bg-gradient-to-r from-primary to-[#6366f1] text-white text-sm font-semibold rounded-lg shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity active:scale-[0.98]" type="submit">
                Send Reset Link
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-slate-500">
        Remember your password?
        <a class="font-semibold text-primary hover:text-primary/80 transition-colors" href="{{ route('login') }}">Back to Sign In</a>
    </p>
</div>
</body>
</html>
