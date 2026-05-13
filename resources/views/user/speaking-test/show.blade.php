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
                        <p class="text-[var(--color-text-secondary)] font-medium text-base">The examiner asks about yourself and familiar topics. Answer each question in about 45 seconds.</p>
                    @elseif($partNumber == 2)
                        <p class="text-[var(--color-text-secondary)] font-medium text-base">Talk about the topic for 1-2 minutes. You have 1 minute to prepare.</p>
                    @else
                        <p class="text-[var(--color-text-secondary)] font-medium text-base">In-depth discussion questions. Answer each in about 90 seconds.</p>
                    @endif
                </div>

                {{-- Questions --}}
                <div class="grid grid-cols-1 gap-6 w-full text-left">
                    @foreach($questions as $qi => $question)
                    <div class="question-card p-6 sm:p-8 rounded-[var(--radius-xl)] bg-[var(--color-bg-primary)] border border-[var(--color-divider)] transition-all" data-qid="{{ $question->id }}" id="sq-{{ $question->id }}">
                        {{-- Prep Instructions (Part 2) --}}
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

                                {{-- TTS: Play question audio --}}
                                <div class="mt-4 flex items-center gap-3">
                                    <button onclick="playTTS({{ $question->id }}, this)" class="tts-btn flex items-center gap-2 px-3 py-1.5 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)] text-xs font-bold uppercase tracking-wider hover:opacity-80 transition-opacity" data-text="{{ addslashes(strip_tags($question->question_text)) }}">
                                        <span class="material-symbols-outlined text-sm">volume_up</span>
                                        Listen to Question
                                    </button>
                                    <span class="text-[10px] font-semibold text-[var(--color-text-secondary)] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">timer</span>
                                        {{ $question->time_limit }}s limit
                                    </span>
                                </div>

                                {{-- Record + Answer area --}}
                                <div class="mt-6 p-5 sm:p-6 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-[var(--color-text-secondary)] uppercase tracking-wider">Your Response</span>
                                        <div class="flex items-center gap-2">
                                            <span class="recording-time text-xs font-mono font-bold text-[var(--color-text-primary)] tabular-nums" id="rec-time-{{ $question->id }}">00:00</span>
                                            <span class="max-badge text-[10px] font-semibold text-[var(--color-text-secondary)]" id="max-badge-{{ $question->id }}">/ {{ floor($question->time_limit / 60) }}:{{ str_pad($question->time_limit % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <button onclick="toggleRecording({{ $question->id }}, {{ $question->time_limit }})"
                                                class="rec-btn flex size-14 items-center justify-center rounded-[var(--radius-base)] transition-transform active:scale-95 bg-[var(--color-text-primary)] text-[var(--color-bg-primary)] hover:opacity-90"
                                                id="rec-btn-{{ $question->id }}">
                                            <span class="material-symbols-outlined text-2xl" id="rec-icon-{{ $question->id }}">mic</span>
                                        </button>

                                        {{-- Audio playback --}}
                                        <div class="flex-1" id="playback-{{ $question->id }}" style="display:none;">
                                            <audio controls class="w-full h-10" id="audio-{{ $question->id }}"></audio>
                                        </div>

                                        {{-- Status badges --}}
                                        <div id="status-{{ $question->id }}">
                                            @if(!empty($existingAnswers[$question->id]))
                                                <span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] text-[var(--color-success)] text-[10px] font-bold uppercase tracking-wider">Recorded</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[var(--color-divider)] text-[var(--color-text-secondary)] text-[10px] font-bold uppercase tracking-wider">Not recorded</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- STT Transcript --}}
                                    <div class="transcript-area mt-4" id="transcript-area-{{ $question->id }}" style="{{ empty($existingAnswers[$question->id]) ? 'display:none' : '' }}">
                                        <span class="block text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider mb-2">Transcript (auto-generated)</span>
                                        <div class="p-4 rounded-[var(--radius-base)] bg-[var(--color-bg-primary)] border border-[var(--color-divider)] text-sm text-[var(--color-text-secondary)] italic min-h-[60px]" id="transcript-{{ $question->id }}">{{ $existingAnswers[$question->id] ?? 'Transcript will appear after recording...' }}</div>
                                    </div>
                                </div>
                            </div>
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

{{-- Hidden submit form --}}
<form id="speaking-submit-form" action="{{ route('user.speaking.submit', $attempt->id) }}" method="POST" class="hidden">
    @csrf
