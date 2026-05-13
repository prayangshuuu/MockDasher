@extends('layouts.exam')

@section('title', 'Speaking Test')
@section('test_type', 'IELTS Speaking')
@section('test_title', 'IELTS Speaking Exam')

@section('timer_area')
<div id="timer-widget" class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 px-4 py-2 rounded-2xl shadow-sm transition-all border-slate-100 dark:border-slate-700">
    <span class="material-symbols-outlined text-xl text-primary" id="timer-icon">timer</span>
    <div class="flex items-baseline gap-1.5">
        <span class="text-2xl font-black font-mono tracking-tighter tabular-nums text-slate-900 dark:text-white" id="timer-display">00:00</span>
        <span class="text-[10px] font-black uppercase tracking-widest opacity-40" id="timer-label">Per Question</span>
    </div>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-4">
    <div id="rec-indicator" class="flex items-center gap-2 text-slate-400">
        <span class="size-2 rounded-full bg-slate-300" id="rec-dot"></span>
        <span class="text-[10px] font-black uppercase tracking-widest" id="rec-label">Standby</span>
    </div>
    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>
    <button onclick="endInterview()" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg shadow-slate-200 dark:shadow-none">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        Complete Interview
    </button>
</div>
@endsection

@section('content')
<div id="speaking-app" class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-slate-950">
    {{-- Stage Progress --}}
    <div class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center px-12 gap-12 z-10 shrink-0">
        @foreach($parts as $partNumber => $questions)
            <div class="part-progress flex items-center gap-4 transition-all opacity-30" data-part="{{ $partNumber }}" id="progress-{{ $partNumber }}">
                <div class="size-8 rounded-lg flex items-center justify-center text-xs font-black bg-slate-100 text-slate-400 transition-all" id="progress-badge-{{ $partNumber }}">
                    {{ $partNumber }}
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400" id="progress-text-{{ $partNumber }}">Part {{ $partNumber }}</span>
            </div>
            @if(!$loop->last)
                <div class="w-8 h-px bg-slate-200 dark:bg-slate-800"></div>
            @endif
        @endforeach
    </div>

    {{-- Main Interview Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar p-12">
        <div class="max-w-4xl mx-auto flex flex-col items-center text-center">
            @foreach($parts as $partNumber => $questions)
            <div class="part-panel w-full space-y-10" data-part="{{ $partNumber }}" style="display:none;">
                {{-- Part Header --}}
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-3 px-4 py-1.5 bg-primary/10 text-primary rounded-full text-[10px] font-black uppercase tracking-widest">
                        <span class="material-symbols-outlined text-sm">record_voice_over</span>
                        IELTS Speaking Part {{ $partNumber }}
                    </div>
                    <h2 class="text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                        @if($partNumber == 1) Introduction &amp; Interview
                        @elseif($partNumber == 2) Long Turn Topic
                        @else Discussion
                        @endif
                    </h2>
                    @if($partNumber == 1)
                        <p class="text-slate-500 font-bold text-lg">The examiner asks about yourself and familiar topics. Answer each question in about 45 seconds.</p>
                    @elseif($partNumber == 2)
                        <p class="text-slate-500 font-bold text-lg">Talk about the topic for 1-2 minutes. You have 1 minute to prepare.</p>
                    @else
                        <p class="text-slate-500 font-bold text-lg">In-depth discussion questions. Answer each in about 90 seconds.</p>
                    @endif
                </div>

                {{-- Questions --}}
                <div class="grid grid-cols-1 gap-6 w-full text-left">
                    @foreach($questions as $qi => $question)
                    <div class="question-card p-8 rounded-3xl bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-800 transition-all" data-qid="{{ $question->id }}" id="sq-{{ $question->id }}">
                        {{-- Prep Instructions (Part 2) --}}
                        @if($question->preparation_instructions)
                        <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 text-sm font-bold flex items-start gap-3">
                            <span class="material-symbols-outlined text-lg mt-0.5">lightbulb</span>
                            <span>{{ $question->preparation_instructions }}</span>
                        </div>
                        @endif

                        <div class="flex gap-6">
                            <div class="size-12 rounded-xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center shrink-0 border border-slate-100 dark:border-slate-700">
                                <span class="text-sm font-black text-primary">{{ $qi + 1 }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-xl font-bold text-slate-900 dark:text-white leading-[1.6] whitespace-pre-line">{{ $question->question_text }}</div>

                                {{-- TTS: Play question audio --}}
                                <div class="mt-4 flex items-center gap-3">
                                    <button onclick="playTTS({{ $question->id }}, this)" class="tts-btn flex items-center gap-2 px-4 py-2 rounded-xl bg-primary/10 text-primary text-xs font-black uppercase tracking-widest hover:bg-primary/20 transition-all" data-text="{{ addslashes(strip_tags($question->question_text)) }}">
                                        <span class="material-symbols-outlined text-sm">volume_up</span>
                                        Listen to Question
                                    </button>
                                    <span class="text-[10px] font-bold text-slate-400 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">timer</span>
                                        {{ $question->time_limit }}s limit
                                    </span>
                                </div>

                                {{-- Record + Answer area --}}
                                <div class="mt-6 p-6 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Your Response</span>
                                        <div class="flex items-center gap-2">
                                            <span class="recording-time text-xs font-mono font-black text-slate-400 tabular-nums" id="rec-time-{{ $question->id }}">00:00</span>
                                            <span class="max-badge text-[10px] font-black text-slate-300" id="max-badge-{{ $question->id }}">/ {{ floor($question->time_limit / 60) }}:{{ str_pad($question->time_limit % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <button onclick="toggleRecording({{ $question->id }}, {{ $question->time_limit }})"
                                                class="rec-btn size-16 rounded-2xl flex items-center justify-center transition-all active:scale-95 shadow-lg bg-slate-900 text-white"
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
                                                <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest">Recorded</span>
                                            @else
                                                <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-widest">Not recorded</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- STT Transcript --}}
                                    <div class="transcript-area" id="transcript-area-{{ $question->id }}" style="{{ empty($existingAnswers[$question->id]) ? 'display:none' : '' }}">
                                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Transcript (auto-generated)</span>
                                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-sm text-slate-600 dark:text-slate-400 italic min-h-[60px]" id="transcript-{{ $question->id }}">{{ $existingAnswers[$question->id] ?? 'Transcript will appear after recording...' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Part Navigation --}}
                <div class="pt-8 flex items-center justify-center gap-8">
                    @if($partNumber > 1)
                        <button onclick="switchPart({{ $partNumber - 1 }})" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                            ← Previous Part
                        </button>
                    @endif
                    @if($partNumber < count($parts))
                        <button onclick="switchPart({{ $partNumber + 1 }})" class="px-10 py-5 bg-primary text-white rounded-2xl text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                            Next Part →
                        </button>
                    @else
                        <button onclick="endInterview()" class="px-10 py-5 bg-emerald-600 text-white rounded-2xl text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 hover:scale-105 transition-all">
                            End Interview
                        </button>
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
        document.querySelectorAll('.part-progress').forEach(el => el.classList.add('opacity-30'));
        const panel = document.querySelector('.part-panel[data-part="'+num+'"]');
        const prog = document.getElementById('progress-'+num);
        if (panel) panel.style.display = 'block';
        if (prog) prog.classList.remove('opacity-30');
        // Update badge
        document.querySelectorAll('.part-progress').forEach(el => {
            const p = parseInt(el.dataset.part);
            const badge = document.getElementById('progress-badge-'+p);
            const text = document.getElementById('progress-text-'+p);
            if (p === num) {
                badge.className = 'size-8 rounded-lg flex items-center justify-center text-xs font-black exam-gradient text-white shadow-lg transition-all';
                text.classList.remove('text-slate-400'); text.classList.add('text-primary');
            } else if (p < num) {
                badge.className = 'size-8 rounded-lg flex items-center justify-center text-xs font-black bg-emerald-500 text-white transition-all';
                text.classList.remove('text-primary'); text.classList.add('text-slate-400');
            } else {
                badge.className = 'size-8 rounded-lg flex items-center justify-center text-xs font-black bg-slate-100 text-slate-400 transition-all';
                text.classList.remove('text-primary'); text.classList.add('text-slate-400');
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
            btn.className = 'rec-btn size-16 rounded-2xl flex items-center justify-center transition-all active:scale-95 shadow-lg bg-rose-500 text-white animate-pulse';
            icon.textContent = 'stop';

            document.getElementById('rec-dot').className = 'size-2 rounded-full bg-rose-500 animate-ping';
            document.getElementById('rec-label').textContent = 'Recording';
            document.getElementById('rec-label').className = 'text-[10px] font-black uppercase tracking-widest text-rose-500';
            document.getElementById('rec-indicator').className = 'flex items-center gap-2 bg-rose-50 border border-rose-200 rounded-lg px-3 py-1.5 text-rose-600';

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
        if (btn) btn.className = 'rec-btn size-16 rounded-2xl flex items-center justify-center transition-all active:scale-95 shadow-lg bg-slate-900 text-white';
        if (icon) icon.textContent = 'mic';
        document.getElementById('rec-dot').className = 'size-2 rounded-full bg-slate-300';
        document.getElementById('rec-label').textContent = 'Standby';
        document.getElementById('rec-label').className = 'text-[10px] font-black uppercase tracking-widest text-slate-400';
        document.getElementById('rec-indicator').className = 'flex items-center gap-2 text-slate-400';
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
        if (status) status.innerHTML = '<span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest">Recorded</span>';

        // Upload to server
        const formData = new FormData();
        formData.append('audio', blob, 'recording_'+qid+'.webm');
        formData.append('question_id', qid);
        formData.append('transcript', sttTranscript.trim());
        formData.append('duration', recElapsed);

        try {
            const uploadStatus = document.getElementById('status-'+qid);
            if (uploadStatus) uploadStatus.innerHTML = '<span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest animate-pulse">Uploading...</span>';

            await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            if (uploadStatus) uploadStatus.innerHTML = '<span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-1"><span class="material-symbols-outlined text-xs">cloud_done</span> Saved</span>';
        } catch(e) {
            console.error('Upload failed:', e);
            const uploadStatus = document.getElementById('status-'+qid);
            if (uploadStatus) uploadStatus.innerHTML = '<span class="px-3 py-1 rounded-lg bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest">Upload failed</span>';
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
