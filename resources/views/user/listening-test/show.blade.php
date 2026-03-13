<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Listening Test – {{ $test->title }} | MockDasher</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }

        /* Audio progress bar */
        #audio-progress-bar { cursor: default; }
        #audio-progress-bar::-webkit-slider-thumb { cursor: default; }
        #audio-progress-bar::-moz-range-thumb   { cursor: default; }

        /* Answered indicator */
        .q-nav-btn.answered { background: #2563eb; color: white; border-color: #2563eb; }
        .q-nav-btn.active   { ring: 2px; ring-color: #2563eb; outline: 2px solid #2563eb; }

        /* Section lock overlay */
        .locked-overlay { background: rgba(15,23,42,0.65); backdrop-filter: blur(4px); }

        /* Fade in animation */
        @keyframes fadeSlideIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        .fade-in { animation: fadeSlideIn 0.35s ease-out both; }

        /* Transfer mode pulse */
        @keyframes pulseBorder { 0%,100% { border-color: #f59e0b; } 50% { border-color: #d97706; } }
        .transfer-pulse { animation: pulseBorder 2s ease-in-out infinite; }

        /* Question highlight */
        .question-block.active-q { border-left: 3px solid #2563eb; background: #eff6ff; }

        /* Scrollbar */
        .questions-panel::-webkit-scrollbar { width: 6px; }
        .questions-panel::-webkit-scrollbar-track { background: #f1f5f9; }
        .questions-panel::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:3px; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

{{-- ════════════════════════════════════════════════════
     TOP BAR – Timer & Test Info
════════════════════════════════════════════════════ --}}
<div id="top-bar" class="sticky top-0 z-50 bg-slate-900 text-white shadow-lg">
    <div class="flex items-center justify-between px-4 py-2">
        {{-- Left: Logo + Test Name --}}
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center font-bold text-sm">MD</div>
            <div>
                <p class="text-xs text-slate-400 leading-tight">IELTS Listening</p>
                <p class="text-sm font-semibold leading-tight truncate max-w-[200px]">{{ $test->title }}</p>
            </div>
        </div>

        {{-- Center: Section Tabs --}}
        <div class="hidden md:flex items-center gap-1">
            @foreach($sections as $s)
                <button
                    id="tab-section-{{ $s->section_number }}"
                    onclick="goToSection({{ $s->section_number }})"
                    class="px-3 py-1.5 rounded text-xs font-semibold transition-all
                        {{ $s->section_number == 1 ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}
                        {{ $attempt->current_section < $s->section_number ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
                    {{ $attempt->current_section < $s->section_number ? 'disabled' : '' }}>
                    Part {{ $s->section_number }}
                </button>
            @endforeach
        </div>

        {{-- Right: Timer --}}
        <div class="flex items-center gap-4">
            <div id="timer-display" class="flex items-center gap-2 bg-slate-800 rounded-lg px-3 py-1.5">
                <i class="fas fa-clock text-blue-400 text-xs"></i>
                <div>
                    <p id="timer-label" class="text-xs text-slate-400 leading-tight">Test Time Remaining</p>
                    <p id="timer-value" class="text-sm font-bold font-mono text-white leading-tight">--:--</p>
                </div>
            </div>
            <div class="text-xs text-slate-400 hidden sm:block">
                <span id="autosave-status" class="flex items-center gap-1">
                    <i class="fas fa-circle-dot text-green-400 text-xs"></i> Auto-saved
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     MAIN LAYOUT: Left Audio Panel | Right Questions Panel
════════════════════════════════════════════════════ --}}
<div class="flex h-[calc(100vh-52px)]">

    {{-- ──────────────────────────────────────
         LEFT PANEL: Audio Player
    ────────────────────────────────────── --}}
    <div class="w-72 xl:w-80 flex-shrink-0 bg-slate-900 flex flex-col border-r border-slate-700">

        {{-- Part indicator --}}
        <div id="left-part-header" class="px-4 pt-4 pb-3 border-b border-slate-700">
            <p class="text-xs text-slate-400 uppercase tracking-widest mb-0.5">Now Playing</p>
            <h2 id="current-part-label" class="text-white text-lg font-bold">Part 1</h2>
            <p id="current-part-desc" class="text-xs text-slate-400">Conversation — everyday social context</p>
        </div>

        {{-- Audio player --}}
        <div class="px-4 py-5 flex-1">
            <audio id="exam-audio" preload="auto" class="hidden">
                @foreach($sections as $s)
                    @if($s->audio_path)
                        <source data-section="{{ $s->section_number }}" src="{{ Storage::url($s->audio_path) }}" type="audio/mpeg">
                    @endif
                @endforeach
            </audio>

            {{-- Waveform visual --}}
            <div class="w-full h-16 mb-4 bg-slate-800 rounded-lg flex items-center justify-center gap-0.5 px-3" id="waveform">
                @for($i=0;$i<48;$i++)
                    <div class="waveform-bar bg-blue-500 rounded-full opacity-60 transition-all duration-75"
                         style="width:3px; height:{{ rand(10,48) }}px;"></div>
                @endfor
            </div>

            {{-- Play/Pause --}}
            <div class="flex items-center justify-center mb-4">
                <button id="play-pause-btn" onclick="togglePlayPause()"
                    class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition shadow-lg shadow-blue-900/40">
                    <i id="play-icon" class="fas fa-play text-lg ml-0.5"></i>
                </button>
            </div>

            {{-- Time display --}}
            <div class="flex justify-between text-xs text-slate-400 mb-2 font-mono">
                <span id="current-time">0:00</span>
                <span id="total-time">0:00</span>
            </div>

            {{-- Progress bar --}}
            <div class="relative mb-5">
                <div class="w-full h-2 bg-slate-700 rounded-full overflow-hidden">
                    <div id="audio-played" class="h-full bg-blue-500 rounded-full transition-all duration-300" style="width:0%"></div>
                </div>
                <p class="text-xs text-slate-500 mt-1 text-center">You may pause but cannot skip ahead</p>
            </div>

            {{-- Volume --}}
            <div class="flex items-center gap-2 mb-5">
                <i class="fas fa-volume-up text-slate-400 text-xs w-4"></i>
                <input type="range" id="volume-control" min="0" max="1" step="0.05" value="1"
                    onchange="document.getElementById('exam-audio').volume=this.value"
                    class="w-full h-1.5 accent-blue-500 cursor-pointer">
            </div>

            {{-- Section status list --}}
            <div class="space-y-2">
                @foreach($sections as $s)
                    <div id="section-status-{{ $s->section_number }}"
                        class="flex items-center gap-2 text-xs rounded p-2
                            {{ $attempt->current_section > $s->section_number ? 'text-green-400' : ($attempt->current_section == $s->section_number ? 'text-blue-300 bg-slate-800' : 'text-slate-500') }}">
                        @if($attempt->current_section > $s->section_number)
                            <i class="fas fa-check-circle text-green-500"></i>
                        @elseif($attempt->current_section == $s->section_number)
                            <i class="fas fa-headphones text-blue-400 animate-pulse"></i>
                        @else
                            <i class="fas fa-lock text-slate-600"></i>
                        @endif
                        <span>Part {{ $s->section_number }}</span>
                        @if($attempt->current_section > $s->section_number)
                            <span class="ml-auto text-green-600 font-semibold">Done</span>
                        @elseif($attempt->current_section == $s->section_number)
                            <span class="ml-auto font-semibold">Active</span>
                        @else
                            <span class="ml-auto">Locked</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Transcript toggle --}}
        <div class="px-4 pb-4">
            <button onclick="toggleTranscript()"
                class="w-full text-xs text-slate-400 hover:text-slate-200 border border-slate-700 hover:border-slate-500 rounded py-2 transition flex items-center justify-center gap-2">
                <i class="fas fa-scroll"></i> Toggle Transcript
            </button>
        </div>
    </div>

    {{-- ──────────────────────────────────────
         RIGHT PANEL: Questions
    ────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-white">

        {{-- Section instruction banner --}}
        <div id="instruction-banner" class="border-b border-slate-200 bg-blue-50 px-6 py-3">
            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider mb-0.5">Instructions</p>
            <p id="instruction-text" class="text-sm text-blue-900">
                {{ $sections->firstWhere('section_number', $attempt->current_section)?->instruction_text ?? 'Listen carefully and answer the questions below.' }}
            </p>
        </div>

        {{-- Questions scroll area --}}
        <div class="questions-panel flex-1 overflow-y-auto px-6 py-5">
            @foreach($sections as $section)
                <div id="section-questions-{{ $section->section_number }}"
                    class="{{ $section->section_number != $attempt->current_section ? 'hidden' : '' }} fade-in">

                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">
                                Part {{ $section->section_number }}
                                <span class="ml-2 text-xs font-normal text-slate-500">
                                    {{ ['','Everyday Social Conversation','Everyday Social Monologue','Educational Conversation','Academic Monologue'][$section->section_number] ?? '' }}
                                </span>
                            </h3>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2 py-1 rounded-full">
                            {{ $section->questions->count() }} Questions
                        </span>
                    </div>

                    @if($section->questions->isEmpty())
                        <div class="text-center py-12 text-slate-400">
                            <i class="fas fa-question-circle text-4xl mb-3"></i>
                            <p>No questions have been configured for this section yet.</p>
                        </div>
                    @else
                        @php $qNum = $section->section_number == 1 ? 1 : $sections->where('section_number', '<', $section->section_number)->sum(fn($s) => $s->questions->count()) + 1; @endphp
                        <div class="space-y-5">
                            @foreach($section->questions as $qi => $question)
                                @php $globalNum = $qNum + $qi; @endphp
                                <div id="question-block-{{ $question->id }}"
                                    class="question-block border border-slate-200 rounded-lg p-4 transition-all"
                                    data-question-id="{{ $question->id }}">

                                    {{-- Question number + type badge --}}
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="flex-shrink-0 w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                                {{ $globalNum }}
                                            </span>
                                            <p class="text-sm font-medium text-slate-800 leading-snug">
                                                {!! nl2br(e($question->question_text)) !!}
                                            </p>
                                        </div>
                                        <span class="ml-3 flex-shrink-0 text-xs text-slate-400 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">
                                            {{ ucwords(str_replace('_', ' ', $question->question_type)) }}
                                        </span>
                                    </div>

                                    {{-- Answer Input - varies by type --}}
                                    @if($question->question_type === 'multiple_choice')
                                        <div class="space-y-2 ml-9">
                                            @foreach($question->options as $oi => $opt)
                                                <label class="flex items-center gap-3 cursor-pointer group hover:bg-blue-50 rounded p-2 -mx-2 transition">
                                                    <input type="radio"
                                                        name="answer_{{ $question->id }}"
                                                        value="{{ $opt->option_text }}"
                                                        data-question-id="{{ $question->id }}"
                                                        {{ (($savedAnswers[$question->id] ?? '') === $opt->option_text) ? 'checked' : '' }}
                                                        onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 flex-shrink-0">
                                                    <span class="text-sm text-slate-700 group-hover:text-blue-800">
                                                        <strong class="text-blue-700 mr-1">{{ chr(65+$oi) }}.</strong>
                                                        {{ $opt->option_text }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @elseif(in_array($question->question_type, ['form_completion', 'table_completion', 'sentence_completion', 'short_answer']))
                                        <div class="ml-9">
                                            <input type="text"
                                                id="answer-input-{{ $question->id }}"
                                                data-question-id="{{ $question->id }}"
                                                value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                placeholder="{{ $question->question_type === 'form_completion' ? 'Write your answer here…' : 'Write ONE WORD AND/OR A NUMBER' }}"
                                                oninput="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                class="w-full max-w-sm border-b-2 border-slate-300 focus:border-blue-500 bg-slate-50 focus:bg-white px-3 py-2 text-sm rounded-t transition outline-none">
                                        </div>

                                    @elseif($question->question_type === 'matching')
                                        <div class="ml-9">
                                            <select
                                                id="answer-input-{{ $question->id }}"
                                                data-question-id="{{ $question->id }}"
                                                onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                class="border border-slate-300 rounded-md text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                                <option value="">— Choose a match —</option>
                                                @foreach($question->options as $opt)
                                                    <option value="{{ $opt->option_text }}" {{ ($savedAnswers[$question->id] ?? '') === $opt->option_text ? 'selected' : '' }}>
                                                        {{ $opt->option_text }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        {{-- Fallback text --}}
                                        <div class="ml-9">
                                            <input type="text"
                                                id="answer-input-{{ $question->id }}"
                                                data-question-id="{{ $question->id }}"
                                                value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                placeholder="Your answer…"
                                                oninput="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                class="w-full max-w-sm border-b-2 border-slate-300 focus:border-blue-500 bg-slate-50 focus:bg-white px-3 py-2 text-sm rounded-t transition outline-none">
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Transcript section (hidden by default) --}}
            @foreach($sections as $s)
                @if($s->passage_text)
                    <div id="transcript-{{ $s->section_number }}" class="hidden mt-6 p-4 bg-slate-50 border border-slate-200 rounded-lg fade-in">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                            Transcript — Part {{ $s->section_number }}
                        </h4>
                        <p class="text-sm text-slate-600 whitespace-pre-line leading-relaxed">{{ $s->passage_text }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- ── Bottom Navigation Bar ── --}}
        <div class="border-t border-slate-200 bg-white px-6 py-3 flex items-center justify-between">
            {{-- Question Navigator --}}
            <div class="flex items-center gap-1.5 flex-wrap" id="question-navigator">
                @php $navNum = 1; @endphp
                @foreach($sections as $s)
                    @foreach($s->questions as $q)
                        <button
                            id="nav-btn-{{ $q->id }}"
                            onclick="scrollToQuestion({{ $q->id }})"
                            class="q-nav-btn w-7 h-7 text-xs font-semibold border border-slate-300 rounded hover:border-blue-500 hover:bg-blue-50 transition
                                   {{ !empty($savedAnswers[$q->id]) ? 'answered' : 'bg-white text-slate-600' }}
                                   {{ $s->section_number != $attempt->current_section ? 'opacity-30 cursor-not-allowed' : '' }}">
                            {{ $navNum++ }}
                        </button>
                    @endforeach
                @endforeach
            </div>

            {{-- Submit button (only shows in transfer or after audio finishes) --}}
            <form id="submit-form" action="{{ route('user.listening.submit', $attempt->id) }}" method="POST" onsubmit="return collectAndSubmit();">
                @csrf
                <div id="hidden-answers-container"></div>
                <button type="submit" id="submit-btn"
                    class="hidden bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Submit Test
                </button>
                <button type="button" id="next-section-btn" onclick="requestNextSection()"
                    class="hidden bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i> Next Part
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     TRANSFER TIME OVERLAY
════════════════════════════════════════════════════ --}}
<div id="transfer-overlay" class="hidden fixed inset-0 z-50 flex items-center justify-center locked-overlay">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 text-center transfer-pulse border-2 border-yellow-400">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exchange-alt text-yellow-600 text-2xl"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-800 mb-2">Answer Transfer Time</h2>
        <p class="text-sm text-slate-600 mb-4">
            You have <strong>10 minutes</strong> to review and confirm your answers before they are submitted.
        </p>
        <div class="text-4xl font-mono font-bold text-yellow-600 mb-4" id="transfer-countdown">10:00</div>
        <form id="final-submit-form" action="{{ route('user.listening.submit', $attempt->id) }}" method="POST" onsubmit="return collectAndSubmit('final-submit-form');">
            @csrf
            <div id="hidden-answers-container-2"></div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-lg mt-2 flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Submit All Answers Now
            </button>
        </form>
        <button onclick="closeTransfer()"
            class="mt-3 text-sm text-slate-500 hover:text-slate-700 underline">
            Return to review answers
        </button>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     DATA PASSED TO JS
════════════════════════════════════════════════════ --}}
<script>
const ATTEMPT_ID    = {{ $attempt->id }};
const CURRENT_SEC   = {{ $attempt->current_section }};
const ATTEMPT_STATUS = '{{ $attempt->status }}';
const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]').content;
const AUTOSAVE_URL  = '{{ route('user.listening.autosave', $attempt->id) }}';
const COMPLETE_URL  = '{{ route('user.listening.completeSection', $attempt->id) }}';
const SECTIONS_DATA = {!! json_encode($sections->map(fn($s) => [
    'id'             => $s->id,
    'number'         => $s->section_number,
    'audio_path'     => $s->audio_path ? Storage::url($s->audio_path) : null,
    'instruction'    => $s->instruction_text ?? 'Listen carefully and answer the questions below.',
    'q_count'        => $s->questions->count(),
    'question_ids'   => $s->questions->pluck('id'),
])) !!};
const TRANSFER_REMAINING = {{ $transferRemainingSeconds ?? 'null' }};
const ALL_QUESTION_IDS   = {!! json_encode($sections->flatMap(fn($s) => $s->questions->pluck('id'))) !!};

// Pre-seeded saved answers
const SAVED_ANSWERS = {!! json_encode($savedAnswers ?? []) !!};
</script>

<script>
// ════════════════════════════════════════════════════
// STATE
// ════════════════════════════════════════════════════
let currentSection = CURRENT_SEC;
let maxReachedAudio = 0;   // Max playback position allowed (prevents skipping)
let autosaveTimer  = null;
let transferInterval = null;
let isAudioPlayed  = false;
let answers = {};          // { questionId: answerText }
let isSectionAudioDone = false;

// Init saved answers
Object.assign(answers, SAVED_ANSWERS);

const audio = document.getElementById('exam-audio');

// ════════════════════════════════════════════════════
// AUDIO SYSTEM
// ════════════════════════════════════════════════════
const partDescriptions = {
    1: 'Conversation — everyday social context',
    2: 'Monologue — everyday social context',
    3: 'Conversation — educational/training context',
    4: 'Monologue — academic subject',
};

function loadSectionAudio(sectionNum) {
    const sec = SECTIONS_DATA.find(s => s.number === sectionNum);
    if (!sec || !sec.audio_path) {
        document.getElementById('current-part-label').textContent = `Part ${sectionNum}`;
        document.getElementById('current-part-desc').textContent = partDescriptions[sectionNum] || '';
        markSectionAudioDone();
        return;
    }
    audio.src = sec.audio_path;
    audio.load();
    document.getElementById('current-part-label').textContent = `Part ${sectionNum}`;
    document.getElementById('current-part-desc').textContent = partDescriptions[sectionNum] || '';
    maxReachedAudio = 0;
    isSectionAudioDone = false;
    audio.play().catch(() => {});
}

function togglePlayPause() {
    if (audio.paused) {
        audio.play();
    } else {
        audio.pause();
    }
}

audio.addEventListener('play', () => {
    document.getElementById('play-icon').className = 'fas fa-pause text-lg';
    animateWaveform(true);
});
audio.addEventListener('pause', () => {
    document.getElementById('play-icon').className = 'fas fa-play text-lg ml-0.5';
    animateWaveform(false);
});

audio.addEventListener('timeupdate', () => {
    const cur = audio.currentTime;
    const dur = audio.duration || 1;

    // Track max played position
    if (cur > maxReachedAudio) maxReachedAudio = cur;

    // Update progress bar
    const pct = (cur / dur) * 100;
    document.getElementById('audio-played').style.width = pct + '%';

    // Update time display
    document.getElementById('current-time').textContent = formatTime(cur);
    document.getElementById('total-time').textContent   = formatTime(dur);
});

audio.addEventListener('seeking', () => {
    // Prevent skipping forward beyond max played
    if (audio.currentTime > maxReachedAudio + 1) {
        audio.currentTime = maxReachedAudio;
    }
});

audio.addEventListener('ended', () => {
    markSectionAudioDone();
});

audio.addEventListener('loadedmetadata', () => {
    document.getElementById('total-time').textContent = formatTime(audio.duration);
});

// Waveform animation
let waveformAnimFrame = null;
function animateWaveform(isPlaying) {
    const bars = document.querySelectorAll('.waveform-bar');
    if (!isPlaying) {
        bars.forEach(b => { b.style.transform = 'scaleY(1)'; b.style.opacity = '0.3'; });
        if (waveformAnimFrame) cancelAnimationFrame(waveformAnimFrame);
        return;
    }
    function step() {
        bars.forEach(b => {
            const h = 0.3 + Math.random() * 0.7;
            b.style.transform = `scaleY(${h})`;
            b.style.opacity = '0.4 + Math.random() * 0.6';
        });
        waveformAnimFrame = requestAnimationFrame(() => setTimeout(step, 80));
    }
    step();
}

function markSectionAudioDone() {
    isSectionAudioDone = true;
    // Show next-section or submit button
    const isLastSection = currentSection >= SECTIONS_DATA.length;
    if (isLastSection) {
        // Trigger answer transfer phase
        startTransferPhase();
    } else {
        document.getElementById('next-section-btn').classList.remove('hidden');
    }
}

// ════════════════════════════════════════════════════
// SECTION NAVIGATION
// ════════════════════════════════════════════════════
function goToSection(num) {
    if (num > currentSection) return; // Locked
    if (num === currentSection) return;

    // Show correct question panel
    document.querySelectorAll('[id^="section-questions-"]').forEach(el => el.classList.add('hidden'));
    const target = document.getElementById(`section-questions-${num}`);
    if (target) { target.classList.remove('hidden'); target.classList.add('fade-in'); }

    // Update instruction
    const sec = SECTIONS_DATA.find(s => s.number === num);
    if (sec) document.getElementById('instruction-text').textContent = sec.instruction || '';

    currentSection = num;
    updateSectionTabs();
    loadSectionAudio(num);
}

function requestNextSection() {
    if (!isSectionAudioDone) return;
    autosaveNow();

    fetch(COMPLETE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ section: currentSection }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'next') {
            unlockSection(data.next_section);
        } else if (data.status === 'transfer') {
            startTransferPhase(data.transfer_seconds);
        }
    });
}

