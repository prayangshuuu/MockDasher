@extends('layouts.exam')

@section('title', 'Writing Test')
@section('test_type', 'IELTS Writing')
@section('test_title', 'IELTS Writing Exam')

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-2 rounded-full border border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-4 py-1.5 transition-all">
    <span class="material-symbols-outlined text-lg text-[var(--color-primary)]" id="timer-icon">timer</span>
    <span class="text-lg font-bold tabular-nums tracking-tight text-[var(--color-text-primary)] font-mono" id="timer-display">--:--</span>
    <span class="hidden text-[10px] font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:inline">left</span>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-3">
    <div id="save-indicator" class="flex items-center gap-1.5 text-[var(--color-success)]">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        <span class="text-[10px] font-bold uppercase tracking-wider">Saved</span>
    </div>
    <x-ui.button variant="primary" onclick="endExam()" class="text-xs" id="end-exam-btn">
        <span class="material-symbols-outlined text-sm">send</span>
        <span class="hidden sm:inline">End Exam</span>
    </x-ui.button>
</div>
@endsection

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Task Tabs --}}
    <div class="flex h-11 shrink-0 items-center gap-1 border-b border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-4 sm:px-6 lg:px-8">
        @foreach($tasks as $index => $task)
            <button onclick="switchWritingTask({{ $index }})"
                    class="writing-tab h-full flex items-center gap-2 px-4 text-xs font-bold uppercase tracking-wider transition-colors border-b-2"
                    data-tab="{{ $index }}" id="wtab-{{ $index }}">
                <span class="material-symbols-outlined text-sm">edit_note</span>
                Task {{ $task->task_number }}
                <span id="tab-status-{{ $task->id }}" class="hidden size-2 rounded-full bg-[var(--color-success)]"></span>
            </button>
        @endforeach
    </div>

    {{-- Main Workspace --}}
    <div class="flex-1 relative overflow-hidden">
        @foreach($tasks as $index => $task)
        @php
            $answer = $answers[$task->id] ?? null;
            $isSubmitted = $answer && $answer->submitted_at;
            $existingEval = ($isSubmitted && $answer->evaluation_json) ? json_decode($answer->evaluation_json, true) : null;
        @endphp
        <div class="writing-panel absolute inset-0 flex flex-col overflow-y-auto" data-tab="{{ $index }}" style="display:none;">

            {{-- 50/50 Work Area — min-height 100% fills the absolute panel; eval panel below triggers scroll --}}
            <div class="flex" style="min-height: 100%;">
                {{-- Left: Task Description --}}
                <div class="w-1/2 overflow-y-auto border-r border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-6 sm:p-8 lg:p-12 custom-scrollbar">
                    <div class="max-w-2xl mx-auto">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)] rounded-[var(--radius-base)] text-[10px] font-bold uppercase tracking-widest mb-6">
                            <span class="material-symbols-outlined text-xs">info</span>
                            Writing Task {{ $task->task_number }}
                        </div>

                        <h2 class="text-2xl font-bold text-[var(--color-text-primary)] tracking-tight mb-6">
                            {{ $task->task_title ?: 'Simulation Prompt' }}
                        </h2>

                        @if($task->task_description)
                            <div class="p-6 bg-[var(--color-bg-secondary)] rounded-[var(--radius-base)] border border-[var(--color-divider)] mb-8 text-[var(--color-text-secondary)] font-medium leading-relaxed italic">
                                {{ $task->task_description }}
                            </div>
                        @endif

                        @if($task->images->count() > 0)
                            <div class="rounded-[var(--radius-lg)] border border-[var(--color-divider)] overflow-hidden mb-8 bg-[var(--color-bg-secondary)]">
                                <img src="{{ Storage::url($task->images->first()->image_path) }}" class="w-full h-auto object-contain max-h-[500px]" alt="Task Content">
                            </div>
                        @endif

                        @if($task->task_number == 2 && $task->task_prompt)
                            <div class="p-8 bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] rounded-[var(--radius-lg)] border border-[color-mix(in_srgb,var(--color-primary)_20%,transparent)] text-[var(--color-text-primary)] font-semibold leading-relaxed">
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-[var(--color-primary)] mb-2">Essay Question</span>
                                <div class="whitespace-pre-line">{{ $task->task_prompt }}</div>
                            </div>
                        @endif

                        @if($task->instruction_text)
                            <div class="mt-6 flex items-center gap-3 p-4 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,#F59E0B_10%,transparent)] border border-[color-mix(in_srgb,#F59E0B_20%,transparent)]">
                                <span class="material-symbols-outlined text-[#B45309] text-lg">info</span>
                                <span class="text-sm font-bold text-[#92400E]">{{ $task->instruction_text }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Answer Editor --}}
                <div class="w-1/2 flex flex-col bg-[var(--color-bg-secondary)]">
                    <div class="flex-1 relative">
                        <textarea id="textarea-{{ $task->id }}"
                                  data-task-id="{{ $task->id }}"
                                  data-min-words="{{ $task->minimum_word_count }}"
                                  oninput="updateWordCount({{ $task->id }}, {{ $task->minimum_word_count }}); scheduleAutosave();"
                                  class="writing-textarea absolute inset-0 w-full h-full p-8 sm:p-12 text-base leading-relaxed bg-transparent border-none focus:ring-0 resize-none custom-scrollbar placeholder:text-[var(--color-text-secondary)] text-[var(--color-text-primary)] outline-none {{ $isSubmitted ? 'opacity-70 cursor-default' : '' }}"
                                  placeholder="Start typing your response here..."
                                  @if($isSubmitted) readonly @endif>{{ $answer->answer_text ?? '' }}</textarea>
                    </div>
                    <div class="flex h-16 shrink-0 items-center justify-between border-t border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-6">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-[var(--radius-sm)] border transition-all bg-[var(--color-bg-secondary)] border-[var(--color-divider)] text-[var(--color-text-secondary)]" id="wc-badge-{{ $task->id }}">
                                <span class="text-xs font-bold tabular-nums" id="wc-{{ $task->id }}">0</span>
                                <span class="text-[10px] font-semibold uppercase tracking-wider opacity-80">Words</span>
                            </div>
                            <div class="text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">
                                Min {{ $task->minimum_word_count }}
                            </div>
                        </div>
                        <div>
                            @if($isSubmitted)
                                <div class="flex items-center gap-2 px-4 py-2 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] border border-[color-mix(in_srgb,var(--color-success)_20%,transparent)] text-[var(--color-success)]">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    <span class="text-xs font-bold uppercase tracking-wider">Task {{ $task->task_number }} Submitted</span>
                                </div>
                            @else
                                <button onclick="submitWritingTask({{ $task->id }}, {{ $task->task_number }})"
                                        id="submit-task-btn-{{ $task->id }}"
                                        class="flex items-center gap-2 px-5 py-2 rounded-[var(--radius-base)] bg-[var(--color-primary)] text-white text-xs font-bold uppercase tracking-wider hover:opacity-90 transition-opacity active:scale-95">
                                    <span class="material-symbols-outlined text-sm" id="submit-task-icon-{{ $task->id }}">upload</span>
                                    <span id="submit-task-label-{{ $task->id }}">Submit Task {{ $task->task_number }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Evaluation Panel (shown after task submit) --}}
            <div id="eval-panel-{{ $task->id }}"
                 class="shrink-0 border-t-4 border-[var(--color-primary)] bg-[var(--color-bg-primary)]"
                 style="{{ $existingEval ? '' : 'display:none;' }}">
                @if($existingEval)
                    <div class="max-w-5xl mx-auto p-8">
                        @include('user.writing-test._evaluation', ['eval' => $existingEval, 'taskNumber' => $task->task_number, 'bandScore' => $answer->band_score])
                    </div>
                @else
                    <div class="max-w-5xl mx-auto p-8" id="eval-content-{{ $task->id }}"></div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Hidden form for final submission --}}
