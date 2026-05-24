@extends('layouts.student')

@section('title', 'Listening Test - IELTS ' . $test->book_number)

@section('breadcrumbs')
<div class="flex items-center gap-4">
    <h1 class="truncate text-lg font-bold tracking-tight text-slate-900 dark:text-white">IELTS {{ $test->book_number }} - Listening Test</h1>
    <div id="timer-widget" class="flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 py-1.5 transition-all shadow-soft duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-500 fill-current" viewBox="0 0 24 24" id="timer-icon">
            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
            <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
        </svg>
        <span class="text-sm font-black tabular-nums tracking-tight text-slate-800 dark:text-slate-200 font-mono" id="timer-display">30:00</span>
        <span class="hidden text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 sm:inline">Remaining</span>
    </div>
    <div id="save-indicator" class="flex items-center gap-1.5 text-emerald-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="text-[9px] font-black uppercase tracking-widest">Saved</span>
    </div>
    <button onclick="showReviewPanel()" class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-soft transition-all duration-150 focus:outline-none ml-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="hidden sm:inline">End Test</span>
    </button>
</div>
@endsection

@section('content')
<style>
    .listening-option:has(input:checked) {
        border-color: #6366f1; /* indigo-500 */
        background-color: rgba(99, 102, 241, 0.04);
        color: #4f46e5; /* indigo-600 */
    }
    .dark .listening-option:has(input:checked) {
        border-color: #818cf8; /* indigo-400 */
        background-color: rgba(129, 140, 248, 0.08);
        color: #c7d2fe; /* indigo-200 */
    }
</style>