function unlockSection(nextNum) {
    currentSection = nextNum;

    // Update section status icons
    const prev = nextNum - 1;
    const prevStatus = document.getElementById(`section-status-${prev}`);
    if (prevStatus) {
        prevStatus.className = 'flex items-center gap-2 text-xs rounded p-2 text-green-400';
        prevStatus.innerHTML = `<i class="fas fa-check-circle text-green-500"></i><span>Part ${prev}</span><span class="ml-auto text-green-600 font-semibold">Done</span>`;
    }
    const curStatus = document.getElementById(`section-status-${nextNum}`);
    if (curStatus) {
        curStatus.className = 'flex items-center gap-2 text-xs rounded p-2 text-blue-300 bg-slate-800';
        curStatus.innerHTML = `<i class="fas fa-headphones text-blue-400 animate-pulse"></i><span>Part ${nextNum}</span><span class="ml-auto font-semibold">Active</span>`;
    }

    // Show new section questions
    document.querySelectorAll('[id^="section-questions-"]').forEach(el => el.classList.add('hidden'));
    const target = document.getElementById(`section-questions-${nextNum}`);
    if (target) { target.classList.remove('hidden'); target.classList.add('fade-in'); }

    // Update instruction text
    const sec = SECTIONS_DATA.find(s => s.number === nextNum);
    if (sec) document.getElementById('instruction-text').textContent = sec.instruction || '';

    isSectionAudioDone = false;
    document.getElementById('next-section-btn').classList.add('hidden');
    document.getElementById('submit-btn').classList.add('hidden');

    // Re-enable nav buttons for new section
    updateSectionTabs();
    loadSectionAudio(nextNum);
}

