@extends('layouts.exam')

@section('title', 'Writing Test')
@section('test_type', 'IELTS Writing')
@section('test_title', 'IELTS Writing Exam')

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 py-1.5 transition-all shadow-soft duration-200">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-500 fill-current" viewBox="0 0 24 24" id="timer-icon">
        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
        <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
    </svg>
    <span class="text-sm font-black tabular-nums tracking-tight text-slate-800 dark:text-slate-200 font-mono" id="timer-display">--:--</span>
    <span class="hidden text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 sm:inline">Remaining</span>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-3.5">
    <div id="save-indicator" class="flex items-center gap-1.5 text-emerald-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="text-[9px] font-black uppercase tracking-widest">Saved</span>
    </div>
    <button onclick="endExam()" class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-soft transition-all duration-150 focus:outline-none" id="end-exam-btn">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
        </svg>
        <span class="hidden sm:inline">End Exam</span>
    </button>
</div>
@endsection

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Task Tabs --}}
    <div class="flex h-11 shrink-0 items-center gap-1 border-b border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 sm:px-6 lg:px-8 shadow-sm">
        @foreach($tasks as $index => $task)
            <button onclick="switchWritingTask({{ $index }})"
                    class="writing-tab h-full flex items-center gap-2 px-5 text-xs font-black uppercase tracking-widest transition-all border-b-2 focus:outline-none"
                    data-tab="{{ $index }}" id="wtab-{{ $index }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
                Task {{ $task->task_number }}
                <span id="tab-status-{{ $task->id }}" class="hidden size-2 rounded-full bg-emerald-500"></span>
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
        <div class="writing-panel absolute inset-0 flex flex-col overflow-y-auto bg-slate-50 dark:bg-slate-900/40" data-tab="{{ $index }}" style="display:none;">

            {{-- 50/50 Work Area --}}
            <div class="flex flex-1" style="min-height: 100%;">
                {{-- Left: Task Description --}}
                <div class="w-1/2 overflow-y-auto border-r border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark p-6 sm:p-8 lg:p-12 custom-scrollbar">
                    <div class="max-w-2xl mx-auto space-y-6">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50 rounded-full text-[9px] font-black uppercase tracking-widest shadow-soft">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            Writing Task {{ $task->task_number }}
                        </div>

                        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ $task->task_title ?: 'Simulation Prompt' }}
                        </h2>

                        @if($task->task_description)
                            <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-medium leading-relaxed italic shadow-inner">
                                {{ $task->task_description }}
                            </div>
                        @endif

                        @if($task->images->count() > 0)
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-900 p-4 shadow-soft">
                                <img src="{{ Storage::url($task->images->first()->image_path) }}" class="w-full h-auto object-contain max-h-[500px]" alt="Task Content">
                            </div>
                        @endif

                        @if($task->task_number == 2 && $task->task_prompt)
                            <div class="p-8 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-2xl border border-indigo-100 dark:border-indigo-900/40 text-slate-850 dark:text-slate-200 font-semibold leading-relaxed shadow-soft">
                                <span class="block text-[9px] font-black uppercase tracking-widest text-indigo-500 mb-2">Essay Question</span>
                                <div class="whitespace-pre-line text-sm sm:text-base">{{ $task->task_prompt }}</div>
                            </div>
                        @endif

                        @if($task->instruction_text)
                            <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50/50 dark:bg-amber-955/20 border border-amber-100 dark:border-amber-900/40 shadow-soft text-amber-800 dark:text-amber-300 text-xs sm:text-sm font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                <span>{{ $task->instruction_text }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Answer Editor --}}
                <div class="w-1/2 flex flex-col bg-slate-50 dark:bg-slate-900/40">
                    <div class="flex-1 relative">
                        <textarea id="textarea-{{ $task->id }}"
                                  data-task-id="{{ $task->id }}"
                                  data-min-words="{{ $task->minimum_word_count }}"
                                  oninput="window.examHasChanges = true; updateWordCount({{ $task->id }}, {{ $task->minimum_word_count }}); scheduleAutosave();"
                                  class="writing-textarea absolute inset-0 w-full h-full p-8 sm:p-12 text-base leading-relaxed bg-transparent border-none focus:ring-0 resize-none custom-scrollbar placeholder:text-slate-400 text-slate-800 dark:text-slate-200 outline-none {{ $isSubmitted ? 'opacity-70 cursor-default' : '' }}"
                                  placeholder="Start typing your response here..."
                                  @if($isSubmitted) readonly @endif>{{ $answer->answer_text ?? '' }}</textarea>
                    </div>
                    <div class="flex h-16 shrink-0 items-center justify-between border-t border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border transition-all bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400" id="wc-badge-{{ $task->id }}">
                                <span class="text-xs font-black tabular-nums" id="wc-{{ $task->id }}">0</span>
                                <span class="text-[9px] font-black uppercase tracking-wider opacity-85">Words</span>
                            </div>
                            <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                Min Target {{ $task->minimum_word_count }}
                            </div>
                        </div>
                        <div>
                            @if($isSubmitted)
                                <div class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-emerald-500 shadow-soft">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    <span class="text-xs font-bold uppercase tracking-wider">Task {{ $task->task_number }} Submitted</span>
                                </div>
                            @else
                                <button onclick="submitWritingTask({{ $task->id }}, {{ $task->task_number }})"
                                        id="submit-task-btn-{{ $task->id }}"
                                        class="flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider hover:opacity-90 transition-opacity active:scale-95 focus:outline-none shadow-soft">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" id="submit-task-icon-{{ $task->id }}"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                                    <span id="submit-task-label-{{ $task->id }}">Submit Task {{ $task->task_number }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


        </div>
        @endforeach
    </div>
