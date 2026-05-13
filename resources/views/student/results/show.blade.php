@extends('layouts.student')

@section('title', 'Exam Results')

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)]">
        <a href="{{ route('dashboard') }}" class="hover:text-[var(--color-primary)] transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <a href="{{ route('user.history.index') }}" class="hover:text-[var(--color-primary)] transition-colors">History</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="font-semibold text-[var(--color-text-primary)]">Results</span>
    </nav>
@endsection

@section('content')

@php
    $passed = ($attempt->overall_band ?? 0) >= 6.5;
    $scoreBg = $passed
        ? 'bg-[color-mix(in_srgb,var(--color-success)_6%,transparent)] border-[color-mix(in_srgb,var(--color-success)_20%,transparent)]'
        : 'bg-[color-mix(in_srgb,var(--color-error)_6%,transparent)] border-[color-mix(in_srgb,var(--color-error)_20%,transparent)]';
    $scoreColor = $passed ? 'text-[var(--color-success)]' : 'text-[var(--color-error)]';
@endphp

{{-- ═══════════════════════════════════════════════════════════════════════════
     TOP SCORE BANNER
     ═══════════════════════════════════════════════════════════════════════════ --}}
<x-ui.card :flush="true" class="{{ $scoreBg }}">
    <div class="flex flex-col items-center justify-center py-10 sm:py-14 text-center">
        <span class="material-symbols-outlined text-5xl {{ $scoreColor }} mb-3">military_tech</span>
        <h1 class="text-6xl sm:text-7xl font-bold {{ $scoreColor }} tabular-nums">
            {{ $attempt->overall_band !== null ? number_format($attempt->overall_band, 1) : 'N/A' }}
        </h1>
        <p class="mt-3 text-base font-medium {{ $scoreColor }}">
            @if($passed) Congratulations, you passed! @else Keep practicing, you can do better! @endif
        </p>
        <p class="mt-1 text-sm text-[var(--color-text-secondary)]">
            {{ $attempt->testSet->test->title ?? 'Mock Exam' }} &bull; {{ $attempt->created_at->format('M j, Y') }}
        </p>
    </div>
</x-ui.card>

{{-- ═══════════════════════════════════════════════════════════════════════════
     STATS ROW — 3 columns
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-ui.card>
        <div class="flex items-center gap-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-primary)]">quiz</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Total Questions</p>
                <p class="text-2xl font-bold text-[var(--color-text-primary)]">{{ $totalQuestions ?? 40 }}</p>
            </div>
        </div>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center gap-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-success)]">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Correct Answers</p>
                <p class="text-2xl font-bold text-[var(--color-success)]">{{ $correctCount ?? 0 }}</p>
            </div>
        </div>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center gap-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-error)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-error)]">cancel</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Incorrect</p>
                <p class="text-2xl font-bold text-[var(--color-error)]">{{ $incorrectCount ?? 0 }}</p>
            </div>
        </div>
    </x-ui.card>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     DETAILED BREAKDOWN
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mt-8">
    <x-ui.card :flush="true">
        <x-slot:header>
            <h3 class="text-base font-bold text-[var(--color-text-primary)]">Detailed Breakdown</h3>
        </x-slot:header>

        <div class="divide-y divide-[var(--color-divider)]">
            @forelse($questions ?? [] as $index => $question)
                @php
                    $isCorrect = ($question['selected'] ?? null) === ($question['correct'] ?? null);
                @endphp
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:px-6">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <span class="flex size-7 shrink-0 items-center justify-center rounded-[var(--radius-xs)] bg-[var(--color-bg-secondary)] text-xs font-bold text-[var(--color-text-secondary)]">{{ $index + 1 }}</span>
                        <p class="text-sm font-medium text-[var(--color-text-primary)] truncate">{{ $question['text'] ?? 'Question ' . ($index + 1) }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <x-ui.badge :variant="$isCorrect ? 'success' : 'error'">
                            {{ $question['selected'] ?? '—' }}
                        </x-ui.badge>
                        @if(!$isCorrect)
                            <span class="material-symbols-outlined text-sm text-[var(--color-text-secondary)]">arrow_forward</span>
                            <x-ui.badge variant="success">{{ $question['correct'] ?? '—' }}</x-ui.badge>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 sm:px-6">
                    <x-ui.empty-state icon="fact_check" title="No breakdown available" description="Detailed question breakdown is not available for this attempt." />
                </div>
            @endforelse
        </div>
    </x-ui.card>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     ACTIONS
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
    <x-ui.button variant="secondary" href="{{ route('user.history.index') }}">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Back to History
    </x-ui.button>
    <x-ui.button variant="primary" href="{{ route('dashboard') }}">
        <span class="material-symbols-outlined text-sm">dashboard</span>
        Dashboard
    </x-ui.button>
</section>

@endsection
