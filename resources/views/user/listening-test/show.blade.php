@extends('layouts.exam')

@section('title', 'Listening Test - ' . $test->book_number)
@section('test_type', 'IELTS Listening')
@section('test_title', 'IELTS ' . $test->book_number)

@section('timer_area')
<div x-data="listeningTimer({{ $transferRemainingSeconds ?? 1800 }})" class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 px-4 py-2 rounded-2xl shadow-sm transition-all" :class="timeRemaining <= 300 ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/20' : 'border-slate-100 dark:border-slate-700'">
    <span class="material-symbols-outlined text-xl" :class="timeRemaining <= 300 ? 'text-rose-500 animate-pulse' : 'text-primary'">timer</span>
    <div class="flex items-baseline gap-1.5">
        <span class="text-2xl font-black font-mono tracking-tighter tabular-nums" :class="timeRemaining <= 300 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white'" x-text="formattedTime"></span>
        <span class="text-[10px] font-black uppercase tracking-widest opacity-40">Remaining</span>
    </div>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-6">
    <div x-data="{ saving: false }" 
         @autosave-start.window="saving = true" 
         @autosave-end.window="saving = false"
         class="flex items-center gap-2">
        <div x-show="saving" x-cloak class="flex items-center gap-2 text-slate-400">
            <span class="material-symbols-outlined text-sm animate-spin">refresh</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Saving...</span>
        </div>
        <div x-show="!saving" x-cloak class="flex items-center gap-2 text-emerald-500">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Saved</span>
        </div>
    </div>
    
    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>

    <button @click="$dispatch('trigger-review')" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg shadow-slate-200 dark:shadow-none">
        <span class="material-symbols-outlined text-sm">assignment_turned_in</span>
        End Test
    </button>
</div>
@endsection

