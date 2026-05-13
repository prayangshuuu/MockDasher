@extends('layouts.exam')

@section('title', 'Writing Test')
@section('test_type', 'IELTS Writing')
@section('test_title', 'IELTS Writing Exam')

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 px-4 py-2 rounded-2xl shadow-sm transition-all border-slate-100 dark:border-slate-700">
    <span class="material-symbols-outlined text-xl text-primary" id="timer-icon">timer</span>
    <div class="flex items-baseline gap-1.5">
        <span class="text-2xl font-black font-mono tracking-tighter tabular-nums text-slate-900 dark:text-white" id="timer-display">--:--</span>
        <span class="text-[10px] font-black uppercase tracking-widest opacity-40">Remaining</span>
    </div>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-4">
    <div id="save-indicator" class="flex items-center gap-2 text-emerald-500">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        <span class="text-[10px] font-black uppercase tracking-widest">Saved</span>
    </div>
    <button onclick="submitWritingTest()" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg shadow-slate-200 dark:shadow-none">
        <span class="material-symbols-outlined text-sm">send</span>
        End Exam
    </button>
</div>
@endsection

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Task Tabs --}}
    <div class="h-12 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center px-8 gap-6 z-10">
        @foreach($tasks as $index => $task)
            <button onclick="switchWritingTask({{ $index }})"
                    class="writing-tab h-full flex items-center gap-2 px-4 text-xs font-black uppercase tracking-widest transition-all border-b-2"
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
            <div class="w-1/2 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/50 p-10 border-r border-slate-200 dark:border-slate-800">
                <div class="max-w-2xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary/10 text-primary rounded-lg text-[10px] font-black uppercase tracking-widest mb-6">
                        <span class="material-symbols-outlined text-xs">info</span>
                        Writing Task {{ $task->task_number }}
                    </div>

                    <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-6 tracking-tight leading-tight">
                        {{ $task->task_title ?: 'Simulation Prompt' }}
                    </h2>

                    @if($task->task_description)
                        <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-soft mb-8 text-slate-600 dark:text-slate-300 font-medium leading-relaxed italic">
                            {{ $task->task_description }}
                        </div>
                    @endif

                    @if($task->images->count() > 0)
                        <div class="rounded-3xl border-4 border-white dark:border-slate-800 shadow-xl overflow-hidden mb-8 bg-white dark:bg-slate-900">
                            <img src="{{ Storage::url($task->images->first()->image_path) }}" class="w-full h-auto object-contain max-h-[500px]" alt="Task Content">
                        </div>
                    @endif

                    @if($task->task_prompt)
                        <div class="p-8 bg-indigo-50 dark:bg-indigo-900/20 rounded-3xl border-2 border-primary/20 text-slate-800 dark:text-slate-200 font-bold leading-relaxed shadow-lg">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">Detailed Prompt</span>
                            <div class="whitespace-pre-line">{{ $task->task_prompt }}</div>
                        </div>
                    @endif

                    @if($task->instruction_text)
                        <div class="mt-6 flex items-center gap-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800">
                            <span class="material-symbols-outlined text-amber-500 text-lg">info</span>
                            <span class="text-sm font-bold text-amber-700 dark:text-amber-400">{{ $task->instruction_text }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right: Answer Editor --}}
            <div class="w-1/2 flex flex-col bg-white dark:bg-slate-950">
                <div class="flex-1 relative">
                    <textarea id="textarea-{{ $task->id }}"
                              data-task-id="{{ $task->id }}"
                              data-min-words="{{ $task->minimum_word_count }}"
                              oninput="updateWordCount({{ $task->id }}, {{ $task->minimum_word_count }}); scheduleAutosave();"
                              class="writing-textarea absolute inset-0 w-full h-full p-12 text-lg font-medium bg-transparent border-none focus:ring-0 resize-none custom-scrollbar leading-relaxed placeholder:text-slate-300 dark:placeholder:text-slate-700 outline-none"
                              placeholder="Start typing your response here...">{{ $answers[$task->id]->answer_text ?? '' }}</textarea>
                </div>
                <div class="h-16 border-t border-slate-200 dark:border-slate-800 px-8 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all bg-slate-50 border-slate-200 text-slate-500" id="wc-badge-{{ $task->id }}">
                            <span class="text-xs font-black tabular-nums" id="wc-{{ $task->id }}">0</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest opacity-60">Words</span>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Target: {{ $task->minimum_word_count }} min.
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-primary">Live Sync Active</span>
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
            el.classList.remove('border-primary', 'text-primary');
            el.classList.add('border-transparent', 'text-slate-400');
        });
        const panel = document.querySelector('.writing-panel[data-tab="'+idx+'"]');
        const tab = document.getElementById('wtab-'+idx);
        if (panel) panel.style.display = 'flex';
        if (tab) {
            tab.classList.remove('border-transparent', 'text-slate-400');
            tab.classList.add('border-primary', 'text-primary');
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
                badge.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all bg-emerald-50 border-emerald-200 text-emerald-700';
            } else {
                badge.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all bg-slate-50 border-slate-200 text-slate-500';
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
