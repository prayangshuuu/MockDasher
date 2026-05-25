<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Two-Factor Authentication — MockDasher</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<div class="w-full max-w-[480px] space-y-8" x-data="{ useRecovery: false }">
    <div class="flex flex-col items-center text-center">
        <a href="{{ url('/') }}" class="flex items-center gap-2 mb-8 group cursor-pointer hover:opacity-90 transition-opacity">
            <img src="/storage/asset/logo.png" alt="MockDasher Logo" class="h-10" />
            <h2 class="text-2xl font-bold tracking-tight">MockDasher</h2>
        </a>
        <div class="size-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-3xl text-primary">security</span>
        </div>
        <h1 class="text-3xl font-bold tracking-tight">Two-Factor Authentication</h1>
        <p class="mt-2 text-slate-500 max-w-sm" x-show="!useRecovery">Enter the 6-digit code from your authenticator app to verify your identity.</p>
        <p class="mt-2 text-slate-500 max-w-sm" x-show="useRecovery" x-cloak>Enter one of your emergency recovery codes to regain access to your account.</p>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <form method="POST" action="{{ url('/two-factor-challenge') }}" class="space-y-5">
            @csrf

            <div class="space-y-2" x-show="!useRecovery">
                <label class="text-sm font-medium leading-none" for="code">Authentication Code</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">pin</span>
                    <input class="flex h-12 w-full rounded-lg border border-slate-200 bg-transparent px-10 py-2 text-sm tracking-[0.3em] font-bold text-center ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                           id="code" name="code" autofocus autocomplete="one-time-code" inputmode="numeric" maxlength="6" placeholder="000000" type="text"/>
                </div>
            </div>

            <div class="space-y-2" x-show="useRecovery" x-cloak>
                <label class="text-sm font-medium leading-none" for="recovery_code">Recovery Code</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">key</span>
                    <input class="flex h-12 w-full rounded-lg border border-slate-200 bg-transparent px-10 py-2 text-sm font-mono ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                           id="recovery_code" name="recovery_code" autocomplete="one-time-code" placeholder="xxxx-xxxx" type="text"/>
                </div>
            </div>

            <button class="w-full flex items-center justify-center h-12 px-4 py-2 bg-gradient-to-r from-primary to-[#6366f1] text-white text-sm font-semibold rounded-lg shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity active:scale-[0.98]" type="submit">
                Verify
            </button>
        </form>

        <div class="mt-6 text-center">
            <button @click="useRecovery = !useRecovery" class="text-sm text-primary font-semibold hover:underline" type="button">
                <span x-show="!useRecovery">Use a recovery code instead</span>
                <span x-show="useRecovery" x-cloak>Use authentication code instead</span>
            </button>
        </div>
    </div>
</div>
</body>
</html>
