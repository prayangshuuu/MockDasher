@extends('layouts.exam')

@section('title', $exam->title ?? 'Exam')
@section('test_type', 'Mock Exam')
@section('test_title', $exam->title ?? 'Practice Test')

{{-- ─── Timer Area (Center of top bar) ─── --}}
@section('timer_area')
    <div
        x-data="examTimer({{ $exam->duration_minutes ?? 60 }})"
        x-init="start()"
        class="flex items-center gap-2 rounded-[var(--radius-base)] border border-[var(--color-divider)] bg-[var(--color-bg-secondary)] px-4 py-1.5"
    >
        <span class="material-symbols-outlined text-[18px] text-[var(--color-primary)]">timer</span>
        <span
            class="text-sm font-bold tabular-nums exam-gradient bg-clip-text text-transparent"
            x-text="display"
        >00:00:00</span>
    </div>
@endsection

{{-- ─── Submit Button (Right of top bar) ─── --}}
@section('top_right_actions')
    <form id="exam-submit-form" method="POST" action="{{ route('user.reading.submit', $attempt->id ?? 0) }}">
        @csrf
        <x-ui.button type="submit" variant="primary" class="text-xs px-4 py-2">
            <span class="material-symbols-outlined text-sm">send</span>
            Submit Exam
        </x-ui.button>
    </form>
@endsection

@section('content')

<div
    x-data="{
        currentQuestion: 0,
        answers: {},
        questions: {{ Js::from($questions ?? []) }},
        selectAnswer(qIndex, optionIndex) {
            this.answers[qIndex] = optionIndex;
        },
        goTo(index) {
            this.currentQuestion = index;
        },
        isAnswered(index) {
            return this.answers[index] !== undefined;
        }
    }"
    class="flex flex-1 overflow-hidden"
>
    {{-- ═══════════════════════════════════════════════════════════════════════
         LEFT COLUMN — Current Question
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 overflow-y-auto p-6 sm:p-8 lg:p-10">
        <template x-for="(question, qIndex) in questions" :key="qIndex">
            <div x-show="currentQuestion === qIndex" x-transition.opacity>
                {{-- Question Number --}}
                <p class="mb-2 text-xs font-bold uppercase tracking-widest text-[var(--color-primary)]">
                    Question <span x-text="qIndex + 1"></span> of <span x-text="questions.length"></span>
                </p>

                {{-- Question Text --}}
                <h3 class="text-xl sm:text-2xl font-bold leading-relaxed text-[var(--color-text-primary)] mb-8" x-text="question.text"></h3>

                {{-- Answer Options — large minimal radio hit areas --}}
                <div class="space-y-3">
                    <template x-for="(option, oIndex) in question.options" :key="oIndex">
                        <button
                            @click="selectAnswer(qIndex, oIndex)"
                            :class="answers[qIndex] === oIndex
                                ? 'border-[var(--color-primary)] bg-[color-mix(in_srgb,var(--color-primary)_6%,transparent)]'
                                : 'border-[var(--color-divider)] bg-[var(--color-bg-primary)] hover:border-[var(--color-primary)]'"
                            class="flex w-full items-center gap-4 rounded-[var(--radius-base)] border px-5 py-4 text-left transition-all btn-active-state"
                        >
                            {{-- Radio Circle --}}
                            <span
                                :class="answers[qIndex] === oIndex
                                    ? 'border-[var(--color-primary)] bg-[var(--color-primary)]'
                                    : 'border-[var(--color-divider)]'"
                                class="flex size-5 shrink-0 items-center justify-center rounded-full border-2 transition-colors"
                            >
                                <span
                                    x-show="answers[qIndex] === oIndex"
                                    class="size-2 rounded-full bg-white"
                                ></span>
                            </span>

                            {{-- Option Letter + Text --}}
                            <div class="flex items-center gap-3">
                                <span class="flex size-7 shrink-0 items-center justify-center rounded-[var(--radius-xs)] bg-[var(--color-bg-secondary)] text-xs font-bold text-[var(--color-text-secondary)]" x-text="String.fromCharCode(65 + oIndex)"></span>
                                <span class="text-sm font-medium text-[var(--color-text-primary)]" x-text="option"></span>
                            </div>
                        </button>
                    </template>
                </div>

                {{-- Navigation Buttons --}}
                <div class="mt-8 flex items-center justify-between">
                    <x-ui.button
                        variant="secondary"
                        x-show="currentQuestion > 0"
                        @click="currentQuestion--"
                        class="text-xs"
                    >
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Previous
                    </x-ui.button>

                    <x-ui.button
                        variant="primary"
                        x-show="currentQuestion < questions.length - 1"
                        @click="currentQuestion++"
                        class="ml-auto text-xs"
                    >
                        Next
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </x-ui.button>
                </div>
            </div>
        </template>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         RIGHT COLUMN — Question Navigator Grid
         ═══════════════════════════════════════════════════════════════════════ --}}
    <aside class="hidden w-72 shrink-0 border-l border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-5 lg:flex lg:flex-col">
        <p class="mb-3 text-xs font-bold uppercase tracking-widest text-[var(--color-text-secondary)]">Questions</p>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="grid grid-cols-5 gap-2">
                <template x-for="(question, qIndex) in questions" :key="qIndex">
                    <button
                        @click="goTo(qIndex)"
                        :class="{
                            'active-question border-l-[var(--color-primary)]': currentQuestion === qIndex,
                            'border-[var(--color-divider)]': currentQuestion !== qIndex
                        }"
                        class="relative flex size-10 items-center justify-center rounded-[var(--radius-xs)] border text-xs font-semibold transition-all hover:border-[var(--color-primary)]"
                    >
                        <span x-text="qIndex + 1" :class="currentQuestion === qIndex ? 'text-[var(--color-primary)]' : 'text-[var(--color-text-secondary)]'"></span>

                        {{-- Answered dot indicator --}}
                        <span
                            x-show="isAnswered(qIndex)"
                            class="absolute -top-0.5 -right-0.5 size-2.5 rounded-full bg-[var(--color-success)] border border-[var(--color-bg-primary)]"
                        ></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Summary --}}
        <div class="mt-4 border-t border-[var(--color-divider)] pt-4">
            <div class="flex items-center justify-between text-xs text-[var(--color-text-secondary)]">
                <span>Answered</span>
                <span class="font-bold text-[var(--color-text-primary)]" x-text="Object.keys(answers).length + ' / ' + questions.length"></span>
            </div>
        </div>
    </aside>
</div>

@endsection

@push('scripts')
<script>
    function examTimer(minutes) {
        return {
            remaining: minutes * 60,
            display: '00:00:00',
            interval: null,
            start() {
                this.tick();
                this.interval = setInterval(() => this.tick(), 1000);
            },
            tick() {
                if (this.remaining <= 0) {
                    clearInterval(this.interval);
                    document.getElementById('exam-submit-form')?.submit();
                    return;
                }
                const h = Math.floor(this.remaining / 3600);
                const m = Math.floor((this.remaining % 3600) / 60);
                const s = this.remaining % 60;
                this.display = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                this.remaining--;
            }
        };
    }
</script>
@endpush
