@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')

<section class="mb-8">
    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
        Welcome back, {{ $user->first_name ?: explode(' ', $user->name)[0] }} 👋
    </h2>
    <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Here's your IELTS preparation overview.</p>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     STATS GRID — 4 stat cards
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

    {{-- Target Score --}}
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Target Score</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ number_format($targetScore, 1) }}<span class="text-lg font-medium text-slate-400">/9</span>
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30">
                <img src="/storage/asset/icons/explore.svg" class="w-5 h-5" alt="Target" />
            </div>
        </div>
    </div>

    {{-- Tests Taken --}}
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tests Taken</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ $testsTakenCount }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                <img src="/storage/asset/icons/check-circle.svg" class="w-5 h-5" alt="Tests" />
            </div>
        </div>
    </div>

    {{-- Average Band --}}
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Average Band</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ $avgBandScore !== null ? number_format($avgBandScore, 1) : '—' }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 dark:bg-sky-900/30">
                <img src="/storage/asset/icons/trending-up.svg" class="w-5 h-5" alt="Band" />
            </div>
        </div>
    </div>

    {{-- Days to Exam --}}
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Days to Exam</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ $daysToExam ?? '—' }}<span class="text-lg font-medium text-slate-400">{{ $user->exam_date ? ' days' : '' }}</span>
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/30">
                <img src="/storage/asset/icons/history.svg" class="w-5 h-5" alt="Exam" />
            </div>
        </div>
    </div>

</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     SCORE CHART + MODULE BREAKDOWN
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-10 grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Score Progression (2/3 width) --}}
    <div class="lg:col-span-2 bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Score Improvement</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Your band scores across recent tests</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            @if(count($chartData) > 0)
                <div class="flex h-52 items-end gap-3">
                    @foreach($chartData as $data)
                        <div class="group relative flex flex-1 flex-col items-center">
                            <div class="absolute -top-7 left-1/2 -translate-x-1/2 rounded-lg bg-slate-900 dark:bg-slate-700 px-2 py-1 text-[10px] font-bold text-white opacity-0 transition-opacity group-hover:opacity-100 whitespace-nowrap pointer-events-none shadow-lg z-10">
                                Band {{ $data['score'] > 0 ? number_format($data['score'], 1) : 'N/A' }}
                            </div>
                            <div
                                class="w-full rounded-t-xl transition-all duration-700 {{ $loop->last ? 'bg-primary' : 'bg-primary/25 dark:bg-primary/20' }}"
                                style="height: {{ max($data['height'], 8) }}%"
                            ></div>
                            <span class="mt-2 text-[10px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $data['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex h-52 flex-col items-center justify-center text-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-700">bar_chart</span>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Complete a test to see your score progression</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Module Breakdown (1/3 width) --}}
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Module Scores</h3>
        </div>
        <div class="p-6 space-y-5">
            @foreach($moduleBreakdown as $module)
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $module['name'] }}</span>
                        @if($module['score'] !== null)
                            <span class="text-sm font-bold text-primary">{{ number_format($module['score'], 1) }}</span>
                        @else
                            <span class="text-sm text-slate-400 dark:text-slate-500">N/A</span>
                        @endif
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        <div
                            class="h-full rounded-full transition-all duration-700 {{ $module['score'] !== null ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-700' }}"
                            style="width: {{ $module['percentage'] }}%"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-6 pb-6">
            <a href="{{ route('user.history.index') }}"
               class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors">
                View Detailed Analysis
            </a>
        </div>
    </div>

