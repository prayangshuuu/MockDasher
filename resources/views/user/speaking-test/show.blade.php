@extends('layouts.exam')

@section('title', 'Speaking Test')
@section('test_type', 'IELTS Speaking')
@section('test_title', 'IELTS Speaking Exam')

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-2 rounded-full border border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-4 py-1.5 transition-all">
    <span class="material-symbols-outlined text-lg text-[var(--color-primary)]" id="timer-icon">timer</span>
    <span class="text-lg font-bold tabular-nums tracking-tight text-[var(--color-text-primary)] font-mono" id="timer-display">00:00</span>
    <span class="hidden text-[10px] font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:inline" id="timer-label">Per Question</span>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-3">
    <div id="rec-indicator" class="flex items-center gap-1.5 text-[var(--color-text-secondary)]">
        <span class="size-2 rounded-full bg-[var(--color-divider)]" id="rec-dot"></span>
        <span class="text-[10px] font-bold uppercase tracking-wider" id="rec-label">Standby</span>
    </div>
    <x-ui.button variant="primary" onclick="endInterview()" class="text-xs">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        <span class="hidden sm:inline">Complete Interview</span>
    </x-ui.button>
</div>
@endsection

@section('content')
<div id="speaking-app" class="flex-1 flex flex-col overflow-hidden bg-[var(--color-bg-secondary)]">
    {{-- Stage Progress --}}
    <div class="flex h-14 shrink-0 items-center gap-8 border-b border-[var(--color-divider)] bg-[var(--color-bg-primary)] px-6 sm:px-12">
        @foreach($parts as $partNumber => $questions)
            <div class="part-progress flex items-center gap-3 transition-all opacity-40" data-part="{{ $partNumber }}" id="progress-{{ $partNumber }}">
                <div class="flex size-7 items-center justify-center rounded-[var(--radius-xs)] text-xs font-bold bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] transition-all" id="progress-badge-{{ $partNumber }}">
                    {{ $partNumber }}
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-secondary)]" id="progress-text-{{ $partNumber }}">Part {{ $partNumber }}</span>
            </div>
            @if(!$loop->last)
                <div class="h-px w-6 bg-[var(--color-divider)]"></div>
            @endif
        @endforeach
    </div>

    {{-- Main Interview Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar p-6 sm:p-12">
        <div class="max-w-4xl mx-auto flex flex-col items-center text-center">
            @foreach($parts as $partNumber => $questions)
            <div class="part-panel w-full space-y-10" data-part="{{ $partNumber }}" style="display:none;">
                {{-- Part Header --}}
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)] rounded-[var(--radius-base)] text-[10px] font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-sm">record_voice_over</span>
                        IELTS Speaking Part {{ $partNumber }}
                    </div>
                    <h2 class="text-4xl font-bold text-[var(--color-text-primary)] tracking-tight leading-tight">
                        @if($partNumber == 1) Introduction &amp; Interview
                        @elseif($partNumber == 2) Long Turn Topic
                        @else Discussion
                        @endif
                    </h2>
                    @if($partNumber == 1)
                        <p class="text-[var(--color-text-secondary)] font-medium text-base">Answer each question in about 45 seconds, then submit it for evaluation.</p>
                    @elseif($partNumber == 2)
                        <p class="text-[var(--color-text-secondary)] font-medium text-base">Talk about the topic for 1-2 minutes. Submit after recording.</p>
                    @else
                        <p class="text-[var(--color-text-secondary)] font-medium text-base">In-depth discussion. Answer each question and submit for evaluation.</p>
                    @endif
                </div>

                {{-- Questions --}}
                <div class="grid grid-cols-1 gap-8 w-full text-left">
                    @foreach($questions as $qi => $question)
                    @php
                        $ans = $existingAnswers->get($question->id);
                        $isSubmitted = $ans && $ans->submitted_at;
                        $existingEval = ($isSubmitted && $ans->evaluation_json) ? json_decode($ans->evaluation_json, true) : null;
                    @endphp
                    <div class="question-card rounded-[var(--radius-xl)] bg-[var(--color-bg-primary)] border border-[var(--color-divider)] overflow-hidden {{ $isSubmitted ? 'border-[var(--color-success)] border-2' : '' }}"
                         data-qid="{{ $question->id }}" id="sq-{{ $question->id }}">

                        {{-- Question header --}}
                        <div class="p-6 sm:p-8">
                            @if($question->preparation_instructions)
                            <div class="mb-6 flex items-start gap-3 p-4 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,#F59E0B_10%,transparent)] border border-[color-mix(in_srgb,#F59E0B_20%,transparent)] text-[#92400E] text-sm font-bold">
                                <span class="material-symbols-outlined text-[#B45309] text-lg mt-0.5">lightbulb</span>
                                <span>{{ $question->preparation_instructions }}</span>
                            </div>
                            @endif

                            <div class="flex gap-4 sm:gap-6">
                                <div class="flex size-10 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)] font-bold shrink-0">
                                    <span class="text-sm">{{ $qi + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg font-semibold text-[var(--color-text-primary)] leading-relaxed whitespace-pre-line">{{ $question->question_text }}</div>

                                    {{-- TTS --}}
                                    @if(!$isSubmitted)
                                    <div class="mt-4 flex items-center gap-3">
                                        <button onclick="playTTS({{ $question->id }}, this)" class="tts-btn flex items-center gap-2 px-3 py-1.5 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)] text-xs font-bold uppercase tracking-wider hover:opacity-80 transition-opacity" data-text="{{ addslashes(strip_tags($question->question_text)) }}">
                                            <span class="material-symbols-outlined text-sm">volume_up</span>
                                            Listen
                                        </button>
                                        <span class="text-[10px] font-semibold text-[var(--color-text-secondary)] flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">timer</span>
                                            {{ $question->time_limit }}s limit
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Record + Transcript --}}
                                    @if(!$isSubmitted)
                                    <div class="mt-6 p-5 sm:p-6 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-[var(--color-text-secondary)] uppercase tracking-wider">Your Response</span>
                                            <div class="flex items-center gap-2">
                                                <span class="recording-time text-xs font-mono font-bold text-[var(--color-text-primary)] tabular-nums" id="rec-time-{{ $question->id }}">00:00</span>
                                                <span class="text-[10px] font-semibold text-[var(--color-text-secondary)]">/ {{ floor($question->time_limit / 60) }}:{{ str_pad($question->time_limit % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <button onclick="toggleRecording({{ $question->id }}, {{ $question->time_limit }})"
                                                    class="rec-btn flex size-14 items-center justify-center rounded-[var(--radius-base)] transition-transform active:scale-95 bg-[var(--color-text-primary)] text-[var(--color-bg-primary)] hover:opacity-90"
                                                    id="rec-btn-{{ $question->id }}">
                                                <span class="material-symbols-outlined text-2xl" id="rec-icon-{{ $question->id }}">mic</span>
                                            </button>
                                            <div class="flex-1" id="playback-{{ $question->id }}" style="display:none;">
                                                <audio controls class="w-full h-10" id="audio-{{ $question->id }}"></audio>
                                            </div>
                                            <div id="rec-status-{{ $question->id }}">
                                                @if($ans && $ans->transcript_text)
                                                    <span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] text-[var(--color-success)] text-[10px] font-bold uppercase tracking-wider">Recorded</span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[var(--color-divider)] text-[var(--color-text-secondary)] text-[10px] font-bold uppercase tracking-wider">Not recorded</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Transcript --}}
                                        <div class="transcript-area mt-2" id="transcript-area-{{ $question->id }}" style="{{ ($ans && $ans->transcript_text) ? '' : 'display:none' }}">
                                            <span class="block text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider mb-2">Transcript</span>
                                            <div class="p-4 rounded-[var(--radius-base)] bg-[var(--color-bg-primary)] border border-[var(--color-divider)] text-sm text-[var(--color-text-secondary)] italic min-h-[60px]" id="transcript-{{ $question->id }}">{{ $ans->transcript_text ?? 'Transcript will appear after recording...' }}</div>
                                        </div>

                                        {{-- Submit Answer Button --}}
                                        <div class="flex justify-end pt-2" id="submit-area-{{ $question->id }}">
                                            <button onclick="submitSpeakingAnswer({{ $question->id }})"
                                                    id="submit-q-btn-{{ $question->id }}"
                                                    class="flex items-center gap-2 px-5 py-2.5 rounded-[var(--radius-base)] bg-[var(--color-primary)] text-white text-xs font-bold uppercase tracking-wider hover:opacity-90 transition-opacity active:scale-95 disabled:opacity-50">
                                                <span class="material-symbols-outlined text-sm" id="submit-q-icon-{{ $question->id }}">upload</span>
                                                <span id="submit-q-label-{{ $question->id }}">Submit Answer</span>
                                            </button>
                                        </div>
                                    </div>
                                    @else
                                    {{-- Already submitted: show transcript as read-only --}}
                                    @if($ans && $ans->transcript_text)
                                    <div class="mt-6 p-5 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] opacity-75">
                                        <span class="block text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider mb-2">Your Answer (Submitted)</span>
                                        <div class="text-sm text-[var(--color-text-secondary)] italic">{{ $ans->transcript_text }}</div>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Evaluation Panel (shown after submit) --}}
                        <div id="eval-panel-q-{{ $question->id }}"
                             class="border-t-4 border-[var(--color-success)] bg-[var(--color-bg-secondary)] p-6 sm:p-8"
                             style="{{ $existingEval ? '' : 'display:none;' }}">
                            @if($existingEval)
                                @include('user.speaking-test._evaluation', ['eval' => $existingEval, 'bandScore' => $ans->band_score, 'part' => $question->part])
                            @else
                                <div id="eval-content-q-{{ $question->id }}"></div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Part Navigation --}}
                <div class="pt-8 flex items-center justify-center gap-6">
                    @if($partNumber > 1)
                        <button onclick="switchPart({{ $partNumber - 1 }})" class="text-xs font-semibold text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition-colors">
                            ← Previous Part
                        </button>
                    @endif
                    @if($partNumber < count($parts))
                        <x-ui.button variant="primary" onclick="switchPart({{ $partNumber + 1 }})">
                            Next Part →
                        </x-ui.button>
                    @else
                        <x-ui.button variant="primary" onclick="endInterview()">
                            End Interview
                        </x-ui.button>
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

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SR();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = 'en-US';
    }

    // ── Part switching ──
    window.switchPart = function(num) {
        currentPart = num;
        document.querySelectorAll('.part-panel').forEach(el => el.style.display = 'none');
        const panel = document.querySelector('.part-panel[data-part="'+num+'"]');
        if (panel) panel.style.display = 'block';

        document.querySelectorAll('.part-progress').forEach(el => {
            const p     = parseInt(el.dataset.part);
            const badge = document.getElementById('progress-badge-'+p);
            const text  = document.getElementById('progress-text-'+p);
            if (!badge || !text) return;

            if (p === num) {
                el.classList.remove('opacity-40');
                badge.className = 'flex size-7 items-center justify-center rounded-[var(--radius-xs)] text-xs font-bold bg-[var(--color-primary)] text-white transition-all';
                text.className  = 'text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]';
            } else if (p < num) {
                el.classList.remove('opacity-40');
                badge.className = 'flex size-7 items-center justify-center rounded-[var(--radius-xs)] text-xs font-bold bg-[var(--color-success)] text-white transition-all';
                text.className  = 'text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-secondary)]';
            } else {
                el.classList.add('opacity-40');
                badge.className = 'flex size-7 items-center justify-center rounded-[var(--radius-xs)] text-xs font-bold bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] transition-all';
                text.className  = 'text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-secondary)]';
            }
        });
    };
    switchPart(currentPart);

    // ── TTS ──
    window.playTTS = function(qid, btn) {
        if (!('speechSynthesis' in window)) { alert('TTS not supported.'); return; }
        const utterance = new SpeechSynthesisUtterance(btn.dataset.text);
        utterance.lang = 'en-GB'; utterance.rate = 0.9;
        const icon = btn.querySelector('.material-symbols-outlined');
        icon.textContent = 'hearing';
        btn.classList.add('animate-pulse');
        utterance.onend = () => { icon.textContent = 'volume_up'; btn.classList.remove('animate-pulse'); };
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
            recorders[qid] = { mediaRecorder, stream };
            activeQid = qid; recElapsed = 0; recMaxTime = maxSeconds; sttTranscript = '';

            const btn  = document.getElementById('rec-btn-'+qid);
            const icon = document.getElementById('rec-icon-'+qid);
            btn.className = 'rec-btn flex size-14 items-center justify-center rounded-[var(--radius-base)] transition-transform active:scale-95 bg-[var(--color-error)] text-[var(--color-bg-primary)] animate-pulse';
            icon.textContent = 'stop';
            document.getElementById('rec-dot').className  = 'size-2 rounded-full bg-[var(--color-error)] animate-ping';
            document.getElementById('rec-label').textContent = 'Recording';
            document.getElementById('rec-label').className  = 'text-[10px] font-bold uppercase tracking-wider text-[var(--color-error)]';

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
            alert('Microphone access denied. Please allow microphone permissions.');
        }
    }

    function stopRecording() {
        if (activeQid === null) return;
        const qid = activeQid;
        const rec = recorders[qid];
        if (rec && rec.mediaRecorder.state === 'recording') rec.mediaRecorder.stop();
        if (recognition) { try { recognition.stop(); } catch(e) {} }
        clearInterval(recTimerInterval);
        const btn  = document.getElementById('rec-btn-'+qid);
        const icon = document.getElementById('rec-icon-'+qid);
        if (btn)  btn.className = 'rec-btn flex size-14 items-center justify-center rounded-[var(--radius-base)] transition-transform active:scale-95 bg-[var(--color-text-primary)] text-[var(--color-bg-primary)] hover:opacity-90';
        if (icon) icon.textContent = 'mic';
        document.getElementById('rec-dot').className    = 'size-2 rounded-full bg-[var(--color-divider)]';
        document.getElementById('rec-label').textContent = 'Standby';
        document.getElementById('rec-label').className  = 'text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-secondary)]';
        document.getElementById('timer-display').textContent = '00:00';
        activeQid = null;
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
        if (status) status.innerHTML = '<span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)] text-[10px] font-bold uppercase tracking-wider animate-pulse">Uploading...</span>';

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
            if (status) status.innerHTML = '<span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] text-[var(--color-success)] text-[10px] font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined text-xs">cloud_done</span> Recorded</span>';
        } catch(e) {
            if (status) status.innerHTML = '<span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-error)_10%,transparent)] text-[var(--color-error)] text-[10px] font-bold uppercase tracking-wider">Upload failed</span>';
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
        const icon  = document.getElementById('submit-q-icon-'+qid);
        const label = document.getElementById('submit-q-label-'+qid);

        if (!confirm('Submit this answer? You cannot re-record after submission.')) return;

        btn.disabled = true;
        icon.textContent  = 'hourglass_empty';
        label.textContent = 'Evaluating...';
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
            if (submitArea) submitArea.innerHTML = `<div class="flex items-center gap-2 px-4 py-2 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] border border-[color-mix(in_srgb,var(--color-success)_20%,transparent)] text-[var(--color-success)]"><span class="material-symbols-outlined text-sm">check_circle</span><span class="text-xs font-bold uppercase tracking-wider">Answer Submitted</span></div>`;

            // Disable recording button
            const recBtn = document.getElementById('rec-btn-'+qid);
            if (recBtn) { recBtn.disabled = true; recBtn.classList.add('opacity-40', 'cursor-not-allowed'); }

            // Mark question card
            const card = document.getElementById('sq-'+qid);
            if (card) { card.classList.add('border-[var(--color-success)]', 'border-2'); }

            // Render evaluation
            if (data.evaluation) {
                renderSpeakingEvaluation(qid, data.band_score, data.evaluation);
            }
        } catch(e) {
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
            icon.textContent  = 'error';
            label.textContent = 'Retry Submit';
            btn.classList.add('bg-[var(--color-error)]');
            alert('Evaluation failed: ' + e.message);
        }
    };

    function renderSpeakingEvaluation(qid, bandScore, eval_) {
        const panel   = document.getElementById('eval-panel-q-'+qid);
        const content = document.getElementById('eval-content-q-'+qid);
        if (!panel || !content) return;

        const isNewSchema = eval_.criteria_scores !== undefined;

        const scoreColor = (s) => s >= 7 ? 'text-emerald-500' : (s >= 5.5 ? 'text-amber-500' : 'text-rose-500');
        const bandColor  = bandScore >= 7 ? 'bg-emerald-500' : (bandScore >= 5.5 ? 'bg-amber-500' : 'bg-rose-500');

        // ── Criteria grid ──────────────────────────────────────────────────────
        let criteriaHtml = '';
        if (isNewSchema) {
            const cs = eval_.criteria_scores || {};
            const newCriteria = [
                { label: 'Fluency & Coherence',          score: cs.fluency_and_coherence },
                { label: 'Lexical Resource',              score: cs.lexical_resource },
                { label: 'Grammatical Range & Accuracy',  score: cs.grammatical_range_and_accuracy },
                { label: 'Pronunciation (estimated)',     score: cs.pronunciation },
            ];
            criteriaHtml = newCriteria.map(c => {
                const s = c.score ?? '—';
                return `<div class="p-4 rounded-[var(--radius-lg)] bg-[var(--color-bg-primary)] border border-[var(--color-divider)]">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-black uppercase tracking-widest text-[var(--color-text-secondary)]">${c.label}</span>
                        <span class="text-lg font-black ${scoreColor(s)}">${s}</span>
                    </div>
                </div>`;
            }).join('');
        } else {
            // Legacy v1 schema
            const oldCriteria = [
                { key: 'fluency_coherence',          label: 'Fluency & Coherence' },
                { key: 'lexical_resource',            label: 'Lexical Resource' },
                { key: 'grammatical_range_accuracy',  label: 'Grammatical Range & Accuracy' },
                { key: 'pronunciation',               label: 'Pronunciation (estimated)' },
            ];
            criteriaHtml = oldCriteria.map(c => {
                const d = eval_[c.key] || {};
                const s = d.score ?? '—';
                return `<div class="p-4 rounded-[var(--radius-lg)] bg-[var(--color-bg-primary)] border border-[var(--color-divider)]">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-black uppercase tracking-widest text-[var(--color-text-secondary)]">${c.label}</span>
                        <span class="text-lg font-black ${scoreColor(s)}">${s}</span>
                    </div>
                    <p class="text-xs text-[var(--color-text-secondary)] leading-relaxed">${d.feedback || ''}</p>
                </div>`;
            }).join('');
        }

        // ── Extra sections ────────────────────────────────────────────────────
        let extraHtml = '';
        if (isNewSchema) {
            if (eval_.detailed_feedback) {
                extraHtml += `<div class="mb-3 p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
                    <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Detailed Feedback</p>
                    <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">${eval_.detailed_feedback}</p>
                </div>`;
            }
            if (eval_.vocabulary_corrections?.length) {
                const items = eval_.vocabulary_corrections.map(v =>
                    `<div class="flex items-start gap-3 text-xs">
                        <span class="shrink-0 px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-mono line-through">${v.incorrect || ''}</span>
                        <span class="shrink-0 text-[var(--color-text-secondary)]">→</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-mono font-semibold">${v.suggested || ''}</span>
                    </div>`
                ).join('');
                extraHtml += `<div class="mb-3 p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#8B5CF6_5%,transparent)] border border-[color-mix(in_srgb,#8B5CF6_20%,transparent)]">
                    <p class="text-[10px] font-black text-violet-500 uppercase tracking-widest mb-3">Vocabulary Improvements</p>
                    <div class="space-y-2">${items}</div>
                </div>`;
            }
            if (eval_.grammar_corrections?.length) {
                const items = eval_.grammar_corrections.map(g =>
                    `<div class="space-y-1 text-xs">
                        <div class="flex items-start gap-2"><span class="shrink-0 text-[10px] font-bold uppercase text-rose-400 mt-0.5 w-12">Wrong:</span><span class="text-rose-700 italic">${g.incorrect || ''}</span></div>
                        <div class="flex items-start gap-2"><span class="shrink-0 text-[10px] font-bold uppercase text-emerald-500 mt-0.5 w-12">Better:</span><span class="text-emerald-700 font-semibold">${g.suggested || ''}</span></div>
                    </div>`
                ).join('');
                extraHtml += `<div class="mb-3 p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#EF4444_5%,transparent)] border border-[color-mix(in_srgb,#EF4444_20%,transparent)]">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3">Grammar Corrections</p>
                    <div class="space-y-3">${items}</div>
                </div>`;
            }
            if (eval_.suggestions_for_improvement) {
                extraHtml += `<div class="p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#10B981_5%,transparent)] border border-[color-mix(in_srgb,#10B981_20%,transparent)]">
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Suggestions for Improvement</p>
                    <p class="text-xs text-[var(--color-text-primary)] leading-relaxed">${eval_.suggestions_for_improvement}</p>
                </div>`;
            }
        } else {
            // Legacy v1
            if (eval_.overall_feedback) {
                extraHtml = `<div class="p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
                    <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Feedback</p>
                    <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">${eval_.overall_feedback}</p>
                </div>`;
            }
        }

        content.innerHTML = `
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h4 class="text-lg font-black text-[var(--color-text-primary)]">Answer Evaluation</h4>
                    <p class="text-xs text-[var(--color-text-secondary)] mt-0.5">AI IELTS Band Assessment</p>
                </div>
                <div class="flex items-center justify-center size-16 rounded-[var(--radius-xl)] ${bandColor} text-white shadow-lg shrink-0">
                    <div class="text-center">
                        <div class="text-2xl font-black leading-none">${bandScore ?? '—'}</div>
                        <div class="text-[8px] font-bold uppercase tracking-wider opacity-80 mt-0.5">Band</div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">${criteriaHtml}</div>
            ${extraHtml}
        `;

        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ── End Interview ──
    window.endInterview = function() {
        if (activeQid !== null) stopRecording();
        if (confirm('End Interview: Submit your speaking test?')) {
            document.getElementById('speaking-submit-form').submit();
        }
    };
})();
</script>
@endpush