function updateSectionTabs() {
    SECTIONS_DATA.forEach(s => {
        const tab = document.getElementById(`tab-section-${s.number}`);
        if (!tab) return;
        if (s.number === currentSection) {
            tab.className = tab.className.replace('bg-slate-700 text-slate-300', 'bg-blue-600 text-white');
        } else if (s.number < currentSection) {
            tab.className = tab.className.replace('bg-blue-600 text-white', 'bg-slate-700 text-slate-300');
            tab.removeAttribute('disabled');
            tab.classList.remove('opacity-50', 'cursor-not-allowed');
            tab.classList.add('cursor-pointer');
        }
    });
}

// ════════════════════════════════════════════════════
// TRANSFER MODE
// ════════════════════════════════════════════════════
function startTransferPhase(seconds) {
    const totalSecs = seconds || 600;
    document.getElementById('transfer-overlay').classList.remove('hidden');
    document.getElementById('timer-label').textContent = 'Transfer Time Remaining';
    document.getElementById('timer-value').style.color = '#f59e0b';

    // Show submit button behind overlay too
    document.getElementById('submit-btn').classList.remove('hidden');

    let remaining = totalSecs;
    document.getElementById('transfer-countdown').textContent = formatTimer(remaining);

    transferInterval = setInterval(() => {
        remaining--;
        document.getElementById('transfer-countdown').textContent = formatTimer(remaining);
        document.getElementById('timer-value').textContent = formatTimer(remaining);
        if (remaining <= 0) {
            clearInterval(transferInterval);
            document.getElementById('final-submit-form').submit();
        }
    }, 1000);
}

