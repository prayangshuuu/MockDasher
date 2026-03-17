@extends('layouts.exam')

@section('title', 'Writing Test - ' . $attempt->test->book_number)
@section('test_type', 'IELTS Writing')
@section('test_title', 'IELTS ' . $attempt->test->book_number)

@section('timer_area')
<div x-data="writingTimer({{ $remainingSeconds }})" class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 px-4 py-2 rounded-2xl shadow-sm transition-all" :class="timeRemaining <= 300 ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/20' : 'border-slate-100 dark:border-slate-700'">
    <span class="material-symbols-outlined text-xl" :class="timeRemaining <= 300 ? 'text-rose-500 animate-pulse' : 'text-primary'">timer</span>
    <div class="flex items-baseline gap-1.5">
        <span class="text-2xl font-black font-mono tracking-tighter tabular-nums" :class="timeRemaining <= 300 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white'" x-text="formattedTime"></span>
        <span class="text-[10px] font-black uppercase tracking-widest opacity-40">Remaining</span>
    </div>
</div>
@endsection

@section('top_right_actions')
<div x-data="{ saving: false }" 
     @autosave-start.window="saving = true" 
     @autosave-end.window="saving = false"
     class="flex items-center gap-4">
    <div x-show="saving" x-cloak class="flex items-center gap-2 text-slate-400">
        <span class="material-symbols-outlined text-sm animate-spin">refresh</span>
        <span class="text-[10px] font-black uppercase tracking-widest">Saving...</span>
    </div>
    <div x-show="!saving" x-cloak class="flex items-center gap-2 text-emerald-500">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        <span class="text-[10px] font-black uppercase tracking-widest">Saved</span>
    </div>
    
    <button @click="$dispatch('trigger-submit')" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg shadow-slate-200 dark:shadow-none">
        <span class="material-symbols-outlined text-sm">send</span>
        End Exam
    </button>
</div>
@endsection

@section('content')
<div x-data="writingApp('{{ route('user.writing.autosave', $attempt->id) }}', '{{ route('user.writing.submit', $attempt->id) }}')" 
     class="flex-1 flex flex-col overflow-hidden"
     @trigger-submit.window="submitTest()">
    
    <!-- Task Nav (If multiple tasks) -->
    <div class="h-12 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center px-8 gap-6 z-10">
        @foreach($tasks as $index => $task)
            <button @click="currentTask = {{ $index }}" 
                    class="h-full flex items-center gap-2 px-4 text-xs font-black uppercase tracking-widest transition-all border-b-2"
                    :class="currentTask === {{ $index }} ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'">
                <span class="material-symbols-outlined text-sm">edit_note</span>
                Task {{ $task->task_number }}
            </button>
        @endforeach
    </div>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden">
        @foreach($tasks as $index => $task)
        <div x-show="currentTask === {{ $index }}" x-cloak class="flex-1 flex overflow-hidden">
            <!-- Left: Task Description -->
            <div class="w-1/2 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/50 p-10 border-r border-slate-200 dark:border-slate-800">
                <div class="max-w-2xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary/10 text-primary rounded-lg text-[10px] font-black uppercase tracking-widest mb-6">
                        <span class="material-symbols-outlined text-xs">info</span>
                        Writing Task {{ $task->task_number }}
                    </div>
                    
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-6 tracking-tight leading-tight">
                        {{ $task->task_title ?: 'Simulation Prompt' }}
                    </h2>

                    @if($task->instruction_text)
                        <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-soft mb-8 text-slate-600 dark:text-slate-300 font-medium leading-relaxed italic">
                            {{ $task->instruction_text }}
                        </div>
                    @endif

                    <div class="prose prose-slate dark:prose-invert max-w-none">
                        @if($task->task_description)
                            <p class="text-lg leading-relaxed text-slate-700 dark:text-slate-300 mb-8 whitespace-pre-wrap">
                                {{ $task->task_description }}
                            </p>
                        @endif

                        @if($task->images->count() > 0)
                            <div class="rounded-3xl border-4 border-white dark:border-slate-800 shadow-xl overflow-hidden mb-8 bg-white dark:bg-slate-900">
                                <img src="{{ Storage::url($task->images->first()->image_path) }}" class="w-full h-auto object-contain max-h-[500px]" alt="Task Content">
                            </div>
                        @endif

                        @if($task->task_prompt)
                            <div class="p-8 bg-indigo-50 dark:bg-indigo-900/20 rounded-3xl border-2 border-primary/20 text-slate-800 dark:text-slate-200 font-bold leading-relaxed shadow-lg">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">Detailed Prompt</span>
                                {{ $task->task_prompt }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Answer Editor -->
            <div class="w-1/2 flex flex-col bg-white dark:bg-slate-950">
                <div class="flex-1 relative">
                    <textarea 
                        x-model="answers[{{ $task->id }}]"
                        @input="updateWordCount({{ $task->id }}, {{ $task->minimum_word_count }})"
                        class="absolute inset-0 w-full h-full p-12 text-lg font-medium bg-transparent border-none focus:ring-0 resize-none custom-scrollbar leading-relaxed placeholder:text-slate-300 dark:placeholder:text-slate-700"
                        placeholder="Start typing your response here..."></textarea>
                </div>
                
                <!-- Editor Footer -->
                <div class="h-16 border-t border-slate-200 dark:border-slate-800 px-8 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all"
                             :class="wordCounts[{{ $task->id }}] >= {{ $task->minimum_word_count }} ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-500'">
                            <span class="text-xs font-black tabular-nums" x-text="wordCounts[{{ $task->id }}] || 0"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest opacity-60">Words</span>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Target: {{ $task->minimum_word_count }} min.
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-primary">Live Sync Active</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Submission Form -->
    <form id="submission-form" :action="submitUrl" method="POST" class="hidden">
        @csrf
        @foreach($tasks as $task)
            <input type="hidden" :name="'answers['+{{ $task->id }}+']'" :value="answers[{{ $task->id }}]">
        @endforeach
    </form>
</div>
@endsection

@push('scripts')
<script>
    function writingTimer(initialSeconds) {
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

    function writingApp(autosaveUrl, submitUrl) {
        return {
            currentTask: 0,
            answers: @json($answers->map(fn($a) => $a->answer_text)),
            wordCounts: {},
            autosaveUrl: autosaveUrl,
            submitUrl: submitUrl,
            init() {
                this.answers = this.answers || {};
                // Pre-calculate word counts
                Object.keys(this.answers).forEach(id => {
                    this.updateWordCount(id);
                });

                // Periodic autosave
                setInterval(() => this.autosave(), 15000);
            },
            updateWordCount(id) {
                const text = this.answers[id] || '';
                const trimmed = text.trim();
                this.wordCounts[id] = trimmed === '' ? 0 : trimmed.split(/\s+/).length;
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
                        body: JSON.stringify({ answers: this.answers })
                    });
                    if (response.ok) $dispatch('autosave-end');
                } catch (e) {
                    console.error('Autosave failed:', e);
                }
            },
            submitTest() {
                if (confirm("Final Submission: Are you sure you want to end your writing exam? Your answers will be locked.")) {
                    document.getElementById('submission-form').submit();
                }
            }
        }
    }
</script>
@endpush
