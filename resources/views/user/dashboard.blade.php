@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     WELCOME HEADER
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-8">
    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[var(--color-text-primary)]">
        Welcome back, {{ explode(' ', $user->name)[0] }}
    </h2>
    <p class="text-small mt-1">Here's an overview of your progress today.</p>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     STATS GRID — 4 stat cards (matches controller output)
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

    {{-- Target Score --}}
    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-small text-xs font-semibold uppercase tracking-wider">Target Score</p>
                <p class="mt-2 text-3xl font-bold text-[var(--color-text-primary)]">
                    {{ number_format($targetScore, 1) }}
                    <span class="text-xs font-medium text-[var(--color-text-secondary)]">/ 9.0</span>
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-primary)]">target</span>
            </div>
        </div>
    </x-ui.card>

    {{-- Tests Taken --}}
    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-small text-xs font-semibold uppercase tracking-wider">Tests Taken</p>
                <p class="mt-2 text-3xl font-bold text-[var(--color-text-primary)]">{{ $testsTakenCount }}</p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-success)]">task_alt</span>
            </div>
        </div>
    </x-ui.card>

    {{-- Average Band --}}
    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-small text-xs font-semibold uppercase tracking-wider">Average Band</p>
                <p class="mt-2 text-3xl font-bold text-[var(--color-text-primary)]">
                    {{ $avgBandScore !== null ? number_format($avgBandScore, 1) : '—' }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-primary)]">trending_up</span>
            </div>
        </div>
    </x-ui.card>

    {{-- Days to Exam --}}
    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-small text-xs font-semibold uppercase tracking-wider">Days to Exam</p>
                <p class="mt-2 text-3xl font-bold text-[var(--color-text-primary)]">
                    {{ $daysToExam ?? '—' }}
                    @if($user->exam_date)
                        <span class="text-xs font-medium text-[var(--color-text-secondary)]">{{ $user->exam_date->format('M d') }}</span>
                    @endif
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,#F59E0B_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[#B45309]">schedule</span>
            </div>
        </div>
    </x-ui.card>

