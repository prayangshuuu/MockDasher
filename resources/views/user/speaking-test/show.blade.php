@extends('layouts.exam')

@section('title', 'Speaking Test - ' . $test->book_number)
@section('test_type', 'IELTS Speaking')
@section('test_title', 'IELTS ' . $test->book_number)

@section('timer_area')
<div x-data="speakingTimer({{ $remainingSeconds }})" class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 px-4 py-2 rounded-2xl shadow-sm transition-all" :class="timeRemaining <= 60 ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/20' : 'border-slate-100 dark:border-slate-700'">
    <span class="material-symbols-outlined text-xl" :class="timeRemaining <= 60 ? 'text-rose-500 animate-pulse' : 'text-primary'">timer</span>
    <div class="flex items-baseline gap-1.5">
        <span class="text-2xl font-black font-mono tracking-tighter tabular-nums" :class="timeRemaining <= 60 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white'" x-text="formattedTime"></span>
        <span class="text-[10px] font-black uppercase tracking-widest opacity-40">Remaining</span>
    </div>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-6">
    <div x-data="{ recording: false }" 
         @recording-start.window="recording = true" 
         @recording-stop.window="recording = false"
         class="flex items-center gap-3 px-4 py-2 rounded-xl border transition-all"
         :class="recording ? 'bg-rose-50 border-rose-200 text-rose-600' : 'bg-slate-50 border-slate-200 text-slate-400'">
        <span class="size-2 rounded-full bg-rose-500" :class="recording && 'animate-ping'"></span>
        <span class="text-[10px] font-black uppercase tracking-widest" x-text="recording ? 'Capturing Audio' : 'Standby'"></span>
    </div>
    
    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>

    <button @click="$dispatch('trigger-submit')" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg shadow-slate-200 dark:shadow-none">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        Complete Interview
    </button>
</div>
@endsection

