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
@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <!-- Page Header -->
    <x-admin.page-header title="Performance Audit" description="Monitor candidate progress and analyze module-specific accuracy levels.">
        <x-slot:actions>
            <form method="GET" action="{{ route('admin.results.index') }}" class="relative group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">search</span>
                <input name="search" value="{{ request('search') }}" class="pl-11 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm w-64 focus:ring-2 focus:ring-primary/20 shadow-sm" placeholder="Search user or test...">
            </form>
            <x-admin.button variant="outline" icon="filter_list" size="md" />
        </x-slot:actions>
    </x-admin.page-header>

    @if($attempts->isEmpty())
        <x-admin.empty-state 
            title="No attempts recorded" 
            description="Ready for student submissions. Once candidates start taking tests, you'll see details here."
            icon="find_in_page"
            :actionHref="route('admin.results.index')"
            actionLabel="Refresh View"
            actionIcon="refresh"
        />
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
                                        <x-admin.badge type="success" label="Graded" />
                                    @elseif($attempt->status === 'in_progress')
                                        <x-admin.badge type="warning" label="Active" />
                                    @else
                                        <x-admin.badge type="info" :label="$attempt->status" />
                                    @endif
                                </td>
                                <td class="px-6 py-6 text-xs font-bold text-slate-500 tracking-tight">
                                    {{ $attempt->created_at->format('M d, Y') }}
                                    <span class="block opacity-60 text-[10px] font-black uppercase mt-0.5">{{ $attempt->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <x-admin.button :href="route('admin.results.show', $attempt->id)" variant="ghost" icon="chevron_right" size="icon" />
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
        <x-admin.stat-card 
            label="Total Attempts" 
            :value="number_format($attempts->total())" 
            icon="trending_up" 
            iconColor="primary" 
        />
        <x-admin.stat-card 
            label="Global Accuracy" 
            :value="$globalAccuracy ?? 'N/A'" 
            icon="history" 
            iconColor="purple" 
        />
        <x-admin.stat-card 
            label="Avg Time Spent" 
            :value="$avgTimeSpent ?? 'N/A'" 
            icon="timer" 
            iconColor="orange" 
        />
    </div>
</div>
@endsection

</div>
@endsection
