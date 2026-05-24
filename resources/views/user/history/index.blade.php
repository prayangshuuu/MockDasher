@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'My Test History')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90 opacity-50" alt=">" />
    <span class="font-semibold text-slate-900 dark:text-white">Test History</span>
</nav>
@endsection

@section('content')

{{-- Header --}}
<section class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">My Test History</h2>
        <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Review all your past mock exams and track your progress.</p>
    </div>
    <div class="shrink-0">
        <a href="{{ route('user.tests.index') }}" 
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
            <img src="/storage/asset/icons/start.svg" class="w-4 h-4 invert brightness-0" alt="+" />
            Take New Test
        </a>
    </div>
</section>

{{-- Quick Stats --}}
<section class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-3">
    {{-- STAT 1: AVERAGE BAND SCORE --}}
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-4 hover:border-indigo-500/20 transition-all duration-250">
        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50">
            <img src="/storage/asset/icons/bar-chart.svg" class="w-6 h-6 filter-indigo-600 dark:invert" alt="Stats" />
        </div>
        <div>
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Average Band Score</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                {{ $stats['averageBandScore'] !== null ? number_format($stats['averageBandScore'], 1) : 'N/A' }}
            </p>
        </div>
    </div>

    {{-- STAT 2: TESTS COMPLETED --}}
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-4 hover:border-emerald-500/20 transition-all duration-250">
        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 border border-emerald-100 dark:border-emerald-900/50">
            <img src="/storage/asset/icons/check-circle.svg" class="w-6 h-6 filter-emerald-600 dark:invert" alt="Completed" />
        </div>
        <div>
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Tests Completed</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $stats['testsCompleted'] }}</p>
        </div>
    </div>

    {{-- STAT 3: STRONGEST MODULE --}}
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-4 hover:border-violet-500/20 transition-all duration-250">
        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-950/40 text-violet-500 border border-violet-100 dark:border-violet-900/50">
            <img src="/storage/asset/icons/bolt.svg" class="w-6 h-6 filter-violet-600 dark:invert" alt="Strongest" />
        </div>
        <div>
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Strongest Module</p>
            <p class="text-lg font-black text-slate-900 dark:text-white mt-0.5 flex items-center gap-1.5">
                @if($stats['strongestModule']['name'])
                    {{ $stats['strongestModule']['name'] }}
                    <span class="inline-flex items-center text-xs font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded border border-emerald-100 dark:border-emerald-900/40">
                        {{ number_format($stats['strongestModule']['score'], 1) }}
                    </span>
                @else
                    N/A
                @endif
            </p>
        </div>
    </div>
</section>

{{-- History Card/Table --}}
<section>
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        {{-- Card Header --}}
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Test Attempts</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-3.5 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Test Info</th>
                        <th class="hidden px-6 py-3.5 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 sm:table-cell">Status</th>
                        <th class="hidden px-6 py-3.5 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 md:table-cell">Date &amp; Time Started</th>
                        <th class="px-6 py-3.5 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Overall Score</th>
                        <th class="px-6 py-3.5 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($attempts as $attempt)
                    <tr class="transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-900/50">
                        {{-- Test name --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50">
                                    <img src="/storage/asset/icons/library.svg" class="w-5 h-5 filter-indigo-600 dark:invert" alt="Test" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                        IELTS {{ $attempt->testSet->test->exam_type ?? 'Academic' }} — Vol. {{ $attempt->testSet->test->book_number ?? '' }}
                                    </p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate mt-0.5">
                                        Set {{ $attempt->testSet->set_number ?? '1' }}
                                    </p>
                                </div>
                            </div>
                        </td>
 
                        {{-- Status --}}
                        <td class="hidden px-6 py-4 sm:table-cell">
                            @if($attempt->status === 'completed')
                                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 px-2.5 py-1 rounded-full border border-emerald-100 dark:border-emerald-800/40">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    Completed
                                </span>
                            @elseif($attempt->status === 'in_progress')
                                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-sky-500 bg-sky-50 dark:bg-sky-950/30 px-2.5 py-1 rounded-full border border-sky-100 dark:border-sky-800/40">
                                    <span class="size-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                    In Progress
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-slate-500 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700/50">
                                    {{ ucfirst($attempt->status) }}
                                </span>
                            @endif
                        </td>
 
                        <td class="hidden px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 md:table-cell">
                            {{ ($attempt->started_at ?? $attempt->created_at)->format('M d, Y \a\t g:i A') }}
                        </td>

                        {{-- Overall score --}}
                        <td class="px-6 py-4">
                            @if($attempt->status === 'completed' && $attempt->overall_band !== null)
                                <span class="text-sm font-black text-primary bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                    {{ number_format($attempt->overall_band, 1) }}
                                </span>
                            @else
                                <span class="text-sm font-semibold text-slate-400 dark:text-slate-500">—</span>
                            @endif
                        </td>

                        {{-- Action buttons --}}
                        <td class="px-6 py-4 text-right">
                            @if($attempt->status === 'completed')
                                <a href="{{ route('user.history.show', $attempt->id) }}" 
                                   class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150">
                                    <img src="/storage/asset/icons/eye.svg" class="w-3.5 h-3.5 dark:invert opacity-75" alt="View" />
                                    Review
                                </a>
                            @else
                                <a href="{{ route('user.tests.start', $attempt->testSet->test_id) }}" 
                                   class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-hover text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-soft transition-all duration-150">
                                    <img src="/storage/asset/icons/start.svg" class="w-3.5 h-3.5 invert brightness-0" alt="Resume" />
                                    Resume
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="size-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                    <img src="/storage/asset/icons/history.svg" class="w-8 h-8 opacity-30 dark:invert" alt="Empty" />
                                </div>
                                <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">No mock tests taken yet</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm leading-relaxed">Start your very first academic or general training mock exam to check details and monitor progress.</p>
                                <div class="mt-5">
                                    <a href="{{ route('user.tests.index') }}" 
                                       class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
                                        Start Your First Test
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($attempts->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">
                    Showing <span class="font-bold text-slate-700 dark:text-slate-300">{{ $attempts->firstItem() }}</span> to <span class="font-bold text-slate-700 dark:text-slate-300">{{ $attempts->lastItem() }}</span> of <span class="font-bold text-slate-700 dark:text-slate-300">{{ $attempts->total() }}</span> attempts
                </p>
                <div class="flex items-center justify-end">
                    {{ $attempts->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

@endsection