<div id="listening-app" class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
    {{-- Audio Control Panel --}}
    <div class="border-b border-slate-200 dark:border-slate-800 p-6 flex flex-col items-center gap-4 z-40 relative">
        <audio id="main-audio" preload="auto">
            @if($sections->first() && $sections->first()->audio_path)
            <source src="{{ Storage::url($sections->first()->audio_path) }}" type="audio/mpeg">
            @endif
        </audio>

        <div class="w-full max-w-4xl flex items-center gap-6">
            <button onclick="toggleAudio()" id="play-btn" class="flex size-12 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-white transition-all hover:bg-indigo-600 hover:scale-105 active:scale-95 shadow-md focus:outline-none">
                <div id="play-icon-container" class="flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24" id="play-icon-svg"><path d="M8 5v14l11-7z"/></svg>
                </div>
            </button>
            <div class="flex-1 space-y-2">
                <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                    <span id="audio-current" class="font-mono">00:00</span>
                    <span id="audio-duration" class="font-mono">00:00</span>
                </div>
                <div onclick="seekAudio(event)" class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full cursor-pointer relative group overflow-hidden border border-slate-200 dark:border-slate-800">
                    <div class="h-full bg-indigo-500 transition-all duration-300 relative rounded-full" id="audio-progress" style="width:0%">
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 size-2 bg-white rounded-full scale-0 group-hover:scale-100 transition-transform"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800/80 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 dark:text-slate-500 fill-current" viewBox="0 0 24 24">
                    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
                </svg>
                <input type="range" id="vol-slider" value="0.8" min="0" max="1" step="0.1" oninput="updateVolume()" class="w-16 sm:w-20 accent-indigo-500 h-1 cursor-pointer">
            </div>
        </div>
        <div class="text-[9px] font-black text-rose-500 uppercase tracking-wider flex items-center gap-1.5 bg-rose-50 dark:bg-rose-950/30 px-3 py-1 rounded-full border border-rose-100 dark:border-rose-900/40 shadow-soft">
            <span class="size-1.5 rounded-full bg-rose-500 animate-pulse"></span>
            Important: In the real IELTS exam, audio is only played once.
        </div>
    </div>

    {{-- Main Question Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/40 p-6 sm:p-10">
        <div class="max-w-4xl mx-auto space-y-8">
            @php $globalQ = 0; @endphp
            @foreach($sections as $section)
            <div class="bg-surface-light dark:bg-surface-dark p-6 sm:p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50">
                        <img src="/storage/asset/icons/headphone.svg" class="w-5 h-5 filter-indigo-600 dark:invert" alt="Section" />
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">Section {{ $section->section_number }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">{{ $section->instruction_text }}</p>
                    </div>
                </div>

                <div class="space-y-6 sm:pl-13">
                   @foreach($section->questions as $qi => $question)
                   @php $globalQ++; @endphp
                   <div id="question-{{ $question->id }}" class="group/q border-t border-slate-100 dark:border-slate-800/80 pt-6 first:border-0 first:pt-0">
                       <div class="flex items-start gap-4">
                           {{-- Question number --}}
                           <div class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700/50 q-badge transition-all" data-qid="{{ $question->id }}">
                               {{ $globalQ }}
                           </div>
                           <div class="flex-1 pt-0.5">
                               <div class="text-sm sm:text-base font-semibold text-slate-800 dark:text-slate-200 leading-relaxed mb-4">
                                   {!! nl2br(e($question->question_text)) !!}
                                </div>
                                <div class="max-w-md">
                                    @if($question->question_type === 'multiple_choice')
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($question->options as $oi => $opt)
                                        <label class="listening-option flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-850 p-3 transition-all hover:border-indigo-500 text-slate-600 dark:text-slate-400" data-qid="{{ $question->id }}" data-val="{{ $opt->option_text }}">
                                            <input type="radio" name="q_{{ $question->id }}" value="{{ $opt->option_text }}" class="size-4 accent-indigo-500 cursor-pointer" onchange="setAnswer({{ $question->id }}, this.value)" {{ (($savedAnswers[$question->id] ?? '') === $opt->option_text) ? 'checked' : '' }}>
                                            <span class="text-xs sm:text-sm flex items-center gap-2">
                                                <span class="font-bold text-slate-450 dark:text-slate-500">{{ chr(65+$oi) }}.</span>
                                                {{ $opt->option_text }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @else
                                    <input type="text" value="{{ $savedAnswers[$question->id] ?? '' }}" oninput="setAnswer({{ $question->id }}, this.value)"
                                           class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-855 dark:text-slate-200 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"
                                           placeholder="Type your answer...">
                                    @endif
                                </div>
                            </div>
                            <button onclick="toggleFlag({{ $question->id }})" class="flag-btn shrink-0 text-slate-300 dark:text-slate-700 transition-colors hover:text-rose-500 focus:outline-none" data-qid="{{ $question->id }}" data-flagged="{{ !empty($flaggedAnswers[$question->id]) ? '1' : '0' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current transition-transform duration-150 active:scale-90" viewBox="0 0 24 24">
                                    <path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6h-5.6z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Bottom Answer Sheet --}}
    <div class="flex h-16 shrink-0 items-center border-t border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 sm:px-6 lg:px-8 z-30 shadow-[0_-2px_10px_rgba(0,0,0,0.02)]">
        <div class="flex flex-1 gap-1.5 overflow-x-auto py-2 custom-scrollbar">
            @php $qi = 0; @endphp
            @foreach($sections as $section)
                @foreach($section->questions as $question)
                @php $qi++; @endphp
                <button onclick="jumpToQuestion({{ $question->id }})"
                        class="nav-btn flex size-9 shrink-0 items-center justify-center rounded-xl text-xs font-black transition-all relative border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500 focus:outline-none" data-qid="{{ $question->id }}">
                    {{ $qi }}
                    <div class="flag-dot absolute -right-1 -top-1 size-3 rounded-full border-2 border-white dark:border-slate-900 bg-rose-500" data-qid="{{ $question->id }}" style="display:none;"></div>
                </button>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Review Overlay --}}
    <div id="review-panel" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-950/40 backdrop-blur-md p-6" style="display:none;">
        <div class="flex max-h-full w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark shadow-premium">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-6 py-5 sm:px-8 shrink-0 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-3.5">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50">
                        <img src="/storage/asset/icons/verified.svg" class="w-5 h-5 filter-indigo-600 dark:invert" alt="Check" />
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">Listening Review Summary</h2>
                        <p class="text-xs text-slate-400 dark:text-slate-500">Verify your inputs and flags before submitting.</p>
                    </div>
                </div>
                <button onclick="hideReviewPanel()" class="flex size-8 items-center justify-center rounded-lg text-slate-400 transition-all hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-rose-500 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-6 sm:px-8 custom-scrollbar">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4">
                        <p class="text-[9px] font-black uppercase tracking-wider text-slate-450 dark:text-slate-500">Total Answered</p>
                        <div class="mt-1.5 flex items-baseline gap-1">
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white leading-none" id="review-answered">0</h3>
                            <span class="text-sm font-semibold text-slate-400 dark:text-slate-500">/ {{ $sections->flatMap(fn($s) => $s->questions)->count() }}</span>
                        </div>
                    </div>
                    <div class="rounded-xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 p-4">
                        <p class="text-[9px] font-black uppercase tracking-wider text-rose-500">Questions Flagged</p>
                        <h3 class="mt-1.5 text-2xl font-black text-rose-500 leading-none" id="review-flagged">0</h3>
                    </div>
                </div>
                <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2.5" id="review-grid"></div>
            </div>
            <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 px-6 py-4 sm:px-8 shrink-0">
                <button onclick="hideReviewPanel()" class="text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-500 transition-colors focus:outline-none">
                    Back to Test
                </button>
                <button onclick="confirmSubmit()" class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-soft transition-all duration-150 focus:outline-none">
                    Submit My Result
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden submit form --}}
<form id="listening-submit-form" action="{{ route('user.listening.submit', $attempt->id) }}" method="POST" class="hidden">
    @csrf
    <div id="submit-fields"></div>
