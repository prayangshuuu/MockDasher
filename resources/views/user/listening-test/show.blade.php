@extends('layouts.exam')

@section('title', 'Listening Test - IELTS ' . $test->book_number)
@section('test_type', 'IELTS Listening')
@section('test_title', 'IELTS ' . $test->book_number)

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 px-4 py-2 rounded-2xl shadow-sm transition-all border-slate-100 dark:border-slate-700">
    <span class="material-symbols-outlined text-xl text-primary" id="timer-icon">timer</span>
    <div class="flex items-baseline gap-1.5">
        <span class="text-2xl font-black font-mono tracking-tighter tabular-nums text-slate-900 dark:text-white" id="timer-display">30:00</span>
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
    <button onclick="showReviewPanel()" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg shadow-slate-200 dark:shadow-none">
        <span class="material-symbols-outlined text-sm">assignment_turned_in</span>
        End Test
    </button>
</div>
@endsection

@section('content')
<div id="listening-app" class="flex-1 flex flex-col overflow-hidden">
    {{-- Audio Control Panel --}}
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 p-6 flex flex-col items-center gap-4 z-40 relative shadow-md">
        <audio id="main-audio" preload="auto">
            @if($sections->first() && $sections->first()->audio_path)
            <source src="{{ Storage::url($sections->first()->audio_path) }}" type="audio/mpeg">
            @endif
        </audio>

        <div class="w-full max-w-4xl flex items-center gap-8">
            <button onclick="toggleAudio()" id="play-btn" class="size-16 rounded-full exam-gradient flex items-center justify-center text-white shadow-xl shadow-primary/30 hover:scale-105 transition-all active:scale-95">
                <span class="material-symbols-outlined text-4xl font-black fill-1" id="play-icon">play_arrow</span>
            </button>
            <div class="flex-1 space-y-3">
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                    <span id="audio-current">00:00</span>
                    <span id="audio-duration">00:00</span>
                </div>
                <div onclick="seekAudio(event)" class="h-3 bg-slate-100 dark:bg-slate-800 rounded-full cursor-pointer relative group overflow-hidden border border-slate-200 dark:border-slate-700">
                    <div class="h-full exam-gradient transition-all duration-300 relative shadow-inner" id="audio-progress" style="width:0%">
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 size-4 bg-white rounded-full border-2 border-primary shadow-sm scale-0 group-hover:scale-100 transition-transform"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl border border-slate-200 dark:border-slate-700">
                <span class="material-symbols-outlined text-slate-400 text-xl">volume_up</span>
                <input type="range" id="vol-slider" value="0.8" min="0" max="1" step="0.1" oninput="updateVolume()" class="w-20 accent-primary h-1">
            </div>
        </div>
        <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest flex items-center gap-2">
            <span class="size-1.5 rounded-full bg-rose-500 animate-pulse"></span>
            Note: In the real exam, audio can only be played once.
        </div>
    </div>

    {{-- Main Question Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/50 p-12">
        <div class="max-w-4xl mx-auto space-y-10">
            @php $globalQ = 0; @endphp
            @foreach($sections as $section)
            <div class="space-y-8 bg-white dark:bg-slate-900/40 p-10 rounded-[40px] border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl font-black">music_note</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic">Section {{ $section->section_number }}</h2>
                        <p class="text-sm text-slate-500 font-bold">{{ $section->instruction_text }}</p>
                    </div>
                </div>

                <div class="space-y-8 pl-14">
                    @foreach($section->questions as $qi => $question)
                    @php $globalQ++; @endphp
                    <div id="question-{{ $question->id }}" class="group/q">
                        <div class="flex items-start gap-6">
                            <div class="size-10 rounded-2xl flex items-center justify-center shrink-0 border-2 transition-all border-slate-100 dark:border-slate-800 text-slate-400 bg-white dark:bg-slate-900 q-badge" data-qid="{{ $question->id }}">
                                <span class="text-sm font-black">{{ $globalQ }}</span>
                            </div>
                            <div class="flex-1 pt-1">
                                <div class="text-[17px] font-bold text-slate-900 dark:text-white leading-relaxed mb-6">
                                    {!! nl2br(e($question->question_text)) !!}
                                </div>
                                <div class="max-w-md">
                                    @if($question->question_type === 'multiple_choice')
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($question->options as $oi => $opt)
                                        <label class="listening-option flex items-center gap-4 p-4 rounded-xl border border-slate-100 dark:border-slate-800 cursor-pointer transition-all hover:border-primary/30 text-slate-500" data-qid="{{ $question->id }}" data-val="{{ $opt->option_text }}">
                                            <input type="radio" name="q_{{ $question->id }}" value="{{ $opt->option_text }}" class="size-4 text-primary focus:ring-primary" onchange="setAnswer({{ $question->id }}, this.value)" {{ (($savedAnswers[$question->id] ?? '') === $opt->option_text) ? 'checked' : '' }}>
                                            <span class="text-sm font-bold flex items-center gap-3">
                                                <span class="opacity-40">{{ chr(65+$oi) }}.</span>
                                                {{ $opt->option_text }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @else
                                    <input type="text" value="{{ $savedAnswers[$question->id] ?? '' }}" oninput="setAnswer({{ $question->id }}, this.value)"
                                           class="w-full bg-white dark:bg-slate-950 border-2 border-slate-100 dark:border-slate-800 rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none italic"
                                           placeholder="Type your answer...">
                                    @endif
                                </div>
                            </div>
                            <button onclick="toggleFlag({{ $question->id }})" class="flag-btn transition-colors text-slate-200 hover:text-slate-400" data-qid="{{ $question->id }}" data-flagged="{{ !empty($flaggedAnswers[$question->id]) ? '1' : '0' }}">
                                <span class="material-symbols-outlined text-3xl font-light">flag</span>
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
    <div class="h-24 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center px-10 z-30 shrink-0">
        <div class="flex-1 flex gap-2 overflow-x-auto py-3 custom-scrollbar">
            @php $qi = 0; @endphp
            @foreach($sections as $section)
                @foreach($section->questions as $question)
                @php $qi++; @endphp
                <button onclick="jumpToQuestion({{ $question->id }})"
                        class="nav-btn size-12 rounded-2xl border-2 flex items-center justify-center shrink-0 transition-all relative border-transparent bg-slate-100 dark:bg-slate-800 text-slate-400" data-qid="{{ $question->id }}">
                    <span class="text-sm font-black">{{ $qi }}</span>
                    <div class="flag-dot absolute -top-1.5 -right-1.5 size-4 bg-rose-500 border-2 border-white dark:border-slate-900 rounded-full" data-qid="{{ $question->id }}" style="display:none;"></div>
                </button>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Review Overlay --}}
    <div id="review-panel" class="fixed inset-0 z-[100] flex items-center justify-center p-8 bg-slate-950/80 backdrop-blur-md" style="display:none;">
        <div class="bg-white dark:bg-slate-900 w-full max-w-4xl rounded-[40px] shadow-2xl flex flex-col overflow-hidden max-h-full border border-slate-200 dark:border-slate-800">
            <div class="p-10 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-6">
                    <div class="size-16 rounded-3xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-4xl font-light">playlist_add_check</span>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none mb-2">Listening Summary</h2>
                        <p class="text-slate-500 font-bold text-xs uppercase tracking-widest italic">Review your inputs before submitting.</p>
                    </div>
                </div>
                <button onclick="hideReviewPanel()" class="size-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-500 transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-12 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div class="p-8 rounded-[32px] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Answered</p>
                        <div class="flex items-end gap-3">
                            <h3 class="text-4xl font-black text-slate-900 dark:text-white leading-none" id="review-answered">0</h3>
                            <span class="text-slate-300 dark:text-slate-600 text-lg font-bold">/ {{ $sections->flatMap(fn($s) => $s->questions)->count() }}</span>
                        </div>
                    </div>
                    <div class="p-8 rounded-[32px] bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30 text-rose-600">
                        <p class="text-[10px] font-black uppercase tracking-widest text-rose-500 mb-2">Flagged</p>
                        <h3 class="text-4xl font-black leading-none" id="review-flagged">0</h3>
                    </div>
                </div>
                <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 gap-3" id="review-grid"></div>
            </div>
            <div class="p-10 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between shrink-0">
                <button onclick="hideReviewPanel()" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Back to Test
                </button>
                <button onclick="confirmSubmit()" class="px-12 py-5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-[28px] text-sm font-black uppercase tracking-[0.2em] shadow-2xl hover:scale-[1.02] transition-all active:scale-95">
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
    let timeRemaining = {{ $transferRemainingSeconds ?? 1800 }};
    const timerEl = document.getElementById('timer-display');
    const timerWidget = document.getElementById('timer-widget');
    setInterval(() => {
        timeRemaining--;
        if (timeRemaining <= 0) { document.getElementById('listening-submit-form').submit(); return; }
        timerEl.textContent = formatTime(timeRemaining);
        if (timeRemaining <= 300) {
            timerWidget.classList.add('border-rose-500', 'bg-rose-50');
            timerEl.classList.add('text-rose-600');
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
        document.getElementById('play-icon').textContent = isPlaying ? 'pause' : 'play_arrow';
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
        answers[qId] = val;
        updateNavBtn(qId);
        debouncedAutosave();
    };

    window.toggleFlag = function(qId) {
        flags[qId] = !flags[qId];
        const btn = document.querySelector('.flag-btn[data-qid="'+qId+'"]');
        const icon = btn.querySelector('.material-symbols-outlined');
        if (flags[qId]) {
            btn.classList.remove('text-slate-200'); btn.classList.add('text-rose-500');
            icon.classList.add('fill-1');
        } else {
            btn.classList.remove('text-rose-500'); btn.classList.add('text-slate-200');
            icon.classList.remove('fill-1');
        }
        document.querySelectorAll('.flag-dot[data-qid="'+qId+'"]').forEach(d => d.style.display = flags[qId] ? 'block' : 'none');
        debouncedAutosave();
    };

    // Init flag display
    Object.keys(flags).forEach(qId => {
        if (flags[qId]) {
            const btn = document.querySelector('.flag-btn[data-qid="'+qId+'"]');
            if (btn) { btn.classList.remove('text-slate-200'); btn.classList.add('text-rose-500'); btn.querySelector('.material-symbols-outlined').classList.add('fill-1'); }
            document.querySelectorAll('.flag-dot[data-qid="'+qId+'"]').forEach(d => d.style.display = 'block');
        }
    });

    // Init nav button colors for saved answers
    Object.keys(answers).forEach(qId => { if (answers[qId]) updateNavBtn(qId); });

    function updateNavBtn(qId) {
        const btn = document.querySelector('.nav-btn[data-qid="'+qId+'"]');
        if (!btn) return;
        if (answers[qId] && answers[qId].trim() !== '') {
            btn.classList.remove('bg-slate-100', 'text-slate-400');
            btn.classList.add('bg-primary', 'text-white');
        } else {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('bg-slate-100', 'text-slate-400');
        }
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
        ind.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin text-slate-400">refresh</span><span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Saving...</span>';
        try {
            await fetch(autosaveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ answers, flagged: flags })
            });
            ind.innerHTML = '<span class="material-symbols-outlined text-sm text-emerald-500">check_circle</span><span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Saved</span>';
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
            div.className = 'aspect-square rounded-2xl border-2 flex flex-col items-center justify-center transition-all relative ' + (hasAnswer ? 'bg-primary border-primary text-white shadow-lg' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400 hover:border-primary/30');
            div.innerHTML = '<span class="text-xs font-black">' + n + '</span>' + (isFlagged ? '<div class="absolute -top-1 -right-1 size-3 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900"></div>' : '');
            div.onclick = () => { jumpToQuestion(parseInt(qId)); hideReviewPanel(); };
            grid.appendChild(div);
        });

        document.getElementById('review-panel').style.display = 'flex';
    };

    window.hideReviewPanel = function() { document.getElementById('review-panel').style.display = 'none'; };

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
