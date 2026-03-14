<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MockDasher - Professional IELTS Simulator</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[var(--color-dwimik-bg)] text-[var(--color-dwimik-text)] selection:bg-[var(--color-dwimik-primary)] selection:text-white flex flex-col min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white border-b border-[var(--color-dwimik-divider)] sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-[var(--color-dwimik-primary)] text-white rounded-[var(--radius-dwimik)] flex items-center justify-center font-bold text-xl shadow-sm transition-transform group-hover:scale-105">
                        M
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-[var(--color-dwimik-text)]">MockDasher</span>
                </a>
            </div>
            <div class="flex items-center space-x-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-[var(--color-dwimik-primary)] transition">Dashboard</a>
                        <a href="{{ route('profile.show') }}" class="text-sm font-semibold text-gray-600 hover:text-[var(--color-dwimik-primary)] transition">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-[var(--color-dwimik-error)] hover:opacity-80 transition">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-[var(--color-dwimik-primary)] transition">Log in</a>
                        @if (Route::has('register'))
                            <x-button variant="primary" onclick="window.location.href='{{ route('register') }}'">Register Now</x-button>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        
        <!-- Hero Section -->
        <div class="relative bg-white overflow-hidden border-b border-[var(--color-dwimik-divider)]">
            <div class="max-w-7xl mx-auto px-6 py-24 sm:py-32 lg:flex lg:items-center lg:gap-x-10">
                <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-xl lg:flex-shrink-0">
                    <div class="mb-6">
                        <x-badge variant="primary" class="text-sm px-4 py-1.5"><i class="fas fa-rocket mr-2"></i> Real Exam Simulation Engine</x-badge>
                    </div>
                    <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-[var(--color-dwimik-text)] sm:text-6xl leading-tight">
                        Master the IELTS Exam with <span class="text-[var(--color-dwimik-primary)]">Confidence</span>
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-500">
                        A clean, distraction-free environment mirroring the computer-delivered IELTS format. Complete rigorous mock tests, track your progress, and get your target band score.
                    </p>
                    <div class="mt-10 flex items-center gap-x-6">
                        <x-button variant="primary" onclick="window.location.href='{{ route('register') }}'" class="px-8 py-4 text-lg shadow-lg shadow-[#3F37C9]/30">
                            Start Practicing It's Free <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </x-button>
                        <a href="#features" class="text-sm font-bold leading-6 text-gray-900 group">
                            Explore Features <span aria-hidden="true" class="transition-transform inline-block group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </div>
                
                <div class="mx-auto mt-16 lg:mt-0 flex max-w-2xl justify-center sm:pl-20">
                    <!-- Abstract Representation of UI -->
                    <div class="w-full max-w-lg bg-[var(--color-dwimik-bg)] rounded-2xl shadow-2xl border border-[var(--color-dwimik-divider)] overflow-hidden relative transform -rotate-2 hover:rotate-0 transition duration-500">
                        <div class="h-10 bg-white border-b border-[var(--color-dwimik-divider)] flex items-center px-4 space-x-2">
                            <div class="w-3 h-3 rounded-full bg-[var(--color-dwimik-error)] opacity-80"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400 opacity-80"></div>
                            <div class="w-3 h-3 rounded-full bg-[var(--color-dwimik-success)] opacity-80"></div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-8">
                                <div class="h-6 bg-white rounded-md border border-[var(--color-dwimik-divider)] w-1/3"></div>
                                <div class="w-24 h-8 bg-blue-100 rounded-full"></div>
                            </div>
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-white border border-[var(--color-dwimik-divider)] rounded-lg flex-shrink-0 shadow-sm"></div>
                                    <div class="flex-1 space-y-3 mt-1">
                                        <div class="h-3 bg-white border border-[var(--color-dwimik-divider)] rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                    </div>
                                </div>
                                <div class="h-32 bg-white shadow-sm border border-[var(--color-dwimik-divider)] rounded-xl mt-6 p-4">
                                    <div class="h-4 w-1/4 bg-gray-100 rounded mb-4"></div>
                                    <div class="flex gap-2">
                                        <div class="h-8 w-8 rounded-full bg-blue-50 border border-blue-200 text-center flex items-center justify-center"><i class="fas fa-check text-blue-500 text-[10px]"></i></div>
                                        <div class="h-8 w-8 rounded-full bg-gray-50 border border-gray-200"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-24 bg-[var(--color-dwimik-bg)]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-20">
                    <h2 class="text-base font-bold text-[var(--color-dwimik-primary)] tracking-widest uppercase mb-3">MockDasher Engine</h2>
                    <p class="text-4xl font-extrabold tracking-tight text-[var(--color-dwimik-text)] sm:text-5xl">
                        Everything you need to score a Band 9
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <x-card class="hover:-translate-y-1 transition-transform duration-300 shadow-sm border-gray-200">
                        <div class="w-14 h-14 rounded-xl bg-blue-50 text-[var(--color-dwimik-primary)] flex items-center justify-center text-2xl mb-6 shadow-sm border border-blue-100">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[var(--color-dwimik-text)] mb-3">Real IELTS Interface</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Experience a testing environment that closely mirrors the official computer-based IELTS exam, reducing test-day anxiety.</p>
                    </x-card>

                    <x-card class="hover:-translate-y-1 transition-transform duration-300 shadow-sm border-gray-200">
                        <div class="w-14 h-14 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl mb-6 shadow-sm border border-orange-100">
                            <i class="fas fa-pen-nib"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[var(--color-dwimik-text)] mb-3">Writing with Word Counter</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Practice your task 1 and task 2 essays with an integrated live word counter and clean rich-text layout.</p>
                    </x-card>

                    <x-card class="hover:-translate-y-1 transition-transform duration-300 shadow-sm border-gray-200">
                        <div class="w-14 h-14 rounded-xl bg-teal-50 text-[var(--color-dwimik-success)] flex items-center justify-center text-2xl mb-6 shadow-sm border border-teal-100">
                            <i class="fas fa-microphone-alt"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[var(--color-dwimik-text)] mb-3">Speaking Practice</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Listen to automated examiner prompts and seamlessly record your vocal responses directly within the browser.</p>
                    </x-card>

                    <x-card class="hover:-translate-y-1 transition-transform duration-300 shadow-sm border-gray-200">
                        <div class="w-14 h-14 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-6 shadow-sm border border-purple-100">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[var(--color-dwimik-text)] mb-3">Listening & Reading</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Full multi-part listening audio tracks and comprehensive reading passages with dynamic inline question styles.</p>
                    </x-card>

                    <x-card class="hover:-translate-y-1 transition-transform duration-300 shadow-sm border-gray-200 lg:col-span-2 bg-[#3F37C9] text-white overflow-hidden relative border-none">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-8">
                            <div class="w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center text-4xl mb-4 sm:mb-0 backdrop-blur-md border border-white/30">
                                <i class="fas fa-chart-pie text-white"></i>
                            </div>
                            <div class="text-center sm:text-left">
                                <h3 class="text-2xl font-bold mb-3 text-white">Track Your Progress</h3>
                                <p class="text-blue-100 leading-relaxed text-lg">Detailed dashboard insights showing your test history, band score improvements, and module-specific weaknesses allowing you to target your study time efficiently.</p>
                            </div>
                        </div>
                    </x-card>

                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-white border-t border-b border-[var(--color-dwimik-divider)]">
            <div class="max-w-4xl mx-auto py-24 px-6 text-center">
                <h2 class="text-4xl font-extrabold tracking-tight text-[var(--color-dwimik-text)] mb-6">
                    Ready to boost your score?
                </h2>
                <p class="text-xl text-gray-500 mb-10">
                    Join thousands of test-takers and experience the most authentic IELTS simulation platform.
                </p>
                <x-button variant="primary" onclick="window.location.href='{{ route('register') }}'" class="px-10 py-4 text-xl shadow-xl shadow-[#3F37C9]/20">
                    Create Your Free Account Now
                </x-button>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 text-[var(--color-dwimik-text)] border-t border-[var(--color-dwimik-divider)] pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="md:flex md:items-center md:justify-between border-b border-[var(--color-dwimik-divider)] pb-10 mb-8">
                <div class="flex justify-center md:justify-start mb-6 md:mb-0">
                    <a href="/" class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-[var(--color-dwimik-primary)] text-white rounded-[var(--radius-dwimik)] flex items-center justify-center font-bold text-sm shadow-sm">
                            M
                        </div>
                        <span class="font-bold text-xl tracking-tight text-[var(--color-dwimik-text)]">MockDasher Engine</span>
                    </a>
                </div>
                <div class="flex justify-center space-x-8 text-sm font-semibold text-gray-500">
                    <a href="{{ route('login') }}" class="hover:text-[var(--color-dwimik-primary)] transition">Login to Studio</a>
                    <a href="{{ route('register') }}" class="hover:text-[var(--color-dwimik-primary)] transition">Create Account</a>
                </div>
            </div>
            
            <div class="text-center md:text-left">
                <p class="text-sm font-semibold text-gray-500">
                    &copy; {{ date('Y') }} MockDasher Engine. All rights reserved. Built with precision.
                </p>
                <p class="text-xs text-gray-400 mt-3 max-w-3xl">
                    IELTS is a registered trademark of University of Cambridge ESOL, the British Council, and IDP Education Australia. MockDasher is not affiliated, approved or endorsed by the University of Cambridge ESOL, the British Council, and IDP Education Australia.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