</form>
@endsection

@push('scripts')
<script>
(function() {
    const uploadUrl = '{{ route("user.speaking.uploadAudio", $attempt->id) }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let currentPart = {{ $parts->keys()->first() ?? 1 }};

    // Recording state per question
    const recorders = {};
    let activeQid = null;
    let recTimerInterval = null;
    let recElapsed = 0;
    let recMaxTime = 0;

    // STT
    let recognition = null;
    let sttTranscript = '';
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
        document.querySelectorAll('.part-progress').forEach(el => el.classList.add('opacity-40'));
        const panel = document.querySelector('.part-panel[data-part="'+num+'"]');
        const prog = document.getElementById('progress-'+num);
        if (panel) panel.style.display = 'block';
        if (prog) prog.classList.remove('opacity-40');
        // Update badge
        document.querySelectorAll('.part-progress').forEach(el => {
            const p = parseInt(el.dataset.part);
            const badge = document.getElementById('progress-badge-'+p);
            const text = document.getElementById('progress-text-'+p);
            if (p === num) {
                badge.className = 'flex size-7 items-center justify-center rounded-[var(--radius-xs)] text-xs font-bold bg-[var(--color-primary)] text-white transition-all';
                text.classList.remove('text-[var(--color-text-secondary)]'); text.classList.add('text-[var(--color-primary)]');
            } else if (p < num) {
                badge.className = 'flex size-7 items-center justify-center rounded-[var(--radius-xs)] text-xs font-bold bg-[var(--color-success)] text-white transition-all';
                text.classList.remove('text-[var(--color-primary)]'); text.classList.add('text-[var(--color-text-secondary)]');
            } else {
                badge.className = 'flex size-7 items-center justify-center rounded-[var(--radius-xs)] text-xs font-bold bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] transition-all';
                text.classList.remove('text-[var(--color-primary)]'); text.classList.add('text-[var(--color-text-secondary)]');
            }
        });
    };
    switchPart(currentPart);

    // ── TTS (Text-to-Speech) ──
    window.playTTS = function(qid, btn) {
        if (!('speechSynthesis' in window)) {
            alert('Text-to-Speech is not supported in this browser.');
            return;
        }
        const text = btn.dataset.text;
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-GB';
        utterance.rate = 0.9;
        utterance.pitch = 1.0;

        // Visual feedback
        const icon = btn.querySelector('.material-symbols-outlined');
        icon.textContent = 'hearing';
        btn.classList.add('animate-pulse');
        utterance.onend = () => {
            icon.textContent = 'volume_up';
            btn.classList.remove('animate-pulse');
        };
        speechSynthesis.cancel();
        speechSynthesis.speak(utterance);
    };

    // ── Recording ──
    window.toggleRecording = function(qid, maxSeconds) {
        if (activeQid === qid) {
            stopRecording();
        } else {
            if (activeQid !== null) stopRecording();
            startRecording(qid, maxSeconds);
        }
    };

    async function startRecording(qid, maxSeconds) {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const mediaRecorder = new MediaRecorder(stream);
            const chunks = [];

            mediaRecorder.ondataavailable = (e) => chunks.push(e.data);
            mediaRecorder.onstop = () => {
                stream.getTracks().forEach(t => t.stop());
                const blob = new Blob(chunks, { type: 'audio/webm' });
                handleRecordingComplete(qid, blob);
            };

            mediaRecorder.start();
            recorders[qid] = { mediaRecorder, stream };
            activeQid = qid;
            recElapsed = 0;
            recMaxTime = maxSeconds;
            sttTranscript = '';

            // UI
            const btn = document.getElementById('rec-btn-'+qid);
            const icon = document.getElementById('rec-icon-'+qid);
            btn.className = 'rec-btn flex size-14 items-center justify-center rounded-[var(--radius-base)] transition-transform active:scale-95 bg-[var(--color-error)] text-[var(--color-bg-primary)] animate-pulse';
            icon.textContent = 'stop';

            document.getElementById('rec-dot').className = 'size-2 rounded-full bg-[var(--color-error)] animate-ping';
            document.getElementById('rec-label').textContent = 'Recording';
            document.getElementById('rec-label').className = 'text-[10px] font-bold uppercase tracking-wider text-[var(--color-error)]';
            document.getElementById('rec-indicator').className = 'flex items-center gap-1.5 text-[var(--color-error)]';

            // Timer
            updateRecTimer(qid);
            recTimerInterval = setInterval(() => {
                recElapsed++;
                updateRecTimer(qid);
                // Global timer update
                document.getElementById('timer-display').textContent = formatTime(recMaxTime - recElapsed);
                if (recElapsed >= recMaxTime) {
                    stopRecording();
                }
            }, 1000);

            // STT
            if (recognition) {
                recognition.onresult = (event) => {
                    let finalText = '';
                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        if (event.results[i].isFinal) {
                            finalText += event.results[i][0].transcript + ' ';
                        }
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
            alert('Microphone access denied or not available. Please allow microphone permissions.');
        }
    }

    function stopRecording() {
        if (activeQid === null) return;
        const qid = activeQid;
        const rec = recorders[qid];
        if (rec && rec.mediaRecorder.state === 'recording') {
            rec.mediaRecorder.stop();
        }
        if (recognition) { try { recognition.stop(); } catch(e) {} }
        clearInterval(recTimerInterval);

        // Reset UI
        const btn = document.getElementById('rec-btn-'+qid);
        const icon = document.getElementById('rec-icon-'+qid);
        if (btn) btn.className = 'rec-btn flex size-14 items-center justify-center rounded-[var(--radius-base)] transition-transform active:scale-95 bg-[var(--color-text-primary)] text-[var(--color-bg-primary)] hover:opacity-90';
        if (icon) icon.textContent = 'mic';
        document.getElementById('rec-dot').className = 'size-2 rounded-full bg-[var(--color-divider)]';
        document.getElementById('rec-label').textContent = 'Standby';
        document.getElementById('rec-label').className = 'text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-secondary)]';
        document.getElementById('rec-indicator').className = 'flex items-center gap-1.5 text-[var(--color-text-secondary)]';
        document.getElementById('timer-display').textContent = '00:00';

        activeQid = null;
    }

    async function handleRecordingComplete(qid, blob) {
        // Show playback
        const audioUrl = URL.createObjectURL(blob);
        const audioEl = document.getElementById('audio-'+qid);
        const playback = document.getElementById('playback-'+qid);
        if (audioEl) { audioEl.src = audioUrl; }
        if (playback) playback.style.display = 'block';

        // Show transcript
        const tArea = document.getElementById('transcript-area-'+qid);
        if (tArea) tArea.style.display = 'block';
        const tEl = document.getElementById('transcript-'+qid);
        if (tEl && sttTranscript.trim()) tEl.textContent = sttTranscript.trim();
        else if (tEl) tEl.textContent = '(No speech detected — you can re-record)';

        // Status
        const status = document.getElementById('status-'+qid);
        if (status) status.innerHTML = '<span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] text-[var(--color-success)] text-[10px] font-bold uppercase tracking-wider">Recorded</span>';

        // Upload to server
        const formData = new FormData();
        formData.append('audio', blob, 'recording_'+qid+'.webm');
        formData.append('question_id', qid);
        formData.append('transcript', sttTranscript.trim());
        formData.append('duration', recElapsed);

        try {
            const uploadStatus = document.getElementById('status-'+qid);
            if (uploadStatus) uploadStatus.innerHTML = '<span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)] text-[10px] font-bold uppercase tracking-wider animate-pulse">Uploading...</span>';

            await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            if (uploadStatus) uploadStatus.innerHTML = '<span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] text-[var(--color-success)] text-[10px] font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined text-xs">cloud_done</span> Saved</span>';
        } catch(e) {
            console.error('Upload failed:', e);
            const uploadStatus = document.getElementById('status-'+qid);
            if (uploadStatus) uploadStatus.innerHTML = '<span class="px-2.5 py-1 rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-error)_10%,transparent)] text-[var(--color-error)] text-[10px] font-bold uppercase tracking-wider">Upload failed</span>';
        }
    }

    function updateRecTimer(qid) {
        const el = document.getElementById('rec-time-'+qid);
        if (el) el.textContent = formatTime(recElapsed);
    }

    function formatTime(sec) {
        sec = Math.max(0, Math.floor(sec));
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
    }

    // ── Submit ──
    window.endInterview = function() {
        if (activeQid !== null) stopRecording();
        if (confirm('End Interview: Are you sure you want to submit your speaking test for evaluation?')) {
            document.getElementById('speaking-submit-form').submit();
        }
    };
})();
</script>
@endpush
