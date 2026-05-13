@extends('layouts.exam')

@section('title', 'Reading Test - IELTS ' . $test->book_number)
@section('test_type', 'IELTS Reading')
@section('test_title', 'IELTS ' . $test->book_number)

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
    <x-ui.button variant="primary" onclick="document.getElementById('review-overlay').classList.remove('hidden')" class="text-xs">
        <span class="material-symbols-outlined text-sm">assignment_turned_in</span>
        <span class="hidden sm:inline">Review & Submit</span>
    </x-ui.button>
</div>
@endsection

@section('content')
<div id="reading-app" class="flex flex-1 flex-col overflow-hidden">

    {{-- Passage Tabs --}}
    <div class="flex h-11 shrink-0 items-center gap-1 border-b border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-4 sm:px-6 lg:px-8">
        @foreach($passages as $passage)
            <button onclick="switchPassage({{ $passage->passage_number }})"
                    class="passage-tab h-full border-b-2 px-4 text-xs font-bold uppercase tracking-wider transition-colors"
                    data-passage="{{ $passage->passage_number }}"
                    id="tab-{{ $passage->passage_number }}">
                Passage {{ $passage->passage_number }}
            </button>
        @endforeach
    </div>

    {{-- Main Split View --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- Left: Passage Text --}}
        <div class="w-1/2 overflow-y-auto border-r border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-6 sm:p-8 lg:p-12 custom-scrollbar">
            @foreach($passages as $passage)
                <div class="passage-content mx-auto max-w-3xl space-y-6" data-passage="{{ $passage->passage_number }}" style="display:none;">
                    <h2 class="text-2xl font-bold tracking-tight text-[var(--color-text-primary)]">{{ $passage->title }}</h2>
                    <div class="prose max-w-none text-base leading-[1.9] text-[var(--color-text-secondary)]">
                        {!! $passage->content !!}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Right: Questions --}}
        <div class="w-1/2 overflow-y-auto bg-[var(--color-bg-secondary)] p-6 sm:p-8 lg:p-12 custom-scrollbar">
            @php $globalQ = 0; @endphp
            @foreach($passages as $passage)
                <div class="questions-panel space-y-8" data-passage="{{ $passage->passage_number }}" style="display:none;">
                    @foreach($passage->questionGroups as $group)
                        @if($group->group_instruction)
                            <div class="rounded-[var(--radius-base)] border border-[color-mix(in_srgb,var(--color-primary)_20%,transparent)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] p-5">
                                <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-[var(--color-primary)]">Instructions</p>
                                <p class="text-sm leading-relaxed text-[var(--color-text-secondary)]">{{ $group->group_instruction }}</p>
                            </div>
                        @endif
                        <div class="space-y-5">
                            @foreach($group->questions as $question)
                                @php $globalQ++; @endphp
                                <div id="question-{{ $question->id }}" class="question-card rounded-[var(--radius-lg)] border border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-5 sm:p-6 transition-colors" data-qid="{{ $question->id }}">
                                    <div class="mb-4 flex items-start gap-3">
                                        <div class="flex size-7 shrink-0 items-center justify-center rounded-[var(--radius-xs)] bg-[var(--color-bg-secondary)] text-xs font-bold text-[var(--color-text-primary)]">{{ $globalQ }}</div>
                                        <div class="flex-1 text-base font-semibold leading-relaxed text-[var(--color-text-primary)]">
                                            {!! nl2br(e($question->question_text)) !!}
                                        </div>
                                        <button onclick="toggleFlag({{ $question->id }})" class="flag-btn shrink-0 text-[var(--color-divider)] transition-colors hover:text-[var(--color-error)]" data-qid="{{ $question->id }}">
                                            <span class="material-symbols-outlined text-xl">flag</span>
                                        </button>
                                    </div>
                                    <div class="pl-10">
                                        @if($question->question_type === 'multiple_choice')
                                            <div class="space-y-2">
                                                @foreach($question->options as $oi => $opt)
                                                    @php $letter = chr(65+$oi); @endphp
                                                    <label class="mcq-option flex cursor-pointer items-center gap-3 rounded-[var(--radius-base)] border border-[var(--color-divider)] p-3.5 transition-all hover:border-[var(--color-primary)]" data-qid="{{ $question->id }}" data-val="{{ $letter }}">
                                                        <input type="radio" name="q_{{ $question->id }}" value="{{ $letter }}" onchange="setAnswer({{ $question->id }}, '{{ $letter }}')" class="size-4 accent-[var(--color-primary)]" {{ ($savedAnswers[$question->id] ?? '') === $letter ? 'checked' : '' }}>
                                                        <span class="text-sm"><span class="font-semibold text-[var(--color-text-secondary)]">{{ $letter }}.</span> {{ $opt->option_text }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif(in_array($question->question_type, ['true_false_not_given', 'yes_no_not_given']))
                                            @php $opts = $question->question_type === 'true_false_not_given' ? ['TRUE', 'FALSE', 'NOT GIVEN'] : ['YES', 'NO', 'NOT GIVEN']; @endphp
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($opts as $opt)
                                                    <button type="button" onclick="setAnswer({{ $question->id }}, '{{ $opt }}')"
                                                            class="tfng-btn perfect-shape btn-active-state border px-5 py-2 text-xs font-bold uppercase tracking-wider transition-all {{ strtoupper($savedAnswers[$question->id] ?? '') === $opt ? 'bg-[var(--color-primary)] border-[var(--color-primary)] text-white' : 'border-[var(--color-divider)] bg-[var(--color-bg-primary)] text-[var(--color-text-secondary)] hover:border-[var(--color-primary)]' }}"
                                                            data-qid="{{ $question->id }}" data-val="{{ $opt }}">
                                                        {{ $opt }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @else
                                            <x-ui.input
                                                value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                oninput="setAnswer({{ $question->id }}, this.value)"
                                                placeholder="Type your answer…"
                                                class="max-w-md" />
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- Bottom Question Navigator --}}
    <div class="flex h-16 shrink-0 items-center border-t border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-4 sm:px-6 lg:px-8">
        <div class="flex flex-1 gap-1.5 overflow-x-auto py-2 custom-scrollbar">
            @php $qIdx = 0; @endphp
            @foreach($passages as $passage)
                @foreach($passage->questionGroups as $group)
                    @foreach($group->questions as $question)
                        @php $qIdx++; @endphp
                        <button onclick="jumpToQuestion({{ $question->id }}, {{ $passage->passage_number }})"
                                class="answer-bubble flex size-9 shrink-0 items-center justify-center rounded-[var(--radius-base)] text-xs font-bold transition-all relative {{ !empty($savedAnswers[$question->id]) ? 'bg-[var(--color-primary)] text-white' : 'bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)]' }}"
                                data-qid="{{ $question->id }}" id="bubble-{{ $question->id }}">
                            {{ $qIdx }}
                            <div class="flag-dot absolute -right-1 -top-1 size-3 rounded-full border-2 border-[var(--color-bg-primary)] bg-[var(--color-error)] {{ !empty($flaggedAnswers[$question->id]) ? '' : 'hidden' }}" data-qid="{{ $question->id }}"></div>
                        </button>
                    @endforeach
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Review Overlay --}}
    <div id="review-overlay" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/40 backdrop-blur-sm p-6">
        <div class="flex max-h-full w-full max-w-3xl flex-col overflow-hidden rounded-[var(--radius-xl)] border border-[var(--color-divider)] bg-[var(--color-bg-primary)]">
            {{-- Review Header --}}
            <div class="flex items-center justify-between border-b border-[var(--color-divider)] px-6 py-5 sm:px-8">
                <div class="flex items-center gap-4">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)]">
                        <span class="material-symbols-outlined text-2xl">fact_check</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[var(--color-text-primary)]">Review Summary</h2>
                        <p class="text-small text-xs">Check all answers before final submission.</p>
                    </div>
                </div>
                <button onclick="document.getElementById('review-overlay').classList.add('hidden')" class="flex size-8 items-center justify-center rounded-[var(--radius-xs)] text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-bg-secondary)] hover:text-[var(--color-error)]">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- Review Stats --}}
            <div class="flex-1 overflow-y-auto px-6 py-6 sm:px-8 custom-scrollbar">
                <div class="mb-6 grid grid-cols-3 gap-3">
                    <div class="rounded-[var(--radius-base)] bg-[var(--color-bg-secondary)] p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Answered</p>
                        <p class="mt-1 text-2xl font-bold text-[var(--color-text-primary)]" id="review-answered">0</p>
                    </div>
                    <div class="rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-error)_6%,transparent)] p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--color-error)]">Flagged</p>
                        <p class="mt-1 text-2xl font-bold text-[var(--color-error)]" id="review-flagged">0</p>
                    </div>
                    <div class="rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_6%,transparent)] p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--color-primary)]">Time Left</p>
                        <p class="mt-1 text-2xl font-bold font-mono text-[var(--color-primary)]" id="review-timer">--:--</p>
                    </div>
                </div>

                {{-- Question Grid per Passage --}}
                @php $revIdx = 0; @endphp
                @foreach($passages as $p)
                    <div class="mb-5">
                        <p class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">
                            Passage {{ $p->passage_number }}
                            <span class="h-px flex-1 bg-[var(--color-divider)]"></span>
                        </p>
                        <div class="grid grid-cols-6 gap-2 sm:grid-cols-8">
                            @foreach($p->questionGroups->flatMap(fn($g) => $g->questions) as $q)
                                @php $revIdx++; @endphp
                                <button onclick="jumpToQuestion({{ $q->id }}, {{ $p->passage_number }}); document.getElementById('review-overlay').classList.add('hidden');"
                                        class="review-bubble flex aspect-square items-center justify-center rounded-[var(--radius-base)] border text-xs font-bold transition-all relative {{ !empty($savedAnswers[$q->id]) ? 'bg-[var(--color-primary)] border-[var(--color-primary)] text-white' : 'border-[var(--color-divider)] bg-[var(--color-bg-primary)] text-[var(--color-text-secondary)] hover:border-[var(--color-primary)]' }}"
                                        data-qid="{{ $q->id }}">
                                    {{ $revIdx }}
                                    <div class="review-flag-dot absolute -right-0.5 -top-0.5 size-2.5 rounded-full border-2 border-[var(--color-bg-primary)] bg-[var(--color-error)] {{ !empty($flaggedAnswers[$q->id]) ? '' : 'hidden' }}" data-qid="{{ $q->id }}"></div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Review Footer --}}
            <div class="flex items-center justify-between border-t border-[var(--color-divider)] bg-[var(--color-bg-secondary)] px-6 py-4 sm:px-8">
                <button onclick="document.getElementById('review-overlay').classList.add('hidden')" class="text-sm font-semibold text-[var(--color-text-secondary)] transition-opacity hover:opacity-70">
                    Back to Questions
                </button>
                <form id="final-submit-form" action="{{ route('user.reading.submit', $attempt->id) }}" method="POST">
                    @csrf
                    <div id="hidden-answers-container"></div>
                    <x-ui.button type="button" variant="primary" onclick="confirmSubmit()">
                        Submit Test Final
                    </x-ui.button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const answers = @json((object) $savedAnswers);
    const flags = {};
    @foreach($flaggedAnswers as $qId => $val)
        flags[{{ $qId }}] = true;
    @endforeach

    let currentPassage = 1;
    let timeRemaining = {{ $remainingSeconds }};
    const autosaveUrl = '{{ route("user.reading.autosave", $attempt->id) }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ── Timer ──
    function updateTimer() {
        const m = Math.floor(timeRemaining / 60);
        const s = timeRemaining % 60;
        const display = m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
        document.getElementById('timer-display').textContent = display;
        const rt = document.getElementById('review-timer');
        if (rt) rt.textContent = display;

        if (timeRemaining <= 300) {
            document.getElementById('timer-widget').style.borderColor = 'var(--color-error)';
            document.getElementById('timer-display').style.color = 'var(--color-error)';
            document.getElementById('timer-icon').style.color = 'var(--color-error)';
            document.getElementById('timer-icon').classList.add('animate-pulse');
        }
    }
    updateTimer();
    setInterval(function() {
        timeRemaining--;
        if (timeRemaining <= 0) { populateHiddenInputs(); document.getElementById('final-submit-form').submit(); return; }
        updateTimer();
    }, 1000);

    // ── Passage Switching ──
    window.switchPassage = function(num) {
        currentPassage = num;
        document.querySelectorAll('.passage-content, .questions-panel').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.passage-tab').forEach(el => {
            el.classList.remove('border-[var(--color-primary)]', 'text-[var(--color-primary)]');
            el.classList.add('border-transparent', 'text-[var(--color-text-secondary)]');
        });
        const ac = document.querySelector('.passage-content[data-passage="'+num+'"]');
        const aq = document.querySelector('.questions-panel[data-passage="'+num+'"]');
        const at = document.getElementById('tab-'+num);
        if (ac) ac.style.display = 'block';
        if (aq) aq.style.display = 'block';
        if (at) { at.classList.remove('border-transparent', 'text-[var(--color-text-secondary)]'); at.classList.add('border-[var(--color-primary)]', 'text-[var(--color-primary)]'); }
    };
    switchPassage(1);

    // ── Answer Management ──
    window.setAnswer = function(qId, value) {
        answers[qId] = value;
        updateBubble(qId);
        updateTfngButtons(qId, value);
        updateMcqOptions(qId, value);
        scheduleAutosave();
    };

    function updateBubble(qId) {
        const bubble = document.getElementById('bubble-' + qId);
        if (!bubble) return;
        const filled = answers[qId] && answers[qId].trim() !== '';
        bubble.className = 'answer-bubble flex size-9 shrink-0 items-center justify-center rounded-[var(--radius-base)] text-xs font-bold transition-all relative ' +
            (filled ? 'bg-[var(--color-primary)] text-white' : 'bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)]');
        document.querySelectorAll('.review-bubble[data-qid="'+qId+'"]').forEach(rb => {
            if (filled) {
                rb.className = rb.className.replace(/border-\[var\(--color-divider\)\]|bg-\[var\(--color-bg-primary\)\]|text-\[var\(--color-text-secondary\)\]/g, '');
                rb.classList.add('bg-[var(--color-primary)]', 'border-[var(--color-primary)]', 'text-white');
            }
        });
    }

    function updateTfngButtons(qId, value) {
        document.querySelectorAll('.tfng-btn[data-qid="'+qId+'"]').forEach(btn => {
            const sel = btn.dataset.val === value;
            btn.className = 'tfng-btn perfect-shape btn-active-state border px-5 py-2 text-xs font-bold uppercase tracking-wider transition-all ' +
                (sel ? 'bg-[var(--color-primary)] border-[var(--color-primary)] text-white' : 'border-[var(--color-divider)] bg-[var(--color-bg-primary)] text-[var(--color-text-secondary)] hover:border-[var(--color-primary)]');
        });
    }

    function updateMcqOptions(qId, value) {
        document.querySelectorAll('.mcq-option[data-qid="'+qId+'"]').forEach(label => {
            const sel = label.dataset.val === value;
            label.className = 'mcq-option flex cursor-pointer items-center gap-3 rounded-[var(--radius-base)] border p-3.5 transition-all ' +
                (sel ? 'border-[var(--color-primary)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)]' : 'border-[var(--color-divider)] hover:border-[var(--color-primary)]');
        });
    }

    // ── Flagging ──
    window.toggleFlag = function(qId) {
        flags[qId] = !flags[qId];
        const btn = document.querySelector('.flag-btn[data-qid="'+qId+'"]');
        if (btn) btn.style.color = flags[qId] ? 'var(--color-error)' : 'var(--color-divider)';
        document.querySelectorAll('.flag-dot[data-qid="'+qId+'"], .review-flag-dot[data-qid="'+qId+'"]').forEach(dot => dot.classList.toggle('hidden', !flags[qId]));
        scheduleAutosave();
    };

    // ── Navigation ──
    window.jumpToQuestion = function(qId, passageNum) {
        switchPassage(passageNum);
        setTimeout(function() { const el = document.getElementById('question-' + qId); if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
    };

    // ── Autosave ──
    let autosaveTimer = null;
    function scheduleAutosave() { clearTimeout(autosaveTimer); autosaveTimer = setTimeout(doAutosave, 3000); }
    async function doAutosave() {
        const ind = document.getElementById('save-indicator');
        ind.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin text-[var(--color-text-secondary)]">refresh</span><span class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-secondary)]">Saving…</span>';
        try {
            await fetch(autosaveUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ answers, flagged: flags }) });
            ind.innerHTML = '<span class="material-symbols-outlined text-sm text-[var(--color-success)]">check_circle</span><span class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-success)]">Saved</span>';
        } catch(e) {
            ind.innerHTML = '<span class="material-symbols-outlined text-sm text-[var(--color-error)]">error</span><span class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-error)]">Error</span>';
        }
    }
    setInterval(doAutosave, 20000);

    // ── Submit ──
    function populateHiddenInputs() {
        const c = document.getElementById('hidden-answers-container');
        c.innerHTML = '';
        for (const [qId, val] of Object.entries(answers)) {
            if (val && val.trim() !== '') { const i = document.createElement('input'); i.type='hidden'; i.name='answers['+qId+']'; i.value=val; c.appendChild(i); }
        }
        for (const [qId, val] of Object.entries(flags)) {
            if (val) { const i = document.createElement('input'); i.type='hidden'; i.name='flagged['+qId+']'; i.value='1'; c.appendChild(i); }
        }
    }

    window.confirmSubmit = function() {
        const answeredCount = Object.values(answers).filter(v => v && typeof v === 'string' && v.trim() !== '').length;
        const flaggedCount = Object.values(flags).filter(v => v === true).length;
        
        const answeredEl = document.getElementById('review-answered');
        const flaggedEl = document.getElementById('review-flagged');
        if (answeredEl) answeredEl.textContent = answeredCount;
        if (flaggedEl) flaggedEl.textContent = flaggedCount;

        if (confirm('Final Submission: Your reading test will be closed for grading. Are you sure?')) { 
            populateHiddenInputs(); 
            const form = document.getElementById('final-submit-form');
            if (form) form.submit();
        }
    };

    const ro = document.getElementById('review-overlay');
    if (ro) {
        new MutationObserver(function() {
            if (!ro.classList.contains('hidden')) {
                const answeredCount = Object.values(answers).filter(v => v && typeof v === 'string' && v.trim() !== '').length;
                const flaggedCount = Object.values(flags).filter(v => v === true).length;
                const answeredEl = document.getElementById('review-answered');
                const flaggedEl = document.getElementById('review-flagged');
                if (answeredEl) answeredEl.textContent = answeredCount;
                if (flaggedEl) flaggedEl.textContent = flaggedCount;
            }
        }).observe(ro, { attributes: true, attributeFilter: ['class'] });
    }
})();
</script>
@endpush
