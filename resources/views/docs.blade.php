<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ sidebarOpen: false, activeSection: 'overview', dark: localStorage.getItem('theme') === 'dark' }" x-init="dark ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'); $watch('dark', v => { localStorage.setItem('theme', v ? 'dark' : 'light'); v ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'); })">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="MockDasher Technical Documentation — Pitch Deck, Architecture, and Live Dashboard for Investors & Evaluators" />
    <title>MockDasher Docs — Pitch Deck & Technical Whitepaper</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --shadow-soft: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-premium: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07);
            --shadow-lift: 0 25px 50px -12px rgb(0 0 0 / 0.15);
        }
        * { font-family: 'Inter', sans-serif; }
        code, pre, .mono { font-family: 'JetBrains Mono', monospace; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }

        /* Sidebar active pill */
        .nav-link { @apply flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all; }
        .nav-link.active { @apply text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50; }

        /* Section headings */
        .section-anchor { scroll-margin-top: 5rem; }

        /* Stat cards */
        .stat-card { @apply bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm; }

        /* Code block */
        .code-block { @apply bg-slate-900 dark:bg-slate-950 text-slate-100 rounded-2xl p-5 text-sm overflow-x-auto border border-slate-700; }

        /* Gradient text */
        .gradient-text { background: linear-gradient(135deg, #4F46E5, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

        /* Dot grid */
        .dot-bg { background-image: radial-gradient(circle, rgba(99,102,241,0.08) 1px, transparent 1px); background-size: 24px 24px; }

        /* Arch node */
        .arch-node { @apply flex flex-col items-center justify-center rounded-2xl border-2 p-4 font-bold text-center text-sm leading-tight transition-all hover:scale-105; }

        /* Timeline */
        .timeline-item::before { content: ''; position: absolute; left: -1px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, #6366f1, transparent); }

        /* Live pulse */
        @keyframes pulse-ring { 0% { transform: scale(.9); opacity: 1; } 70% { transform: scale(1.3); opacity: 0; } 100% { transform: scale(.9); opacity: 0; } }
        .live-dot { position: relative; display: inline-flex; }
        .live-dot::before { content: ''; position: absolute; inset: -4px; border-radius: 50%; background: #10b981; animation: pulse-ring 1.5s ease-out infinite; }
    </style>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        'primary-hover': '#4338CA',
                        'surface-light': '#FFFFFF',
                        'surface-dark': '#1E293B',
                        'bg-light': '#F8FAFC',
                        'bg-dark': '#0F172A',
                    },
                    fontFamily: { display: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    boxShadow: { soft: 'var(--shadow-soft)', premium: 'var(--shadow-premium)', lift: 'var(--shadow-lift)' },
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased" x-cloak>

{{-- ══════════════════════════════════════════════════════════
     TOP NAVIGATION BAR
══════════════════════════════════════════════════════════ --}}
<header class="fixed top-0 left-0 right-0 z-50 h-14 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 flex items-center px-4 lg:px-8 gap-4">

    {{-- Brand --}}
    <a href="/" class="flex items-center gap-2.5 shrink-0">
        <div class="flex size-8 items-center justify-center rounded-xl bg-primary text-white shadow-soft">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
        </div>
        <span class="font-extrabold text-slate-900 dark:text-white tracking-tight text-sm hidden sm:block">MockDasher</span>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-wider border border-indigo-100 dark:border-indigo-900">Docs</span>
    </a>

    {{-- Live badge --}}
    <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/60 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider">
        <span class="live-dot size-2 rounded-full bg-emerald-500 shrink-0"></span>
        Live Data
    </div>

    <div class="ml-auto flex items-center gap-3">
        {{-- Section quick jump (mobile) --}}
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        </button>

        {{-- Dark mode toggle --}}
        <button @click="dark = !dark" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 dark:text-slate-400">
            <svg x-show="!dark" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.02 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.02 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.02 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0 .39-.39.39-1.02 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.02 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.02 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.02 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.02 0 1.41s1.03.39 1.41 0l1.06-1.06z"/></svg>
            <svg x-show="dark" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>
        </button>

        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center gap-1.5 bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-xl text-xs font-bold shadow-soft transition-all">
            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            Get Started Free
        </a>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════
     LAYOUT WRAPPER
══════════════════════════════════════════════════════════ --}}
<div class="pt-14 flex min-h-screen">

    {{-- ── LEFT SIDEBAR ──────────────────────────────────── --}}
    <aside class="fixed left-0 top-14 bottom-0 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 overflow-y-auto hidden lg:flex flex-col z-40 shadow-soft">
        <div class="p-4 space-y-0.5 flex-1">
            <p class="px-3 pt-2 pb-1 text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Introduction</p>
            <a href="#overview"   class="nav-link" onclick="setActive('overview')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                Overview
            </a>
            <a href="#pitch"      class="nav-link" onclick="setActive('pitch')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
                Executive Pitch
            </a>
            <a href="#metrics"    class="nav-link" onclick="setActive('metrics')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Live Metrics
            </a>

            <p class="px-3 pt-4 pb-1 text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Product</p>
            <a href="#modules"    class="nav-link" onclick="setActive('modules')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                Exam Modules
            </a>
            <a href="#ai"         class="nav-link" onclick="setActive('ai')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79s7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58s9.14-3.47 12.65 0L21 3v7.12zM12.5 8v4.25l3.5 2.08-.72 1.21L11 13V8h1.5z"/></svg>
                AI Evaluation Engine
            </a>
            <a href="#security"   class="nav-link" onclick="setActive('security')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4.58l6 2.68v4.2c0 3.78-2.59 7.27-6 8.38-3.41-1.11-6-4.6-6-8.38v-4.2l6-2.68z"/></svg>
                Security & Proctoring
            </a>

            <p class="px-3 pt-4 pb-1 text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Technical</p>
            <a href="#architecture" class="nav-link" onclick="setActive('architecture')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
                Architecture
            </a>
            <a href="#techstack"  class="nav-link" onclick="setActive('techstack')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/></svg>
                Tech Stack
            </a>
            <a href="#datamodel"  class="nav-link" onclick="setActive('datamodel')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 11.5v-7l5.5 3.5-5.5 3.5z"/></svg>
                Data Model
            </a>

            <p class="px-3 pt-4 pb-1 text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Company</p>
            <a href="#team"       class="nav-link" onclick="setActive('team')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                Team
            </a>
            <a href="#roadmap"    class="nav-link" onclick="setActive('roadmap')">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M21 6.5l-4-4-9 9-2 4 4-2 9-9 2 2zM3.5 21h17v-2h-17v2z"/></svg>
                Roadmap
            </a>
        </div>

        {{-- Footer links --}}
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-2">
            <a href="/" class="nav-link text-xs">← Back to Home</a>
            <a href="{{ route('register') }}" class="flex items-center justify-center gap-1.5 w-full bg-primary hover:bg-primary-hover text-white py-2.5 rounded-xl text-xs font-bold transition-all">
                Start Free Trial
            </a>
        </div>
    </aside>

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-30 bg-slate-950/60 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>
    <aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed left-0 top-14 bottom-0 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 overflow-y-auto z-40 lg:hidden p-4 space-y-0.5">
        <a href="#overview"     class="nav-link" @click="sidebarOpen=false">Overview</a>
        <a href="#pitch"        class="nav-link" @click="sidebarOpen=false">Executive Pitch</a>
        <a href="#metrics"      class="nav-link" @click="sidebarOpen=false">Live Metrics</a>
        <a href="#modules"      class="nav-link" @click="sidebarOpen=false">Exam Modules</a>
        <a href="#ai"           class="nav-link" @click="sidebarOpen=false">AI Engine</a>
        <a href="#security"     class="nav-link" @click="sidebarOpen=false">Security</a>
        <a href="#architecture" class="nav-link" @click="sidebarOpen=false">Architecture</a>
        <a href="#techstack"    class="nav-link" @click="sidebarOpen=false">Tech Stack</a>
        <a href="#datamodel"    class="nav-link" @click="sidebarOpen=false">Data Model</a>
        <a href="#team"         class="nav-link" @click="sidebarOpen=false">Team</a>
        <a href="#roadmap"      class="nav-link" @click="sidebarOpen=false">Roadmap</a>
    </aside>

    {{-- ── MAIN CONTENT ──────────────────────────────────── --}}
    <main class="lg:ml-64 flex-1 min-w-0">
        <div class="max-w-4xl mx-auto px-4 sm:px-8 py-12 space-y-20">

            {{-- ══════════════════════════════════════════════
                 HERO / COVER
            ══════════════════════════════════════════════ --}}
            <section id="overview" class="section-anchor">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700 p-8 sm:p-14 text-white shadow-lift">
                    <div class="absolute inset-0 dot-bg opacity-30"></div>
                    <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="absolute -left-8 -bottom-8 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-[10px] font-black uppercase tracking-widest mb-6">
                            <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Infinity AI BuildFest 2026 · Submission
                        </div>
                        <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-[1.05] mb-4">
                            MockDasher
                        </h1>
                        <p class="text-xl sm:text-2xl font-semibold text-indigo-100 mb-3">
                            AI-Powered IELTS Practice Platform
                        </p>
                        <p class="text-indigo-200 text-base sm:text-lg leading-relaxed max-w-2xl mb-8">
                            The only platform that replicates the official IELTS computerised exam interface with real-time AI evaluation across all four modules — built for the 3.5 million annual test-takers who can't afford to guess.
                        </p>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 hover:bg-indigo-50 px-5 py-2.5 rounded-xl text-sm font-bold shadow-lift transition-all">
                                Try the Platform →
                            </a>
                            <a href="#pitch" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all">
                                View Pitch Deck ↓
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Quick facts row --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                    @php
                        $facts = [
                            ['label' => 'Launch Year', 'value' => '2026', 'icon' => '📅'],
                            ['label' => 'Tech Stack', 'value' => 'Laravel + Gemini', 'icon' => '⚙️'],
                            ['label' => 'Model', 'value' => 'Bring Your Own Key', 'icon' => '🔑'],
                            ['label' => 'License', 'value' => 'Open Beta', 'icon' => '🚀'],
                        ];
                    @endphp
                    @foreach($facts as $f)
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-4 shadow-soft text-center">
                        <div class="text-2xl mb-1">{{ $f['icon'] }}</div>
                        <div class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-0.5">{{ $f['label'] }}</div>
                        <div class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $f['value'] }}</div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 EXECUTIVE PITCH
            ══════════════════════════════════════════════ --}}
            <section id="pitch" class="section-anchor space-y-8">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-900 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest mb-3">Pitch Deck</div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Executive Summary</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 text-base leading-relaxed">Everything an investor or technical evaluator needs to assess MockDasher in under five minutes.</p>
                </div>

                {{-- Problem / Solution --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-900/60 rounded-2xl p-6">
                        <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400 font-black uppercase text-[10px] tracking-widest mb-3">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            The Problem
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3">3.5M test-takers flying blind</h3>
                        <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                            <li class="flex items-start gap-2"><span class="text-rose-500 shrink-0 mt-0.5">✕</span> No free platform replicates the official IELTS computer interface</li>
                            <li class="flex items-start gap-2"><span class="text-rose-500 shrink-0 mt-0.5">✕</span> Human examiner feedback costs $200–$400 per session</li>
                            <li class="flex items-start gap-2"><span class="text-rose-500 shrink-0 mt-0.5">✕</span> Writing and Speaking have zero automated grading in free tools</li>
                            <li class="flex items-start gap-2"><span class="text-rose-500 shrink-0 mt-0.5">✕</span> Existing apps use gamified UIs that don't match real exam pressure</li>
                        </ul>
                    </div>

                    <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/60 rounded-2xl p-6">
                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-black uppercase text-[10px] tracking-widest mb-3">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            The Solution
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3">MockDasher — Pixel-perfect + AI-graded</h3>
                        <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                            <li class="flex items-start gap-2"><span class="text-emerald-500 shrink-0 mt-0.5">✓</span> Faithful replica of the official IELTS computer-delivered UI</li>
                            <li class="flex items-start gap-2"><span class="text-emerald-500 shrink-0 mt-0.5">✓</span> Gemini 2.5 Flash grades Writing & Speaking against official band descriptors</li>
                            <li class="flex items-start gap-2"><span class="text-emerald-500 shrink-0 mt-0.5">✓</span> BYOK model = unlimited evaluations at zero platform cost</li>
                            <li class="flex items-start gap-2"><span class="text-emerald-500 shrink-0 mt-0.5">✓</span> Real exam timers, proctoring, and anti-cheat across all 4 modules</li>
                        </ul>
                    </div>
                </div>

                {{-- Market + Differentiation --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                        <div class="text-3xl font-black gradient-text mb-1">$1.8B</div>
                        <div class="text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Global IELTS Prep Market</div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Forecast to reach $3.2B by 2028 driven by global migration & university admissions demand.</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                        <div class="text-3xl font-black gradient-text mb-1">3.5M+</div>
                        <div class="text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Annual Test-Takers</div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">IELTS administered in 140+ countries. TAM includes TOEFL and other English proficiency exams.</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                        <div class="text-3xl font-black gradient-text mb-1">$0</div>
                        <div class="text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Platform Cost to User</div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">BYOK means no infrastructure cost per evaluation. Users bring their own Gemini API key.</p>
                    </div>
                </div>

                {{-- Business Model --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-lg mb-4">Business Model</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @php
                            $tiers = [
                                ['phase' => 'Phase 1 (Now)', 'name' => 'Open Beta', 'color' => 'emerald', 'desc' => 'Free BYOK access. Build user base, collect usage data, validate product-market fit.'],
                                ['phase' => 'Phase 2 (Q3 2026)', 'name' => 'SaaS Subscription', 'color' => 'indigo', 'desc' => '$9/mo includes Gemini API credits, institutional content, and analytics dashboard.'],
                                ['phase' => 'Phase 3 (2027)', 'name' => 'B2B Licensing', 'color' => 'violet', 'desc' => 'White-label licensing to coaching institutes and universities. Revenue share model.'],
                            ];
                        @endphp
                        @foreach($tiers as $tier)
                        <div class="border border-{{ $tier['color'] }}-100 dark:border-{{ $tier['color'] }}-900/60 bg-{{ $tier['color'] }}-50/50 dark:bg-{{ $tier['color'] }}-950/30 rounded-xl p-4">
                            <div class="text-[9px] font-black uppercase tracking-wider text-{{ $tier['color'] }}-500 mb-1">{{ $tier['phase'] }}</div>
                            <div class="font-extrabold text-slate-900 dark:text-white text-sm mb-2">{{ $tier['name'] }}</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $tier['desc'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Competitive matrix --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-soft">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="font-extrabold text-slate-900 dark:text-white">Competitive Landscape</h3>
                        <p class="text-xs text-slate-400 mt-0.5">MockDasher vs major alternatives</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider text-slate-400">Feature</th>
                                    <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider text-indigo-500">MockDasher</th>
                                    <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">IELTS.org</th>
                                    <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">Magoosh</th>
                                    <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">IELTS Ninja</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @php
                                $matrix = [
                                    ['Official exam UI replica', true, true, false, false],
                                    ['AI Writing evaluation', true, false, false, true],
                                    ['AI Speaking evaluation', true, false, false, false],
                                    ['Full proctoring system', true, true, false, false],
                                    ['Free to use', true, false, false, false],
                                    ['Real-time band score', true, false, false, false],
                                    ['All 4 IELTS modules', true, true, true, true],
                                    ['Performance analytics', true, false, true, true],
                                ];
                                @endphp
                                @foreach($matrix as $row)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors">
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300 font-medium">{{ $row[0] }}</td>
                                    @foreach(array_slice($row, 1) as $i => $val)
                                    <td class="px-4 py-3 text-center">
                                        @if($val)
                                        <span class="{{ $i === 0 ? 'text-indigo-500' : 'text-emerald-500' }} font-black">✓</span>
                                        @else
                                        <span class="text-slate-300 dark:text-slate-600">—</span>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 LIVE METRICS DASHBOARD
            ══════════════════════════════════════════════ --}}
            <section id="metrics" class="section-anchor space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-900 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest mb-3">
                        <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Platform Data
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Platform Metrics</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">Real-time aggregated data from the production database — refreshed on every page load.</p>
                </div>

                {{-- Primary stats --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                    $stats = [
                        ['label' => 'Registered Users', 'value' => number_format($totalUsers), 'sub' => 'Total accounts created', 'icon' => '#6366f1', 'bg' => 'indigo'],
                        ['label' => 'Total Attempts', 'value' => number_format($totalAttempts), 'sub' => 'Full exam sessions started', 'icon' => '#8b5cf6', 'bg' => 'violet'],
                        ['label' => 'Completed Exams', 'value' => number_format($completedAttempts), 'sub' => $completionRate . '% completion rate', 'icon' => '#10b981', 'bg' => 'emerald'],
                        ['label' => 'AI Evaluations', 'value' => number_format($totalEvaluations), 'sub' => 'Writing + Speaking graded', 'icon' => '#f59e0b', 'bg' => 'amber'],
                    ];
                    @endphp
                    @foreach($stats as $s)
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-soft hover:shadow-premium hover:-translate-y-0.5 transition-all">
                        <div class="flex items-start justify-between mb-3">
                            <div class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 leading-tight">{{ $s['label'] }}</div>
                            <div class="size-8 rounded-xl flex items-center justify-center" style="background: {{ $s['icon'] }}18;">
                                <div class="size-2.5 rounded-full" style="background: {{ $s['icon'] }};"></div>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white tabular-nums leading-none mb-1">{{ $s['value'] }}</div>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $s['sub'] }}</div>
                    </div>
                    @endforeach
                </div>

                {{-- Secondary stats row --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    @php
                    $secondary = [
                        ['label' => 'Active Tests', 'value' => $activeTests],
                        ['label' => 'Total Questions', 'value' => number_format($totalQuestions)],
                        ['label' => 'Completion Rate', 'value' => $completionRate . '%'],
                        ['label' => 'Avg Writing Band', 'value' => $avgWritingBand ? number_format($avgWritingBand, 1) : '—'],
                        ['label' => 'Avg Speaking Band', 'value' => $avgSpeakingBand ? number_format($avgSpeakingBand, 1) : '—'],
                    ];
                    @endphp
                    @foreach($secondary as $s)
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 shadow-soft text-center">
                        <div class="text-xl font-extrabold text-slate-900 dark:text-white tabular-nums">{{ $s['value'] }}</div>
                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-400 mt-0.5">{{ $s['label'] }}</div>
                    </div>
                    @endforeach
                </div>

                {{-- AI Evaluation breakdown chart --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-1">AI Evaluation Breakdown</h3>
                        <p class="text-[10px] text-slate-400 mb-4">Writing vs Speaking evaluations completed</p>
                        <div class="relative h-48">
                            <canvas id="evalChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-1">Exam Funnel</h3>
                        <p class="text-[10px] text-slate-400 mb-4">Attempts → Completions → AI Evaluations</p>
                        <div class="relative h-48">
                            <canvas id="funnelChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 EXAM MODULES
            ══════════════════════════════════════════════ --}}
            <section id="modules" class="section-anchor space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-50 dark:bg-violet-950/60 border border-violet-100 dark:border-violet-900 text-violet-600 dark:text-violet-400 text-[10px] font-black uppercase tracking-widest mb-3">Product</div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Exam Modules</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">All four IELTS modules implemented to spec, each with its own server-side timer and proctoring.</p>
                </div>

                @php
                $modules = [
                    [
                        'name' => 'Listening', 'letter' => 'L', 'color' => 'indigo',
                        'time' => '30 min + 10 min transfer', 'q' => '40 questions across 4 sections',
                        'desc' => 'Audio plays once (mirroring real exam rules). Waveform progress bar, section-based questions, answer transfer timer, MCQ and fill-in-the-blank types.',
                        'features' => ['Single-play audio enforcement', 'Section-based question groups', '10-minute answer transfer period', 'Drag-and-drop seating diagram support (roadmap)', 'Real-time answer flagging'],
                    ],
                    [
                        'name' => 'Reading', 'letter' => 'R', 'color' => 'sky',
                        'time' => '60 minutes', 'q' => '40 questions across 3 passages',
                        'desc' => 'Split-screen layout — passage left, questions right. Full text highlighting in 4 colours, question flagging, and a review overlay before submission.',
                        'features' => ['4-colour text highlighting', 'True/False/Not Given buttons', 'Match headings & sentence completion', 'Review panel with answered/flagged count', 'Per-passage tab navigation'],
                    ],
                    [
                        'name' => 'Writing', 'letter' => 'W', 'color' => 'violet',
                        'time' => '60 minutes', 'q' => 'Task 1 (150w) + Task 2 (250w)',
                        'desc' => 'Dual-panel editor with live word count, graph/image for Task 1, per-task submission, and Gemini AI scoring after both tasks are submitted.',
                        'features' => ['Real-time word counter with colour coding', 'Task 1 chart/graph image display', 'Cut/Copy/Paste toolbar + keyboard shortcuts', 'Per-task independent submission', 'Gemini AI band score per IELTS criteria'],
                    ],
                    [
                        'name' => 'Speaking', 'letter' => 'S', 'color' => 'emerald',
                        'time' => '11–14 minutes', 'q' => '3 parts, multiple questions each',
                        'desc' => 'In-browser audio recording with per-question time limits, live STT transcript, 60-second Part 2 preparation timer, and per-question Gemini AI evaluation.',
                        'features' => ['MediaRecorder API recording', 'Web Speech API live transcript', 'Part 2 prep countdown with beep alert', 'Per-question independent submission', 'AI fluency, coherence, pronunciation scoring'],
                    ],
                ];
                @endphp

                <div class="space-y-5">
                    @foreach($modules as $mod)
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-soft hover:shadow-premium transition-shadow">
                        <div class="flex items-start gap-5 p-6">
                            <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-{{ $mod['color'] }}-50 dark:bg-{{ $mod['color'] }}-950/40 border border-{{ $mod['color'] }}-100 dark:border-{{ $mod['color'] }}-900/60 text-{{ $mod['color'] }}-600 dark:text-{{ $mod['color'] }}-400 text-2xl font-black">
                                {{ $mod['letter'] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $mod['name'] }}</h3>
                                    <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-{{ $mod['color'] }}-50 dark:bg-{{ $mod['color'] }}-950/40 text-{{ $mod['color'] }}-600 dark:text-{{ $mod['color'] }}-400 border border-{{ $mod['color'] }}-100 dark:border-{{ $mod['color'] }}-900/60">{{ $mod['time'] }}</span>
                                    <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">{{ $mod['q'] }}</span>
                                </div>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-4">{{ $mod['desc'] }}</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($mod['features'] as $feat)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/60 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3 h-3 text-indigo-500 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                        {{ $feat }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 AI EVALUATION ENGINE
            ══════════════════════════════════════════════ --}}
            <section id="ai" class="section-anchor space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-950/60 border border-amber-100 dark:border-amber-900 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase tracking-widest mb-3">AI Core</div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Gemini Evaluation Engine</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">How MockDasher turns a 30-second Gemini API call into an authoritative IELTS band score.</p>
                </div>

                {{-- Flow diagram --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-5">Evaluation Pipeline</h3>
                    <div class="flex flex-col sm:flex-row items-center gap-2 overflow-x-auto">
                        @php
                        $pipeline = [
                            ['step' => '1', 'label' => 'Exam Submit', 'sub' => 'User triggers final submit', 'color' => 'slate'],
                            ['step' => '2', 'label' => 'Queue Job', 'sub' => 'Laravel queues evaluation job', 'color' => 'indigo'],
                            ['step' => '3', 'label' => 'Gemini API', 'sub' => 'gemini-2.5-flash call with IELTS system prompt', 'color' => 'amber'],
                            ['step' => '4', 'label' => 'JSON Parse', 'sub' => 'Extract band scores + feedback', 'color' => 'violet'],
                            ['step' => '5', 'label' => 'Score Stored', 'sub' => 'Results saved to DB', 'color' => 'emerald'],
                        ];
                        @endphp
                        @foreach($pipeline as $i => $p)
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="flex flex-col items-center text-center">
                                <div class="size-10 rounded-full bg-{{ $p['color'] }}-100 dark:bg-{{ $p['color'] }}-950/60 border-2 border-{{ $p['color'] }}-300 dark:border-{{ $p['color'] }}-700 text-{{ $p['color'] }}-700 dark:text-{{ $p['color'] }}-300 font-black text-sm flex items-center justify-center mb-2">{{ $p['step'] }}</div>
                                <div class="text-[10px] font-black text-slate-900 dark:text-white">{{ $p['label'] }}</div>
                                <div class="text-[9px] text-slate-400 max-w-[80px] leading-snug mt-0.5">{{ $p['sub'] }}</div>
                            </div>
                            @if($i < count($pipeline) - 1)
                            <div class="text-slate-300 dark:text-slate-600 text-xl font-thin shrink-0 hidden sm:block">→</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Evaluation criteria --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-4">Writing Band Criteria</h3>
                        <div class="space-y-3">
                            @php
                            $wCriteria = [
                                ['name' => 'Task Achievement / Response', 'color' => 'indigo'],
                                ['name' => 'Coherence & Cohesion', 'color' => 'violet'],
                                ['name' => 'Lexical Resource', 'color' => 'sky'],
                                ['name' => 'Grammatical Range & Accuracy', 'color' => 'emerald'],
                            ];
                            @endphp
                            @foreach($wCriteria as $c)
                            <div class="flex items-center gap-3">
                                <div class="size-2.5 rounded-full bg-{{ $c['color'] }}-500 shrink-0"></div>
                                <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $c['name'] }}</div>
                                <div class="ml-auto text-[9px] font-black text-slate-400 uppercase tracking-widest">0–9 band</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft">
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-4">Speaking Band Criteria</h3>
                        <div class="space-y-3">
                            @php
                            $sCriteria = [
                                ['name' => 'Fluency & Coherence', 'color' => 'indigo'],
                                ['name' => 'Lexical Resource', 'color' => 'violet'],
                                ['name' => 'Grammatical Range & Accuracy', 'color' => 'sky'],
                                ['name' => 'Pronunciation', 'color' => 'rose'],
                            ];
                            @endphp
                            @foreach($sCriteria as $c)
                            <div class="flex items-center gap-3">
                                <div class="size-2.5 rounded-full bg-{{ $c['color'] }}-500 shrink-0"></div>
                                <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $c['name'] }}</div>
                                <div class="ml-auto text-[9px] font-black text-slate-400 uppercase tracking-widest">0–9 band</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Sample Gemini output --}}
                <div class="space-y-3">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">Sample Gemini Response Schema</h3>
                    <pre class="code-block text-xs leading-relaxed overflow-x-auto"><code>{
  "evaluation_type": "Writing",
  "module_type": "Writing Task 2",
  "overall_band_score": 7.0,
  "criteria_scores": {
    "task_achievement_or_response": 7.0,
    "coherence_and_cohesion": 7.5,
    "lexical_resource": 6.5,
    "grammatical_range_and_accuracy": 7.0
  },
  "detailed_feedback": "The essay presents a clear position and develops it well...",
  "vocabulary_corrections": [
    { "incorrect": "alot", "suggested": "a lot" }
  ],
  "grammar_corrections": [
    { "incorrect": "the peoples", "suggested": "the people" }
  ],
  "suggestions_for_improvement": "Use more complex sentence structures..."
}</code></pre>
                </div>

                {{-- Key design decisions --}}
                <div class="bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/60 rounded-2xl p-6">
                    <h3 class="font-extrabold text-indigo-900 dark:text-indigo-100 text-sm mb-4">Engineering Decisions</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        @php
                        $decisions = [
                            ['q' => 'Why BYOK?', 'a' => 'Eliminates API cost as a scaling blocker. Zero marginal cost per evaluation for the platform.'],
                            ['q' => 'Why Gemini 2.5 Flash?', 'a' => 'Best JSON output quality, low latency (< 8s), and free tier via Google AI Studio.'],
                            ['q' => 'Why per-task, not batch?', 'a' => 'Granular retries. If Task 1 fails, Task 2 is not lost. Better UX with partial loading states.'],
                            ['q' => 'Band score rounding?', 'a' => 'Gemini may return 6.3; we round to nearest 0.5 to stay within IELTS scoring convention.'],
                        ];
                        @endphp
                        @foreach($decisions as $d)
                        <div>
                            <div class="font-bold text-indigo-800 dark:text-indigo-200 mb-1">{{ $d['q'] }}</div>
                            <div class="text-indigo-700/80 dark:text-indigo-300/80 text-xs leading-relaxed">{{ $d['a'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 SECURITY & PROCTORING
            ══════════════════════════════════════════════ --}}
            <section id="security" class="section-anchor space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950/60 border border-rose-100 dark:border-rose-900 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase tracking-widest mb-3">Security</div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Security & Exam Integrity</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">Multi-layer security model covering authentication, proctoring, and data integrity.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @php
                    $security = [
                        [
                            'title' => 'Server-Side Timers', 'icon' => '⏱',
                            'desc' => 'Exam start timestamps are stored in the database. All remaining time calculations happen server-side, preventing client-side timer manipulation.',
                        ],
                        [
                            'title' => 'Tab-Switch Proctoring', 'icon' => '👁',
                            'desc' => 'document.visibilitychange events are tracked. Each tab switch increments a server-authoritative violation counter. Three violations = auto-submit.',
                        ],
                        [
                            'title' => 'Anti-Cheat Clipboard', 'icon' => '📋',
                            'desc' => 'Copy/paste from passage text is blocked at the DOM event level. Only form elements (answer boxes) allow clipboard access.',
                        ],
                        [
                            'title' => 'CSRF & Role Guards', 'icon' => '🔒',
                            'desc' => 'All state-changing requests require a valid CSRF token. Admin routes are gated by a custom RoleMiddleware using database-backed roles.',
                        ],
                        [
                            'title' => 'IDOR Prevention', 'icon' => '🛡',
                            'desc' => 'All attempt-scoped resources are validated against the authenticated user\'s ID before any data is read or written.',
                        ],
                        [
                            'title' => 'Answer Integrity', 'icon' => '✅',
                            'desc' => 'Submitted answers are locked at the database level (submitted_at timestamp). Re-submission or modification after lock is rejected.',
                        ],
                    ];
                    @endphp
                    @foreach($security as $s)
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-soft flex gap-4">
                        <div class="text-2xl shrink-0">{{ $s['icon'] }}</div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-1.5">{{ $s['title'] }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $s['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 SYSTEM ARCHITECTURE
            ══════════════════════════════════════════════ --}}
            <section id="architecture" class="section-anchor space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 dark:bg-sky-950/60 border border-sky-100 dark:border-sky-900 text-sky-600 dark:text-sky-400 text-[10px] font-black uppercase tracking-widest mb-3">Technical</div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">System Architecture</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">A layered monolith with async queue workers — designed for fast shipping and easy scaling.</p>
                </div>

                {{-- Architecture visual --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 sm:p-8 shadow-soft">
                    <div class="space-y-4">

                        {{-- Layer 1: Client --}}
                        <div class="flex items-center gap-4">
                            <div class="w-28 shrink-0 text-[9px] font-black uppercase tracking-widest text-slate-400 text-right">Client</div>
                            <div class="flex-1 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-xl p-3 flex flex-wrap gap-2">
                                @foreach(['Browser (Tailwind + Alpine.js)', 'Chart.js Analytics', 'MediaRecorder API', 'Web Speech API (STT)'] as $c)
                                <span class="text-[10px] font-bold bg-white dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 px-2.5 py-1 rounded-lg border border-indigo-100 dark:border-indigo-700">{{ $c }}</span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Arrow --}}
                        <div class="flex items-center gap-4">
                            <div class="w-28 shrink-0"></div>
                            <div class="flex-1 text-center text-slate-300 dark:text-slate-600 text-xs font-mono">── HTTPS / JSON API ──▼</div>
                        </div>

                        {{-- Layer 2: Web --}}
                        <div class="flex items-center gap-4">
                            <div class="w-28 shrink-0 text-[9px] font-black uppercase tracking-widest text-slate-400 text-right">Web Layer</div>
                            <div class="flex-1 bg-violet-50 dark:bg-violet-950/40 border border-violet-200 dark:border-violet-800 rounded-xl p-3 flex flex-wrap gap-2">
                                @foreach(['Laravel 11 (PHP 8.3)', 'Laravel Fortify Auth', 'Route Middleware (CSRF + Role)', 'Blade Templates'] as $c)
                                <span class="text-[10px] font-bold bg-white dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 px-2.5 py-1 rounded-lg border border-violet-100 dark:border-violet-700">{{ $c }}</span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Arrow --}}
                        <div class="flex items-center gap-4">
                            <div class="w-28 shrink-0"></div>
                            <div class="flex-1 flex gap-8 text-center text-slate-300 dark:text-slate-600 text-xs font-mono">
                                <span class="flex-1">── Eloquent ORM ──▼</span>
                                <span class="flex-1">── Queue Dispatch ──▼</span>
                            </div>
                        </div>

                        {{-- Layer 3: Data + Queue --}}
                        <div class="flex items-center gap-4">
                            <div class="w-28 shrink-0 text-[9px] font-black uppercase tracking-widest text-slate-400 text-right">Data / Queue</div>
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 flex flex-wrap gap-2">
                                    @foreach(['MySQL Database', 'File Storage (Local/S3)', '30+ Eloquent Models'] as $c)
                                    <span class="text-[10px] font-bold bg-white dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 px-2.5 py-1 rounded-lg border border-emerald-100 dark:border-emerald-700">{{ $c }}</span>
                                    @endforeach
                                </div>
                                <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl p-3 flex flex-wrap gap-2">
                                    @foreach(['Laravel Queue Worker', 'EvaluateWritingSubmission', 'EvaluateSpeakingSubmission'] as $c)
                                    <span class="text-[10px] font-bold bg-white dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 px-2.5 py-1 rounded-lg border border-amber-100 dark:border-amber-700">{{ $c }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Arrow --}}
                        <div class="flex items-center gap-4">
                            <div class="w-28 shrink-0"></div>
                            <div class="flex-1 text-center text-slate-300 dark:text-slate-600 text-xs font-mono text-right pr-12">── REST API (HTTPS) ──▼</div>
                        </div>

                        {{-- Layer 4: External --}}
                        <div class="flex items-center gap-4">
                            <div class="w-28 shrink-0 text-[9px] font-black uppercase tracking-widest text-slate-400 text-right">External</div>
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl p-3 flex flex-wrap gap-2">
                                    @foreach(['Google Gemini 2.5 Flash', 'generativelanguage API', 'Per-user BYOK auth'] as $c)
                                    <span class="text-[10px] font-bold bg-white dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 px-2.5 py-1 rounded-lg border border-rose-100 dark:border-rose-700">{{ $c }}</span>
                                    @endforeach
                                </div>
                                <div class="bg-slate-100 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-600 rounded-xl p-3 flex flex-wrap gap-2">
                                    @foreach(['Google Fonts CDN', 'Tailwind CSS CDN', 'Chart.js CDN'] as $c)
                                    <span class="text-[10px] font-bold bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-600">{{ $c }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 TECH STACK
            ══════════════════════════════════════════════ --}}
            <section id="techstack" class="section-anchor space-y-6">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Tech Stack</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Every library and service powering MockDasher in production.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @php
                    $stack = [
                        ['cat' => 'Backend Framework', 'name' => 'Laravel 11', 'detail' => 'PHP 8.3 · MVC · Eloquent ORM', 'why' => 'Rapid development, excellent queue system, battle-tested auth via Fortify.', 'badge' => 'Core'],
                        ['cat' => 'AI / Evaluation', 'name' => 'Google Gemini 2.5 Flash', 'detail' => 'REST API · JSON mode · 1M token context', 'why' => 'Best JSON output fidelity for structured band score responses at near-zero latency.', 'badge' => 'Core'],
                        ['cat' => 'Frontend UI', 'name' => 'Tailwind CSS v3 + Alpine.js', 'detail' => 'CDN · JIT · Reactivity without a build step', 'why' => 'Zero-config setup. Dark mode, responsive design, and micro-interactions with minimal JS.', 'badge' => 'Core'],
                        ['cat' => 'Database', 'name' => 'MySQL 8.0', 'detail' => '30+ tables · Soft deletes · JSON columns', 'why' => 'Relational integrity for nested exam content (tests → sets → tasks → answers).', 'badge' => 'Core'],
                        ['cat' => 'Authentication', 'name' => 'Laravel Fortify', 'detail' => 'Email/password · 2FA · Password resets', 'why' => 'Headless auth layer with 2FA support out of the box.', 'badge' => 'Auth'],
                        ['cat' => 'Job Queue', 'name' => 'Laravel Queue (DB driver)', 'detail' => 'Async AI evaluation · Retry logic', 'why' => 'Non-blocking Gemini API calls. Jobs run in background, UI shows status polling.', 'badge' => 'Infra'],
                        ['cat' => 'File Storage', 'name' => 'Laravel Storage', 'detail' => 'Local / S3-compatible · Audio uploads', 'why' => 'Speaking audio recordings stored per-question. S3 swap-in for production scaling.', 'badge' => 'Infra'],
                        ['cat' => 'Charts', 'name' => 'Chart.js 4', 'detail' => 'Line · Bar · Doughnut · Responsive', 'why' => 'Lightweight, no build step needed. Powers both student dashboard and docs metrics.', 'badge' => 'UI'],
                        ['cat' => 'Speech Recognition', 'name' => 'Web Speech API', 'detail' => 'webkitSpeechRecognition · STT transcript', 'why' => 'Browser-native, no API cost. Used to generate speaking transcripts for Gemini input.', 'badge' => 'Browser'],
                    ];
                    @endphp
                    @foreach($stack as $s)
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-soft hover:shadow-premium hover:-translate-y-0.5 transition-all">
                        <div class="flex items-start justify-between mb-2">
                            <div class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $s['cat'] }}</div>
                            <span class="text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full {{ $s['badge'] === 'Core' ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">{{ $s['badge'] }}</span>
                        </div>
                        <div class="font-extrabold text-slate-900 dark:text-white text-sm mb-1">{{ $s['name'] }}</div>
                        <div class="text-[10px] font-mono text-slate-400 dark:text-slate-500 mb-3">{{ $s['detail'] }}</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $s['why'] }}</p>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 DATA MODEL
            ══════════════════════════════════════════════ --}}
            <section id="datamodel" class="section-anchor space-y-6">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Data Model</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Core entities and their relationships — simplified ERD.</p>
                </div>

                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft overflow-x-auto">
                    <pre class="code-block text-xs leading-loose whitespace-pre"><code>users
 ├── id, name, email, gemini_api_key (encrypted), exam_date, target_band
 └── roles[] (many-to-many via role_user pivot)

