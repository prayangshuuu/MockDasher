<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>MockDasher - Premium IELTS Simulation Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        :root {
            --shadow-soft: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-premium: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07);
            --shadow-lift: 0 25px 50px -12px rgb(0 0 0 / 0.15);
        }

        .font-display {
            font-family: 'Inter', sans-serif;
        }

        .glass-nav {
            background-color: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #eee;
        }

        .dark .glass-nav {
            background-color: rgba(15, 23, 42, 0.5);
            border-bottom: 1px solid rgb(30, 41, 59);
        }

        .hero-gradient {
            background: linear-gradient(to right, #4F46E5, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .dot-pattern {
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 1rem 1rem;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-10deg);
        }

        .testimonial-grid {
            --col-gap: 2rem;
            --row-gap: 2rem;
            --col-count: 3;
            display: grid;
            grid-template-columns: repeat(var(--col-count), 1fr);
            gap: var(--row-gap) var(--col-gap);
        }

        @media (max-width: 1024px) {
            .testimonial-grid {
                --col-count: 2;
            }
        }

        @media (max-width: 640px) {
            .testimonial-grid {
                --col-count: 1;
            }
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4F46E5",
                        "primary-hover": "#4338CA",
                        "background-light": "#F8FAFC",
                        "background-dark": "#0F172A",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1E293B",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "xs": "0.375rem",
                        "sm": "0.5rem",
                        "base": "0.75rem",
                        "DEFAULT": "0.75rem",
                        "md": "0.75rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "2xl": "2rem",
                        "3xl": "2.5rem",
                        "full": "9999px"
                    },
                    boxShadow: {
                        'soft': 'var(--shadow-soft)',
                        'premium': 'var(--shadow-premium)',
                        'lift': 'var(--shadow-lift)',
                    }
                },
            },
        }
    </script>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased"
    x-data="{ mobileMenuOpen: false }">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-nav">
        <div class="max-w-7xl mx-auto px-4 md:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <img src="/storage/asset/logo.png" alt="MockDasher Logo" class="h-9" />
                <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white uppercase">MockDasher</span>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-8">
                <a class="flex items-center gap-1.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors"
                    href="#features">
                    <img src="/storage/asset/icons/features.svg" class="w-4 h-4" alt="Features" />
                    Features
                </a>
                <a class="flex items-center gap-1.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors"
                    href="#testimonials">
                    <img src="/storage/asset/icons/testimonial.svg" class="w-4 h-4" alt="Testimonials" />
                    Testimonials
                </a>
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors">Dashboard</a>
                @else
                    <a class="flex items-center gap-1.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors"
                        href="{{ route('login') }}">
                        <img src="/storage/asset/icons/login.svg" class="w-4 h-4" alt="Sign In" />
                        Sign In
                    </a>
                @endauth
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-full text-sm font-bold shadow-premium hover:shadow-lift transition-all duration-300">
                            Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}"
                        class="flex items-center gap-1.5 bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-full text-sm font-bold shadow-premium hover:shadow-lift transition-all duration-300">
                        <img src="/storage/asset/icons/start.svg" class="w-4 h-4 invert brightness-0" alt="Get Started" />
                        Get Started
                    </a>
                @endauth
            </div>

            <!-- Mobile Nav Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-slate-900 dark:text-white">
                <span class="material-symbols-outlined text-3xl"
                    x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
            </button>
        </div>

        <!-- Mobile Nav Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-b border-slate-200 dark:border-slate-800 p-6 absolute top-20 left-0 right-0 shadow-xl">
            <div class="flex flex-col gap-4">
                <a class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white" href="#features"
                    @click="mobileMenuOpen = false">
                    <img src="/storage/asset/icons/features.svg" class="w-5 h-5" alt="Features" />
                    Features
                </a>
                <a class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white" href="#testimonials"
                    @click="mobileMenuOpen = false">
                    <img src="/storage/asset/icons/testimonial.svg" class="w-5 h-5" alt="Testimonials" />
                    Testimonials
                </a>
                @auth
                    <a class="text-lg font-bold text-slate-900 dark:text-white"
                        href="{{ url('/dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full bg-primary text-white py-4 rounded-xl font-bold shadow-lift">Log Out</button>
                    </form>
                @else
                    <a class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white" href="{{ route('login') }}">
                        <img src="/storage/asset/icons/login.svg" class="w-5 h-5" alt="Sign In" />
                        Sign In
                    </a>
                    <a class="flex items-center justify-center gap-2 w-full bg-primary text-white py-4 rounded-xl font-bold text-center shadow-lift"
                        href="{{ route('register') }}">
                        <img src="/storage/asset/icons/start.svg" class="w-5 h-5 invert brightness-0" alt="Get Started" />
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="pt-32 pb-24 px-6">
        <div class="max-w-5xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider w-fit mb-6">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                Computer delivered Mock
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 dark:text-white leading-[1.1] tracking-tight">
                Master the IELTS Exam with <span class="hero-gradient">Confidence</span>
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed max-w-3xl mx-auto mt-8">
                A clean, distraction-free environment mirroring the computer-delivered IELTS format. Get real-time feedback and detailed band score analysis.
            </p>
            <div class="flex flex-wrap gap-4 justify-center pt-8">
                <a href="{{ route('register') }}"
                    class="inline-flex justify-center items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lift hover:shadow-indigo-500/50 hover:-translate-y-1 transition-all duration-300">
                    <img src="/storage/asset/icons/start.svg" class="w-5 h-5 invert brightness-0" alt="Start Icon" />
                    Start Practicing For Free
                </a>
                <a href="#features"
                    class="inline-flex justify-center items-center gap-2 bg-surface-light hover:bg-slate-50 text-slate-800 dark:bg-surface-dark dark:hover:bg-slate-700 dark:text-white border border-slate-200 dark:border-slate-700 px-8 py-4 rounded-xl font-bold text-lg hover:-translate-y-1 hover:shadow-premium transition-all duration-300">
                    <img src="/storage/asset/icons/explore.svg" class="w-5 h-5" alt="Explore Icon" />
                    Explore Features
                </a>
            </div>
        </div>

        <!-- App Screenshot -->
        <div class="max-w-7xl mx-auto mt-24">
            <div class="relative">
                <div class="absolute -inset-8 bg-gradient-to-r from-primary to-violet-500 opacity-10 blur-3xl rounded-full"></div>
                <div class="relative bg-surface-light dark:bg-surface-dark rounded-3xl shadow-premium border border-slate-200 dark:border-slate-800 overflow-hidden aspect-video flex flex-col">
                    <div class="bg-slate-100 dark:bg-slate-800/50 px-4 py-2 flex items-center gap-2 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        </div>
                        <div class="mx-auto text-[10px] font-mono text-slate-400">ielts-sim.mockdasher.io/reading/test-01</div>
                    </div>
                    <div class="flex-1 p-2 sm:p-4 md:p-6 flex items-center justify-center bg-slate-50/50 dark:bg-slate-900/50">
                        <img src="https://placehold.co/1200x600/4F46E5/FFFFFF?text=MockDasher+UI" alt="MockDasher App Screenshot" class="w-full h-full object-cover rounded-xl">
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Social Proof -->
    <section class="py-16 border-y border-slate-200 dark:border-slate-800 bg-white/30 dark:bg-slate-900/30">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-center text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-10">Trusted by over 50,000 test-takers</p>
            <div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-8 md:gap-x-24 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                <p class="text-2xl font-bold text-slate-400">ACADEMIA</p>
                <p class="text-2xl font-bold text-slate-400">EDUFLOW</p>
                <p class="text-2xl font-bold text-slate-400">GLOBALED</p>
                <p class="text-2xl font-bold text-slate-400">STUDYPORT</p>
                <p class="text-2xl font-bold text-slate-400">LEARNLY</p>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white">Built for High Performance</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Every feature is meticulously designed to help you reach your target band score with efficiency.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft hover:shadow-premium hover:-translate-y-1 transition-all cursor-default">
                    <div class="feature-icon size-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary mb-6 shadow-sm transition-transform">
                        <img src="/storage/asset/icons/laptop.svg" alt="Laptop Icon" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-black mb-3 dark:text-white tracking-tight">Real Interface</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed font-medium">Experience the exact UI and controls used in the actual computer-delivered IELTS exam.</p>
                </div>
                <!-- Feature 2 -->
                <div class="feature-card bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft hover:shadow-premium hover:-translate-y-1 transition-all cursor-default">
                    <div class="feature-icon size-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary mb-6 shadow-sm transition-transform">
                        <img src="/storage/asset/icons/edit.svg" alt="Edit Icon" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-black mb-3 dark:text-white tracking-tight">Writing Counter</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed font-medium">Live word counts and time management alerts to keep your essays on track and structured.</p>
                </div>
                <!-- Feature 3 -->
                <div class="feature-card bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft hover:shadow-premium hover:-translate-y-1 transition-all cursor-default">
                    <div class="feature-icon size-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary mb-6 shadow-sm transition-transform">
                        <img src="/storage/asset/icons/microphone.svg" alt="Microphone Icon" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-black mb-3 dark:text-white tracking-tight">Speaking Practice</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed font-medium">AI-powered speaking modules with voice recognition and immediate fluency feedback.</p>
                </div>
                <!-- Feature 4 -->
                <div class="feature-card bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft hover:shadow-premium hover:-translate-y-1 transition-all cursor-default">
                    <div class="feature-icon size-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary mb-6 shadow-sm transition-transform">
                        <img src="/storage/asset/icons/menu.svg" alt="Menu Icon" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-black mb-3 dark:text-white tracking-tight">Full Modules</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed font-medium">Comprehensive tests for Listening and Reading with automatic grading and explanations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Progress Highlight -->
    <section class="mx-6 mb-24">
        <div class="max-w-7xl mx-auto bg-gradient-to-br from-primary to-violet-700 rounded-3xl overflow-hidden relative p-8 md:p-16 lg:p-24 shadow-lift">
            <div class="absolute inset-0 opacity-10 dot-pattern"></div>
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="text-white space-y-6">
                    <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight">Track Your Progress with Visual Analytics</h2>
                    <p class="text-indigo-100 text-lg leading-relaxed">
                        Don't just practice, improve. Our analytics engine tracks your performance across all modules, predicting your band score with 95% accuracy based on historical data.
                    </p>
                    <ul class="space-y-4 pt-4">
                        <li class="flex items-center gap-3">
                            <img src="/storage/asset/icons/check-circle.svg" class="w-6 h-6" alt="Check Circle" />
                            <span>Historical score comparisons</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <img src="/storage/asset/icons/check-circle.svg" class="w-6 h-6" alt="Check Circle" />
                            <span>Weakness identification alerts</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-premium">
                    <div class="flex items-center justify-between mb-8">
                        <span class="text-white font-bold">Estimated Band Score</span>
                        <span class="bg-emerald-400 text-slate-900 px-3 py-1 rounded-full text-xs font-bold">+1.5 Improvement</span>
                    </div>
                    <div class="h-48 flex items-end gap-3 px-2" x-data="{ heights: ['40%', '55%', '45%', '70%', '85%', '95%'] }">
                        <template x-for="(height, index) in heights" :key="index">
                            <div class="flex-1 bg-white/20 rounded-t-lg transition-all hover:bg-white/40" :class="{'!bg-white shadow-lg': index === 5}" :style="`height: ${height}`"></div>
                        </template>
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

    <!-- Testimonials -->
    <section id="testimonials" class="py-24 px-6 bg-slate-50 dark:bg-slate-900/50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white">Why Students Love MockDasher</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Real stories from students who have achieved their dream scores.</p>
            </div>
            <div class="testimonial-grid">
                <!-- Testimonial 1 -->
                <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft space-y-6">
                    <div class="flex items-center gap-4">
                        <img class="w-12 h-12 rounded-full object-cover" src="https://randomuser.me/api/portraits/women/11.jpg" alt="Avatar">
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">Priya Sharma</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Target Band: 8.0</p>
                        </div>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300">"The interface is identical to the real test. I felt so confident on exam day because I knew exactly what to expect. Scored a 7.5 in Reading, up from 6.0!"</p>
                </div>
                <!-- Testimonial 2 -->
                <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft space-y-6">
                    <div class="flex items-center gap-4">
                        <img class="w-12 h-12 rounded-full object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Avatar">
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">Chen Wei</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Target Band: 7.5</p>
                        </div>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300">"MockDasher's analytics are a game-changer. It pinpointed my weaknesses in Writing Task 2, and I was able to focus my practice. Highly recommended."</p>
                </div>
                <!-- Testimonial 3 -->
                <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft space-y-6">
                    <div class="flex items-center gap-4">
                        <img class="w-12 h-12 rounded-full object-cover" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Avatar">
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">Fatima Al-Fassi</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Target Band: 8.5</p>
                        </div>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300">"The speaking practice tool is incredible. Getting instant feedback on my pronunciation and fluency helped me improve faster than any other method."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team -->
    <section id="team" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white">Meet The Team</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">The passionate individuals behind MockDasher.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Team Member 1 -->
                <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft text-center group hover:-translate-y-2 hover:shadow-premium transition-all duration-300">
                    <div class="relative w-32 h-32 mx-auto mb-6">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary to-violet-500 rounded-full blur-lg opacity-40 group-hover:opacity-60 transition-opacity"></div>
                        <img class="relative w-32 h-32 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-md" src="/storage/asset/team/daniel.JPG" alt="Daniel Rozario">
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Daniel Rozario</h3>
                    <p class="text-primary font-semibold text-sm mb-3">Team Lead / Frontend / DevOps</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">CSE, Varendra University</p>
                    <a href="mailto:daniel@dwimiksoftware.com" class="text-sm text-slate-400 hover:text-primary transition-colors">daniel@dwimiksoftware.com</a>
                </div>

                <!-- Team Member 2 -->
                <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft text-center group hover:-translate-y-2 hover:shadow-premium transition-all duration-300">
                    <div class="relative w-32 h-32 mx-auto mb-6">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary to-violet-500 rounded-full blur-lg opacity-40 group-hover:opacity-60 transition-opacity"></div>
                        <img class="relative w-32 h-32 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-md" src="/storage/asset/team/prayangshu.jpg" alt="Prayangshu Biswas Hritwick">
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Prayangshu Biswas</h3>
                    <p class="text-primary font-semibold text-sm mb-3">Lead Backend / Database</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">CST, Jessore Polytechnic Institute</p>
                    <a href="mailto:prayangshu@dwimiksoftware.com" class="text-sm text-slate-400 hover:text-primary transition-colors">prayangshu@dwimiksoftware.com</a>
                </div>

                <!-- Team Member 3 -->
                <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft text-center group hover:-translate-y-2 hover:shadow-premium transition-all duration-300">
                    <div class="relative w-32 h-32 mx-auto mb-6">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary to-violet-500 rounded-full blur-lg opacity-40 group-hover:opacity-60 transition-opacity"></div>
                        <img class="relative w-32 h-32 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-md" src="/storage/asset/team/dipanwita.png" alt="Dipanwita Maitra">
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Dipanwita Maitra</h3>
                    <p class="text-primary font-semibold text-sm mb-3">UI/UX Designer</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">CSE, Varendra University</p>
                    <a href="mailto:dipanwita@dwimiksoftware.com" class="text-sm text-slate-400 hover:text-primary transition-colors">dipanwita@dwimiksoftware.com</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 px-6 text-center border-t border-slate-200 dark:border-slate-800">
        <div class="max-w-3xl mx-auto space-y-10">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">Ready to boost your score?</h2>
            <p class="text-xl text-slate-600 dark:text-slate-400">Join thousands of students who have already achieved their dream scores using MockDasher.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}"
                    class="inline-flex justify-center items-center gap-3 bg-primary hover:bg-primary-hover text-white px-10 py-5 rounded-2xl font-bold text-xl shadow-lift hover:shadow-indigo-500/60 hover:-translate-y-1 transition-all duration-300">
                    <img src="/storage/asset/icons/start.svg" alt="Start Icon" class="w-6 h-6 invert brightness-0" />
                    Create Your Free Account
                </a>
            </div>
            <p class="text-sm text-slate-400 font-medium">No credit card required • Instant access to 2 full tests</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-surface-light dark:bg-slate-950 border-t border-slate-200 dark:border-slate-900 py-16 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-12 mb-16">
            <div class="flex-shrink-0">
                <a href="/" class="block">
                    <img src="/storage/asset/logo.png" alt="MockDasher Logo" class="h-16 md:h-20" />
                </a>
            </div>

            <div class="flex-grow grid grid-cols-2 md:grid-cols-4 gap-8 w-full md:w-auto">
                <div>
                    <h4 class="font-bold mb-6 dark:text-white">Platform</h4>
                    <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                        <li><a class="hover:text-primary transition-colors" href="#features">Features</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#testimonials">Testimonials</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('register') }}">Get Started</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('login') }}">Sign In</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-6 dark:text-white">Company</h4>
                    <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                        <li><a class="hover:text-primary transition-colors" href="#team">Team</a></li>
                        <li><span class="cursor-not-allowed text-slate-400" title="Coming soon">Blog</span></li>
                        <li><span class="cursor-not-allowed text-slate-400" title="Coming soon">Careers</span></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-6 dark:text-white">Support</h4>
                    <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                        <li><span class="cursor-not-allowed text-slate-400" title="Coming soon">Help Center</span></li>
                        <li><span class="cursor-not-allowed text-slate-400" title="Coming soon">Contact</span></li>
                        <li><span class="cursor-not-allowed text-slate-400" title="Coming soon">Privacy</span></li>
                    </ul>
                </div>
                 <div>
                    <h4 class="font-bold mb-6 dark:text-white">Legal</h4>
                    <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                        <li><span class="cursor-not-allowed text-slate-400" title="Coming soon">Terms of Service</span></li>
                    </ul>
                </div>
            </div>

            <div class="flex-shrink-0 md:self-end md:ml-auto">
                <span class="text-3xl md:text-5xl font-black tracking-widest text-slate-900 dark:text-white uppercase opacity-10">MOCKDASHER</span>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-sm text-slate-500 dark:text-slate-400">© {{ date('Y') }} MockDasher Inc. All rights reserved.</p>
            <p class="text-xs text-slate-400 dark:text-slate-500">IELTS is a registered trademark of University of Cambridge ESOL, the British Council, and IDP Education Australia. This site and its materials are not officially endorsed by them.</p>
        </div>
    </footer>

</body>

</html>