</div>

{{-- Hidden form for final submission --}}
<form id="writing-submit-form" action="{{ route('user.writing.submit', $attempt->id) }}" method="POST" class="hidden">
    @csrf
</form>

{{-- Sleek screen blocker spinner when evaluating in batch --}}
<div id="ai-evaluation-loading-modal" class="fixed inset-0 z-[99999] hidden flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
    <div class="relative w-full max-w-md rounded-2xl border border-indigo-100 dark:border-indigo-900/50 bg-white dark:bg-slate-900 p-8 shadow-premium text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 mb-6 border border-indigo-100 dark:border-indigo-900/40">
            <svg class="animate-spin h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2 uppercase tracking-wide">AI Grading In Progress</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
            Please wait while the AI Gemini Examiner analyzes and evaluates your Writing tasks. 
            <br/><br/>
            <strong class="text-indigo-500">Do not close, reload, or navigate away from this page.</strong>
            <br/>
            This process takes about 20-30 seconds to run sequential criteria-specific evaluations.
        </p>
    </div>
</div>
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
            window.isAutoSubmitting = true;
            window.onbeforeunload = null;
            document.getElementById('writing-submit-form').submit();
            return;
        }
        updateTimer();
        if (timeRemaining <= 300) {
            document.getElementById('timer-widget').classList.add('border-rose-500', 'bg-rose-50', 'dark:bg-rose-955/20');
            document.getElementById('timer-display').classList.add('text-rose-500');
        }
    }, 1000);

    // ── Word count ──
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
                ? 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg border transition-all bg-emerald-50 dark:bg-emerald-950/30 border-emerald-100 dark:border-emerald-900/40 text-emerald-500 font-bold'
                : 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg border transition-all bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-550 dark:text-slate-400';
        }
    };

    // ── Tab switching ──
    window.switchWritingTask = function(idx) {
        currentTab = idx;
        document.querySelectorAll('.writing-panel').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.writing-tab').forEach(el => {
            el.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
            el.classList.add('border-transparent', 'text-slate-400', 'dark:text-slate-500');
        });
        const panel = document.querySelector('.writing-panel[data-tab="'+idx+'"]');
        const tab   = document.getElementById('wtab-'+idx);
        if (panel) panel.style.display = 'flex';
        if (tab) {
            tab.classList.remove('border-transparent', 'text-slate-400', 'dark:text-slate-500');
            tab.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
        }
        const ta = panel && panel.querySelector('.writing-textarea');
        if (ta) window.updateWordCount(parseInt(ta.dataset.taskId), parseInt(ta.dataset.minWords));
    };

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
        indicator.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin text-slate-400 fill-current" viewBox="0 0 24 24"><path d="M12 4V2C6.48 2 2 6.48 2 12h2c0-4.41 3.59-8 8-8zm0 14c4.41 0 8-3.59 8-8h2c0 5.52-4.48 10-10 10v-2z"/></svg><span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Saving...</span>';
        try {
            await fetch(autosaveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ answers })
            });
            indicator.innerHTML = '<svg class="w-3.5 h-3.5 text-emerald-500 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg><span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Saved</span>';
        } catch(e) {
            indicator.innerHTML = '<svg class="w-3.5 h-3.5 text-rose-500 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg><span class="text-[9px] font-black uppercase tracking-widest text-rose-500">Error</span>';
        }
    }
    setInterval(doAutosave, 15000);

    // ── Per-task Submit ──
    window.submitWritingTask = async function(taskId, taskNumber) {
        const btn   = document.getElementById('submit-task-btn-' + taskId);
        const iconSvg = document.getElementById('submit-task-icon-' + taskId);
        const label = document.getElementById('submit-task-label-' + taskId);
        const ta    = document.getElementById('textarea-' + taskId);

        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        if (iconSvg) {
            iconSvg.outerHTML = '<svg id="submit-task-icon-'+taskId+'" class="w-4 h-4 animate-spin fill-current" viewBox="0 0 24 24"><path d="M12 4V2C6.48 2 2 6.48 2 12h2c0-4.41 3.59-8 8-8zm0 14c4.41 0 8-3.59 8-8h2c0 5.52-4.48 10-10 10v-2z"/></svg>';
        }
        label.textContent = 'Saving...';

        try {
            const res = await fetch(submitUrls[taskId], {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ answer: ta.value })
            });
            const data = await res.json();

            if (!res.ok || !data.success) throw new Error(data.error || 'Submission failed');

            ta.setAttribute('readonly', '');
            ta.classList.add('opacity-70', 'cursor-default');

            btn.outerHTML = `<div class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-emerald-500 shadow-soft"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg><span class="text-xs font-bold uppercase tracking-wider">Task ${taskNumber} Locked</span></div>`;

            document.getElementById('tab-status-' + taskId)?.classList.remove('hidden');
            submittedTasks.add(taskId);

        } catch(e) {
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
            const submitIcon = document.getElementById('submit-task-icon-' + taskId);
            if (submitIcon) {
                submitIcon.outerHTML = '<svg id="submit-task-icon-'+taskId+'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>';
            }
            label.textContent = 'Retry Submit';
            btn.classList.add('bg-rose-500');
            alert('Saving failed: ' + e.message + '. Please try again.');
        }
    };

    window.prepareWritingSubmit = async function() {
        await doAutosave();
    };

    // ── End Exam ──
    window.endExam = function() {
        window.isAutoSubmitting = true;
        window.onbeforeunload = null;
        window.prepareWritingSubmit().then(() => {
            document.getElementById('writing-submit-form').submit();
        });
    };
})();
</script>
@endpush