</form>
@endsection

@push('scripts')
<script>
(function() {
    const autosaveUrl = '{{ route("user.listening.autosave", $attempt->id) }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const answers = @json($savedAnswers ?? (object)[]);
    const flags = {};

    // Init flags
    @foreach($flaggedAnswers as $qId => $val)
        flags[{{ $qId }}] = true;
    @endforeach

    // Timer
    let timeRemaining = {{ $transferRemainingSeconds ?? $listeningRemainingSeconds ?? 1800 }};
    const timerEl = document.getElementById('timer-display');
    const timerWidget = document.getElementById('timer-widget');
    setInterval(() => {
        timeRemaining--;
        if (timeRemaining <= 0) { document.getElementById('listening-submit-form').submit(); return; }
        if(timerEl) timerEl.textContent = formatTime(timeRemaining);
        if (timeRemaining <= 300 && timerWidget) {
            timerWidget.classList.add('border-rose-500', 'bg-rose-50', 'dark:bg-rose-950/20');
            if(timerEl) timerEl.classList.add('text-rose-500');
        }
    }, 1000);

    // Audio
    const audio = document.getElementById('main-audio');
    let isPlaying = false;
    audio.volume = 0.8;
    audio.addEventListener('loadedmetadata', () => {
        document.getElementById('audio-duration').textContent = formatTime(audio.duration);
    });
    audio.addEventListener('timeupdate', () => {
        const pct = (audio.currentTime / audio.duration) * 100;
        document.getElementById('audio-progress').style.width = pct + '%';
        document.getElementById('audio-current').textContent = formatTime(audio.currentTime);
    });

    window.toggleAudio = function() {
        if (isPlaying) { audio.pause(); } else { audio.play(); }
        isPlaying = !isPlaying;
        const playIconContainer = document.getElementById('play-icon-container');
        if (isPlaying) {
            playIconContainer.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
        } else {
            playIconContainer.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
        }
    };

    window.seekAudio = function(e) {
        const rect = e.currentTarget.getBoundingClientRect();
        const pos = (e.clientX - rect.left) / rect.width;
        audio.currentTime = pos * audio.duration;
    };

    window.updateVolume = function() {
        audio.volume = document.getElementById('vol-slider').value;
    };

    // Answers
    window.setAnswer = function(qId, val) {
        window.examHasChanges = true;
        answers[qId] = val;
        updateNavBtn(qId);
        debouncedAutosave();
    };

    window.toggleFlag = function(qId) {
        flags[qId] = !flags[qId];
        const btn = document.querySelector('.flag-btn[data-qid="'+qId+'"]');
        if (btn) btn.style.color = flags[qId] ? '#ef4444' : '';
        document.querySelectorAll('.flag-dot[data-qid="'+qId+'"]').forEach(d => d.style.display = flags[qId] ? 'block' : 'none');
        debouncedAutosave();
    };

    // Init flag display
    Object.keys(flags).forEach(qId => {
        if (flags[qId]) {
            const btn = document.querySelector('.flag-btn[data-qid="'+qId+'"]');
            if (btn) btn.style.color = '#ef4444';
            document.querySelectorAll('.flag-dot[data-qid="'+qId+'"]').forEach(d => d.style.display = 'block');
        }
    });

    // Init nav button colors for saved answers
    Object.keys(answers).forEach(qId => { if (answers[qId]) updateNavBtn(qId); });

    function updateNavBtn(qId) {
        const btn = document.querySelector('.nav-btn[data-qid="'+qId+'"]');
        if (!btn) return;
        const filled = answers[qId] && answers[qId].trim() !== '';
        btn.className = 'nav-btn flex size-9 shrink-0 items-center justify-center rounded-xl text-xs font-black transition-all relative border ' +
            (filled ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500');
    }

    window.jumpToQuestion = function(qId) {
        const el = document.getElementById('question-'+qId);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    // Autosave
    let saveTimer = null;
    function debouncedAutosave() { clearTimeout(saveTimer); saveTimer = setTimeout(autosave, 3000); }
    async function autosave() {
        const ind = document.getElementById('save-indicator');
        if(ind) ind.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin text-slate-400 fill-current" viewBox="0 0 24 24"><path d="M12 4V2C6.48 2 2 6.48 2 12h2c0-4.41 3.59-8 8-8zm0 14c4.41 0 8-3.59 8-8h2c0 5.52-4.48 10-10 10v-2z"/></svg><span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Saving...</span>';
        try {
            await fetch(autosaveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ answers, flagged: flags })
            });
            if(ind) ind.innerHTML = '<svg class="w-3.5 h-3.5 text-emerald-500 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg><span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Saved</span>';
        } catch(e) { console.error(e); }
    }
    setInterval(autosave, 20000);

    // Review
    window.showReviewPanel = function() {
        const answered = Object.keys(answers).filter(k => answers[k] && answers[k].trim() !== '').length;
        const flagged = Object.keys(flags).filter(k => flags[k]).length;
        document.getElementById('review-answered').textContent = answered;
        document.getElementById('review-flagged').textContent = flagged;

        // Build grid
        const grid = document.getElementById('review-grid');
        grid.innerHTML = '';
        let n = 0;
        document.querySelectorAll('.nav-btn').forEach(btn => {
            n++;
            const qId = btn.dataset.qid;
            const hasAnswer = answers[qId] && answers[qId].trim() !== '';
            const isFlagged = !!flags[qId];
            const div = document.createElement('button');
            div.className = 'review-bubble flex aspect-square items-center justify-center rounded-xl border text-xs font-black transition-all relative ' +
                (hasAnswer ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500');
            div.innerHTML = n + (isFlagged ? '<div class="absolute -top-0.5 -right-0.5 size-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900"></div>' : '');
            div.onclick = () => { jumpToQuestion(parseInt(qId)); hideReviewPanel(); };
            grid.appendChild(div);
        });

        document.getElementById('review-panel').style.display = 'flex';
        document.getElementById('review-panel').classList.remove('hidden');
    };

    window.hideReviewPanel = function() {
        document.getElementById('review-panel').style.display = 'none';
        document.getElementById('review-panel').classList.add('hidden');
    };

    window.confirmSubmit = function() {
        if (confirm('End Listening Test: Are you ready to submit your answers?')) {
            const fieldsContainer = document.getElementById('submit-fields');
            fieldsContainer.innerHTML = '';
            Object.keys(answers).forEach(qId => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'answers['+qId+']'; inp.value = answers[qId] || '';
                fieldsContainer.appendChild(inp);
            });
            Object.keys(flags).forEach(qId => {
                if (flags[qId]) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden'; inp.name = 'flagged['+qId+']'; inp.value = '1';
                    fieldsContainer.appendChild(inp);
                }
            });
            document.getElementById('listening-submit-form').submit();
        }
    };

    function formatTime(s) {
        s = Math.max(0, Math.floor(s));
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return m.toString().padStart(2, '0') + ':' + sec.toString().padStart(2, '0');
    }
})();
</script>
@endpush