@section('content')
<div x-data="speakingApp('{{ route('user.speaking.submit', $attempt->id) }}')"
     class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-slate-950"
     @trigger-submit.window="submitInterview()">
    
    <!-- Stage Progress -->
    <div class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center px-12 gap-12 z-10 shrink-0">
        @foreach($test->parts as $index => $part)
            <div class="flex items-center gap-4 transition-all" :class="currentPart >= {{ $index }} ? 'opacity-100' : 'opacity-30'">
                <div class="size-8 rounded-xl flex items-center justify-center text-xs font-black transition-all"
                     :class="currentPart === {{ $index }} ? 'exam-gradient text-white shadow-lg' : (currentPart > {{ $index }} ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400')">
                    @if($index < 0) <!-- Placeholder for logic -->
                    @else
                        {{ $index + 1 }}
                    @endif
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest" :class="currentPart === {{ $index }} ? 'text-primary' : 'text-slate-400'">{{ $part->part_title }}</span>
            </div>
            @if(!$loop->last)
                <div class="w-8 h-px bg-slate-200 dark:bg-slate-800"></div>
            @endif
        @endforeach
    </div>

    <!-- Main Interview Area -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-12">
        <div class="max-w-4xl mx-auto flex flex-col items-center text-center">
            @foreach($test->parts as $index => $part)
                <div x-show="currentPart === {{ $index }}" x-cloak class="w-full space-y-12 animate-in fade-in zoom-in-95 duration-500">
                    
                    <!-- Part Header -->
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-3 px-4 py-1.5 bg-primary/10 text-primary rounded-full text-[10px] font-black uppercase tracking-widest">
                            <span class="material-symbols-outlined text-sm">record_voice_over</span>
                            IELTS Speaking Part {{ $index + 1 }}
                        </div>
                        <h2 class="text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                            {{ $part->part_title }}
                        </h2>
                        @if($part->part_description)
                            <p class="text-lg text-slate-500 font-medium italic">
                                {{ $part->part_description }}
                            </p>
                        @endif
                    </div>

                    <!-- Prompts / Questions -->
                    <div class="grid grid-cols-1 gap-6 w-full text-left">
                        @foreach($part->prompts as $pi => $prompt)
                            <div class="p-8 rounded-[32px] bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-100 dark:border-slate-800 transition-all hover:border-primary/20 shadow-soft">
                                <div class="flex gap-6">
                                    <div class="size-12 rounded-2xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center shrink-0 border border-slate-100 dark:border-slate-700">
                                        <span class="text-sm font-black text-primary">{{ $pi + 1 }}</span>
                                    </div>
                                    <div class="text-2xl font-bold text-slate-900 dark:text-white leading-[1.6]">
                                        {{ $prompt->prompt_text }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Audio Visualization Placeholder -->
                    <div x-show="isRecording" class="w-full h-32 flex items-center justify-center gap-1.5 px-12">
                        <template x-for="i in 40">
                            <div class="w-1.5 bg-rose-500 rounded-full animate-pulse" 
                                 :style="`height: ${Math.random() * 80 + 20}%; animation-delay: ${i * 0.05}s`"></div>
                        </template>
                    </div>

                    <!-- Controls -->
                    <div class="pt-12 flex items-center justify-center gap-8">
                        @if($index > 0)
                            <button @click="currentPart--" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                Previous Part
                            </button>
                        @endif

                        <button @click="startOrStopRecording()" 
                                class="size-24 rounded-full flex items-center justify-center transition-all active:scale-95 shadow-2xl relative"
                                :class="isRecording ? 'bg-rose-500 text-white shadow-rose-500/30' : 'bg-slate-900 text-white shadow-slate-900/30'">
                            <span class="material-symbols-outlined text-4xl font-black fill-1" x-text="isRecording ? 'stop' : 'mic'"></span>
                            <div x-show="isRecording" class="absolute inset-0 rounded-full border-4 border-rose-500 animate-ping opacity-20"></div>
                        </button>

                        @if($index < count($test->parts) - 1)
                            <button @click="currentPart++; stopRecording();" class="px-10 py-5 bg-primary text-white rounded-[28px] text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Next Part
                            </button>
                        @else
                            <button @click="submitInterview()" class="px-10 py-5 bg-emerald-600 text-white rounded-[28px] text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 hover:scale-105 transition-all">
                                End Interview
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Hidden Submission Form -->
    <form id="speaking-form" :action="submitUrl" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" name="recording" x-ref="audioInput">
    </form>
</div>
@endsection

@push('scripts')
<script>
    function speakingTimer(initialSeconds) {
        return {
            timeRemaining: initialSeconds,
            init() {
                setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining <= 0) $dispatch('trigger-submit');
                }, 1000);
            },
            get formattedTime() {
                const m = Math.floor(this.timeRemaining / 60);
                const s = this.timeRemaining % 60;
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            }
        }
    }

    function speakingApp(submitUrl) {
        return {
            currentPart: 0,
            isRecording: false,
            mediaRecorder: null,
            audioChunks: [],
            submitUrl: submitUrl,
            
            init() {
                // Potential setup for recording
            },
            
            async startOrStopRecording() {
                if (this.isRecording) {
                    this.stopRecording();
                } else {
                    await this.startRecording();
                }
            },
            
            async startRecording() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    this.mediaRecorder = new MediaRecorder(stream);
                    this.audioChunks = [];
                    
                    this.mediaRecorder.ondataavailable = (e) => this.audioChunks.push(e.data);
                    this.mediaRecorder.onstop = () => {
                        $dispatch('recording-stop');
                        // In a real app, we'd append to form or upload
                    };
                    
                    this.mediaRecorder.start();
                    this.isRecording = true;
                    $dispatch('recording-start');
                } catch (e) {
                    alert('Audio permissions denied or microphone not found.');
                }
            },
            
            stopRecording() {
                if (this.mediaRecorder && this.isRecording) {
                    this.mediaRecorder.stop();
                    this.isRecording = false;
                }
            },
            
            submitInterview() {
                if (confirm("Finish Interview: Are you ready to submit your speaking responses for evaluation?")) {
                    document.getElementById('speaking-form').submit();
                }
            }
        }
    }
</script>
@endpush