</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     RECOMMENDED MOCK TESTS
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-10">
    <div class="mb-5 flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Recommended Mock Tests</h3>
        <a href="{{ route('user.tests.index') }}" class="text-sm font-bold text-primary hover:text-primary-hover transition-colors">
            View All
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($recommendedTests as $test)
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col hover:shadow-premium hover:border-primary/30 dark:hover:border-primary/30 transition-all duration-200">
                <div class="h-1 bg-gradient-to-r from-primary to-violet-500"></div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-primary bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-full border border-indigo-100 dark:border-indigo-800">
                            {{ $test->exam_type }}
                        </span>
                        @if($test->year)
                            <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">{{ $test->year }}</span>
                        @endif
                    </div>
                    <h4 class="text-base font-bold text-slate-900 dark:text-white">IELTS {{ $test->book_number ?? '' }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                        {{ $test->exam_type }} — Volume {{ $test->book_number }} ({{ $test->year }})
                    </p>
                    <div class="mt-4 flex items-center gap-4 text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-1.5">
                            <img src="/storage/asset/icons/history.svg" class="w-4 h-4 opacity-50" alt="Duration" />
                            <span class="text-xs font-medium">{{ $test->duration ?? '2h 45m' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <img src="/storage/asset/icons/section.svg" class="w-4 h-4 opacity-50" alt="Modules" />
                            <span class="text-xs font-medium">{{ $test->modules_count ?? 4 }} Modules</span>
                        </div>
                    </div>
                    <div class="mt-auto pt-5">
                        <form action="{{ route('user.tests.start', $test->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
                                <img src="/storage/asset/icons/start.svg" class="w-4 h-4 invert brightness-0" alt="Start" />
                                Start Test
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft px-6 py-10 text-center">
                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-700">auto_stories</span>
                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mt-4">No tests available</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">No tests currently available. Check back later.</p>
                </div>
            </div>
        @endforelse
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     RECENT TEST HISTORY TABLE
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section>
    <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Test History</h3>
                <a href="{{ route('user.history.index') }}" class="text-sm font-bold text-primary hover:text-primary-hover transition-colors">
                    See All
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Test</th>
                        <th class="hidden px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">Date</th>
                        <th class="hidden px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 md:table-cell">Modules</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($recentAttempts as $attempt)
                        <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">

                            {{-- Test --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-xs font-bold text-primary overflow-hidden border border-indigo-100 dark:border-indigo-800">
                                        <img src="/storage/asset/icons/bar-chart.svg" class="w-4 h-4 opacity-60" alt="Test" />
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">
                                        IELTS {{ $attempt->testSet->test->book_number ?? '' }} {{ $attempt->testSet->test->exam_type ?? 'Test' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="hidden px-6 py-4 text-sm text-slate-500 dark:text-slate-400 sm:table-cell">
                                {{ $attempt->created_at->format('M d, Y h:i A') }}
                            </td>

                            {{-- Modules --}}
                            <td class="hidden px-6 py-4 md:table-cell">
                                <div class="flex gap-1">
                                    @foreach(['L','R','W','S'] as $mod)
                                        <span class="flex size-6 items-center justify-center rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $mod }}</span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @if(($attempt->status ?? 'pending') === 'completed')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-medium text-emerald-800 dark:text-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-medium text-amber-800 dark:text-amber-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        In Progress
                                    </span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                @if($attempt->status === 'completed')
                                    <a href="{{ route('user.history.show', $attempt->id) }}"
                                       class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 hover:border-primary/50 dark:hover:border-primary/50 bg-white dark:bg-surface-dark hover:bg-primary/5 dark:hover:bg-primary/10">
                                        <img src="/storage/asset/icons/eye.svg" class="w-4 h-4" alt="View" />
                                        Review
                                    </a>
                                @else
                                    <a href="{{ route('user.tests.start', $attempt->testSet->test_id) }}"
                                       class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 hover:border-primary/50 dark:hover:border-primary/50 bg-white dark:bg-surface-dark hover:bg-primary/5 dark:hover:bg-primary/10">
                                        <img src="/storage/asset/icons/start.svg" class="w-4 h-4 opacity-60" alt="Resume" />
                                        Resume
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="max-w-xs mx-auto">
                                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-700">monitoring</span>
                                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mt-4">No test history yet</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Start your first mock test to see your history here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