</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODULE BREAKDOWN + SCORE CHART (2-column)
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-10 grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Score Progression (2/3 width) --}}
    <x-ui.card class="lg:col-span-2">
        <x-slot:header>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-[var(--color-text-primary)]">Score Improvement</h3>
                    <p class="text-small text-xs mt-0.5">Your band scores across recent tests</p>
                </div>
            </div>
        </x-slot:header>

        @if(count($chartData) > 0)
            <div class="flex h-52 items-end gap-3">
                @foreach($chartData as $data)
                    <div class="group relative flex flex-1 flex-col items-center">
                        {{-- Tooltip --}}
                        <div class="absolute -top-7 rounded-[var(--radius-xs)] bg-[var(--color-text-primary)] px-2 py-1 text-[10px] font-semibold text-white opacity-0 transition-opacity group-hover:opacity-100">
                            {{ $data['score'] }}
                        </div>
                        {{-- Bar --}}
                        <div
                            class="w-full rounded-t-[var(--radius-sm)] transition-all duration-700
                                   {{ $loop->last ? 'bg-[var(--color-primary)]' : 'bg-[color-mix(in_srgb,var(--color-primary)_' . (20 + ($loop->index * 12)) . '%,transparent)]' }}"
                            style="height: {{ max($data['height'], 8) }}%"
                        ></div>
                        {{-- Label --}}
                        <span class="mt-2 text-[10px] font-semibold uppercase tracking-wide text-[var(--color-text-secondary)]">{{ $data['label'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex h-52 flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined mb-2 text-4xl text-[var(--color-divider)]">bar_chart</span>
                <p class="text-small text-sm">Complete a test to see your score progression</p>
            </div>
        @endif
    </x-ui.card>

    {{-- Module Breakdown (1/3 width) --}}
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-base font-bold text-[var(--color-text-primary)]">Module Breakdown</h3>
        </x-slot:header>

        <div class="space-y-5">
            @foreach($moduleBreakdown as $module)
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <span class="text-sm font-medium text-[var(--color-text-primary)]">{{ $module['name'] }}</span>
                        @if($module['score'] !== null)
                            <span class="text-sm font-semibold text-[var(--color-primary)]">{{ number_format($module['score'], 1) }}</span>
                        @else
                            <span class="text-sm text-[var(--color-text-secondary)]">N/A</span>
                        @endif
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-[var(--color-bg-secondary)]">
                        <div
                            class="h-full rounded-full transition-all duration-700 {{ $module['score'] !== null ? 'bg-[var(--color-primary)]' : 'bg-[var(--color-divider)]' }}"
                            style="width: {{ $module['percentage'] }}%"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <x-ui.button variant="outline" href="{{ route('user.history.index') }}" class="w-full text-xs">
                View Detailed Analysis
            </x-ui.button>
        </div>
    </x-ui.card>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     RECOMMENDED MOCK TESTS
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-10">
    <div class="mb-5 flex items-center justify-between">
        <h3 class="text-lg font-bold text-[var(--color-text-primary)]">Recommended Mock Tests</h3>
        <a href="{{ route('user.history.index') }}" class="text-sm font-semibold text-[var(--color-primary)] transition-opacity hover:opacity-80">
            View All
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($recommendedTests as $test)
            <x-ui.card class="flex flex-col hover-lift">
                <div class="mb-3 flex items-center gap-2">
                    <x-ui.badge variant="primary">{{ $test->exam_type }}</x-ui.badge>
                </div>

                <h4 class="text-base font-bold text-[var(--color-text-primary)]">{{ $test->title }}</h4>
                <p class="text-small mt-1 line-clamp-2 text-xs">
                    {{ $test->exam_type }} — Book {{ $test->book_number }} ({{ $test->year }})
                </p>

                <div class="mt-4 flex items-center gap-4 text-[var(--color-text-secondary)]">
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">timer</span>
                        <span class="text-xs font-medium">{{ $test->duration ?? '2h 45m' }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">menu_book</span>
                        <span class="text-xs font-medium">{{ $test->modules_count ?? 4 }} Modules</span>
                    </div>
                </div>

                <div class="mt-auto pt-5">
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="primary" class="w-full">
                            Start Preparation
                        </x-ui.button>
                    </form>
                </div>
            </x-ui.card>
        @empty
            <div class="col-span-full">
                <x-ui.card>
                    <x-ui.empty-state
                        icon="auto_stories"
                        title="No tests available"
                        description="No tests currently available. Check back later."
                    />
                </x-ui.card>
            </div>
        @endforelse
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     RECENT TEST HISTORY TABLE
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section>
    <x-ui.card :flush="true">
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-[var(--color-text-primary)]">Recent Test History</h3>
                <a href="{{ route('user.history.index') }}" class="text-sm font-semibold text-[var(--color-primary)] transition-opacity hover:opacity-80">
                    See All
                </a>
            </div>
        </x-slot:header>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-[var(--color-divider)]">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Test</th>
                        <th class="hidden px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:table-cell sm:px-6">Date</th>
                        <th class="hidden px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] md:table-cell sm:px-6">Modules</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttempts as $attempt)
                        <tr class="border-b border-[var(--color-divider)] last:border-b-0 transition-colors hover:bg-[var(--color-bg-secondary)]">
                            {{-- Test Title --}}
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                                        <span class="material-symbols-outlined text-base text-[var(--color-primary)]">task</span>
                                    </div>
                                    <span class="text-sm font-semibold text-[var(--color-text-primary)]">
                                        {{ $attempt->testSet->test->title ?? 'Practice Test' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="hidden px-5 py-4 text-sm text-[var(--color-text-secondary)] sm:table-cell sm:px-6">
                                {{ $attempt->created_at->format('M d, Y') }}
                            </td>

                            {{-- Module Pills --}}
                            <td class="hidden px-5 py-4 md:table-cell sm:px-6">
                                <div class="flex gap-1">
                                    @foreach(['L','R','W','S'] as $mod)
                                        <span class="flex size-6 items-center justify-center rounded-[var(--radius-xs)] bg-[var(--color-bg-secondary)] text-[10px] font-bold text-[var(--color-text-secondary)]">{{ $mod }}</span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-5 py-4 sm:px-6">
                                @if($attempt->status === 'completed')
                                    <x-ui.badge variant="success">Completed</x-ui.badge>
                                @else
                                    <x-ui.badge variant="pending">In Progress</x-ui.badge>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="px-5 py-4 sm:px-6">
                                <a href="{{ route('user.history.show', $attempt->id) }}"
                                   class="text-sm font-semibold text-[var(--color-primary)] transition-opacity hover:opacity-80">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center sm:px-6">
                                <x-ui.empty-state
                                    icon="history"
                                    title="No test history yet"
                                    description="Start your first mock test to see your history here."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</section>

@endsection