@section('content')
<div x-data="listeningApp('{{ Storage::url($test->audio_path) }}', '{{ route('user.listening.autosave', $attempt->id) }}', '{{ route('user.listening.submit', $attempt->id) }}')"
     class="flex-1 flex flex-col overflow-hidden"
     @trigger-review.window="showReview = true">
    
    <!-- Audio Control Panel -->
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 p-6 flex flex-col items-center gap-4 z-40 relative shadow-md">
        <audio x-ref="audio" @timeupdate="updateProgress()" @ended="audioEnded = true"></audio>
        
        <div class="w-full max-w-4xl flex items-center gap-8">
            <button @click="toggleAudio()" class="size-16 rounded-full exam-gradient flex items-center justify-center text-white shadow-xl shadow-primary/30 hover:scale-105 transition-all active:scale-95 group">
                <span class="material-symbols-outlined text-4xl font-black fill-1" x-text="isPlaying ? 'pause' : 'play_arrow'"></span>
            </button>
            
            <div class="flex-1 space-y-3">
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                    <span x-text="formattedCurrentTime">00:00</span>
                    <span x-text="formattedDuration">00:00</span>
                </div>
                <!-- Progress Bar -->
                <div @click="seekTo($event)" class="h-3 bg-slate-100 dark:bg-slate-800 rounded-full cursor-pointer relative group overflow-hidden border border-slate-200 dark:border-slate-700">
                    <div class="h-full exam-gradient transition-all duration-300 relative" :style="`width: ${progress}%` shadow-inner">
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 size-4 bg-white rounded-full border-2 border-primary shadow-sm scale-0 group-hover:scale-100 transition-transform"></div>
                    </div>
                </div>
            </div>

            <!-- Volume -->
            <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl border border-slate-200 dark:border-slate-700">
                <span class="material-symbols-outlined text-slate-400 text-xl">volume_up</span>
                <input type="range" x-model="volume" @input="updateVolume()" min="0" max="1" step="0.1" class="w-20 accent-primary h-1">
            </div>
        </div>
        
        <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest flex items-center gap-2">
            <span class="size-1.5 rounded-full bg-rose-500 animate-pulse"></span>
            Note: Audio can only be played once in the actual exam.
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Question List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/50 p-12">
            <div class="max-w-4xl mx-auto space-y-10">
                @foreach($test->sections as $section)
                    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500 shadow-sm bg-white dark:bg-slate-900/40 p-10 rounded-[40px] border border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl font-black">music_note</span>
                            </div>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic">
                                Section {{ $section->section_number }}
                            </h2>
                        </div>
                        
                        @foreach($section->questionGroups as $group)
                            <div class="space-y-6 pl-14">
                                @if($group->group_instruction)
                                    <div class="p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-primary/20 text-slate-800 dark:text-slate-200 font-bold text-sm leading-relaxed italic shadow-sm">
                                        {{ $group->group_instruction }}
                                    </div>
                                @endif

                                <div class="space-y-8">
                                    @foreach($group->questions as $qi => $question)
                                        @php $qNum = 1 + $test->sections->where('section_number', '<', $section->section_number)->flatMap(fn($s) => $s->questionGroups->flatMap(fn($g) => $g->questions))->count() + $qi; @endphp
                                        <div id="question-{{ $question->id }}" class="group/q" @click="activeQuestion = {{ $question->id }}">
                                            <div class="flex items-start gap-6">
                                                <div class="size-10 rounded-2xl flex items-center justify-center shrink-0 border-2 transition-all"
                                                     :class="[
                                                        activeQuestion === {{ $question->id }} ? 'border-primary bg-primary text-white shadow-lg' : 'border-slate-100 dark:border-slate-800 text-slate-400 bg-white dark:bg-slate-900'
                                                     ]">
                                                    <span class="text-sm font-black">{{ $qNum }}</span>
                                                </div>
                                                
                                                <div class="flex-1 pt-1">
                                                    <div class="text-[17px] font-bold text-slate-900 dark:text-white leading-relaxed mb-6">
                                                        {!! nl2br(e($question->question_text)) !!}
                                                    </div>

                                                    <!-- Answer Input Area -->
                                                    <div class="max-w-md">
                                                        @if($question->question_type === 'multiple_choice')
                                                            <div class="grid grid-cols-1 gap-2">
                                                                @foreach($question->options as $oi => $opt)
                                                                    <label class="flex items-center gap-4 p-4 rounded-xl border border-slate-100 dark:border-slate-800 cursor-pointer transition-all hover:border-primary/30"
                                                                           :class="answers[{{ $question->id }}] == '{{ str_replace("'", "\'", $opt->option_text) }}' ? 'bg-primary/5 border-primary/50 text-slate-900 dark:text-white' : 'text-slate-500'">
                                                                        <input type="radio" name="q_{{ $question->id }}" value="{{ $opt->option_text }}" x-model="answers[{{ $question->id }}]" class="size-4 text-primary focus:ring-primary">
                                                                        <span class="text-sm font-bold flex items-center gap-3">
                                                                            <span class="opacity-40">{{ chr(65+$oi) }}.</span>
                                                                            {{ $opt->option_text }}
                                                                        </span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <input type="text" x-model="answers[{{ $question->id }}]" 
                                                                   class="w-full bg-white dark:bg-slate-950 border-2 border-slate-100 dark:border-slate-800 rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none italic"
                                                                   placeholder="Click to enter answer...">
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <button @click.stop="toggleFlag({{ $question->id }})" class="transition-colors" :class="flags[{{ $question->id }}] ? 'text-rose-500' : 'text-slate-200 hover:text-slate-400'">
                                                    <span class="material-symbols-outlined text-3xl font-light" :class="flags[{{ $question->id }}] ? 'fill-1' : ''">flag</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom: Answer Sheet Navigation -->
    <div class="h-24 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center px-10 z-30 shrink-0">
        <div class="flex-1 flex gap-2 overflow-x-auto py-3 custom-scrollbar">
            @php $qIdx = 1; @endphp
            @foreach($test->sections as $section)
                @foreach($section->questionGroups as $group)
                    @foreach($group->questions as $question)
                    <button @click="jumpToQuestion({{ $question->id }})"
                            class="size-12 rounded-2xl border-2 flex items-center justify-center shrink-0 transition-all relative"
                            :class="[
                                activeQuestion === {{ $question->id }} ? 'border-primary ring-2 ring-primary/30 scale-110' : 'border-transparent',
                                answers[{{ $question->id }}] ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'
                            ]">
                        <span class="text-sm font-black">{{ $qIdx++ }}</span>
                        <div x-show="flags[{{ $question->id }}]" x-cloak class="absolute -top-1.5 -right-1.5 size-4 bg-rose-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                    </button>
                    @endforeach
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- Review Overlay -->
    <div x-show="showReview" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-8 bg-slate-950/80 backdrop-blur-md">
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
                <button @click="showReview = false" class="size-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-500 transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-12 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div class="p-8 rounded-[32px] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Answered</p>
                        <div class="flex items-end gap-3">
                            <h3 class="text-4xl font-black text-slate-900 dark:text-white leading-none" x-text="Object.keys(answers).filter(id => answers[id] != null && answers[id].trim() !== '').length"></h3>
                            <span class="text-slate-300 dark:text-slate-600 text-lg font-bold">/ {!! $test->sections->flatMap(fn($s) => $s->questionGroups->flatMap(fn($g) => $g->questions))->count() !!}</span>
                        </div>
                    </div>
                    <div class="p-8 rounded-[32px] bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30 text-rose-600">
                        <p class="text-[10px] font-black uppercase tracking-widest text-rose-500 mb-2">Requires Attention</p>
                        <h3 class="text-4xl font-black leading-none" x-text="Object.keys(flags).filter(id => flags[id]).length"></h3>
                    </div>
                </div>

                <div class="space-y-12">
                    @foreach($test->sections as $section)
                        <div>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3 italic">
                                <span>Section {{ $section->section_number }}</span>
                                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800"></div>
                            </h4>
                            <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 gap-3">
                                @foreach($section->questionGroups->flatMap(fn($g) => $g->questions) as $q)
                                    @php $revNum = 1 + $test->sections->flatMap(fn($s) => $s->questionGroups->flatMap(fn($g) => $g->questions))->filter(fn($qq) => $qq->id < $q->id)->count(); @endphp
                                    <button @click="jumpToQuestion({{ $q->id }}); showReview = false;" 
                                            class="aspect-square rounded-2xl border-2 flex flex-col items-center justify-center transition-all relative"
                                            :class="answers[{{ $q->id }}] ? 'bg-primary border-primary text-white shadow-lg' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400 hover:border-primary/30'">
                                        <span class="text-xs font-black">{{ $revNum }}</span>
                                        <div x-show="flags[{{ $q->id }}]" x-cloak class="absolute -top-1 -right-1 size-3 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900"></div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-10 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between shrink-0">
                <button @click="showReview = false" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Back to Section
                </button>

                <form id="final-submit-form" :action="submitUrl" method="POST" class="flex items-center gap-4">
                    @csrf
                    <template x-for="(val, qId) in answers" :key="qId">
                        <input type="hidden" :name="'answers['+qId+']'" :value="val">
                    </template>
                    <template x-for="(val, qId) in flags" :key="qId">
                        <input x-if="val" type="hidden" :name="'flagged['+qId+']'" value="1">
                    </template>
                    
                    <button type="button" @click="confirmSubmit()" class="px-12 py-5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-[28px] text-sm font-black uppercase tracking-[0.2em] shadow-2xl hover:scale-[1.02] transition-all active:scale-95">
                        Submit My Result
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function listeningTimer(initialSeconds) {
        return {
            timeRemaining: initialSeconds,
            init() {
                setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining <= 0) document.getElementById('final-submit-form').submit();
                }, 1000);
            },
            get formattedTime() {
                const m = Math.floor(this.timeRemaining / 60);
                const s = this.timeRemaining % 60;
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            }
        }
    }

    function listeningApp(audioUrl, autosaveUrl, submitUrl) {
        return {
            isPlaying: false,
            progress: 0,
            volume: 0.8,
            currentTime: 0,
            duration: 0,
            audioEnded: false,
            activeQuestion: null,
            answers: @json($savedAnswers),
            flags: @json($flaggedAnswers),
            showReview: false,
            autosaveUrl: autosaveUrl,
            submitUrl: submitUrl,
            
            init() {
                this.$refs.audio.src = audioUrl;
                this.$refs.audio.volume = this.volume;
                this.$refs.audio.onloadedmetadata = () => this.duration = this.$refs.audio.duration;
                
                this.answers = this.answers || {};
                this.flags = this.flags || {};
                
                setInterval(() => this.autosave(), 20000);
            },
            
            toggleAudio() {
                if (this.isPlaying) {
                    this.$refs.audio.pause();
                } else {
                    this.$refs.audio.play();
                }
                this.isPlaying = !this.isPlaying;
            },
            
            updateProgress() {
                this.currentTime = this.$refs.audio.currentTime;
                this.progress = (this.currentTime / this.duration) * 100;
            },
            
            seekTo(e) {
                const rect = e.currentTarget.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                this.$refs.audio.currentTime = pos * this.duration;
            },
            
            updateVolume() {
                this.$refs.audio.volume = this.volume;
            },
            
            get formattedCurrentTime() { return this.formatTime(this.currentTime); },
            get formattedDuration() { return this.formatTime(this.duration); },
            
            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = Math.floor(seconds % 60);
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            },
            
            toggleFlag(qId) {
                this.flags[qId] = !this.flags[qId];
                this.autosave();
            },
            
            jumpToQuestion(qId) {
                this.activeQuestion = qId;
                this.$nextTick(() => {
                    const el = document.getElementById('question-' + qId);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            },
            
            async autosave() {
                $dispatch('autosave-start');
                try {
                    const response = await fetch(this.autosaveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ 
                            answers: this.answers,
                            flagged: this.flags
                        })
                    });
                    if (response.ok) $dispatch('autosave-end');
                } catch (e) {
                    console.error('Autosave failed:', e);
                }
            },
            
            confirmSubmit() {
                if (confirm("End Listening Test: Are you ready to submit?")) {
                    document.getElementById('final-submit-form').submit();
                }
            }
        }
    }
</script>
@endpush