tests
 ├── id, book_number, exam_type (Academic|General), year, status
 └── test_sets[]
      ├── writing_tasks[]      → writing_task_images[]
      ├── speaking_questions[]
      ├── listening_sections[] → questions[] → question_options[]
      └── reading_passages[]
           └── reading_question_groups[]
                └── questions[] → question_options[]

test_attempts (pivot: user × test_set × session)
 ├── status (pending|in_progress|completed)
 ├── started_at, writing_started_at, speaking_started_at, completed_at
 ├── proctoring_violations (int, server-authoritative)
 ├── writing_answers[]    → evaluation_json, band_score
 ├── speaking_answers[]   → audio_path, transcript_text, band_score
 ├── reading_attempt      → reading_answers[]
 └── listening_attempt    → listening_answers[]

ai_writing_evaluations   (aggregated per attempt)
 ├── task_1_band_score, task_2_band_score, band_score
 └── evaluation_status (pending|evaluating|completed|failed)

ai_speaking_evaluations  (aggregated per attempt)
 ├── full_transcript, band_score
 └── evaluation_status (pending|evaluating|completed|failed)</code></pre>
                </div>

                {{-- Key design choices --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="font-black text-slate-900 dark:text-white text-xs mb-2">Polymorphic Questions</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Reading and listening questions share a unified <code class="text-indigo-500">questions</code> table with a <code class="text-indigo-500">question_type</code> discriminator. New types (matching, diagram) require zero schema changes.</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="font-black text-slate-900 dark:text-white text-xs mb-2">Immutable Answers</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Once <code class="text-indigo-500">submitted_at</code> is set, server middleware blocks further writes. Autosave routes check this before persisting.</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="font-black text-slate-900 dark:text-white text-xs mb-2">Evaluation as Aggregate</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Per-question scores live on their answer rows. The <code class="text-indigo-500">ai_*_evaluations</code> tables hold pre-computed aggregates for fast dashboard queries.</p>
                    </div>
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 TEAM
            ══════════════════════════════════════════════ --}}
            <section id="team" class="section-anchor space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest mb-3">Company</div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">The Team</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">Built by a tight-knit team from Dwimik Software — shipping from Bangladesh to the world.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @php
                    $team = [
                        [
                            'name' => 'Daniel Rozario', 'role' => 'Team Lead · Frontend · DevOps',
                            'org' => 'CSE, Varendra University', 'email' => 'daniel@dwimiksoftware.com',
                            'img' => '/storage/asset/team/daniel.JPG',
                            'skills' => ['React', 'Tailwind', 'Docker', 'CI/CD'],
                            'color' => 'indigo',
                        ],
                        [
                            'name' => 'Prayangshu Biswas', 'role' => 'Lead Backend · Database Architect',
                            'org' => 'CST, Jessore Polytechnic Institute', 'email' => 'prayangshu@dwimiksoftware.com',
                            'img' => '/storage/asset/team/prayangshu.jpg',
                            'skills' => ['Laravel', 'MySQL', 'Queue', 'AI APIs'],
                            'color' => 'violet',
                        ],
                        [
                            'name' => 'Dipanwita Maitra', 'role' => 'UI/UX Designer',
                            'org' => 'CSE, Varendra University', 'email' => 'dipanwita@dwimiksoftware.com',
                            'img' => '/storage/asset/team/dipanwita.jpeg',
                            'skills' => ['Figma', 'Design Systems', 'Accessibility'],
                            'color' => 'rose',
                        ],
                    ];
                    @endphp
                    @foreach($team as $m)
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-soft hover:shadow-premium hover:-translate-y-1 transition-all text-center group">
                        <div class="relative w-20 h-20 mx-auto mb-4">
                            <div class="absolute inset-0 bg-gradient-to-br from-{{ $m['color'] }}-500 to-violet-600 rounded-full blur-lg opacity-30 group-hover:opacity-50 transition-opacity"></div>
                            <img src="{{ $m['img'] }}" alt="{{ $m['name'] }}" class="relative w-20 h-20 rounded-full object-cover border-4 border-white dark:border-slate-700 shadow-md" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($m['name']) }}&background=4F46E5&color=fff&size=80'">
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-0.5">{{ $m['name'] }}</h3>
                        <p class="text-[10px] font-black text-{{ $m['color'] }}-500 uppercase tracking-wider mb-1">{{ $m['role'] }}</p>
                        <p class="text-[10px] text-slate-400 mb-3">{{ $m['org'] }}</p>
                        <div class="flex flex-wrap justify-center gap-1.5 mb-3">
                            @foreach($m['skills'] as $skill)
                            <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">{{ $skill }}</span>
                            @endforeach
                        </div>
                        <a href="mailto:{{ $m['email'] }}" class="text-[10px] text-slate-400 hover:text-primary transition-colors">{{ $m['email'] }}</a>
                    </div>
                    @endforeach
                </div>

                <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 text-center">
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Under the banner of</div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white tracking-wider uppercase">Dwimik Software</div>
                    <a href="https://www.dwimiksoftware.com" target="_blank" class="text-xs text-indigo-500 hover:text-indigo-600 transition-colors mt-1 inline-block">www.dwimiksoftware.com</a>
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 ROADMAP
            ══════════════════════════════════════════════ --}}
            <section id="roadmap" class="section-anchor space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-900 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest mb-3">Roadmap</div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Vision & Roadmap</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">Where MockDasher is headed — phased execution with clear milestones.</p>
                </div>

                <div class="space-y-4">
                    @php
                    $roadmap = [
                        [
                            'phase' => 'Phase 1', 'period' => 'Q1–Q2 2026 · NOW', 'status' => 'shipped',
                            'title' => 'Foundation — Full IELTS Simulation',
                            'items' => [
                                'All 4 modules (Listening, Reading, Writing, Speaking)',
                                'Gemini AI evaluation for Writing & Speaking',
                                'Proctoring system with tab-switch detection',
                                'Admin panel for test content management',
                                'Student dashboard with score analytics',
                                'Text highlighting in reading passages',
                            ],
                        ],
                        [
                            'phase' => 'Phase 2', 'period' => 'Q3 2026', 'status' => 'planned',
                            'title' => 'Monetisation & Scale',
                            'items' => [
                                'Subscription tier with bundled Gemini credits',
                                'Stripe payment integration',
                                'Email notifications for evaluation completion',
                                'Expanded test library (20+ official test sets)',
                                'Mobile-responsive exam interface',
                            ],
                        ],
                        [
                            'phase' => 'Phase 3', 'period' => 'Q4 2026 – Q1 2027', 'status' => 'vision',
                            'title' => 'Platform Expansion',
                            'items' => [
                                'Native iOS & Android app via React Native',
                                'B2B institute white-labelling',
                                'TOEFL and OET module support',
                                'AI tutor chatbot for targeted practice',
                                'Leaderboard and peer comparison analytics',
                            ],
                        ],
                    ];
                    @endphp
                    @foreach($roadmap as $r)
                    <div class="bg-white dark:bg-slate-800 border {{ $r['status'] === 'shipped' ? 'border-emerald-200 dark:border-emerald-800' : ($r['status'] === 'planned' ? 'border-indigo-200 dark:border-indigo-800' : 'border-slate-200 dark:border-slate-700') }} rounded-2xl p-6 shadow-soft">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full {{ $r['status'] === 'shipped' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900' : ($r['status'] === 'planned' ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 border border-slate-200 dark:border-slate-600') }}">
                                {{ $r['status'] === 'shipped' ? '✓ Shipped' : ($r['status'] === 'planned' ? '⚡ Planned' : '🔭 Vision') }}
                            </span>
                            <span class="font-extrabold text-slate-900 dark:text-white">{{ $r['phase'] }} — {{ $r['title'] }}</span>
                            <span class="ml-auto text-[10px] text-slate-400 font-mono">{{ $r['period'] }}</span>
                        </div>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($r['items'] as $item)
                            <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-400">
                                <span class="{{ $r['status'] === 'shipped' ? 'text-emerald-500' : ($r['status'] === 'planned' ? 'text-indigo-400' : 'text-slate-300') }} shrink-0 mt-0.5 text-xs">{{ $r['status'] === 'shipped' ? '✓' : '○' }}</span>
                                {{ $item }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ══════════════════════════════════════════════
                 FOOTER CTA
            ══════════════════════════════════════════════ --}}
            <section class="bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700 rounded-3xl p-8 sm:p-12 text-white text-center shadow-lift relative overflow-hidden">
                <div class="absolute inset-0 dot-bg opacity-20"></div>
                <div class="relative z-10">
                    <h2 class="text-3xl sm:text-4xl font-black mb-3 tracking-tight">Ready to practice smarter?</h2>
                    <p class="text-indigo-100 mb-6 text-base max-w-xl mx-auto">Join thousands of test-takers already using MockDasher. No credit card, no subscriptions — just bring your free Gemini API key.</p>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 hover:bg-indigo-50 px-6 py-3 rounded-xl text-sm font-extrabold shadow-lift transition-all">
                            Create Free Account →
                        </a>
                        <a href="/" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all">
                            View Landing Page
                        </a>
                    </div>
                    <p class="text-indigo-200/70 text-xs mt-4">Built by Dwimik Software · Bangladesh · 2026</p>
                </div>
            </section>

        </div>{{-- /max-w-4xl --}}
    </main>{{-- /main --}}
