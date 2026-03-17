@extends('layouts.admin')

@section('title', 'Admin Overview')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <span class="font-semibold text-slate-900 dark:text-white">Dashboard</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">System Overview</h2>
            <p class="text-slate-500 dark:text-slate-400 text-base">Key performance indicators and recent system activity.</p>
        </div>
        <div class="flex items-center gap-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl shadow-sm">
            <span class="material-symbols-outlined text-emerald-500 animate-pulse">check_circle</span>
            <span class="text-xs font-black uppercase tracking-widest text-slate-500">System Live</span>
        </div>
    </div>

    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft group hover:border-primary/50 transition-all">
            <div class="flex justify-between items-start mb-6">
                <div class="size-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">description</span>
                </div>
                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-lg uppercase tracking-widest">+12%</span>
            </div>
            <h3 class="text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-widest mb-1">Total Tests</h3>
            <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($stats['total_tests'] ?? 0) }}</p>
        </div>
        
        <div class="glass-card p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft group hover:border-primary/50 transition-all">
            <div class="flex justify-between items-start mb-6">
                <div class="size-12 rounded-2xl bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">layers</span>
                </div>
                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-lg uppercase tracking-widest">+5%</span>
            </div>
            <h3 class="text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-widest mb-1">Test Sets</h3>
            <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($stats['total_test_sets'] ?? 0) }}</p>
        </div>
        
        <div class="glass-card p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft group hover:border-primary/50 transition-all">
            <div class="flex justify-between items-start mb-6">
                <div class="size-12 rounded-2xl bg-purple-50 dark:bg-purple-900/40 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">group</span>
                </div>
                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-lg uppercase tracking-widest">+18%</span>
            </div>
            <h3 class="text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-widest mb-1">Active Students</h3>
            <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($stats['users'] ?? 0) }}</p>
        </div>
        
        <div class="glass-card p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft group hover:border-primary/50 transition-all">
            <div class="flex justify-between items-start mb-6">
                <div class="size-12 rounded-2xl bg-orange-50 dark:bg-orange-900/40 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">analytics</span>
                </div>
                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-lg uppercase tracking-widest">+22%</span>
            </div>
            <h3 class="text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-widest mb-1">Global Attempts</h3>
            <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($stats['attempts'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Analytics & Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart Section -->
        <div class="lg:col-span-2 glass-card p-10 rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-soft">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">System Engagement</h3>
                    <p class="text-sm text-slate-400 font-medium">Weekly active session distribution</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">94.2%</p>
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Efficiency Rate</p>
                </div>
            </div>
            
            <div class="relative h-64 w-full">
                <svg class="w-full h-full" viewBox="0 0 800 240" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="chartGradient" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="5%" stop-color="#5048e5" stop-opacity="0.3"></stop>
                            <stop offset="95%" stop-color="#5048e5" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M0,200 Q100,180 200,120 T400,100 T600,60 T800,40 V240 H0 Z" fill="url(#chartGradient)"></path>
                    <path d="M0,200 Q100,180 200,120 T400,100 T600,60 T800,40" fill="none" stroke="#5048e5" stroke-linecap="round" stroke-width="4"></path>
                </svg>
            </div>
            <div class="flex justify-between mt-6 px-4">
                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <span class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest">{{ $day }}</span>
                @endforeach
            </div>
        </div>

        <div class="space-y-8">
            <!-- Quick Action Card -->
            <div class="bg-gradient-to-br from-primary via-indigo-700 to-indigo-900 p-8 rounded-[2.5rem] text-white shadow-xl shadow-primary/20 relative overflow-hidden group">
                <div class="relative z-10">
                    <h3 class="text-xl font-extrabold mb-2 tracking-tight">Expand Library</h3>
                    <p class="text-sm opacity-80 mb-8 leading-relaxed font-medium">Publish new IELTS books and sets for your candidates using the high-conversion toolset.</p>
                    <a href="{{ route('admin.tests.create') }}" class="inline-flex items-center gap-2 bg-white text-primary px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:scale-105 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-lg">add_circle</span>
                        Create Test
                    </a>
                </div>
                <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-[12rem] opacity-5 group-hover:rotate-12 transition-transform duration-700">auto_awesome</span>
            </div>

            <!-- System Health Card -->
            <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800">
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">dataset</span>
                    Database Latency
                </h4>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-500">API Gateway</span>
                            <span class="text-xs font-black text-emerald-500 uppercase">Excellent</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full w-[94%]"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-500">Resource Load</span>
                            <span class="text-xs font-black text-primary uppercase">Optimal</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full w-[12%]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="glass-card rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-10 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Recent Infrastructure Updates</h3>
                <p class="text-sm text-slate-400 font-medium">Monitoring the latest test and user interactions</p>
            </div>
            <a href="{{ route('admin.tests.index') }}" class="inline-flex items-center gap-2 text-primary font-black text-xs uppercase tracking-widest hover:gap-3 transition-all">
                Full Audit
                <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <tbody>
                    @forelse($tests->take(5) as $test)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-50 dark:border-slate-800/50 last:border-0">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-5">
                                    <div class="size-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary transition-all">
                                        <span class="material-symbols-outlined text-2xl">menu_book</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 dark:text-white text-base">IELTS Edition {{ $test->book_number }}</h4>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $test->exam_type }} • {{ $test->year }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 text-[10px] font-black uppercase tracking-widest">
                                    {{ $test->status }}
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <a href="{{ route('admin.tests.show', $test->id) }}" class="size-10 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-300 hover:text-primary hover:bg-primary/10 transition-all">
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-10 py-20 text-center opacity-40">
                                <span class="material-symbols-outlined text-6xl mb-2">database_off</span>
                                <p class="font-black text-xs uppercase tracking-widest">No recent data available</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
