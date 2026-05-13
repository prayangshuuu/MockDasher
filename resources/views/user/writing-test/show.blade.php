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
    <x-ui.button variant="primary" onclick="submitWritingTest()" class="text-xs">
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
            </button>
        @endforeach
    </div>

    {{-- Main Workspace --}}
    <div class="flex-1 flex overflow-hidden">
        @foreach($tasks as $index => $task)
        <div class="writing-panel flex-1 flex overflow-hidden" data-tab="{{ $index }}" style="display:none;">
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

                    @if($task->task_prompt)
                        <div class="p-8 bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] rounded-[var(--radius-lg)] border border-[color-mix(in_srgb,var(--color-primary)_20%,transparent)] text-[var(--color-text-primary)] font-semibold leading-relaxed">
                            <span class="block text-[10px] font-bold uppercase tracking-widest text-[var(--color-primary)] mb-2">Detailed Prompt</span>
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
                              class="writing-textarea absolute inset-0 w-full h-full p-8 sm:p-12 text-base leading-relaxed bg-transparent border-none focus:ring-0 resize-none custom-scrollbar placeholder:text-[var(--color-text-secondary)] text-[var(--color-text-primary)] outline-none"
                              placeholder="Start typing your response here...">{{ $answers[$task->id]->answer_text ?? '' }}</textarea>
                </div>
                <div class="flex h-16 shrink-0 items-center justify-between border-t border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-8">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-[var(--radius-sm)] border transition-all bg-[var(--color-bg-secondary)] border-[var(--color-divider)] text-[var(--color-text-secondary)]" id="wc-badge-{{ $task->id }}">
                            <span class="text-xs font-bold tabular-nums" id="wc-{{ $task->id }}">0</span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider opacity-80">Words</span>
                        </div>
                        <div class="text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">
                            Target: {{ $task->minimum_word_count }} min.
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-[var(--color-primary)] animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-primary)]">Live Sync Active</span>
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
    <div id="writing-hidden-inputs"></div>
</form>
@endsection

@push('scripts')
<script>
(function() {
    const autosaveUrl = '{{ route("user.writing.autosave", $attempt->id) }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let timeRemaining = {{ (int) $remainingSeconds }};
    let currentTab = 0;

    // ── Timer ──
    function updateTimer() {
        const m = Math.floor(timeRemaining / 60);
        const s = timeRemaining % 60;
        document.getElementById('timer-display').textContent = m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
    }
    updateTimer();
    setInterval(function() {
        timeRemaining--;
        if (timeRemaining <= 0) {
            populateHiddenInputs();
            document.getElementById('writing-submit-form').submit();
            return;
        }
        updateTimer();
    }, 1000);

    // ── Tab switching ──
    window.switchWritingTask = function(idx) {
        currentTab = idx;
        document.querySelectorAll('.writing-panel').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.writing-tab').forEach(el => {
            el.classList.remove('border-[var(--color-primary)]', 'text-[var(--color-primary)]');
            el.classList.add('border-transparent', 'text-[var(--color-text-secondary)]');
        });
        const panel = document.querySelector('.writing-panel[data-tab="'+idx+'"]');
        const tab = document.getElementById('wtab-'+idx);
        if (panel) panel.style.display = 'flex';
        if (tab) {
            tab.classList.remove('border-transparent', 'text-[var(--color-text-secondary)]');
            tab.classList.add('border-[var(--color-primary)]', 'text-[var(--color-primary)]');
        }
    };
    switchWritingTask(0);

    // Init word counts
    document.querySelectorAll('.writing-textarea').forEach(ta => {
        window.updateWordCount(parseInt(ta.dataset.taskId), parseInt(ta.dataset.minWords));
    });

    // ── Word count ──
    window.updateWordCount = function(taskId, minWords) {
        const ta = document.getElementById('textarea-' + taskId);
        if (!ta) return;
        const text = ta.value.trim();
        const count = text === '' ? 0 : text.split(/\s+/).length;
        const wcEl = document.getElementById('wc-' + taskId);
        const badge = document.getElementById('wc-badge-' + taskId);
        if (wcEl) wcEl.textContent = count;
        if (badge) {
            if (count >= minWords) {
                badge.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-[var(--radius-sm)] border transition-all bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] border-[color-mix(in_srgb,var(--color-success)_20%,transparent)] text-[var(--color-success)]';
            } else {
                badge.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-[var(--radius-sm)] border transition-all bg-[var(--color-bg-secondary)] border-[var(--color-divider)] text-[var(--color-text-secondary)]';
            }
        }
    };

    // ── Autosave ──
    let autosaveTimer = null;
    window.scheduleAutosave = function() {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(doAutosave, 3000);
    };
    async function doAutosave() {
        const indicator = document.getElementById('save-indicator');
        indicator.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin text-slate-400">refresh</span><span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Saving...</span>';
        const answers = {};
        document.querySelectorAll('.writing-textarea').forEach(ta => {
            answers[ta.dataset.taskId] = ta.value;
        });
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

    // ── Submit ──
    function populateHiddenInputs() {
        const container = document.getElementById('writing-hidden-inputs');
        container.innerHTML = '';
        document.querySelectorAll('.writing-textarea').forEach(ta => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'answers[' + ta.dataset.taskId + ']';
            input.value = ta.value;
            container.appendChild(input);
        });
    }
    window.submitWritingTest = function() {
        if (confirm('Final Submission: Are you sure you want to end your writing exam? Your answers will be locked.')) {
            populateHiddenInputs();
            document.getElementById('writing-submit-form').submit();
        }
    };
})();
</script>
@endpush
