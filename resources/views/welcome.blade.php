<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MockDasher') }} - IELTS Mock Tests</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 font-sans selection:bg-blue-500 selection:text-white flex flex-col min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="/" class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded flex items-center justify-center font-bold text-lg shadow-sm">
                                M
                            </div>
                            <span class="font-bold text-xl tracking-tight text-gray-900">MockDasher</span>
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Dashboard</a>
                            <a href="{{ route('profile.show') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Profile</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 transition">Log Out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        
        <!-- Hero Section -->
        <div class="relative bg-white overflow-hidden border-b border-gray-200">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32 pt-20 px-4 sm:px-6 lg:px-8">
                    <main class="mx-auto max-w-7xl">
                        <div class="sm:text-center lg:text-left">
                            <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-sm font-semibold tracking-wide mb-4 border border-blue-100 shadow-sm">
                                🚀 The Ultimate Exam Simulator
                            </span>
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block xl:inline">Free IELTS Mock Tests</span>
                                <span class="block text-blue-600">That Simulate The Real Exam</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Prepare for your IELTS Academic or General Training with our fully interactive, authentic mock exams. Practice Listening, Reading, Writing, and Speaking all in one unified dashboard.
                            </p>
                            <div class="mt-8 sm:mt-12 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10 transition">
                                        Start Practicing — It's Free
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="#features" class="w-full flex items-center justify-center px-8 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10 transition shadow-sm">
                                        Explore Features
                                    </a>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <!-- Decorative Visual (Hidden on mobile) -->
            <div class="hidden lg:block lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-gray-50 border-l border-gray-100 flex items-center justify-center">
                <div class="p-12 relative w-full h-full flex items-center justify-center">
                    <!-- Abstract Representation of UI -->
                    <div class="w-full max-w-lg bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden relative z-10 transform rotate-2 hover:rotate-0 transition duration-500">
                        <div class="h-8 bg-gray-100 border-b border-gray-200 flex items-center px-4 space-x-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="p-6">
                            <div class="h-4 bg-gray-200 rounded w-1/3 mb-6"></div>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex-shrink-0"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                    </div>
                                </div>
                                <div class="h-32 bg-gray-50 border border-gray-100 rounded-md"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Background Pattern Elements -->
                    <div class="absolute top-1/4 right-1/4 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                    <div class="absolute top-1/3 left-1/4 w-72 h-72 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">MockDasher Engine</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        Everything you need to score a Band 9
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                        A clean, distraction-free environment mirroring the computer-delivered IELTS format.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-6 border border-blue-100">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Real IELTS Interface</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Experience a testing environment that closely mirrors the official computer-based IELTS exam, reducing test-day anxiety.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-6 border border-amber-100">
                            <i class="fas fa-pen-nib"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Writing with Word Counter</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Practice your task 1 and task 2 essays with an integrated live word counter and clean rich-text layout.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xl mb-6 border border-rose-100">
                            <i class="fas fa-microphone-alt"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Speaking Practice Recording</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Listen to automated examiner prompts and seamlessly record your vocal responses directly within the browser.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-6 border border-emerald-100">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Listening & Reading Simulators</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Full multi-part listening audio tracks and comprehensive reading passages with dynamic inline question styles.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition lg:col-span-2">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center">
                            <div class="w-16 h-16 rounded-full bg-violet-50 text-violet-600 flex flex-shrink-0 items-center justify-center text-2xl mb-4 sm:mb-0 sm:mr-6 border border-violet-100">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Track Your Progress</h3>
                                <p class="text-gray-500 leading-relaxed">Detailed dashboard insights showing your test history, band score improvements, and module-specific weaknesses allowing you to target your study time efficiently.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-blue-600">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between text-center lg:text-left">
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl mb-8 lg:mb-0">
                    <span class="block">Ready to boost your score?</span>
                    <span class="block text-blue-200">Start your free mock test today.</span>
                </h2>
                <div class="flex justify-center lg:justify-end space-x-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 transition shadow-sm">
                        Create Account
                    </a>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white border-t border-gray-800 pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between border-b border-gray-800 pb-8 mb-8">
                <div class="flex justify-center md:justify-start mb-6 md:mb-0">
                    <a href="/" class="flex items-center gap-2 opacity-90 hover:opacity-100 transition">
                        <div class="w-6 h-6 bg-blue-500 text-white rounded flex items-center justify-center font-bold text-sm">
                            M
                        </div>
                        <span class="font-bold text-xl tracking-tight text-white">MockDasher</span>
                    </a>
                </div>
                <div class="flex justify-center space-x-6 text-sm text-gray-400">
                    <a href="{{ route('login') }}" class="hover:text-white transition">Login</a>
                    <a href="{{ route('register') }}" class="hover:text-white transition">Register</a>
                </div>
            </div>
            
            <div class="md:flex md:items-center md:justify-between">
                <div class="mt-8 md:mt-0 md:order-1 text-center md:text-left">
                    <p class="text-sm text-gray-400">
                        &copy; {{ date('Y') }} MockDasher Engine. All rights reserved.
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        IELTS is a registered trademark of University of Cambridge ESOL, the British Council, and IDP Education Australia. MockDasher is not affiliated, approved or endorsed by the University of Cambridge ESOL, the British Council, and IDP Education Australia.
                    </p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
