@extends('layouts.admin')

@section('title', 'Test Results')

@section('content')
<!-- Topbar -->
<header class="h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-40 px-4 sm:px-8 flex items-center justify-between">
    <form method="GET" action="{{ route('admin.results.index') }}" class="flex items-center gap-4 flex-1 max-w-xl">
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl leading-none">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl py-2 pl-10 pr-10 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400" placeholder="Search user or test..." type="text"/>
            @if(request('search'))
                <a href="{{ route('admin.results.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500">
                    <span class="material-symbols-outlined text-xl">close</span>
                </a>
            @endif
        </div>
    </form>
    <div class="flex items-center gap-4">
        <button class="size-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-primary transition-colors hidden sm:flex">
            <span class="material-symbols-outlined">notifications</span>
        </button>
        <button class="size-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-primary transition-colors hidden sm:flex">
            <span class="material-symbols-outlined">help</span>
        </button>
        <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 mx-1 sm:mx-2 hidden sm:block"></div>
        <a href="{{ route('admin.tests.create') }}" class="gradient-primary text-white text-sm font-bold px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl shadow-elevated hover:opacity-90 transition-opacity whitespace-nowrap">
            New Mock Test
        </a>
    </div>
</header>

<!-- Page Content -->
<div class="p-4 sm:p-10 flex-1 flex flex-col max-w-[1280px] mx-auto w-full">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-custom uppercase tracking-wider mb-2">
                <span>Main</span>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-primary">Results</span>
            </nav>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Test Results</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm sm:text-lg">Detailed overview of all user attempts and statistics.</p>
        </div>
        <div class="flex flex-wrap gap-2 sm:gap-3">
            <button class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-slate-50 transition-colors">
                <span class="material-symbols-outlined text-lg">calendar_today</span>
                <span>All Time</span>
                <span class="material-symbols-outlined text-lg">expand_more</span>
            </button>
            <button class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-slate-50 transition-colors">
                <span class="material-symbols-outlined text-lg">filter_list</span>
                <span>Filters</span>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    @if($attempts->isEmpty())
        <!-- Empty State Content -->
        <div class="flex-1 flex flex-col items-center justify-center py-12 px-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-soft min-h-[400px]">
            <div class="relative mb-8">
                <!-- Decorative Elements -->
                <div class="absolute -top-6 -left-6 size-12 bg-primary/10 rounded-full animate-pulse"></div>
                <div class="absolute -bottom-4 -right-4 size-16 bg-primary/10 rounded-full"></div>
                
                <!-- Main Illustration Concept -->
                <div class="relative w-64 sm:w-72 h-40 sm:h-48 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden">
                    <div class="flex flex-col items-center opacity-40">
                        <span class="material-symbols-outlined text-5xl sm:text-7xl text-slate-300 dark:text-slate-600 mb-2">find_in_page</span>
                        <div class="w-24 sm:w-32 h-2 bg-slate-200 dark:bg-slate-700 rounded-full mb-2"></div>
                        <div class="w-16 sm:w-24 h-2 bg-slate-200 dark:bg-slate-700 rounded-full"></div>
                    </div>
                    
                    <!-- Floating status tags as decorative -->
                    <div class="absolute top-4 sm:top-8 right-6 sm:right-12 bg-white dark:bg-slate-700 shadow-lg px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg text-[10px] font-bold text-slate-400 rotate-12 border border-slate-100 dark:border-slate-600">
                        NO DATA
                    </div>
                    <div class="absolute bottom-6 sm:bottom-10 left-6 sm:left-10 bg-white dark:bg-slate-700 shadow-lg px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg text-[10px] font-bold text-slate-400 -rotate-6 border border-slate-100 dark:border-slate-600">
                        0 ATTEMPTS
                    </div>
                </div>
            </div>
            
            <div class="max-w-md text-center">
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-3">No test attempts found</h3>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed mb-8 sm:mb-10 text-base sm:text-lg">
                    We couldn't find any results matching your current filters or account activity. Wait for users to complete simulations.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                    <a href="{{ route('admin.results.index') }}" class="w-full sm:w-auto min-w-[180px] flex items-center justify-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-6 py-3 sm:py-3.5 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95 text-sm sm:text-base">
                        <span class="material-symbols-outlined text-xl">refresh</span>
                        <span>Refresh Dashboard</span>
                    </a>
                    @if(request('search'))
                        <a href="{{ route('admin.results.index') }}" class="w-full sm:w-auto min-w-[180px] flex items-center justify-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-6 py-3 sm:py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 text-sm sm:text-base">
                            <span class="material-symbols-outlined text-xl">filter_list_off</span>
                            <span>Clear Filters</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- Results Table -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Candidate / User</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Test Details</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date &amp; Time</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($attempts as $attempt)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-700 dark:text-indigo-400 font-bold overflow-hidden shrink-0">
                                            @if($attempt->user)
                                                {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                                            @else
                                                <span class="material-symbols-outlined">person_off</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $attempt->user->name ?? 'Unknown User' }}</p>
                                            <p class="text-xs text-slate-500">{{ $attempt->user->email ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $attempt->test->title ?? 'Unknown Test' }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Collection: {{ optional($attempt->test->collection)->title ?? 'Standalone' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($attempt->status === 'completed')
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Completed</span>
                                        </div>
                                    @elseif($attempt->status === 'in_progress')
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span class="text-sm font-medium text-amber-600 dark:text-amber-400">In Progress</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ ucfirst($attempt->status) }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $attempt->created_at->format('M d, Y') }}
                                    <span class="text-xs text-slate-400 block mt-0.5">{{ $attempt->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.results.show', $attempt->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-primary dark:text-indigo-400 hover:bg-primary hover:text-white dark:hover:bg-primary rounded-xl text-xs font-bold transition-colors">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($attempts->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-sm text-slate-500 hidden sm:block">Showing {{ $attempts->firstItem() ?? 0 }} to {{ $attempts->lastItem() ?? 0 }} of {{ $attempts->total() }} results</p>
                    <div class="w-full sm:w-auto">
                        {{ $attempts->links() }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Footer Summary (Analytics Strip) -->
    <div class="mt-auto pt-8 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 opacity-80 pointer-events-none">
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-4">
            <div class="size-10 sm:size-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 shrink-0">
                <span class="material-symbols-outlined">trending_up</span>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider leading-none">Total Attempts</p>
                <p class="text-lg sm:text-xl font-extrabold mt-1 text-slate-900 dark:text-white">{{ number_format($attempts->total() ?? 0) }}</p>
            </div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-4">
            <div class="size-10 sm:size-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 shrink-0">
                <span class="material-symbols-outlined">history</span>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider leading-none">Global Accuracy</p>
                <p class="text-lg sm:text-xl font-extrabold mt-1 text-slate-900 dark:text-white">N/A</p>
            </div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-4">
            <div class="size-10 sm:size-12 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 shrink-0">
                <span class="material-symbols-outlined">timer</span>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider leading-none">Avg Time Spent</p>
                <p class="text-lg sm:text-xl font-extrabold mt-1 text-slate-900 dark:text-white">N/A</p>
            </div>
        </div>
    </div>
</div>
@endsection
