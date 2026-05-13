@extends('layouts.exam')

@section('title', 'Listening Test - IELTS ' . $test->book_number)
@section('test_type', 'IELTS Listening')
@section('test_title', 'IELTS ' . $test->book_number)

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-2 rounded-full border border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-4 py-1.5 transition-all">
    <span class="material-symbols-outlined text-lg text-[var(--color-primary)]" id="timer-icon">timer</span>
    <span class="text-lg font-bold tabular-nums tracking-tight text-[var(--color-text-primary)] font-mono" id="timer-display">30:00</span>
    <span class="hidden text-[10px] font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:inline">Remaining</span>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-3">
    <div id="save-indicator" class="flex items-center gap-1.5 text-[var(--color-success)]">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        <span class="text-[10px] font-bold uppercase tracking-wider">Saved</span>
    </div>
    <x-ui.button variant="primary" onclick="showReviewPanel()" class="text-xs">
        <span class="material-symbols-outlined text-sm">assignment_turned_in</span>
        <span class="hidden sm:inline">End Test</span>
    </x-ui.button>
</div>
@endsection

@section('content')
<div id="listening-app" class="flex-1 flex flex-col overflow-hidden">
    {{-- Audio Control Panel --}}
    <div class="bg-[var(--color-bg-primary)] border-b border-[var(--color-divider)] p-6 flex flex-col items-center gap-4 z-40 relative shadow-sm">
        <audio id="main-audio" preload="auto">
            @if($sections->first() && $sections->first()->audio_path)
            <source src="{{ Storage::url($sections->first()->audio_path) }}" type="audio/mpeg">
            @endif
        </audio>

        <div class="w-full max-w-4xl flex items-center gap-8">
            <button onclick="toggleAudio()" id="play-btn" class="flex size-14 items-center justify-center rounded-full bg-[var(--color-primary)] text-white transition-transform active:scale-95 hover:opacity-90">
                <span class="material-symbols-outlined text-3xl font-bold" id="play-icon">play_arrow</span>
            </button>
            <div class="flex-1 space-y-2">
                <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-secondary)]">
                    <span id="audio-current">00:00</span>
                    <span id="audio-duration">00:00</span>
                </div>
                <div onclick="seekAudio(event)" class="h-2 bg-[var(--color-bg-secondary)] rounded-full cursor-pointer relative group overflow-hidden border border-[var(--color-divider)]">
                    <div class="h-full bg-[var(--color-primary)] transition-all duration-300 relative" id="audio-progress" style="width:0%">
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 size-3 bg-white rounded-full border border-[var(--color-primary)] scale-0 group-hover:scale-100 transition-transform"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-[var(--color-bg-secondary)] p-2.5 rounded-[var(--radius-base)] border border-[var(--color-divider)]">
                <span class="material-symbols-outlined text-[var(--color-text-secondary)] text-lg">volume_up</span>
                <input type="range" id="vol-slider" value="0.8" min="0" max="1" step="0.1" oninput="updateVolume()" class="w-20 accent-[var(--color-primary)] h-1">
            </div>
        </div>
        <div class="text-[10px] font-bold text-[var(--color-error)] uppercase tracking-wider flex items-center gap-2">
            <span class="size-1.5 rounded-full bg-[var(--color-error)] animate-pulse"></span>
            Note: In the real exam, audio can only be played once.
        </div>
    </div>

    {{-- Main Question Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar bg-[var(--color-bg-secondary)] p-6 sm:p-12">
        <div class="max-w-4xl mx-auto space-y-10">
            @php $globalQ = 0; @endphp
            @foreach($sections as $section)
            <div class="space-y-8 bg-[var(--color-bg-primary)] p-8 sm:p-10 rounded-[var(--radius-xl)] border border-[var(--color-divider)] shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)]">
                        <span class="material-symbols-outlined text-2xl font-bold">music_note</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-[var(--color-text-primary)] tracking-tight">Section {{ $section->section_number }}</h2>
                        <p class="text-sm text-[var(--color-text-secondary)] font-medium">{{ $section->instruction_text }}</p>
                    </div>
                </div>

                <div class="space-y-8 sm:pl-14">
                    @foreach($section->questions as $qi => $question)
                    @php $globalQ++; @endphp
                    <div id="question-{{ $question->id }}" class="group/q">
                        <div class="flex items-start gap-4 sm:gap-6">
                            <div class="flex size-8 shrink-0 items-center justify-center rounded-[var(--radius-xs)] bg-[var(--color-bg-secondary)] text-xs font-bold text-[var(--color-text-primary)] q-badge border border-[var(--color-divider)] transition-all" data-qid="{{ $question->id }}">
                                {{ $globalQ }}
                            </div>
                            <div class="flex-1 pt-1">
                                <div class="text-base font-semibold text-[var(--color-text-primary)] leading-relaxed mb-6">
                                    {!! nl2br(e($question->question_text)) !!}
                                </div>
                                <div class="max-w-md">
                                    @if($question->question_type === 'multiple_choice')
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($question->options as $oi => $opt)
                                        <label class="listening-option flex cursor-pointer items-center gap-3 rounded-[var(--radius-base)] border border-[var(--color-divider)] p-3.5 transition-all hover:border-[var(--color-primary)] text-[var(--color-text-secondary)]" data-qid="{{ $question->id }}" data-val="{{ $opt->option_text }}">
                                            <input type="radio" name="q_{{ $question->id }}" value="{{ $opt->option_text }}" class="size-4 accent-[var(--color-primary)]" onchange="setAnswer({{ $question->id }}, this.value)" {{ (($savedAnswers[$question->id] ?? '') === $opt->option_text) ? 'checked' : '' }}>
                                            <span class="text-sm flex items-center gap-2">
                                                <span class="font-semibold text-[var(--color-text-secondary)]">{{ chr(65+$oi) }}.</span>
                                                {{ $opt->option_text }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @else
                                    <input type="text" value="{{ $savedAnswers[$question->id] ?? '' }}" oninput="setAnswer({{ $question->id }}, this.value)"
                                           class="w-full bg-[var(--color-bg-primary)] border border-[var(--color-divider)] rounded-[var(--radius-base)] px-4 py-3 text-sm focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all outline-none"
                                           placeholder="Type your answer...">
                                    @endif
                                </div>
                            </div>
                            <button onclick="toggleFlag({{ $question->id }})" class="flag-btn shrink-0 text-[var(--color-divider)] transition-colors hover:text-[var(--color-error)]" data-qid="{{ $question->id }}" data-flagged="{{ !empty($flaggedAnswers[$question->id]) ? '1' : '0' }}">
                                <span class="material-symbols-outlined text-xl">flag</span>
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
    <div class="flex h-16 shrink-0 items-center border-t border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-4 sm:px-6 lg:px-8 z-30">
        <div class="flex flex-1 gap-1.5 overflow-x-auto py-2 custom-scrollbar">
            @php $qi = 0; @endphp
            @foreach($sections as $section)
                @foreach($section->questions as $question)
                @php $qi++; @endphp
                <button onclick="jumpToQuestion({{ $question->id }})"
                        class="nav-btn flex size-9 shrink-0 items-center justify-center rounded-[var(--radius-base)] text-xs font-bold transition-all relative border border-[var(--color-divider)] bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] hover:border-[var(--color-primary)]" data-qid="{{ $question->id }}">
                    {{ $qi }}
                    <div class="flag-dot absolute -right-1 -top-1 size-3 rounded-full border-2 border-[var(--color-bg-primary)] bg-[var(--color-error)]" data-qid="{{ $question->id }}" style="display:none;"></div>
                </button>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Review Overlay --}}
    <div id="review-panel" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/40 backdrop-blur-sm p-6" style="display:none;">
        <div class="flex max-h-full w-full max-w-3xl flex-col overflow-hidden rounded-[var(--radius-xl)] border border-[var(--color-divider)] bg-[var(--color-bg-primary)]">
            <div class="flex items-center justify-between border-b border-[var(--color-divider)] px-6 py-5 sm:px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)]">
                        <span class="material-symbols-outlined text-2xl">playlist_add_check</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[var(--color-text-primary)]">Listening Summary</h2>
                        <p class="text-xs text-[var(--color-text-secondary)]">Review your inputs before submitting.</p>
                    </div>
                </div>
                <button onclick="hideReviewPanel()" class="flex size-8 items-center justify-center rounded-[var(--radius-xs)] text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-bg-secondary)] hover:text-[var(--color-error)]">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-6 sm:px-8 custom-scrollbar">
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="rounded-[var(--radius-base)] bg-[var(--color-bg-secondary)] p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Answered</p>
                        <div class="mt-1 flex items-baseline gap-1">
                            <h3 class="text-2xl font-bold text-[var(--color-text-primary)] leading-none" id="review-answered">0</h3>
                            <span class="text-sm font-semibold text-[var(--color-text-secondary)]">/ {{ $sections->flatMap(fn($s) => $s->questions)->count() }}</span>
                        </div>
                    </div>
                    <div class="rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-error)_6%,transparent)] p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--color-error)]">Flagged</p>
                        <h3 class="mt-1 text-2xl font-bold text-[var(--color-error)] leading-none" id="review-flagged">0</h3>
                    </div>
                </div>
                <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2" id="review-grid"></div>
            </div>
            <div class="flex items-center justify-between border-t border-[var(--color-divider)] bg-[var(--color-bg-secondary)] px-6 py-4 sm:px-8 shrink-0">
                <button onclick="hideReviewPanel()" class="text-sm font-semibold text-[var(--color-text-secondary)] transition-opacity hover:opacity-70">
                    Back to Test
                </button>
                <x-ui.button variant="primary" onclick="confirmSubmit()">
                    Submit My Result
                </x-ui.button>
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
            timerWidget.classList.add('border-[var(--color-error)]', 'bg-[color-mix(in_srgb,var(--color-error)_10%,transparent)]');
            timerEl.classList.add('text-[var(--color-error)]');
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
        if (btn) btn.style.color = flags[qId] ? 'var(--color-error)' : 'var(--color-divider)';
        document.querySelectorAll('.flag-dot[data-qid="'+qId+'"]').forEach(d => d.style.display = flags[qId] ? 'block' : 'none');
        debouncedAutosave();
    };

    // Init flag display
    Object.keys(flags).forEach(qId => {
        if (flags[qId]) {
            const btn = document.querySelector('.flag-btn[data-qid="'+qId+'"]');
            if (btn) btn.style.color = 'var(--color-error)';
            document.querySelectorAll('.flag-dot[data-qid="'+qId+'"]').forEach(d => d.style.display = 'block');
        }
    });

    // Init nav button colors for saved answers
    Object.keys(answers).forEach(qId => { if (answers[qId]) updateNavBtn(qId); });

    function updateNavBtn(qId) {
        const btn = document.querySelector('.nav-btn[data-qid="'+qId+'"]');
        if (!btn) return;
        const filled = answers[qId] && answers[qId].trim() !== '';
        btn.className = 'nav-btn flex size-9 shrink-0 items-center justify-center rounded-[var(--radius-base)] text-xs font-bold transition-all relative border ' +
            (filled ? 'bg-[var(--color-primary)] border-[var(--color-primary)] text-white' : 'border-[var(--color-divider)] bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] hover:border-[var(--color-primary)]');
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
            div.className = 'review-bubble flex aspect-square items-center justify-center rounded-[var(--radius-base)] border text-xs font-bold transition-all relative ' +
                (hasAnswer ? 'bg-[var(--color-primary)] border-[var(--color-primary)] text-white' : 'border-[var(--color-divider)] bg-[var(--color-bg-primary)] text-[var(--color-text-secondary)] hover:border-[var(--color-primary)]');
            div.innerHTML = n + (isFlagged ? '<div class="absolute -top-0.5 -right-0.5 size-2.5 bg-[var(--color-error)] rounded-full border-2 border-[var(--color-bg-primary)]"></div>' : '');
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