<form id="writing-submit-form" action="{{ route('user.writing.submit', $attempt->id) }}" method="POST" class="hidden">
    @csrf
</form>
@endsection

@push('scripts')
<script>
(function() {
    const autosaveUrl  = '{{ route("user.writing.autosave", $attempt->id) }}';
    const csrfToken    = document.querySelector('meta[name="csrf-token"]').content;
    let timeRemaining  = {{ (int) $remainingSeconds }};
    let currentTab     = 0;

    const submitUrls   = {
        @foreach($tasks as $task)
        {{ $task->id }}: '{{ route("user.writing.submitTask", [$attempt->id, $task->id]) }}',
        @endforeach
    };

    const submittedTasks = new Set([
        @foreach($tasks as $task)
            @if($answers[$task->id] ?? false)
                @if($answers[$task->id]->submitted_at)
                    {{ $task->id }},
                @endif
            @endif
        @endforeach
    ]);

    // ── Timer ──
    function updateTimer() {
        const m = Math.floor(timeRemaining / 60);
        const s = timeRemaining % 60;
        document.getElementById('timer-display').textContent =
            m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
    }
    updateTimer();
    setInterval(function() {
        timeRemaining--;
        if (timeRemaining <= 0) {
            document.getElementById('writing-submit-form').submit();
            return;
        }
        updateTimer();
        if (timeRemaining <= 300) {
            document.getElementById('timer-display').classList.add('text-[var(--color-error)]');
        }
    }, 1000);

    // ── Word count — defined first so init can call it ──
    window.updateWordCount = function(taskId, minWords) {
        const ta = document.getElementById('textarea-' + taskId);
        if (!ta) return;
        const text  = ta.value.trim();
        const count = text === '' ? 0 : text.split(/\s+/).length;
        const wcEl  = document.getElementById('wc-' + taskId);
        const badge = document.getElementById('wc-badge-' + taskId);
        if (wcEl) wcEl.textContent = count;
        if (badge) {
            badge.className = count >= minWords
                ? 'flex items-center gap-1.5 px-3 py-1.5 rounded-[var(--radius-sm)] border transition-all bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] border-[color-mix(in_srgb,var(--color-success)_20%,transparent)] text-[var(--color-success)]'
                : 'flex items-center gap-1.5 px-3 py-1.5 rounded-[var(--radius-sm)] border transition-all bg-[var(--color-bg-secondary)] border-[var(--color-divider)] text-[var(--color-text-secondary)]';
        }
    };

    // ── Tab switching ──
    window.switchWritingTask = function(idx) {
        currentTab = idx;
        document.querySelectorAll('.writing-panel').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.writing-tab').forEach(el => {
            el.classList.remove('border-[var(--color-primary)]', 'text-[var(--color-primary)]');
            el.classList.add('border-transparent', 'text-[var(--color-text-secondary)]');
        });
        const panel = document.querySelector('.writing-panel[data-tab="'+idx+'"]');
        const tab   = document.getElementById('wtab-'+idx);
        if (panel) panel.style.display = 'flex';
        if (tab) {
            tab.classList.remove('border-transparent', 'text-[var(--color-text-secondary)]');
            tab.classList.add('border-[var(--color-primary)]', 'text-[var(--color-primary)]');
        }
        // Refresh word counts when switching to a tab (textarea was hidden before)
        const ta = panel && panel.querySelector('.writing-textarea');
        if (ta) window.updateWordCount(parseInt(ta.dataset.taskId), parseInt(ta.dataset.minWords));
    };

    // Init: run after updateWordCount is defined
    switchWritingTask(0);
    document.querySelectorAll('.writing-textarea').forEach(ta => {
        window.updateWordCount(parseInt(ta.dataset.taskId), parseInt(ta.dataset.minWords));
    });

    // ── Autosave ──
    let autosaveTimer = null;
    window.scheduleAutosave = function() {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(doAutosave, 3000);
    };
    async function doAutosave() {
        const answers = {};
        document.querySelectorAll('.writing-textarea').forEach(ta => {
            if (!ta.hasAttribute('readonly') && !ta.readOnly) answers[ta.dataset.taskId] = ta.value;
        });
        if (!Object.keys(answers).length) return;
        const indicator = document.getElementById('save-indicator');
        indicator.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin text-slate-400">refresh</span><span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Saving...</span>';
        try {
            await fetch(autosaveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ answers })
            });
            indicator.innerHTML = '<span class="material-symbols-outlined text-sm text-emerald-500">check_circle</span><span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Saved</span>';
        } catch(e) {
            indicator.innerHTML = '<span class="material-symbols-outlined text-sm text-rose-500">error</span><span class="text-[10px] font-black uppercase tracking-widest text-rose-500">Error</span>';
        }
    }
    setInterval(doAutosave, 15000);

    // ── Per-task Submit ──
    window.submitWritingTask = async function(taskId, taskNumber) {
        const btn   = document.getElementById('submit-task-btn-' + taskId);
        const icon  = document.getElementById('submit-task-icon-' + taskId);
        const label = document.getElementById('submit-task-label-' + taskId);
        const ta    = document.getElementById('textarea-' + taskId);

        // Loading state
        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        icon.textContent  = 'hourglass_empty';
        label.textContent = 'Evaluating...';

        try {
            const res = await fetch(submitUrls[taskId], {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ answer: ta.value })
            });
            const data = await res.json();

            if (!res.ok || !data.success) throw new Error(data.error || 'Submission failed');

            // Lock textarea
            ta.setAttribute('readonly', '');
            ta.classList.add('opacity-70', 'cursor-default');

            // Replace button with success badge
            btn.outerHTML = `<div class="flex items-center gap-2 px-4 py-2 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] border border-[color-mix(in_srgb,var(--color-success)_20%,transparent)] text-[var(--color-success)]"><span class="material-symbols-outlined text-sm">check_circle</span><span class="text-xs font-bold uppercase tracking-wider">Task ${taskNumber} Submitted</span></div>`;

            // Tab dot
            document.getElementById('tab-status-' + taskId)?.classList.remove('hidden');

            submittedTasks.add(taskId);

            // Render evaluation
            if (data.evaluation) {
                renderWritingEvaluation(taskId, taskNumber, data.band_score, data.evaluation);
            }

        } catch(e) {
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
            icon.textContent  = 'error';
            label.textContent = 'Retry Submit';
            btn.classList.add('bg-[var(--color-error)]');
            alert('Evaluation failed: ' + e.message + '. Please try again.');
        }
    };

    function renderWritingEvaluation(taskId, taskNumber, bandScore, eval_) {
        const panel   = document.getElementById('eval-panel-' + taskId);
        const content = document.getElementById('eval-content-' + taskId);
        if (!panel || !content) return;

        const isNewSchema = eval_.criteria_scores !== undefined;

        const scoreColor = (s) => {
            if (s >= 7)   return 'text-emerald-500';
            if (s >= 5.5) return 'text-amber-500';
            return 'text-rose-500';
        };
        const bandColor = bandScore >= 7 ? 'bg-emerald-500' : bandScore >= 5.5 ? 'bg-amber-500' : 'bg-rose-500';

        // ── Criteria grid ──────────────────────────────────────────────────────
        let criteriaHtml = '';
        if (isNewSchema) {
            const cs = eval_.criteria_scores || {};
            const newCriteria = [
                { label: taskNumber === 1 ? 'Task Achievement' : 'Task Response', score: cs.task_achievement_or_response },
                { label: 'Coherence & Cohesion',           score: cs.coherence_and_cohesion },
                { label: 'Lexical Resource',               score: cs.lexical_resource },
                { label: 'Grammatical Range & Accuracy',   score: cs.grammatical_range_and_accuracy },
            ];
            criteriaHtml = newCriteria.map(c => {
                const s = c.score ?? '—';
                return `<div class="p-5 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-black uppercase tracking-widest text-[var(--color-text-secondary)]">${c.label}</span>
                        <span class="text-xl font-black ${scoreColor(s)}">${s}</span>
                    </div>
                </div>`;
            }).join('');
        } else {
            // Legacy v1 schema
            const firstCrit = taskNumber === 1
                ? { key: 'task_achievement', label: 'Task Achievement' }
                : { key: 'task_response',    label: 'Task Response' };
            const oldCriteria = [
                firstCrit,
                { key: 'coherence_cohesion',         label: 'Coherence & Cohesion' },
                { key: 'lexical_resource',            label: 'Lexical Resource' },
                { key: 'grammatical_range_accuracy',  label: 'Grammatical Range & Accuracy' },
            ];
            criteriaHtml = oldCriteria.map(c => {
                const d = eval_[c.key] || {};
                const s = d.score ?? '—';
                return `<div class="p-5 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-black uppercase tracking-widest text-[var(--color-text-secondary)]">${c.label}</span>
                        <span class="text-xl font-black ${scoreColor(s)}">${s}</span>
                    </div>
                    <p class="text-sm text-[var(--color-text-secondary)] leading-relaxed">${d.feedback || ''}</p>
                </div>`;
            }).join('');
        }

        // ── Extra sections (v2 new schema) ────────────────────────────────────
        let extraHtml = '';
        if (isNewSchema) {
            // Detailed Feedback
            if (eval_.detailed_feedback) {
                extraHtml += `<div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
                    <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Detailed Feedback</p>
                    <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">${eval_.detailed_feedback}</p>
                </div>`;
            }

            // Vocabulary Corrections
            if (eval_.vocabulary_corrections?.length) {
                const items = eval_.vocabulary_corrections.map(v =>
                    `<div class="flex items-start gap-3 text-sm">
                        <span class="shrink-0 px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-mono line-through">${v.incorrect || ''}</span>
                        <span class="shrink-0 text-[var(--color-text-secondary)]">→</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-mono font-semibold">${v.suggested || ''}</span>
                    </div>`
                ).join('');
                extraHtml += `<div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#8B5CF6_5%,transparent)] border border-[color-mix(in_srgb,#8B5CF6_20%,transparent)]">
                    <p class="text-[10px] font-black text-violet-500 uppercase tracking-widest mb-3">Vocabulary Improvements</p>
                    <div class="space-y-2">${items}</div>
                </div>`;
            }

            // Grammar Corrections
            if (eval_.grammar_corrections?.length) {
                const items = eval_.grammar_corrections.map(g =>
                    `<div class="space-y-1 text-sm">
                        <div class="flex items-start gap-2"><span class="shrink-0 text-[10px] font-bold uppercase text-rose-400 mt-0.5 w-16">Wrong:</span><span class="text-rose-700 italic">${g.incorrect || ''}</span></div>
                        <div class="flex items-start gap-2"><span class="shrink-0 text-[10px] font-bold uppercase text-emerald-500 mt-0.5 w-16">Better:</span><span class="text-emerald-700 font-semibold">${g.suggested || ''}</span></div>
                    </div>`
                ).join('');
                extraHtml += `<div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#EF4444_5%,transparent)] border border-[color-mix(in_srgb,#EF4444_20%,transparent)]">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3">Grammar Corrections</p>
                    <div class="space-y-3">${items}</div>
                </div>`;
            }

            // Suggestions for Improvement
            if (eval_.suggestions_for_improvement) {
                extraHtml += `<details class="group">
                    <summary class="flex items-center gap-2 cursor-pointer text-xs font-black uppercase tracking-widest text-[var(--color-primary)] select-none list-none p-4 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)] hover:opacity-80 transition-opacity">
                        <span class="material-symbols-outlined text-sm group-open:rotate-90 transition-transform">chevron_right</span>
                        Suggestions for Improvement
                    </summary>
                    <div class="mt-3 p-6 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] text-sm text-[var(--color-text-primary)] leading-relaxed whitespace-pre-line">${eval_.suggestions_for_improvement}</div>
                </details>`;
            }
        } else {
            // Legacy v1 extra sections
            if (eval_.grammatical_errors?.length) {
                const items = eval_.grammatical_errors.map(e => `<li class="text-sm text-rose-700 dark:text-rose-400">${e}</li>`).join('');
                extraHtml += `<div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#EF4444_5%,transparent)] border border-[color-mix(in_srgb,#EF4444_20%,transparent)]">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3">Grammatical Errors Found</p>
                    <ul class="space-y-1 list-disc list-inside">${items}</ul>
                </div>`;
            }
            if (eval_.overall_review) {
                extraHtml += `<div class="mt-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
                    <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Overall Review</p>
                    <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">${eval_.overall_review}</p>
                </div>`;
            }
            if (eval_.improved_version) {
                extraHtml += `<details class="group mt-4">
                    <summary class="flex items-center gap-2 cursor-pointer text-xs font-black uppercase tracking-widest text-[var(--color-primary)] select-none list-none p-4 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)] hover:opacity-80 transition-opacity">
                        <span class="material-symbols-outlined text-sm group-open:rotate-90 transition-transform">chevron_right</span>
                        View Improved Version
                    </summary>
                    <div class="mt-3 p-6 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] text-sm text-[var(--color-text-primary)] leading-relaxed whitespace-pre-line">${eval_.improved_version}</div>
                </details>`;
            }
        }

        content.innerHTML = `
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-2xl font-black text-[var(--color-text-primary)]">Task ${taskNumber} Evaluation</h3>
                    <p class="text-sm text-[var(--color-text-secondary)] mt-1">AI-powered IELTS band assessment</p>
                </div>
                <div class="flex items-center justify-center size-20 rounded-[var(--radius-xl)] ${bandColor} text-white shadow-lg">
                    <div class="text-center">
                        <div class="text-3xl font-black leading-none">${bandScore ?? '—'}</div>
                        <div class="text-[9px] font-bold uppercase tracking-wider opacity-80 mt-1">Band</div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">${criteriaHtml}</div>
            ${extraHtml}
        `;

        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ── End Exam ──
    window.endExam = function() {
        document.getElementById('writing-submit-form').submit();
    };
})();
</script>
@endpush
