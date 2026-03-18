<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>MockDasher - Premium IELTS Simulation Platform</title>
    <!-- Alpine JS or other scripts if required by the main app could be loaded here -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: {
                            "primary": "#4F46E5",
                            "primary-hover": "#4338CA",
                            "background-light": "#F8FAFC",
                            "background-dark": "#0F172A",
                            "surface": "#FFFFFF",
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
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased">
    
<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 glass-nav">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined">bolt</span>
            </div>
            <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">MockDasher</span>
        </div>
        <div class="hidden md:flex items-center gap-10">
            <a class="text-sm font-medium text-slate-600 hover:text-primary transition-colors" href="#features">Features</a>
            <a class="text-sm font-medium text-slate-600 hover:text-primary transition-colors" href="{{ route('login') }}">Sign In</a>
        </div>
        <div class="flex items-center gap-4">
            @auth
                <a href="{{ url('/dashboard') }}" class="px-5 py-2 text-sm font-semibold text-slate-700 hover:text-primary transition-colors">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-gradient px-6 py-2.5 rounded-full text-white text-sm font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                        Log Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-semibold text-slate-700 hover:text-primary transition-colors">Sign In</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-gradient inline-block px-6 py-2.5 rounded-full text-white text-sm font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                        Start Free
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="pt-40 pb-24 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div class="flex flex-col gap-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider w-fit">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                New: Computer-delivered Mock v2.0
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 dark:text-white leading-[1.1] tracking-tight">
                Master the IELTS Exam with <span class="hero-gradient">Confidence</span>
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed max-w-xl">
                A clean, distraction-free environment mirroring the computer-delivered IELTS format. Get real-time feedback and detailed band score analysis.
            </p>
            <div class="flex flex-wrap gap-4 pt-4">
                <a href="{{ route('register') }}" class="btn-gradient inline-block px-8 py-4 rounded-xl text-white font-bold text-lg shadow-xl shadow-primary/30 hover:scale-[1.02] transition-transform">
                    Start Practicing
                </a>
                <a href="#features" class="inline-block bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-8 py-4 rounded-xl text-slate-700 dark:text-slate-200 font-bold text-lg hover:bg-slate-50 transition-colors">
                    Explore Features
                </a>
            </div>
        </div>
        <div class="relative">
            <div class="absolute -inset-4 bg-gradient-to-r from-primary to-violet-500 opacity-20 blur-3xl rounded-full"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden aspect-[4/3] flex flex-col">
                <div class="bg-slate-100 dark:bg-slate-800 px-4 py-2 flex items-center gap-2 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    </div>
                    <div class="mx-auto text-[10px] font-mono text-slate-400">ielts-sim.mockdasher.io/reading/test-01</div>
                </div>
                <div class="flex-1 p-6 flex gap-6">
                    <div class="w-1/2 flex flex-col gap-4">
                        <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-3/4"></div>
                        <div class="space-y-2">
                            <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded"></div>
                            <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded"></div>
                            <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded w-5/6"></div>
                        </div>
                        <div class="h-40 bg-slate-50 dark:bg-slate-800/50 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-slate-300 text-4xl">image</span>
                        </div>
                    </div>
                    <div class="w-1/2 border-l border-slate-100 dark:border-slate-800 pl-6 flex flex-col gap-6">
                        <div class="h-8 w-8 rounded bg-primary/20 flex items-center justify-center text-primary font-bold">1</div>
                        <div class="space-y-4">
                            <div class="h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg"></div>
                            <div class="h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg"></div>
                        </div>
                        <div class="mt-auto h-12 bg-primary rounded-lg flex items-center justify-center text-white text-sm font-bold">Next Question</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Proof -->
<section class="py-12 border-y border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-center text-sm font-semibold text-slate-400 uppercase tracking-[0.2em] mb-10">Trusted by 50,000+ test-takers</p>
        <div class="flex flex-wrap justify-center items-center gap-12 md:gap-24 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
            <div class="text-2xl font-black text-slate-400">ACADEMIA</div>
            <div class="text-2xl font-black text-slate-400">EDUFLOW</div>
            <div class="text-2xl font-black text-slate-400">GLOBALED</div>
            <div class="text-2xl font-black text-slate-400">STUDYPORT</div>
            <div class="text-2xl font-black text-slate-400">LEARNLY</div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section id="features" class="py-24 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-4xl font-bold text-slate-900 dark:text-white">Built for High Performance</h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Every feature is meticulously designed to help you reach your target band score with efficiency.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="feature-card bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-100 dark:border-slate-800 transition-all cursor-default">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary mb-6">
                    <span class="material-symbols-outlined">laptop_mac</span>
                </div>
                <h3 class="text-xl font-bold mb-3 dark:text-white">Real Interface</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Experience the exact UI and controls used in the actual computer-delivered IELTS exam.</p>
            </div>
            <!-- Feature 2 -->
            <div class="feature-card bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-100 dark:border-slate-800 transition-all cursor-default">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary mb-6">
                    <span class="material-symbols-outlined">edit_note</span>
                </div>
                <h3 class="text-xl font-bold mb-3 dark:text-white">Writing Counter</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Live word counts and time management alerts to keep your essays on track and structured.</p>
            </div>
            <!-- Feature 3 -->
            <div class="feature-card bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-100 dark:border-slate-800 transition-all cursor-default">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary mb-6">
                    <span class="material-symbols-outlined">mic</span>
                </div>
                <h3 class="text-xl font-bold mb-3 dark:text-white">Speaking Practice</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">AI-powered speaking modules with voice recognition and immediate fluency feedback.</p>
            </div>
            <!-- Feature 4 -->
            <div class="feature-card bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-100 dark:border-slate-800 transition-all cursor-default">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary mb-6">
                    <span class="material-symbols-outlined">menu_book</span>
                </div>
                <h3 class="text-xl font-bold mb-3 dark:text-white">Full Modules</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Comprehensive tests for Listening and Reading with automatic grading and explanations.</p>
            </div>
        </div>
    </div>
</section>

<!-- Progress Highlight -->
<section class="mx-6 mb-24">
    <div class="max-w-7xl mx-auto bg-gradient-to-br from-primary to-violet-700 rounded-[2.5rem] overflow-hidden relative p-8 md:p-16 lg:p-24">
        <div class="absolute inset-0 opacity-10 dot-pattern"></div>
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="text-white space-y-6">
                <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight">Track Your Progress with Visual Analytics</h2>
                <p class="text-indigo-100 text-lg leading-relaxed">
                    Don't just practice, improve. Our analytics engine tracks your performance across all modules, predicting your band score with 95% accuracy based on historical data.
                </p>
                <ul class="space-y-4 pt-4">
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-300">check_circle</span>
                        <span>Historical score comparisons</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-300">check_circle</span>
                        <span>Weakness identification alerts</span>
                    </li>
                </ul>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                <div class="flex items-center justify-between mb-8">
                    <span class="text-white font-bold">Estimated Band Score</span>
                    <span class="bg-emerald-400 text-slate-900 px-3 py-1 rounded-full text-xs font-bold">+1.5 Improvement</span>
                </div>
                <div class="h-48 flex items-end gap-3 px-2">
                    <div class="flex-1 bg-white/20 rounded-t-lg transition-all hover:bg-white/40" style="height: 40%"></div>
                    <div class="flex-1 bg-white/20 rounded-t-lg transition-all hover:bg-white/40" style="height: 55%"></div>
                    <div class="flex-1 bg-white/20 rounded-t-lg transition-all hover:bg-white/40" style="height: 45%"></div>
                    <div class="flex-1 bg-white/20 rounded-t-lg transition-all hover:bg-white/40" style="height: 70%"></div>
                    <div class="flex-1 bg-white/40 rounded-t-lg transition-all hover:bg-white/60" style="height: 85%"></div>
                    <div class="flex-1 bg-white rounded-t-lg shadow-lg" style="height: 95%"></div>
                </div>
                <div class="flex justify-between mt-4 text-white/60 text-xs font-medium">
                    <span>WK 1</span>
                    <span>WK 2</span>
                    <span>WK 3</span>
                    <span>WK 4</span>
                    <span>WK 5</span>
                    <span class="text-white">CURRENT</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 px-6 text-center">
    <div class="max-w-3xl mx-auto space-y-10">
        <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">Ready to boost your score?</h2>
        <p class="text-xl text-slate-600 dark:text-slate-400">Join thousands of students who have already achieved their dream scores using MockDasher.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="btn-gradient inline-block px-10 py-5 rounded-2xl text-white font-bold text-xl shadow-2xl shadow-primary/40 hover:scale-[1.02] transition-all">
                Create Your Free Account
            </a>
        </div>
        <p class="text-sm text-slate-400 font-medium">No credit card required • Instant access to 2 full tests</p>
    </div>
</section>

<!-- Footer -->
<footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-900 py-16 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-12 mb-16">
        <div class="col-span-2">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-sm">bolt</span>
                </div>
                <span class="text-lg font-extrabold tracking-tight dark:text-white">MockDasher</span>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xs leading-relaxed">
                The ultimate destination for IELTS preparation. Professional, reliable, and mirrors the actual test experience.
            </p>
        </div>
        <div>
            <h4 class="font-bold mb-6 dark:text-white">Platform</h4>
            <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                <li><a class="hover:text-primary transition-colors" href="#features">Features</a></li>
                <li><a class="hover:text-primary transition-colors" href="{{ route('register') }}">Get Started</a></li>
                <li><a class="hover:text-primary transition-colors" href="{{ route('login') }}">Sign In</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-bold mb-6 dark:text-white">Company</h4>
            <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                <li><span class="cursor-default" title="Coming soon">About</span></li>
                <li><span class="cursor-default" title="Coming soon">Blog</span></li>
                <li><span class="cursor-default" title="Coming soon">Careers</span></li>
            </ul>
        </div>
        <div>
            <h4 class="font-bold mb-6 dark:text-white">Support</h4>
            <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                <li><span class="cursor-default" title="Coming soon">Help Center</span></li>
                <li><span class="cursor-default" title="Coming soon">Contact</span></li>
                <li><span class="cursor-default" title="Coming soon">Privacy</span></li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto pt-8 border-t border-slate-100 dark:border-slate-900 flex flex-col md:row justify-between items-center gap-6">
        <p class="text-xs text-slate-400">© {{ date('Y') }} MockDasher Inc. All rights reserved. IELTS is a registered trademark.</p>
    </div>
</footer>

</body>
</html>