</div>{{-- /layout --}}

{{-- ══════════════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════════════ --}}
<script>
// Active sidebar nav tracking via IntersectionObserver
function setActive(id) {
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    document.querySelectorAll('.nav-link[href="#'+id+'"]').forEach(l => l.classList.add('active'));
}

const sections = document.querySelectorAll('.section-anchor');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) setActive(e.target.id); });
}, { rootMargin: '-60px 0px -60% 0px', threshold: 0 });
sections.forEach(s => observer.observe(s));

// Charts
(function() {
    const isDark = document.documentElement.classList.contains('dark');
    const tooltipBg = isDark ? '#0f172a' : '#1e293b';
    const gridColor = isDark ? 'rgba(51,65,85,0.4)' : 'rgba(226,232,240,0.6)';
    const labelColor = '#94a3b8';

    // Eval breakdown doughnut
    const evalCtx = document.getElementById('evalChart');
    if (evalCtx) {
        new Chart(evalCtx, {
            type: 'doughnut',
            data: {
                labels: ['Writing Evals', 'Speaking Evals'],
                datasets: [{
                    data: [{{ $aiWritingDone }}, {{ $aiSpeakingDone }}],
                    backgroundColor: ['#6366f1cc', '#8b5cf6cc'],
                    borderColor: isDark ? '#1e293b' : '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: labelColor, font: { weight: '700', size: 11 }, padding: 16 } },
                    tooltip: { backgroundColor: tooltipBg, bodyFont: { size: 12, weight: '700' }, padding: 10, cornerRadius: 10, displayColors: true }
                }
            }
        });
    }

    // Funnel bar
    const funnelCtx = document.getElementById('funnelChart');
    if (funnelCtx) {
        const max = Math.max({{ $totalAttempts }}, 1);
        new Chart(funnelCtx, {
            type: 'bar',
            data: {
                labels: ['Total Attempts', 'Completions', 'AI Evals'],
                datasets: [{
                    data: [{{ $totalAttempts }}, {{ $completedAttempts }}, {{ $totalEvaluations }}],
                    backgroundColor: ['#6366f1bb', '#10b981bb', '#f59e0bbb'],
                    borderRadius: 10,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: tooltipBg, bodyFont: { size: 12, weight: '700' }, padding: 10, cornerRadius: 10, displayColors: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: labelColor, font: { weight: '700', size: 11 } }, border: { display: false } },
                    y: { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 10 } }, border: { display: false }, beginAtZero: true }
                }
            }
        });
    }

    // Re-init charts on dark mode toggle
    document.addEventListener('alpine:mutated', () => {});
})();

// Scroll spy — highlight sidebar on initial load
document.addEventListener('DOMContentLoaded', () => setActive('overview'));
</script>

</body>
</html>
