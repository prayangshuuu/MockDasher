@extends('layouts.exam')

@section('title', 'Speaking Test')
@section('test_type', 'IELTS Speaking')
@section('test_title', 'IELTS Speaking Exam')

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 py-1.5 transition-all shadow-soft duration-200">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-500 fill-current" viewBox="0 0 24 24" id="timer-icon">
        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm.5-13H11v6l5.2 3.1.8-1.2-4.5-2.7V7z"/>
    </svg>
    <span class="text-sm font-black tabular-nums tracking-tight text-slate-800 dark:text-slate-200 font-mono" id="timer-display">00:00</span>
    <span class="hidden text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 sm:inline" id="timer-label">Per Question</span>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-3.5">
    <div id="rec-indicator" class="flex items-center gap-1.5 text-slate-400 dark:text-slate-500">
        <span class="size-2 rounded-full bg-slate-300 dark:bg-slate-700" id="rec-dot"></span>
        <span class="text-[9px] font-black uppercase tracking-widest" id="rec-label">Standby</span>
    </div>
    <button onclick="endInterview()" class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-soft transition-all duration-150 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="hidden sm:inline">Complete Interview</span>
    </button>
</div>
@endsection

@section('content')
<div id="speaking-app" class="flex-1 flex flex-col overflow-hidden bg-slate-50 dark:bg-slate-900/40">
    {{-- Stage Progress --}}
    <div class="flex h-14 shrink-0 items-center gap-6 border-b border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-6 sm:px-12 overflow-x-auto shadow-sm">
        @foreach($parts as $partNumber => $questions)
            <div class="part-progress flex items-center gap-3 transition-all opacity-40 shrink-0" data-part="{{ $partNumber }}" id="progress-{{ $partNumber }}">
                <div class="flex size-7 items-center justify-center rounded-lg text-xs font-black bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700/50" id="progress-badge-{{ $partNumber }}">
                    {{ $partNumber }}
                </div>
                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500" id="progress-text-{{ $partNumber }}">Part {{ $partNumber }}</span>
            </div>
            @if(!$loop->last)
                <div class="h-px w-6 bg-slate-200 dark:bg-slate-800 shrink-0"></div>
            @endif
        @endforeach
    </div>

    {{-- Main Interview Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar p-6 sm:p-12">
        <div class="max-w-4xl mx-auto flex flex-col items-center">
            @foreach($parts as $partNumber => $questions)
            <div class="part-panel w-full space-y-8" data-part="{{ $partNumber }}" style="display:none;">
                {{-- Part Header --}}
                <div class="space-y-3 text-center">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50 rounded-full text-[9px] font-black uppercase tracking-widest shadow-soft">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/>
                        </svg>
                        IELTS Speaking Part {{ $partNumber }}
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        @if($partNumber == 1) Introduction &amp; Interview
                        @elseif($partNumber == 2) Long Turn Topic
                        @else Discussion
                        @endif
                    </h2>
                    @if($partNumber == 1)
                        <p class="text-slate-500 dark:text-slate-400 font-medium text-sm sm:text-base leading-relaxed">Answer each question in about 45 seconds, then submit it for evaluation.</p>
                    @elseif($partNumber == 2)
                        <p class="text-slate-500 dark:text-slate-400 font-medium text-sm sm:text-base leading-relaxed">Talk about the topic for 1-2 minutes. Submit after recording.</p>
                    @else
                        <p class="text-slate-500 dark:text-slate-400 font-medium text-sm sm:text-base leading-relaxed">In-depth discussion. Answer each question and submit for evaluation.</p>
                    @endif
                </div>

                {{-- Questions Grid --}}
                <div class="grid grid-cols-1 gap-6 w-full text-left">
                    @foreach($questions as $qi => $question)
                    @php
                        $ans = $existingAnswers->get($question->id);
                        $isSubmitted = $ans && $ans->submitted_at;
                        $existingEval = ($isSubmitted && $ans->evaluation_json) ? json_decode($ans->evaluation_json, true) : null;
                    @endphp
                    <div class="question-card rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden transition-all duration-200 {{ $isSubmitted ? 'border-emerald-500 border-2' : '' }}"
                         data-qid="{{ $question->id }}" data-limit="{{ $question->time_limit }}" id="sq-{{ $question->id }}">

                        {{-- Question Header --}}
                        <div class="p-6 sm:p-8">
                            @if($question->preparation_instructions)
                            <div class="mb-6 flex flex-col gap-3 p-4 rounded-xl bg-amber-50/50 dark:bg-amber-955/20 border border-amber-100 dark:border-amber-900/40 text-amber-800 dark:text-amber-300 text-xs sm:text-sm font-semibold shadow-soft">
                                <div class="flex items-start gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                    <span>{{ $question->preparation_instructions }}</span>
                                </div>
                                @if(!$isSubmitted)
                                <div class="flex items-center gap-3 mt-1 pl-8" id="prep-timer-container-{{ $question->id }}">
                                    <button type="button" onclick="startPrepTimer({{ $question->id }}, 60)" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white text-[11px] font-black uppercase tracking-widest transition-all shadow-soft flex items-center gap-1.5 focus:outline-none" id="prep-btn-{{ $question->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm.5-13H11v6l5.2 3.1.8-1.2-4.5-2.7V7z"/></svg>
                                        Start 60s Preparation Timer
                                    </button>
                                    <span class="text-xs font-mono font-black text-amber-600 dark:text-amber-400 tabular-nums hidden" id="prep-countdown-{{ $question->id }}">01:00</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            <div class="flex gap-4">
                                <div class="flex size-9 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50 font-bold shrink-0">
                                    <span class="text-xs">{{ $qi + 1 }}</span>
                                </div>
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <div class="text-base sm:text-lg font-bold text-slate-850 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $question->question_text }}</div>

                                    {{-- TTS --}}
                                    @if(!$isSubmitted)
                                    <div class="mt-4 flex items-center gap-4">
                                        <button onclick="playTTS({{ $question->id }}, this)" 
                                                class="tts-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-955/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50 text-[10px] font-black uppercase tracking-wider hover:bg-indigo-100/50 transition-colors focus:outline-none" 
                                                data-text="{{ addslashes(strip_tags($question->question_text)) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
                                            </svg>
                                            <span class="tts-label">Listen</span>
                                        </button>
                                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm.5-13H11v6l5.2 3.1.8-1.2-4.5-2.7V7z"/></svg>
                                            {{ $question->time_limit }}s limit
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Record / Audio control --}}
                                    @if(!$isSubmitted)
                                    <div class="mt-6 p-5 sm:p-6 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-4 shadow-inner">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Candidate Speech Response</span>
                                            <div class="flex items-center gap-1">
                                                <span class="recording-time text-xs font-mono font-black text-slate-800 dark:text-slate-200 tabular-nums" id="rec-time-{{ $question->id }}">00:00</span>
                                                <span class="text-[9px] font-black text-slate-400 dark:text-slate-500">/ {{ floor($question->time_limit / 60) }}:{{ str_pad($question->time_limit % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <button onclick="toggleRecording({{ $question->id }}, {{ $question->time_limit }})"
                                                    class="rec-btn flex size-12 shrink-0 items-center justify-center rounded-full transition-all duration-150 active:scale-95 bg-indigo-500 hover:bg-indigo-600 text-white shadow-soft focus:outline-none"
                                                    id="rec-btn-{{ $question->id }}">
                                                <div id="rec-icon-container-{{ $question->id }}" class="flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/></svg>
                                                </div>
                                            </button>
                                            <div class="flex-1" id="playback-{{ $question->id }}" style="display:none;">
                                                <audio controls class="w-full h-10 rounded-xl" id="audio-{{ $question->id }}"></audio>
                                            </div>
                                            <div id="rec-status-{{ $question->id }}" class="shrink-0">
                                                @if($ans && $ans->transcript_text)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 text-[9px] font-black uppercase tracking-widest border border-emerald-100 dark:border-emerald-900/40">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                                        Recorded
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-[9px] font-black uppercase tracking-widest border border-slate-200 dark:border-slate-700/50">
                                                        Not recorded
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Transcript --}}
                                        <div class="transcript-area mt-2" id="transcript-area-{{ $question->id }}" style="{{ ($ans && $ans->transcript_text) ? '' : 'display:none' }}">
                                            <span class="block text-[9px] font-black text-slate-450 dark:text-slate-500 uppercase tracking-widest mb-2">Speech Transcript Preview</span>
                                            <div class="p-4 rounded-xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 text-sm text-slate-500 dark:text-slate-400 italic min-h-[60px] leading-relaxed" id="transcript-{{ $question->id }}">{{ $ans->transcript_text ?? 'Transcript will appear here as you speak...' }}</div>
                                        </div>

                                        {{-- Submit Answer Button --}}
                                        <div class="flex justify-end pt-2" id="submit-area-{{ $question->id }}">
                                            <button onclick="submitSpeakingAnswer({{ $question->id }})"
                                                    id="submit-q-btn-{{ $question->id }}"
                                                    class="flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider hover:opacity-90 transition-opacity active:scale-95 disabled:opacity-50 focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24" id="submit-q-icon-{{ $question->id }}"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                                                <span id="submit-q-label-{{ $question->id }}">Submit Answer</span>
                                            </button>
                                        </div>
                                    </div>
                                    @else
                                    {{-- Already submitted: read-only transcript --}}
                                    @if($ans && $ans->transcript_text)
                                    <div class="mt-6 p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 opacity-80 shadow-soft">
                                        <span class="block text-[9px] font-black text-slate-450 dark:text-slate-500 uppercase tracking-widest mb-2 flex items-center gap-1.5 text-emerald-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                            Your Speech Answer (Submitted)
                                        </span>
                                        <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed italic">{{ $ans->transcript_text }}</div>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>


                    </div>
                    @endforeach
                </div>

                {{-- Part Navigation --}}
                <div class="pt-8 flex items-center justify-center gap-6 shrink-0">
                    @if($partNumber > 1)
                        <button onclick="switchPart({{ $partNumber - 1 }})" class="text-xs font-bold text-slate-450 dark:text-slate-500 hover:text-indigo-500 transition-colors focus:outline-none">
                            ← Previous Part
                        </button>
                    @endif
                    @if($partNumber < count($parts))
                        <button onclick="switchPart({{ $partNumber + 1 }})" class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-soft transition-all duration-155 focus:outline-none">
                            Next Part →
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<form id="speaking-submit-form" action="{{ route('user.speaking.submit', $attempt->id) }}" method="POST" class="hidden">
    @csrf
</form>

{{-- Sleek screen blocker spinner when evaluating in batch --}}
<div id="ai-evaluation-loading-modal" class="fixed inset-0 z-[99999] hidden flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
    <div class="relative w-full max-w-md rounded-2xl border border-indigo-100 dark:border-indigo-900/50 bg-white dark:bg-slate-900 p-8 shadow-premium text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-955/30 text-indigo-600 mb-6 border border-indigo-100 dark:border-indigo-900/40">
            <svg class="animate-spin h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2 uppercase tracking-wide">AI Grading In Progress</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
            Please wait while the AI Gemini Examiner analyzes and evaluates your Speaking recordings. 
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
    const uploadUrl  = '{{ route("user.speaking.uploadAudio", $attempt->id) }}';
    const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
    let currentPart  = {{ $parts->keys()->first() ?? 1 }};

    const submitQuestionUrls = {
        @foreach($speakingQuestions as $q)
        {{ $q->id }}: '{{ route("user.speaking.submitQuestion", [$attempt->id, $q->id]) }}',
        @endforeach
    };

    // Recording state
    const recorders = {};
    let activeQid = null;
    let recTimerInterval = null;
    let recElapsed = 0;
    let recMaxTime = 0;
    let sttTranscript = '';
    let recognition = null;
    let prepTimerInterval = null;

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SR();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = 'en-US';
        // Chrome STT auto-stops after ~60s; restart it while recording is still active
        recognition.onend = function() {
            if (activeQid !== null) {
                try { recognition.start(); } catch(e) {}
            }
        };
        recognition.onerror = function(event) {
            // 'no-speech' and 'aborted' are non-fatal; restart if recording
            if (['no-speech', 'aborted', 'network'].includes(event.error) && activeQid !== null) {
                try { recognition.start(); } catch(e) {}
            }
        };
    }

    // Reset top bar timer to standby limit of first unsubmitted question in active part
    window.resetStandbyTimer = function() {
        const activePanel = document.querySelector('.part-panel[data-part="'+currentPart+'"]');
        if (activePanel) {
            const cards = activePanel.querySelectorAll('.question-card');
            const firstUnsubmittedCard = Array.from(cards).find(card => {
                const qid = card.dataset.qid;
                const recBtn = document.getElementById('rec-btn-' + qid);
                return recBtn && !recBtn.disabled;
            });
            if (firstUnsubmittedCard) {
                const limit = parseInt(firstUnsubmittedCard.dataset.limit) || 45;
                document.getElementById('timer-display').textContent = formatTime(limit);
            } else {
                document.getElementById('timer-display').textContent = '00:00';
            }
        } else {
            document.getElementById('timer-display').textContent = '00:00';
        }
    };

    // ── Part switching ──
    window.switchPart = function(num) {
        currentPart = num;
        document.querySelectorAll('.part-panel').forEach(el => el.style.display = 'none');
        const panel = document.querySelector('.part-panel[data-part="'+num+'"]');
        if (panel) {
            panel.style.display = 'block';
            resetStandbyTimer();
        }

        document.querySelectorAll('.part-progress').forEach(el => {
            const p     = parseInt(el.dataset.part);
            const badge = document.getElementById('progress-badge-'+p);
            const text  = document.getElementById('progress-text-'+p);
            if (!badge || !text) return;

            if (p === num) {
                el.classList.remove('opacity-40');
                badge.className = 'flex size-7 items-center justify-center rounded-lg text-xs font-black bg-indigo-500 text-white border border-indigo-500 transition-all';
                text.className  = 'text-[9px] font-black uppercase tracking-widest text-indigo-500';
            } else if (p < num) {
                el.classList.remove('opacity-40');
                badge.className = 'flex size-7 items-center justify-center rounded-lg text-xs font-black bg-emerald-500 text-white border border-emerald-500 transition-all';
                text.className  = 'text-[9px] font-black uppercase tracking-widest text-slate-500';
            } else {
                el.classList.add('opacity-40');
                badge.className = 'flex size-7 items-center justify-center rounded-lg text-xs font-black bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700/50 transition-all';
                text.className  = 'text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500';
            }
        });
    };
    switchPart(currentPart);

    // Audio synthesizer for prep complete notification (no external assets needed)
    window.playBeep = function() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
            gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.35); // 350ms beep
        } catch(e) {}
    };

    // Part 2 Preparation Timer
    window.startPrepTimer = function(qid, seconds) {
        if (activeQid !== null) stopRecording();
        clearInterval(prepTimerInterval);
        
        const btn = document.getElementById('prep-btn-' + qid);
        const countdownSpan = document.getElementById('prep-countdown-' + qid);
        const widgetLabel = document.getElementById('timer-label');
        
        if (btn) btn.classList.add('hidden');
        if (countdownSpan) {
            countdownSpan.classList.remove('hidden');
            countdownSpan.textContent = formatTime(seconds);
        }
        
        if (widgetLabel) {
            widgetLabel.textContent = 'Prep Time';
            widgetLabel.classList.remove('text-slate-400', 'dark:text-slate-500');
            widgetLabel.classList.add('text-amber-500', 'font-black');
        }
        
        document.getElementById('timer-widget').classList.add('border-amber-500', 'bg-amber-50/50', 'dark:bg-amber-955/20');
        document.getElementById('timer-icon').classList.remove('text-indigo-500');
        document.getElementById('timer-icon').classList.add('text-amber-500');
        
        let prepElapsed = 0;
        document.getElementById('timer-display').textContent = formatTime(seconds);
        
        prepTimerInterval = setInterval(() => {
            prepElapsed++;
            const remaining = seconds - prepElapsed;
            
            if (countdownSpan) countdownSpan.textContent = formatTime(remaining);
            document.getElementById('timer-display').textContent = formatTime(remaining);
            
            if (remaining <= 0) {
                clearInterval(prepTimerInterval);
                playBeep();
                
                const container = document.getElementById('prep-timer-container-' + qid);
                if (container) {
                    container.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 text-[10px] font-black uppercase tracking-widest border border-emerald-100 dark:border-emerald-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        Preparation Complete
                    </span>`;
                }
                
                // Reset global widgets
                if (widgetLabel) {
                    widgetLabel.textContent = 'Per Question';
                    widgetLabel.classList.remove('text-amber-500');
                    widgetLabel.classList.add('text-slate-400', 'dark:text-slate-500');
                }
                document.getElementById('timer-widget').classList.remove('border-amber-500', 'bg-amber-50/50', 'dark:bg-amber-955/20');
                document.getElementById('timer-icon').classList.remove('text-amber-500');
                document.getElementById('timer-icon').classList.add('text-indigo-500');
                
                resetStandbyTimer();
                
                // Highlight and pulse the mic button to guide user
                const recBtn = document.getElementById('rec-btn-' + qid);
                if (recBtn) {
                    recBtn.classList.add('ring-4', 'ring-indigo-400', 'animate-bounce');
                    setTimeout(() => {
                        recBtn.classList.remove('ring-4', 'ring-indigo-400', 'animate-bounce');
                    }, 5000);
                }
            }
        }, 1000);
    };

    // ── TTS ──
    window.playTTS = function(qid, btn) {
        if (!('speechSynthesis' in window)) { alert('TTS not supported.'); return; }
        const utterance = new SpeechSynthesisUtterance(btn.dataset.text);
        utterance.lang = 'en-GB'; utterance.rate = 0.9;
        
        const label = btn.querySelector('.tts-label');
        if (label) label.textContent = 'Listening...';
        btn.classList.add('animate-pulse');
        
        utterance.onend = () => { 
            if (label) label.textContent = 'Listen'; 
            btn.classList.remove('animate-pulse'); 
        };
        speechSynthesis.cancel();
        speechSynthesis.speak(utterance);
    };

    // ── Recording ──
    window.toggleRecording = function(qid, maxSeconds) {
        if (activeQid === qid) { stopRecording(); }
        else { if (activeQid !== null) stopRecording(); startRecording(qid, maxSeconds); }
    };

    async function startRecording(qid, maxSeconds) {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const mediaRecorder = new MediaRecorder(stream);
            const chunks = [];
            mediaRecorder.ondataavailable = (e) => chunks.push(e.data);
            mediaRecorder.onstop = () => {
                stream.getTracks().forEach(t => t.stop());
                handleRecordingComplete(qid, new Blob(chunks, { type: 'audio/webm' }));
            };
            mediaRecorder.start();
            window.examHasChanges = true;
            recorders[qid] = { mediaRecorder, stream };
            activeQid = qid; recElapsed = 0; recMaxTime = maxSeconds; sttTranscript = '';

            const btn  = document.getElementById('rec-btn-'+qid);
            const iconContainer = document.getElementById('rec-icon-container-'+qid);
            
            btn.className = 'rec-btn flex size-12 shrink-0 items-center justify-center rounded-full transition-all duration-150 bg-rose-500 text-white animate-pulse shadow-md focus:outline-none';
            iconContainer.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M6 19h12V5H6v14z"/></svg>';
            
            const dot = document.getElementById('rec-dot');
            dot.className  = 'size-2 rounded-full bg-rose-500 animate-ping';
            const lbl = document.getElementById('rec-label');
            lbl.textContent = 'Recording';
            lbl.className  = 'text-[9px] font-black uppercase tracking-widest text-rose-500';

            updateRecTimer(qid);
            recTimerInterval = setInterval(() => {
                recElapsed++;
                updateRecTimer(qid);
                document.getElementById('timer-display').textContent = formatTime(recMaxTime - recElapsed);
                if (recElapsed >= recMaxTime) stopRecording();
            }, 1000);

            if (recognition) {
                recognition.onresult = (event) => {
                    let finalText = '';
                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        if (event.results[i].isFinal) finalText += event.results[i][0].transcript + ' ';
                    }
                    if (finalText) {
                        sttTranscript += finalText;
                        const ta = document.getElementById('transcript-'+qid);
                        if (ta) ta.textContent = sttTranscript;
                    }
                };
                try { recognition.start(); } catch(e) {}
            }
        } catch(e) {
            alert('Microphone access denied. Please verify microphone hardware permissions in your system settings.');
        }
    }

    function stopRecording() {
        if (activeQid === null) return;
        const qid = activeQid;
        // Set activeQid to null BEFORE stopping recognition so the onend handler
        // does not restart STT in an infinite loop after we explicitly stop.
        activeQid = null;
        const rec = recorders[qid];
        if (rec && rec.mediaRecorder.state === 'recording') rec.mediaRecorder.stop();
        if (recognition) { try { recognition.stop(); } catch(e) {} }
        clearInterval(recTimerInterval);
        
        const btn  = document.getElementById('rec-btn-'+qid);
        const iconContainer = document.getElementById('rec-icon-container-'+qid);
        if (btn)  btn.className = 'rec-btn flex size-12 shrink-0 items-center justify-center rounded-full transition-all duration-150 bg-indigo-500 hover:bg-indigo-600 text-white shadow-soft focus:outline-none';
        if (iconContainer) iconContainer.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/></svg>';
        
        document.getElementById('rec-dot').className    = 'size-2 rounded-full bg-slate-300 dark:bg-slate-700';
        document.getElementById('rec-label').textContent = 'Standby';
        document.getElementById('rec-label').className  = 'text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500';

        resetStandbyTimer();
        // activeQid already set to null at the top of stopRecording()
    }

    async function handleRecordingComplete(qid, blob) {
        const audioUrl = URL.createObjectURL(blob);
        const audioEl  = document.getElementById('audio-'+qid);
        const playback = document.getElementById('playback-'+qid);
        if (audioEl)  audioEl.src = audioUrl;
        if (playback) playback.style.display = 'block';

        const tArea = document.getElementById('transcript-area-'+qid);
        const tEl   = document.getElementById('transcript-'+qid);
        if (tArea) tArea.style.display = 'block';
        if (tEl)   tEl.textContent = sttTranscript.trim() || '(No speech detected — you can re-record)';

        const status = document.getElementById('rec-status-'+qid);
        if (status) status.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 text-indigo-500 text-[9px] font-black uppercase tracking-widest border border-indigo-100 dark:border-indigo-900/40 animate-pulse">Uploading...</span>';

        const formData = new FormData();
        formData.append('audio', blob, 'recording_'+qid+'.webm');
        formData.append('question_id', qid);
        formData.append('transcript', sttTranscript.trim());
        formData.append('duration', recElapsed);

        try {
            await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            if (status) status.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 text-[9px] font-black uppercase tracking-widest border border-emerald-100 dark:border-emerald-900/40 font-bold"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> Recorded</span>';
        } catch(e) {
            if (status) status.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-955/30 text-rose-500 text-[9px] font-black uppercase tracking-widest border border-rose-100 dark:border-rose-900/40">Upload failed</span>';
        }
    }

    function updateRecTimer(qid) {
        const el = document.getElementById('rec-time-'+qid);
        if (el) el.textContent = formatTime(recElapsed);
    }

    function formatTime(sec) {
        sec = Math.max(0, Math.floor(sec));
        return Math.floor(sec/60).toString().padStart(2,'0') + ':' + (sec%60).toString().padStart(2,'0');
    }

    // ── Per-question Submit ──
    window.submitSpeakingAnswer = async function(qid) {
        const btn   = document.getElementById('submit-q-btn-'+qid);
        const submitIconSvg = document.getElementById('submit-q-icon-'+qid);
        const label = document.getElementById('submit-q-label-'+qid);

        if (!confirm('Submit this answer? You cannot re-record after submission.')) return;

        btn.disabled = true;
        if (submitIconSvg) {
            submitIconSvg.outerHTML = '<svg id="submit-q-icon-'+qid+'" class="w-4 h-4 animate-spin fill-current" viewBox="0 0 24 24"><path d="M12 4V2C6.48 2 2 6.48 2 12h2c0-4.41 3.59-8 8-8zm0 14c4.41 0 8-3.59 8-8h2c0 5.52-4.48 10-10 10v-2z"/></svg>';
        }
        label.textContent = 'Saving...';
        btn.classList.add('opacity-60', 'cursor-not-allowed');

        try {
            const res  = await fetch(submitQuestionUrls[qid], {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({})
            });
            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.error || 'Submission failed');

            // Lock the recording area
            const submitArea = document.getElementById('submit-area-'+qid);
            if (submitArea) submitArea.innerHTML = `<div class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-emerald-500 shadow-soft"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg><span class="text-xs font-bold uppercase tracking-wider">Answer Locked</span></div>`;

            // Disable recording button
            const recBtn = document.getElementById('rec-btn-'+qid);
            if (recBtn) { recBtn.disabled = true; recBtn.classList.add('opacity-40', 'cursor-not-allowed'); }

            // Mark question card
            const card = document.getElementById('sq-'+qid);
            if (card) { card.classList.add('border-emerald-500', 'border-2'); }

            // Re-evaluate standby timer since a question is now submitted
            resetStandbyTimer();
        } catch(e) {
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
            const submitIcon = document.getElementById('submit-q-icon-'+qid);
            if (submitIcon) {
                submitIcon.outerHTML = '<svg id="submit-q-icon-'+qid+'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>';
            }
            label.textContent = 'Retry Submit';
            btn.classList.add('bg-rose-500');
            alert('Saving failed: ' + e.message);
        }
    };

    window.prepareSpeakingSubmit = function() {
        if (activeQid !== null) stopRecording();
    };

    // ── End Interview ──
    window.endInterview = function() {
        window.prepareSpeakingSubmit();
        if (confirm('End Interview: Submit your speaking test?')) {
            // Show AI evaluation loader spinner blocker
            const loader = document.getElementById('ai-evaluation-loading-modal');
            if (loader) {
                loader.classList.remove('hidden');
            }
            
            // Bypass beforeunload confirm modal
            window.isAutoSubmitting = true;
            window.onbeforeunload = null;
            
            document.getElementById('speaking-submit-form').submit();
        }
    };
})();
</script>
@endpush