function closeTransfer() {
    document.getElementById('transfer-overlay').classList.add('hidden');
}

// ════════════════════════════════════════════════════
// ANSWERS & AUTOSAVE
// ════════════════════════════════════════════════════
function markAnswered(qId) {
    const btn = document.getElementById(`nav-btn-${qId}`);
    if (btn) { btn.classList.add('answered'); btn.classList.remove('bg-white', 'text-slate-600'); }
}

function getAnswerValue(qId) {
    // Radio
    const radio = document.querySelector(`input[name="answer_${qId}"]:checked`);
    if (radio) return radio.value;
    // Text/Select
    const inp = document.getElementById(`answer-input-${qId}`);
    if (inp) return inp.value;
    return '';
}

function collectAllAnswers() {
    const out = {};
    ALL_QUESTION_IDS.forEach(id => {
        const val = getAnswerValue(id);
        if (val !== '') out[id] = val;
    });
    return out;
}

let autosavePending = false;
function scheduleAutosave() {
    if (autosavePending) return;
    autosavePending = true;
    autosaveTimer = setTimeout(autosaveNow, 7000); // 7 second debounce
}

function autosaveNow() {
    clearTimeout(autosaveTimer);
    autosavePending = false;
    const payload = collectAllAnswers();
    if (Object.keys(payload).length === 0) return;

    document.getElementById('autosave-status').innerHTML =
        '<i class="fas fa-circle-notch fa-spin text-yellow-400 text-xs"></i> Saving…';

    fetch(AUTOSAVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ answers: payload }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('autosave-status').innerHTML =
                `<i class="fas fa-check-circle text-green-400 text-xs"></i> Saved ${data.saved_at}`;
        }
    })
    .catch(() => {
        document.getElementById('autosave-status').innerHTML =
            '<i class="fas fa-exclamation-circle text-red-400 text-xs"></i> Save failed';
    });
}

