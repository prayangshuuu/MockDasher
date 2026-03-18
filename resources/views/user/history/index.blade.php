@extends('layouts.student')

@section('title', 'My Test History')

@section('content')
<main class="max-w-7xl mx-auto py-4">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight dark:text-white">My Test History</h1>
            <p class="text-slate-500 mt-1 max-w-lg dark:text-slate-400">Review all your past mock exams and track your progress across different academic modules.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="indigo-violet-gradient text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/20 active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-xl">add</span>
            Take New Test
        </a>
    </div>

    <!-- Quick Stats Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-soft border-l-4 border-indigo-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Average Band Score</p>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['averageBandScore'], 1) }}</span>
                <span class="text-indigo-600 font-bold text-sm flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-xs">trending_up</span>
                    {{ $stats['trend'] }}
                </span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-soft border-l-4 border-tertiary">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tests Completed</p>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ $stats['testsCompleted'] }}</span>
                <span class="text-slate-400 font-medium text-sm">/ 20 Target</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-soft border-l-4 border-emerald-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Strongest Module</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $stats['strongestModule']['name'] }}</span>
                <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 text-[10px] font-bold rounded uppercase">
                    {{ number_format($stats['strongestModule']['score'], 1) }} High
                </span>
            </div>
        </div>
    </div>

    <!-- History Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-soft border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Attempts</h2>
            <div class="flex gap-2">
                <button class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 rounded-lg transition-colors">Filter</button>
                <button class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 rounded-lg transition-colors">Export CSV</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Test Information</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date Taken</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Band Score</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($attempts as $attempt)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600">
                                    <span class="material-symbols-outlined">description</span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $attempt->test->title ?? 'IELTS Mock Test' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $attempt->testSet->title ?? 'Full Simulation' }} • Part of Set #{{ $attempt->test_set_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($attempt->status === 'completed')
                                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 text-xs font-bold rounded-full">Completed</span>
                            @elseif($attempt->status === 'in_progress')
                                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-600 text-xs font-bold rounded-full">In Progress</span>
                            @else
                                <span class="px-3 py-1 bg-slate-50 dark:bg-slate-800 text-slate-500 text-xs font-bold rounded-full">{{ ucfirst($attempt->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600 dark:text-slate-400 font-medium">
                            {{ $attempt->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-5">
                            @if($attempt->status === 'completed')
                                <div class="w-10 h-10 rounded-full border-2 border-indigo-600 flex items-center justify-center text-indigo-600 font-extrabold text-sm bg-indigo-50 dark:bg-indigo-900/20">
                                    {{ number_format($attempt->overall_band, 1) }}
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full border-2 border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 font-extrabold text-sm">
                                    —
                                </div>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($attempt->status === 'completed')
                                <a href="{{ route('user.history.show', $attempt->id) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Review Details</a>
                            @else
                                <a href="#" class="px-4 py-2 bg-slate-900 dark:bg-white dark:text-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition-colors">Resume</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 px-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-32 h-32 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-6xl text-slate-300 dark:text-slate-600">history</span>
                                </div>
                                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">No tests taken yet</h3>
                                <p class="text-slate-500 dark:text-slate-400 max-w-sm mb-8">Start your first mock exam today to track your progress and get detailed AI-powered feedback.</p>
                                <a href="{{ route('dashboard') }}" class="indigo-violet-gradient text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-indigo-500/20 active:scale-95 transition-transform inline-block">
                                    Start Your First Test
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($attempts->hasPages())
    <div class="mt-6 flex justify-between items-center px-4">
        <p class="text-xs text-slate-500 font-medium dark:text-slate-400">
            Showing <span class="text-slate-900 dark:text-white">{{ $attempts->firstItem() }}-{{ $attempts->lastItem() }}</span> of <span class="text-slate-900 dark:text-white">{{ $attempts->total() }}</span> attempts
        </p>
        <div class="flex gap-1">
            {{-- Custom Pagination Links can be implemented here if needed, or use default --}}
            {{ $attempts->links() }}
        </div>
    </div>
    @endif
</main>
@endsection
