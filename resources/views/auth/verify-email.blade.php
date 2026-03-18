<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Verify Email — MockDasher</title>
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
        <div class="size-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-3xl text-primary">mark_email_unread</span>
        </div>
        <h1 class="text-3xl font-bold tracking-tight">Check your inbox</h1>
        <p class="mt-2 text-slate-500 max-w-sm">We've sent a verification link to your email address. Please click the link in the email to verify your account.</p>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700 font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                A new verification link has been sent to your email address.
            </div>
        @endif

        <div class="space-y-4">
            <p class="text-sm text-slate-600 text-center">Didn't receive the email? Check your spam folder or request a new link below.</p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="w-full flex items-center justify-center h-12 px-4 py-2 bg-gradient-to-r from-primary to-[#6366f1] text-white text-sm font-semibold rounded-lg shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity active:scale-[0.98]" type="submit">
                    <span class="material-symbols-outlined text-lg mr-2">send</span>
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="text-sm text-slate-500 hover:text-primary transition-colors font-medium">
                    Sign out and use a different account
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
