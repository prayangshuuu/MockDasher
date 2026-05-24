@extends('layouts.exam')

@section('title', 'Reading Test - IELTS ' . $test->book_number)
@section('test_type', 'IELTS Reading')
@section('test_title', 'IELTS ' . $test->book_number)

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 py-1.5 transition-all shadow-soft duration-200">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-500 fill-current" viewBox="0 0 24 24" id="timer-icon">
        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
        <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
    </svg>
    <span class="text-sm font-black tabular-nums tracking-tight text-slate-800 dark:text-slate-200 font-mono" id="timer-display">60:00</span>
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
    <button onclick="document.getElementById('review-overlay').classList.remove('hidden')" class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-soft transition-all duration-150 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="hidden sm:inline">Review & Submit</span>
    </button>
</div>
@endsection

@section('content')
<style>
    .mcq-option:has(input:checked) {
        border-color: #6366f1; /* indigo-500 */
        background-color: rgba(99, 102, 241, 0.04);
        color: #4f46e5; /* indigo-600 */
    }
    .dark .mcq-option:has(input:checked) {
        border-color: #818cf8; /* indigo-400 */
        background-color: rgba(129, 140, 248, 0.08);
        color: #c7d2fe; /* indigo-200 */
    }
</style>

<div id="reading-app" class="flex flex-1 flex-col overflow-hidden">

    {{-- Passage Tabs --}}
    <div class="flex h-11 shrink-0 items-center gap-1 border-b border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 sm:px-6 lg:px-8">
        @foreach($passages as $passage)
            <button onclick="switchPassage({{ $passage->passage_number }})"
                    class="passage-tab h-full border-b-2 px-5 text-xs font-black uppercase tracking-widest transition-all focus:outline-none"
                    data-passage="{{ $passage->passage_number }}"
                    id="tab-{{ $passage->passage_number }}">
                Passage {{ $passage->passage_number }}
            </button>
        @endforeach
    </div>

    {{-- Main Split View --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- Left: Passage Text --}}
        <div class="w-1/2 overflow-y-auto border-r border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark p-6 sm:p-8 lg:p-12 custom-scrollbar">
            @foreach($passages as $passage)
                <div class="passage-content mx-auto max-w-3xl space-y-6" data-passage="{{ $passage->passage_number }}" style="display:none;">
                    <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ $passage->title }}</h2>
                    <div class="prose max-w-none text-base leading-[1.9] text-slate-600 dark:text-slate-350">
                        {!! $passage->content !!}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Right: Questions --}}
        <div class="w-1/2 overflow-y-auto bg-slate-50 dark:bg-slate-900/40 p-6 sm:p-8 lg:p-12 custom-scrollbar">
            @php $globalQ = 0; @endphp
            @foreach($passages as $passage)
                <div class="questions-panel space-y-6" data-passage="{{ $passage->passage_number }}" style="display:none;">
                    @foreach($passage->questionGroups as $group)
                        @if($group->group_instruction)
                            <div class="rounded-xl border border-indigo-100 dark:border-indigo-900/40 bg-indigo-50/50 dark:bg-indigo-950/20 p-5 shadow-soft">
                                <p class="mb-1 text-[9px] font-black uppercase tracking-widest text-indigo-500">Instructions</p>
                                <p class="text-xs sm:text-sm leading-relaxed text-slate-600 dark:text-slate-400 font-medium">{{ $group->group_instruction }}</p>
                            </div>
                        @endif
                        <div class="space-y-5">
                            @foreach($group->questions as $question)
                                @php $globalQ++; @endphp
                                <div id="question-{{ $question->id }}" class="question-card rounded-2xl border border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark p-5 sm:p-6 shadow-soft transition-colors" data-qid="{{ $question->id }}">
                                    <div class="mb-4 flex items-start gap-4">
                                        <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700/50">{{ $globalQ }}</div>
                                        <div class="flex-1 text-sm sm:text-base font-semibold leading-relaxed text-slate-800 dark:text-slate-200">
                                            {!! nl2br(e($question->question_text)) !!}
                                        </div>
                                        <button onclick="toggleFlag({{ $question->id }})" class="flag-btn shrink-0 text-slate-300 dark:text-slate-700 transition-colors hover:text-rose-500 focus:outline-none" data-qid="{{ $question->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current transition-transform duration-150 active:scale-90" viewBox="0 0 24 24">
                                                <path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6h-5.6z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="pl-11">
                                        @if($question->question_type === 'multiple_choice')
                                            <div class="space-y-2">
                                                @foreach($question->options as $oi => $opt)
                                                    @php $letter = chr(65+$oi); @endphp
                                                    <label class="mcq-option flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-800 p-3 transition-all hover:border-indigo-500 text-slate-600 dark:text-slate-400" data-qid="{{ $question->id }}" data-val="{{ $letter }}">
                                                        <input type="radio" name="q_{{ $question->id }}" value="{{ $letter }}" onchange="setAnswer({{ $question->id }}, '{{ $letter }}')" class="size-4 accent-indigo-500 cursor-pointer" {{ ($savedAnswers[$question->id] ?? '') === $letter ? 'checked' : '' }}>
                                                        <span class="text-xs sm:text-sm"><span class="font-bold text-slate-400 dark:text-slate-500">{{ $letter }}.</span> {{ $opt->option_text }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif(in_array($question->question_type, ['true_false_not_given', 'yes_no_not_given']))
                                            @php $opts = $question->question_type === 'true_false_not_given' ? ['TRUE', 'FALSE', 'NOT GIVEN'] : ['YES', 'NO', 'NOT GIVEN']; @endphp
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($opts as $opt)
                                                    <button type="button" onclick="setAnswer({{ $question->id }}, '{{ $opt }}')"
                                                            class="tfng-btn border px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-150 focus:outline-none {{ strtoupper($savedAnswers[$question->id] ?? '') === $opt ? 'bg-indigo-500 border-indigo-500 text-white shadow-soft' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500' }}"
                                                            data-qid="{{ $question->id }}" data-val="{{ $opt }}">
                                                        {{ $opt }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @else
                                            <input type="text" 
                                                   value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                   oninput="setAnswer({{ $question->id }}, this.value)"
                                                   placeholder="Type your answer…"
                                                   class="w-full max-w-md bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-200 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none" />
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
    <div class="flex h-16 shrink-0 items-center border-t border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 sm:px-6 lg:px-8 z-30 shadow-[0_-2px_10px_rgba(0,0,0,0.02)]">
        <div class="flex flex-1 gap-1.5 overflow-x-auto py-2 custom-scrollbar">
            @php $qIdx = 0; @endphp
            @foreach($passages as $passage)
                @foreach($passage->questionGroups as $group)
                    @foreach($group->questions as $question)
                        @php $qIdx++; @endphp
                        <button onclick="jumpToQuestion({{ $question->id }}, {{ $passage->passage_number }})"
                                class="answer-bubble flex size-9 shrink-0 items-center justify-center rounded-xl text-xs font-black transition-all relative border focus:outline-none {{ !empty($savedAnswers[$question->id]) ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400' }}"
                                data-qid="{{ $question->id }}" id="bubble-{{ $question->id }}">
                            {{ $qIdx }}
                            <div class="flag-dot absolute -right-1 -top-1 size-3 rounded-full border-2 border-white dark:border-slate-900 bg-rose-500 {{ !empty($flaggedAnswers[$question->id]) ? '' : 'hidden' }}" data-qid="{{ $question->id }}"></div>
                        </button>
                    @endforeach
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Review Overlay --}}
    <div id="review-overlay" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-950/40 backdrop-blur-md p-6">
        <div class="flex max-h-full w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark shadow-premium">
            {{-- Review Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-6 py-5 sm:px-8 shrink-0 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-3.5">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50">
                        <img src="/storage/asset/icons/verified.svg" class="w-5 h-5 filter-indigo-600 dark:invert" alt="Check" />
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">Reading Review Summary</h2>
                        <p class="text-xs text-slate-400 dark:text-slate-500">Check all answers and flagged items before final submission.</p>
                    </div>
                </div>
                <button onclick="document.getElementById('review-overlay').classList.add('hidden')" class="flex size-8 items-center justify-center rounded-lg text-slate-400 transition-all hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-rose-500 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>

            {{-- Review Stats --}}
            <div class="flex-1 overflow-y-auto px-6 py-6 sm:px-8 custom-scrollbar">
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4">
                        <p class="text-[9px] font-black uppercase tracking-wider text-slate-450 dark:text-slate-500">Total Answered</p>
                        <p class="mt-1.5 text-2xl font-black text-slate-900 dark:text-white leading-none" id="review-answered">0</p>
                    </div>
                    <div class="rounded-xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 p-4">
                        <p class="text-[9px] font-black uppercase tracking-wider text-rose-500">Questions Flagged</p>
                        <p class="mt-1.5 text-2xl font-black text-rose-500 leading-none" id="review-flagged">0</p>
                    </div>
                    <div class="rounded-xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 p-4">
                        <p class="text-[9px] font-black uppercase tracking-wider text-indigo-500">Time Left</p>
                        <p class="mt-1.5 text-2xl font-black font-mono text-indigo-500 leading-none" id="review-timer">60:00</p>
                    </div>
                </div>

                {{-- Question Grid per Passage --}}
                @php $revIdx = 0; @endphp
                @foreach($passages as $p)
                    <div class="mb-6">
                        <p class="mb-3 flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 text-left">
                            Passage {{ $p->passage_number }}
                            <span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span>
                        </p>
                        <div class="grid grid-cols-6 gap-2.5 sm:grid-cols-8">
                            @foreach($p->questionGroups->flatMap(fn($g) => $g->questions) as $q)
                                @php $revIdx++; @endphp
                                <button onclick="jumpToQuestion({{ $q->id }}, {{ $p->passage_number }}); document.getElementById('review-overlay').classList.add('hidden');"
                                        class="review-bubble flex aspect-square items-center justify-center rounded-xl border text-xs font-black transition-all relative focus:outline-none {{ !empty($savedAnswers[$q->id]) ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500' }}"
                                        data-qid="{{ $q->id }}">
                                    {{ $revIdx }}
                                    <div class="review-flag-dot absolute -right-0.5 -top-0.5 size-2.5 rounded-full border-2 border-white dark:border-slate-900 bg-rose-500 {{ !empty($flaggedAnswers[$q->id]) ? '' : 'hidden' }}" data-qid="{{ $q->id }}"></div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Review Footer --}}
            <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 px-6 py-4 sm:px-8 shrink-0">
                <button onclick="document.getElementById('review-overlay').classList.add('hidden')" class="text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-500 transition-colors focus:outline-none">
                    Back to Questions
                </button>
                <form id="final-submit-form" action="{{ route('user.reading.submit', $attempt->id) }}" method="POST">
                    @csrf
                    <div id="hidden-answers-container"></div>
                    <button type="button" onclick="confirmSubmit()" class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-soft transition-all duration-150 focus:outline-none">
                        Submit Test Final
                    </button>
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
            document.getElementById('timer-widget').classList.add('border-rose-500', 'bg-rose-50', 'dark:bg-rose-950/20');
            document.getElementById('timer-display').classList.add('text-rose-500');
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
            el.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
            el.classList.add('border-transparent', 'text-slate-400', 'dark:text-slate-500');
        });
        const ac = document.querySelector('.passage-content[data-passage="'+num+'"]');
        const aq = document.querySelector('.questions-panel[data-passage="'+num+'"]');
        const at = document.getElementById('tab-'+num);
        if (ac) ac.style.display = 'block';
        if (aq) aq.style.display = 'block';
        if (at) { 
            at.classList.remove('border-transparent', 'text-slate-400', 'dark:text-slate-500'); 
            at.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400'); 
        }
    };
    switchPassage(1);

    // ── Answer Management ──
    window.setAnswer = function(qId, value) {
        window.examHasChanges = true;
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
        bubble.className = 'answer-bubble flex size-9 shrink-0 items-center justify-center rounded-xl text-xs font-black transition-all relative border ' +
            (filled ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500');
        
        document.querySelectorAll('.review-bubble[data-qid="'+qId+'"]').forEach(rb => {
            if (filled) {
                rb.className = 'review-bubble flex aspect-square items-center justify-center rounded-xl border text-xs font-black transition-all relative bg-indigo-500 border-indigo-500 text-white';
            } else {
                rb.className = 'review-bubble flex aspect-square items-center justify-center rounded-xl border text-xs font-black transition-all relative border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500';
            }
        });
    }

    function updateTfngButtons(qId, value) {
        document.querySelectorAll('.tfng-btn[data-qid="'+qId+'"]').forEach(btn => {
            const sel = btn.dataset.val === value;
            btn.className = 'tfng-btn border px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-150 focus:outline-none ' +
                (sel ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500');
        });
    }

    function updateMcqOptions(qId, value) {
        document.querySelectorAll('.mcq-option[data-qid="'+qId+'"]').forEach(label => {
            // MCQ uses radio inputs natively checked state via has() CSS, but we keep this JS wrapper just in case:
            const sel = label.dataset.val === value;
        });
    }

    // ── Flagging ──
    window.toggleFlag = function(qId) {
        flags[qId] = !flags[qId];
        const btn = document.querySelector('.flag-btn[data-qid="'+qId+'"]');
        if (btn) btn.style.color = flags[qId] ? 'var(--color-error)' : '';
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
        ind.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin text-slate-400 fill-current" viewBox="0 0 24 24"><path d="M12 4V2C6.48 2 2 6.48 2 12h2c0-4.41 3.59-8 8-8zm0 14c4.41 0 8-3.59 8-8h2c0 5.52-4.48 10-10 10v-2z"/></svg><span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Saving...</span>';
        try {
            await fetch(autosaveUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ answers, flagged: flags }) });
            ind.innerHTML = '<svg class="w-3.5 h-3.5 text-emerald-500 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg><span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Saved</span>';
        } catch(e) {
            ind.innerHTML = '<svg class="w-3.5 h-3.5 text-rose-500 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg><span class="text-[9px] font-black uppercase tracking-widest text-rose-500">Error</span>';
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

    // Initialize state
    Object.keys(answers).forEach(qId => { if (answers[qId]) { updateBubble(qId); updateTfngButtons(qId, answers[qId]); } });
})();
</script>
@endpush
