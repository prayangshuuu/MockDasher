@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'Test Result — Attempt #' . $attempt->id)

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)]">
        <a href="{{ route('dashboard') }}" class="hover:text-[var(--color-primary)] transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <a href="{{ route('user.history.index') }}" class="hover:text-[var(--color-primary)] transition-colors">History</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="font-semibold text-[var(--color-text-primary)]">Attempt #{{ $attempt->id }}</span>
    </nav>
@endsection

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- ═══════════════════════════════════════════════════════════════════════
         SCORE HERO
         ═══════════════════════════════════════════════════════════════════════ --}}
    <x-ui.card>
        <div class="flex flex-col items-center gap-8 md:flex-row">
            {{-- Score Circle --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="flex size-36 items-center justify-center rounded-full border-4 border-[var(--color-primary)]">
                    <div class="text-center">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)]">Band Score</p>
                        <p class="text-5xl font-bold text-[var(--color-text-primary)] tabular-nums leading-none mt-1">
                            {{ $attempt->overall_band !== null ? number_format($attempt->overall_band, 1) : 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="mt-3">
                    @if($attempt->status === 'completed')
                        <x-ui.badge variant="success">Completed</x-ui.badge>
                    @else
                        <x-ui.badge variant="pending">{{ ucfirst($attempt->status) }}</x-ui.badge>
                    @endif
                </div>
            </div>

            {{-- Test Info --}}
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-2xl sm:text-3xl font-bold text-[var(--color-text-primary)]">
                    IELTS {{ $attempt->testSet->test->book_number ?? '' }} ({{ $attempt->testSet->test->exam_type ?? 'Mock Exam' }})
                </h2>
                <p class="mt-1 text-sm text-[var(--color-text-secondary)]">
                    {{ $attempt->testSet->test->exam_type ?? 'Academic' }} Simulation &bull; Book {{ $attempt->testSet->test->book_number ?? '?' }}
                </p>

                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 border-t border-[var(--color-divider)] pt-5">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)] mb-1">Date</p>
                        <p class="text-sm font-semibold text-[var(--color-text-primary)]">{{ $attempt->created_at->format('M j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)] mb-1">Time Spent</p>
                        <p class="text-sm font-semibold text-[var(--color-text-primary)]">{{ $attempt->time_spent ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)] mb-1">Candidate</p>
                        <p class="text-sm font-semibold text-[var(--color-text-primary)]">MD-{{ str_pad($attempt->user_id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- ═══════════════════════════════════════════════════════════════════════
         MODULE BREAKDOWN GRID
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

        {{-- Reading --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                        <span class="material-symbols-outlined text-xl text-[var(--color-primary)]">auto_stories</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[var(--color-text-primary)]">Reading</h4>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)]">Academic Passage Analysis</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-[var(--color-primary)] tabular-nums">{{ $attempt->reading_band !== null ? number_format($attempt->reading_band, 1) : 'N/A' }}</span>
            </div>
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-[var(--color-bg-secondary)]">
                <div class="h-full rounded-full bg-[var(--color-primary)] transition-all duration-700" style="width: {{ ($attempt->reading_band ?? 0) * 11 }}%"></div>
            </div>
            <p class="mt-2 text-xs text-[var(--color-text-secondary)]">Correct: {{ $attempt->reading_score !== null ? $attempt->reading_score . '/40' : 'N/A' }}</p>
        </x-ui.card>

        {{-- Listening --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)]">
                        <span class="material-symbols-outlined text-xl text-[var(--color-success)]">headphones</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[var(--color-text-primary)]">Listening</h4>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)]">Audio Comprehension</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-[var(--color-success)] tabular-nums">{{ $attempt->listening_band !== null ? number_format($attempt->listening_band, 1) : 'N/A' }}</span>
            </div>
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-[var(--color-bg-secondary)]">
                <div class="h-full rounded-full bg-[var(--color-success)] transition-all duration-700" style="width: {{ ($attempt->listening_band ?? 0) * 11 }}%"></div>
            </div>
            <p class="mt-2 text-xs text-[var(--color-text-secondary)]">Correct: {{ $attempt->listening_score !== null ? $attempt->listening_score . '/40' : 'N/A' }}</p>
        </x-ui.card>

        {{-- Writing --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,#F59E0B_10%,transparent)]">
                        <span class="material-symbols-outlined text-xl text-[#B45309]">edit_square</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[var(--color-text-primary)]">Writing</h4>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)]">Task 1 & 2</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-[#B45309] tabular-nums">
                    @if($attempt->aiWritingEvaluation) {{ number_format($attempt->aiWritingEvaluation->band_score, 1) }} @else <span class="text-sm text-[var(--color-text-secondary)]">Pending</span> @endif
                </span>
            </div>
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-[var(--color-bg-secondary)]">
                <div class="h-full rounded-full bg-[#F59E0B] transition-all duration-700" style="width: {{ ($attempt->aiWritingEvaluation->band_score ?? 0) * 11 }}%"></div>
            </div>
            <p class="mt-2 text-xs text-[var(--color-text-secondary)]">@if($attempt->aiWritingEvaluation) AI Scored @else Evaluation pending @endif</p>
        </x-ui.card>

        {{-- Speaking --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-error)_10%,transparent)]">
                        <span class="material-symbols-outlined text-xl text-[var(--color-error)]">record_voice_over</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[var(--color-text-primary)]">Speaking</h4>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-text-secondary)]">Oral Assessment</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-[var(--color-error)] tabular-nums">
                    @if($attempt->aiSpeakingEvaluation) {{ number_format($attempt->aiSpeakingEvaluation->band_score, 1) }} @else <span class="text-sm text-[var(--color-text-secondary)]">Pending</span> @endif
                </span>
            </div>
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-[var(--color-bg-secondary)]">
                <div class="h-full rounded-full bg-[var(--color-error)] transition-all duration-700" style="width: {{ ($attempt->aiSpeakingEvaluation->band_score ?? 0) * 11 }}%"></div>
            </div>
            <p class="mt-2 text-xs text-[var(--color-text-secondary)]">@if($attempt->aiSpeakingEvaluation) AI Scored @else Evaluation pending @endif</p>
        </x-ui.card>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         AI FEEDBACK
         ═══════════════════════════════════════════════════════════════════════ --}}
    @if($attempt->aiWritingEvaluation || $attempt->aiSpeakingEvaluation)
    <div class="space-y-5">
        <h3 class="text-lg font-bold text-[var(--color-text-primary)]">AI Examiner Reports</h3>

        @if($attempt->aiWritingEvaluation)
        <x-ui.card>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex size-8 items-center justify-center rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,#F59E0B_12%,transparent)]">
                    <span class="material-symbols-outlined text-base text-[#B45309]">edit_square</span>
                </div>
                <h4 class="text-sm font-bold text-[var(--color-text-primary)]">Writing Evaluation</h4>
                <span class="ml-auto text-lg font-bold text-[#B45309]">Band {{ $attempt->aiWritingEvaluation->band_score }}</span>
            </div>
            <div class="rounded-[var(--radius-base)] bg-[var(--color-bg-secondary)] p-5 text-sm leading-relaxed text-[var(--color-text-secondary)] whitespace-pre-wrap">{{ $attempt->aiWritingEvaluation->evaluation_text }}</div>
        </x-ui.card>
        @endif

        @if($attempt->aiSpeakingEvaluation)
        <x-ui.card>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex size-8 items-center justify-center rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-error)_12%,transparent)]">
                    <span class="material-symbols-outlined text-base text-[var(--color-error)]">record_voice_over</span>
                </div>
                <h4 class="text-sm font-bold text-[var(--color-text-primary)]">Speaking Evaluation</h4>
                <span class="ml-auto text-lg font-bold text-[var(--color-error)]">Band {{ $attempt->aiSpeakingEvaluation->band_score }}</span>
            </div>
            <div class="rounded-[var(--radius-base)] bg-[var(--color-bg-secondary)] p-5 text-sm leading-relaxed text-[var(--color-text-secondary)] whitespace-pre-wrap">{{ $attempt->aiSpeakingEvaluation->evaluation_text }}</div>
        </x-ui.card>
        @endif
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════
         ACTIONS
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
        <x-ui.button variant="secondary" href="{{ route('user.history.index') }}">
            <span class="material-symbols-outlined text-sm">list</span>
            View All Results
        </x-ui.button>
        <x-ui.button variant="primary" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined text-sm">dashboard</span>
            Back to Dashboard
        </x-ui.button>
    </div>

</div>

@endsection