// Periodic autosave every 10s
setInterval(autosaveNow, 10000);

// ════════════════════════════════════════════════════
// QUESTION NAVIGATION
// ════════════════════════════════════════════════════
function scrollToQuestion(qId) {
    const block = document.getElementById(`question-block-${qId}`);
    if (!block) return;
    document.querySelectorAll('.question-block').forEach(b => b.classList.remove('active-q'));
    block.classList.add('active-q');
    block.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ════════════════════════════════════════════════════
// FORM SUBMISSION
// ════════════════════════════════════════════════════
function collectAndSubmit(formId) {
    const form = document.getElementById(formId || 'submit-form');
    const container = form.querySelector('[id^="hidden-answers-container"]');
    container.innerHTML = '';
    const answers = collectAllAnswers();
    Object.entries(answers).forEach(([qId, val]) => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = `answers[${qId}]`;
        inp.value = val;
        container.appendChild(inp);
    });
    return true;
}

// ════════════════════════════════════════════════════
// TRANSCRIPT TOGGLE
// ════════════════════════════════════════════════════
function toggleTranscript() {
    const t = document.getElementById(`transcript-${currentSection}`);
    if (t) t.classList.toggle('hidden');
}

// ════════════════════════════════════════════════════
// TIMER (overall listening test — no hard limit for now; managed by server)
// We display a counting-up timer so user can track duration.
// ════════════════════════════════════════════════════
let elapsedSeconds = 0;
const timerEl = document.getElementById('timer-value');
setInterval(() => {
    elapsedSeconds++;
    timerEl.textContent = formatTimer(elapsedSeconds);
}, 1000);

