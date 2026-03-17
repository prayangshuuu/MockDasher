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
<body class="antialiased bg-[var(--color-bg)] text-[var(--color-text)] flex flex-col min-h-screen">

    <!-- Navigation -->
    <nav class="bg-[var(--color-bg)] border-b border-[var(--color-divider)] sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-[24px] h-[80px] flex items-center justify-between">
            <div class="flex items-center gap-[16px]">
                <a href="/" class="flex items-center gap-[16px] group">
                    <div class="w-[40px] h-[40px] bg-[var(--color-primary)] text-[var(--color-white)] rounded-[var(--radius-base)] flex items-center justify-center font-bold text-[20px] transition-transform group-hover:scale-105">
                        M
                    </div>
                    <span class="font-bold text-[24px] tracking-tight text-[var(--color-text)]">MockDasher</span>
                </a>
            </div>
            <div class="flex items-center space-x-[24px]">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-[14px] font-bold text-[var(--color-text)] opacity-70 hover:opacity-100 transition">Dashboard</a>
                        <a href="{{ route('profile.show') }}" class="text-[14px] font-bold text-[var(--color-text)] opacity-70 hover:opacity-100 transition">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[14px] font-bold text-[var(--color-error)] hover:opacity-80 transition">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-[14px] font-bold text-[var(--color-text)] hover:opacity-80 transition">Log in</a>
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
        <div class="relative bg-[var(--color-bg)] overflow-hidden border-b border-[var(--color-divider)]">
            <div class="max-w-7xl mx-auto px-[24px] py-[64px] sm:py-[128px] lg:flex lg:items-center lg:gap-x-[40px]">
                <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-xl lg:flex-shrink-0">
                    <div class="mb-[24px]">
                        <x-badge variant="neutral" class="text-[14px] px-[16px] py-[8px] bg-transparent"><i class="fas fa-rocket mr-[8px]"></i> Real Exam Simulation Engine</x-badge>
                    </div>
                    <h1 class="mt-[16px]">
                        Master the IELTS Exam with <span class="text-[var(--color-primary)]">Confidence</span>
                    </h1>
                    <p class="mt-[24px] text-[18px] opacity-70">
                        A clean, distraction-free environment mirroring the computer-delivered IELTS format. Complete rigorous mock tests, track your progress, and get your target band score.
                    </p>
                    <div class="mt-[40px] flex items-center gap-x-[24px]">
                        <x-button variant="primary" onclick="window.location.href='{{ route('register') }}'">
                            Start Practicing It's Free <i class="fas fa-arrow-right ml-[8px] text-[14px]"></i>
                        </x-button>
                        <a href="#features" class="text-[14px] font-bold leading-6 text-[var(--color-text)] group">
                            Explore Features <span aria-hidden="true" class="transition-transform inline-block group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </div>
                
                <div class="mx-auto mt-[64px] lg:mt-0 flex max-w-2xl justify-center sm:pl-[64px]">
                    <!-- Abstract Representation of UI -->
                    <div class="w-full max-w-lg bg-[var(--color-bg)] rounded-[var(--radius-base)] border border-[var(--color-divider)] overflow-hidden relative">
                        <div class="h-[40px] border-b border-[var(--color-divider)] flex items-center px-[16px] space-x-[8px]">
                            <div class="w-[12px] h-[12px] rounded-[var(--radius-base)] bg-[var(--color-error)]"></div>
                            <div class="w-[12px] h-[12px] rounded-[var(--radius-base)] bg-[#EAB308]"></div>
                            <div class="w-[12px] h-[12px] rounded-[var(--radius-base)] bg-[var(--color-success)]"></div>
                        </div>
                        <div class="p-[32px]">
                            <div class="flex items-center justify-between mb-[32px]">
                                <div class="h-[24px] border border-[var(--color-divider)] rounded-[var(--radius-base)] w-1/3"></div>
                                <div class="w-[96px] h-[32px] bg-[var(--color-primary)] rounded-[var(--radius-base)] opacity-20"></div>
                            </div>
                            <div class="space-y-[24px]">
                                <div class="flex items-start space-x-[16px]">
                                    <div class="w-[40px] h-[40px] border border-[var(--color-divider)] rounded-[var(--radius-base)] flex-shrink-0"></div>
                                    <div class="flex-1 space-y-[12px] mt-[4px]">
                                        <div class="h-[12px] border border-[var(--color-divider)] rounded w-3/4"></div>
                                        <div class="h-[12px] bg-[var(--color-divider)] rounded w-1/2 opacity-50"></div>
                                    </div>
                                </div>
                                <div class="h-[128px] border border-[var(--color-divider)] rounded-[var(--radius-base)] mt-[24px] p-[16px]">
                                    <div class="h-[16px] w-1/4 bg-[var(--color-divider)] rounded mb-[16px] opacity-30"></div>
                                    <div class="flex gap-[8px]">
                                        <div class="h-[32px] w-[32px] rounded-[var(--radius-base)] border border-[var(--color-primary)] text-center flex items-center justify-center"><i class="fas fa-check text-[var(--color-primary)] text-[10px]"></i></div>
                                        <div class="h-[32px] w-[32px] rounded-[var(--radius-base)] border border-[var(--color-divider)]"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-[128px] bg-[var(--color-bg)]">
            <div class="max-w-7xl mx-auto px-[24px]">
                <div class="text-center max-w-3xl mx-auto mb-[64px]">
                    <h2 class="text-[16px] font-bold text-[var(--color-primary)] tracking-widest uppercase mb-[16px]">MockDasher Engine</h2>
                    <h2 class="text-[48px] font-bold tracking-tight text-[var(--color-text)]">
                        Everything you need to score a Band 9
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[32px]">
                    
                    <x-card class="transition-transform duration-300">
                        <div class="w-[56px] h-[56px] rounded-[var(--radius-base)] bg-[var(--color-primary)] text-[var(--color-white)] flex items-center justify-center text-[24px] mb-[24px]">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <h3 class="text-[24px] font-bold text-[var(--color-text)] mb-[16px]">Real IELTS Interface</h3>
                        <p class="text-[16px] opacity-70 leading-relaxed">Experience a testing environment that closely mirrors the official computer-based IELTS exam, reducing test-day anxiety.</p>
                    </x-card>

                    <x-card class="transition-transform duration-300">
                        <div class="w-[56px] h-[56px] rounded-[var(--radius-base)] bg-[var(--color-text)] text-[var(--color-white)] flex items-center justify-center text-[24px] mb-[24px]">
                            <i class="fas fa-pen-nib"></i>
                        </div>
                        <h3 class="text-[24px] font-bold text-[var(--color-text)] mb-[16px]">Writing with Counter</h3>
                        <p class="text-[16px] opacity-70 leading-relaxed">Practice your task 1 and task 2 essays with an integrated live word counter and clean rich-text layout.</p>
                    </x-card>

                    <x-card class="transition-transform duration-300">
                        <div class="w-[56px] h-[56px] rounded-[var(--radius-base)] bg-[var(--color-success)] text-[var(--color-white)] flex items-center justify-center text-[24px] mb-[24px]">
                            <i class="fas fa-microphone-alt"></i>
                        </div>
                        <h3 class="text-[24px] font-bold text-[var(--color-text)] mb-[16px]">Speaking Practice</h3>
                        <p class="text-[16px] opacity-70 leading-relaxed">Listen to automated examiner prompts and seamlessly record your vocal responses directly within the browser.</p>
                    </x-card>

                    <x-card class="transition-transform duration-300">
                        <div class="w-[56px] h-[56px] rounded-[var(--radius-base)] border border-[var(--color-text)] text-[var(--color-text)] flex items-center justify-center text-[24px] mb-[24px]">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <h3 class="text-[24px] font-bold text-[var(--color-text)] mb-[16px]">Listening & Reading</h3>
                        <p class="text-[16px] opacity-70 leading-relaxed">Full multi-part listening audio tracks and comprehensive reading passages with dynamic inline question styles.</p>
                    </x-card>

                    <x-card class="transition-transform duration-300 lg:col-span-2 bg-[var(--color-primary)] text-[var(--color-white)] relative !border-none">
                        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-[32px]">
                            <div class="w-[80px] h-[80px] rounded-[var(--radius-base)] border border-[var(--color-divider)] opacity-80 flex items-center justify-center text-[36px] mb-[16px] sm:mb-0">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div class="text-center sm:text-left">
                                <h3 class="text-[28px] font-bold mb-[16px]">Track Your Progress</h3>
                                <p class="opacity-90 leading-relaxed text-[18px]">Detailed dashboard insights showing your test history, band score improvements, and module-specific weaknesses allowing you to target your study time efficiently.</p>
                            </div>
                        </div>
                    </x-card>

                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-[var(--color-bg)] border-t border-b border-[var(--color-divider)]">
            <div class="max-w-4xl mx-auto py-[128px] px-[24px] text-center">
                <h2 class="text-[48px] font-bold tracking-tight text-[var(--color-text)] mb-[24px]">
                    Ready to boost your score?
                </h2>
                <p class="text-[24px] opacity-70 mb-[40px]">
                    Join thousands of test-takers and experience the most authentic IELTS simulation platform.
                </p>
                <x-button variant="primary" onclick="window.location.href='{{ route('register') }}'">
                    Create Your Free Account Now
                </x-button>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-[var(--color-bg)] text-[var(--color-text)] border-t border-[var(--color-divider)] pt-[64px] pb-[32px]">
        <div class="max-w-7xl mx-auto px-[24px]">
            <div class="md:flex md:items-center md:justify-between border-b border-[var(--color-divider)] pb-[40px] mb-[32px]">
                <div class="flex justify-center md:justify-start mb-[24px] md:mb-0">
                    <a href="/" class="flex items-center gap-[16px]">
                        <div class="w-[32px] h-[32px] bg-[var(--color-primary)] text-[var(--color-white)] rounded-[var(--radius-base)] flex items-center justify-center font-bold text-[14px]">
                            M
                        </div>
                        <span class="font-bold text-[24px] tracking-tight text-[var(--color-text)]">MockDasher Engine</span>
                    </a>
                </div>
                <div class="flex justify-center space-x-[32px] text-[16px] font-bold opacity-70">
                    <a href="{{ route('login') }}" class="hover:opacity-100 transition">Login to Studio</a>
                    <a href="{{ route('register') }}" class="hover:opacity-100 transition">Create Account</a>
                </div>
            </div>
            
            <div class="text-center md:text-left opacity-70">
                <p class="text-[14px] font-bold">
                    &copy; {{ date('Y') }} MockDasher Engine. All rights reserved. Built with precision.
                </p>
                <p class="text-[14px] mt-[16px] max-w-3xl leading-relaxed">
                    IELTS is a registered trademark of University of Cambridge ESOL, the British Council, and IDP Education Australia. MockDasher is not affiliated, approved or endorsed by the University of Cambridge ESOL, the British Council, and IDP Education Australia.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
