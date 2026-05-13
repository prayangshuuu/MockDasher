@extends('layouts.exam')

@section('title', 'Reading Test - IELTS ' . $test->book_number)
@section('test_type', 'IELTS Reading')
@section('test_title', 'IELTS ' . $test->book_number)

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
<div class="flex items-center gap-6">
    <div id="save-indicator" class="flex items-center gap-2 text-emerald-500">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        <span class="text-[10px] font-black uppercase tracking-widest">Saved</span>
    </div>
    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>
    <button onclick="document.getElementById('review-overlay').classList.remove('hidden')" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg shadow-slate-200 dark:shadow-none">
        <span class="material-symbols-outlined text-sm">assignment_turned_in</span>
        Review & Submit
    </button>
</div>
@endsection

@section('content')
<div id="reading-app" class="flex-1 flex flex-col overflow-hidden">
    {{-- Passage Tabs --}}
    <div class="h-12 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center px-8 gap-6 z-10 shrink-0">
        @foreach($passages as $passage)
            <button onclick="switchPassage({{ $passage->passage_number }})" 
                    class="passage-tab h-full flex items-center gap-2 px-4 text-xs font-black uppercase tracking-widest transition-all border-b-2"
                    data-passage="{{ $passage->passage_number }}"
                    id="tab-{{ $passage->passage_number }}">
                Passage {{ $passage->passage_number }}
            </button>
        @endforeach
    </div>

    {{-- Main Workspace --}}
    <div class="flex-1 flex overflow-hidden">
        {{-- Left: Passage Content --}}
        <div class="w-1/2 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/50 p-12 border-r border-slate-200 dark:border-slate-800">
            @foreach($passages as $passage)
                <div class="passage-content max-w-3xl mx-auto space-y-8" data-passage="{{ $passage->passage_number }}" style="display:none;">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">{{ $passage->title }}</h2>
                    <div class="prose prose-slate dark:prose-invert max-w-none text-lg leading-[1.8] font-serif text-slate-700 dark:text-slate-300">
                        {!! $passage->content !!}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Right: Questions --}}
        <div class="w-1/2 overflow-y-auto custom-scrollbar bg-white dark:bg-slate-950 p-12">
            @php $globalQ = 0; @endphp
            @foreach($passages as $passage)
                <div class="questions-panel space-y-10" data-passage="{{ $passage->passage_number }}" style="display:none;">
                    @foreach($passage->questionGroups as $group)
                        @if($group->group_instruction)
                            <div class="p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-primary/20 text-slate-800 dark:text-slate-200 font-bold text-sm leading-relaxed italic">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">Instructions</span>
                                {{ $group->group_instruction }}
                            </div>
                        @endif
                        <div class="space-y-6">
                            @foreach($group->questions as $question)
                                @php $globalQ++; @endphp
                                <div id="question-{{ $question->id }}" class="question-card rounded-2xl border border-slate-100 dark:border-slate-800 p-8 transition-all hover:bg-slate-50 dark:hover:bg-slate-800/30" data-qid="{{ $question->id }}">
                                    <div class="flex items-start gap-4 mb-6">
                                        <div class="size-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-black text-slate-900 dark:text-white">{{ $globalQ }}</span>
                                        </div>
                                        <div class="text-[17px] font-bold text-slate-900 dark:text-white leading-relaxed pt-1 flex-1">
                                            {!! nl2br(e($question->question_text)) !!}
                                        </div>
                                        <button onclick="toggleFlag({{ $question->id }})" class="flag-btn ml-auto transition-colors text-slate-300 hover:text-slate-400" data-qid="{{ $question->id }}">
                                            <span class="material-symbols-outlined font-light">flag</span>
                                        </button>
                                    </div>
                                    <div class="pl-12">
                                        @if($question->question_type === 'multiple_choice')
                                            <div class="grid grid-cols-1 gap-2">
                                                @foreach($question->options as $oi => $opt)
                                                    @php $letter = chr(65+$oi); @endphp
                                                    <label class="mcq-option flex items-center gap-4 p-4 rounded-xl border border-slate-100 dark:border-slate-800 cursor-pointer transition-all hover:border-primary/30" data-qid="{{ $question->id }}" data-val="{{ $letter }}">
                                                        <input type="radio" name="q_{{ $question->id }}" value="{{ $letter }}" onchange="setAnswer({{ $question->id }}, '{{ $letter }}')" class="size-4 text-primary focus:ring-primary" {{ ($savedAnswers[$question->id] ?? '') === $letter ? 'checked' : '' }}>
                                                        <span class="text-sm font-bold flex items-center gap-3">
                                                            <span class="opacity-40">{{ $letter }}.</span>
                                                            {{ $opt->option_text }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif(in_array($question->question_type, ['true_false_not_given', 'yes_no_not_given']))
                                            @php $opts = $question->question_type === 'true_false_not_given' ? ['TRUE', 'FALSE', 'NOT GIVEN'] : ['YES', 'NO', 'NOT GIVEN']; @endphp
                                            <div class="flex flex-wrap gap-3">
                                                @foreach($opts as $opt)
                                                    <button type="button" onclick="setAnswer({{ $question->id }}, '{{ $opt }}')" 
                                                            class="tfng-btn px-6 py-2.5 rounded-xl border text-xs font-black uppercase tracking-widest transition-all {{ strtoupper($savedAnswers[$question->id] ?? '') === $opt ? 'bg-primary border-primary text-white shadow-lg shadow-primary/20' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-500 hover:border-primary/50' }}"
                                                            data-qid="{{ $question->id }}" data-val="{{ $opt }}">
                                                        {{ $opt }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @else
                                            <input type="text" value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                   oninput="setAnswer({{ $question->id }}, this.value)"
                                                   class="w-full max-w-md bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                                                   placeholder="Write your answer here...">
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

    {{-- Bottom Answer Sheet --}}
    <div class="h-20 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center px-8 z-30 shrink-0">
        <div class="flex-1 flex gap-2 overflow-x-auto py-2 custom-scrollbar">
            @php $qIdx = 0; @endphp
            @foreach($passages as $passage)
                @foreach($passage->questionGroups as $group)
                    @foreach($group->questions as $question)
                        @php $qIdx++; @endphp
                        <button onclick="jumpToQuestion({{ $question->id }}, {{ $passage->passage_number }})"
                                class="answer-bubble size-10 rounded-xl border-2 flex items-center justify-center shrink-0 transition-all relative {{ !empty($savedAnswers[$question->id]) ? 'bg-primary text-white border-transparent' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-transparent' }}"
                                data-qid="{{ $question->id }}" id="bubble-{{ $question->id }}">
                            <span class="text-xs font-black">{{ $qIdx }}</span>
                            <div class="flag-dot absolute -top-1.5 -right-1.5 size-4 bg-rose-500 border-2 border-white dark:border-slate-900 rounded-full {{ !empty($flaggedAnswers[$question->id]) ? '' : 'hidden' }}" data-qid="{{ $question->id }}"></div>
                        </button>
                    @endforeach
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Review Overlay --}}
    <div id="review-overlay" class="fixed inset-0 z-[100] flex items-center justify-center p-8 bg-slate-950/80 backdrop-blur-md hidden">
        <div class="bg-white dark:bg-slate-900 w-full max-w-4xl rounded-[40px] shadow-2xl flex flex-col overflow-hidden max-h-full border border-slate-200 dark:border-slate-800">
            <div class="p-10 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-6">
                    <div class="size-16 rounded-3xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-4xl font-light">fact_check</span>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none mb-2">Review Summary</h2>
                        <p class="text-slate-500 font-bold text-xs uppercase tracking-widest">Check all answers before final submission.</p>
                    </div>
                </div>
                <button onclick="document.getElementById('review-overlay').classList.add('hidden')" class="size-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-500 transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-12 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="p-8 rounded-[32px] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Completion</p>
                        <div class="flex items-end gap-3">
                            <h3 class="text-4xl font-black text-slate-900 dark:text-white leading-none" id="review-answered">0</h3>
                            <span class="text-slate-300 dark:text-slate-600 text-lg font-bold">/ {{ $passages->flatMap(fn($p) => $p->questionGroups->flatMap(fn($g) => $g->questions))->count() }}</span>
                        </div>
                    </div>
                    <div class="p-8 rounded-[32px] bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-rose-500 mb-2">Flagged Items</p>
                        <h3 class="text-4xl font-black text-rose-600 leading-none" id="review-flagged">0</h3>
                    </div>
                    <div class="p-8 rounded-[32px] bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500 mb-2">Time Remaining</p>
                        <h3 class="text-4xl font-black text-indigo-700 leading-none font-mono" id="review-timer">--:--</h3>
                    </div>
                </div>
                <div class="space-y-12">
                    @php $revIdx = 0; @endphp
                    @foreach($passages as $p)
                        <div>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                <span>Reading Passage {{ $p->passage_number }}</span>
                                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800"></div>
                            </h4>
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                                @foreach($p->questionGroups->flatMap(fn($g) => $g->questions) as $q)
                                    @php $revIdx++; @endphp
                                    <button onclick="jumpToQuestion({{ $q->id }}, {{ $p->passage_number }}); document.getElementById('review-overlay').classList.add('hidden');"
                                            class="review-bubble aspect-square rounded-2xl border-2 flex flex-col items-center justify-center transition-all relative {{ !empty($savedAnswers[$q->id]) ? 'bg-primary border-primary text-white' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400 hover:border-primary/30' }}"
                                            data-qid="{{ $q->id }}">
                                        <span class="text-xs font-black">{{ $revIdx }}</span>
                                        <div class="review-flag-dot absolute -top-1 -right-1 size-3 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900 {{ !empty($flaggedAnswers[$q->id]) ? '' : 'hidden' }}" data-qid="{{ $q->id }}"></div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="p-10 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between shrink-0">
                <button onclick="document.getElementById('review-overlay').classList.add('hidden')" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Back to Questions
                </button>
                <form id="final-submit-form" action="{{ route('user.reading.submit', $attempt->id) }}" method="POST">
                    @csrf
                    <div id="hidden-answers-container"></div>
                    <button type="button" onclick="confirmSubmit()" class="px-10 py-4 bg-primary text-white rounded-[24px] text-sm font-black uppercase tracking-[0.1em] shadow-xl shadow-primary/20 hover:-translate-y-1 transition-all active:scale-95">
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
    // ── State ──
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
        const reviewTimer = document.getElementById('review-timer');
        if (reviewTimer) reviewTimer.textContent = display;

        const widget = document.getElementById('timer-widget');
        const icon = document.getElementById('timer-icon');
        if (timeRemaining <= 300) {
            widget.className = widget.className.replace('border-slate-100', 'border-rose-500').replace('dark:border-slate-700', 'bg-rose-50');
            icon.className = 'material-symbols-outlined text-xl text-rose-500 animate-pulse';
            document.getElementById('timer-display').className = document.getElementById('timer-display').className.replace('text-slate-900', 'text-rose-600');
        }
    }
    updateTimer();
    setInterval(function() {
        timeRemaining--;
        if (timeRemaining <= 0) {
            populateHiddenInputs();
            document.getElementById('final-submit-form').submit();
            return;
        }
        updateTimer();
    }, 1000);

    // ── Passage Switching ──
    window.switchPassage = function(num) {
        currentPassage = num;
        document.querySelectorAll('.passage-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.questions-panel').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.passage-tab').forEach(el => {
            el.classList.remove('border-primary', 'text-primary');
            el.classList.add('border-transparent', 'text-slate-400');
        });
        const activeContent = document.querySelector('.passage-content[data-passage="'+num+'"]');
        const activeQuestions = document.querySelector('.questions-panel[data-passage="'+num+'"]');
        const activeTab = document.getElementById('tab-'+num);
        if (activeContent) activeContent.style.display = 'block';
        if (activeQuestions) activeQuestions.style.display = 'block';
        if (activeTab) {
            activeTab.classList.remove('border-transparent', 'text-slate-400');
            activeTab.classList.add('border-primary', 'text-primary');
        }
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
        if (bubble) {
            if (answers[qId] && answers[qId].trim() !== '') {
                bubble.className = 'answer-bubble size-10 rounded-xl border-2 flex items-center justify-center shrink-0 transition-all relative bg-primary text-white border-transparent';
            } else {
                bubble.className = 'answer-bubble size-10 rounded-xl border-2 flex items-center justify-center shrink-0 transition-all relative bg-slate-100 dark:bg-slate-800 text-slate-400 border-transparent';
            }
        }
        // Update review bubble too
        document.querySelectorAll('.review-bubble[data-qid="'+qId+'"]').forEach(rb => {
            if (answers[qId] && answers[qId].trim() !== '') {
                rb.className = rb.className.replace(/bg-white|border-slate-200|text-slate-400/g, '').replace('dark:bg-slate-900', '');
                rb.classList.add('bg-primary', 'border-primary', 'text-white');
            }
        });
    }

    function updateTfngButtons(qId, value) {
        document.querySelectorAll('.tfng-btn[data-qid="'+qId+'"]').forEach(btn => {
            if (btn.dataset.val === value) {
                btn.className = 'tfng-btn px-6 py-2.5 rounded-xl border text-xs font-black uppercase tracking-widest transition-all bg-primary border-primary text-white shadow-lg shadow-primary/20';
            } else {
                btn.className = 'tfng-btn px-6 py-2.5 rounded-xl border text-xs font-black uppercase tracking-widest transition-all bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-500 hover:border-primary/50';
            }
        });
    }

    function updateMcqOptions(qId, value) {
        document.querySelectorAll('.mcq-option[data-qid="'+qId+'"]').forEach(label => {
            if (label.dataset.val === value) {
                label.className = 'mcq-option flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all bg-primary/5 border-primary/50 text-slate-900 dark:text-white';
            } else {
                label.className = 'mcq-option flex items-center gap-4 p-4 rounded-xl border border-slate-100 dark:border-slate-800 cursor-pointer transition-all hover:border-primary/30 text-slate-500';
            }
        });
    }

    // ── Flagging ──
    window.toggleFlag = function(qId) {
        flags[qId] = !flags[qId];
        const btn = document.querySelector('.flag-btn[data-qid="'+qId+'"]');
        if (btn) {
            const icon = btn.querySelector('.material-symbols-outlined');
            if (flags[qId]) {
                btn.classList.remove('text-slate-300');
                btn.classList.add('text-rose-500');
                if (icon) icon.classList.add('fill-1');
            } else {
                btn.classList.remove('text-rose-500');
                btn.classList.add('text-slate-300');
                if (icon) icon.classList.remove('fill-1');
            }
        }
        // Toggle dots
        document.querySelectorAll('.flag-dot[data-qid="'+qId+'"], .review-flag-dot[data-qid="'+qId+'"]').forEach(dot => {
            dot.classList.toggle('hidden', !flags[qId]);
        });
        scheduleAutosave();
    };

    // ── Navigation ──
    window.jumpToQuestion = function(qId, passageNum) {
        switchPassage(passageNum);
        setTimeout(function() {
            const el = document.getElementById('question-' + qId);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    };

    // ── Autosave ──
    let autosaveTimer = null;
    function scheduleAutosave() {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(doAutosave, 3000);
    }
    async function doAutosave() {
        const indicator = document.getElementById('save-indicator');
        indicator.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin text-slate-400">refresh</span><span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Saving...</span>';
        try {
            await fetch(autosaveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ answers: answers, flagged: flags })
            });
            indicator.innerHTML = '<span class="material-symbols-outlined text-sm text-emerald-500">check_circle</span><span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Saved</span>';
        } catch(e) {
            indicator.innerHTML = '<span class="material-symbols-outlined text-sm text-rose-500">error</span><span class="text-[10px] font-black uppercase tracking-widest text-rose-500">Error</span>';
        }
    }
    // Periodic autosave
    setInterval(doAutosave, 20000);

    // ── Submit ──
    function populateHiddenInputs() {
        const container = document.getElementById('hidden-answers-container');
        container.innerHTML = '';
        for (const [qId, val] of Object.entries(answers)) {
            if (val && val.trim() !== '') {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'answers['+qId+']'; input.value = val;
                container.appendChild(input);
            }
        }
        for (const [qId, val] of Object.entries(flags)) {
            if (val) {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'flagged['+qId+']'; input.value = '1';
                container.appendChild(input);
            }
        }
    }

    window.confirmSubmit = function() {
        // Update review stats
        const answered = Object.values(answers).filter(v => v && v.trim() !== '').length;
        const flagged = Object.values(flags).filter(v => v).length;
        document.getElementById('review-answered').textContent = answered;
        document.getElementById('review-flagged').textContent = flagged;

        if (confirm('Final Submission: Your reading test will be closed for grading. Are you sure?')) {
            populateHiddenInputs();
            document.getElementById('final-submit-form').submit();
        }
    };

    // Open review updates stats
    const reviewOverlay = document.getElementById('review-overlay');
    const observer = new MutationObserver(function() {
        if (!reviewOverlay.classList.contains('hidden')) {
            document.getElementById('review-answered').textContent = Object.values(answers).filter(v => v && v.trim() !== '').length;
            document.getElementById('review-flagged').textContent = Object.values(flags).filter(v => v).length;
        }
    });
    observer.observe(reviewOverlay, { attributes: true, attributeFilter: ['class'] });
})();
</script>
@endpush
