@extends('layouts.admin')

@section('title', 'Test Results')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <span class="text-slate-900 dark:text-white font-medium">Results</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Performance Audit</h2>
            <p class="text-slate-500 dark:text-slate-400 text-base">Monitor candidate progress and analyze module-specific accuracy levels.</p>
        </div>
        <div class="flex items-center gap-4">
            <form method="GET" action="{{ route('admin.results.index') }}" class="relative group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">search</span>
                <input name="search" value="{{ request('search') }}" class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm w-64 focus:ring-2 focus:ring-primary/20 shadow-sm" placeholder="Search user or test...">
            </form>
            <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-2.5 rounded-xl text-slate-400 hover:text-primary transition-colors shadow-sm">
                <span class="material-symbols-outlined">filter_list</span>
            </button>
        </div>
    </div>

    @if($attempts->isEmpty())
        <div class="glass-card rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800 p-20 text-center">
            <div class="size-20 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-4xl text-slate-300">find_in_page</span>
            </div>
            <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">No attempts recorded</h3>
            <p class="text-slate-500 font-medium mb-8">Ready for student submissions. Once candidates start taking tests, you'll see details here.</p>
            <a href="{{ route('admin.results.index') }}" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:scale-105 transition-all">
                Refresh View
            </a>
        </div>
    @else
        <!-- Results Table -->
        <div class="glass-card rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Candidate Information</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Test Details</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Outcome</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Attempt Date</th>
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        @foreach($attempts as $attempt)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="size-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center font-black text-xl group-hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($attempt->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-900 dark:text-white">{{ $attempt->user->name ?? 'Unknown Student' }}</span>
                                            <span class="text-xs font-medium text-slate-400">{{ $attempt->user->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 font-bold text-slate-700 dark:text-slate-300">
                                    {{ $attempt->test->title ?? 'IELTS Mock Exam' }}
                                    <span class="block text-[10px] font-black text-slate-400 uppercase mt-0.5">{{ $attempt->test->exam_type ?? 'Simulation' }}</span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if($attempt->status === 'completed')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/20">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Graded</span>
                                        </div>
                                    @elseif($attempt->status === 'in_progress')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/20">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600">Active</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $attempt->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-6 text-xs font-bold text-slate-500 tracking-tight">
                                    {{ $attempt->created_at->format('M d, Y') }}
                                    <span class="block opacity-60 text-[10px] font-black uppercase mt-0.5">{{ $attempt->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <a href="{{ route('admin.results.show', $attempt->id) }}" class="size-10 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-300 hover:text-primary hover:bg-primary/10 transition-all opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined">chevron_right</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($attempts->hasPages())
                <div class="px-10 py-6 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Page {{ $attempts->currentPage() }} of {{ $attempts->lastPage() }}</p>
                    <div class="scale-90">
                        {{ $attempts->links() }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Aggregate Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6">
            <div class="size-16 rounded-[1.5rem] bg-indigo-50 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">trending_up</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Attempts</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($attempts->total()) }}</p>
            </div>
        </div>
        
        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6">
            <div class="size-16 rounded-[1.5rem] bg-purple-50 flex items-center justify-center text-purple-600">
                <span class="material-symbols-outlined text-3xl">history</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Global Accuracy</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">74%</p>
            </div>
        </div>
        
        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6">
            <div class="size-16 rounded-[1.5rem] bg-orange-50 flex items-center justify-center text-orange-600">
                <span class="material-symbols-outlined text-3xl">timer</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Avg Time Spent</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">42m</p>
            </div>
        </div>
    </div>
</div>
@endsection