// ════════════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════════════
function formatTime(s) {
    if (isNaN(s)) return '0:00';
    const mins = Math.floor(s / 60);
    const secs = Math.floor(s % 60).toString().padStart(2, '0');
    return `${mins}:${secs}`;
}

function formatTimer(s) {
    const m = Math.floor(Math.abs(s) / 60).toString().padStart(2, '0');
    const ss = (Math.abs(s) % 60).toString().padStart(2, '0');
    return `${m}:${ss}`;
}

// ════════════════════════════════════════════════════
// INIT
// ════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    // If we're in transfer mode (page refresh)
    if (ATTEMPT_STATUS === 'transfer' && TRANSFER_REMAINING !== null) {
        startTransferPhase(TRANSFER_REMAINING);
    } else if (ATTEMPT_STATUS === 'completed') {
        window.location = '{{ route("dashboard") }}';
    } else {
        // Load audio for current section
        loadSectionAudio(currentSection);
    }

    // Pre-mark answered buttons
    ALL_QUESTION_IDS.forEach(id => {
        if (SAVED_ANSWERS[id]) markAnswered(id);
    });

    // Timer label (just show as elapsed)
    document.getElementById('timer-label').textContent = 'Test Time Elapsed';

    // Pre-populate answer inputs from saved state
    ALL_QUESTION_IDS.forEach(id => {
        const saved = SAVED_ANSWERS[id];
        if (!saved) return;
        // Text inputs
        const inp = document.getElementById(`answer-input-${id}`);
        if (inp) inp.value = saved;
        // Radio
        const radio = document.querySelector(`input[name="answer_${id}"][value="${saved}"]`);
        if (radio) radio.checked = true;
    });
});
</script>

</body>
</html>
