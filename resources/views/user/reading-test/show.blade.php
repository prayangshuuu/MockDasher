@extends('layouts.exam')

@section('title', 'Reading Test - ' . $test->book_number)
@section('test_type', 'IELTS Reading')
@section('test_title', 'IELTS ' . $test->book_number)

@section('timer_area')
<div x-data="readingTimer({{ $remainingSeconds }})" class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 px-4 py-2 rounded-2xl shadow-sm transition-all" :class="timeRemaining <= 300 ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/20' : 'border-slate-100 dark:border-slate-700'">
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
        Review & Submit
    </button>
</div>
@endsection

@section('content')
<div x-data="readingApp({!! json_encode($passages->map(fn($p) => ['id' => $p->id, 'number' => $p->passage_number, 'questions' => $p->questionGroups->flatMap(fn($g) => $g->questions->pluck('id'))])) !!}, '{{ route('user.reading.autosave', $attempt->id) }}', '{{ route('user.reading.submit', $attempt->id) }}')"
     class="flex-1 flex flex-col overflow-hidden"
     @trigger-review.window="showReview = true">
    
    <!-- Passage Selector Tabs -->
    <div class="h-12 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center px-8 gap-6 z-10 shrink-0">
        @foreach($passages as $index => $passage)
            <button @click="currentPassage = {{ $passage->passage_number }}" 
                    class="h-full flex items-center gap-2 px-4 text-xs font-black uppercase tracking-widest transition-all border-b-2"
                    :class="currentPassage === {{ $passage->passage_number }} ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'">
                Passage {{ $passage->passage_number }}
            </button>
        @endforeach
    </div>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Left: Passage Content -->
        <div class="w-1/2 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/50 p-12 border-r border-slate-200 dark:border-slate-800">
            @foreach($passages as $passage)
                <div x-show="currentPassage === {{ $passage->passage_number }}" x-cloak class="max-w-3xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                        {{ $passage->title }}
                    </h2>
                    <div class="prose prose-slate dark:prose-invert max-w-none text-lg leading-[1.8] font-serif text-slate-700 dark:text-slate-300">
                        {!! $passage->content !!}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right: Questions Content -->
        <div class="w-1/2 overflow-y-auto custom-scrollbar bg-white dark:bg-slate-950 p-12">
            @foreach($passages as $passage)
                <div x-show="currentPassage === {{ $passage->passage_number }}" x-cloak class="space-y-10">
                    @foreach($passage->questionGroups as $group)
                        @if($group->group_instruction)
                            <div class="p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-primary/20 text-slate-800 dark:text-slate-200 font-bold text-sm leading-relaxed italic">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">Instructions</span>
                                {{ $group->group_instruction }}
                            </div>
                        @endif

                        <div class="space-y-6">
                            @foreach($group->questions as $qi => $question)
                                @php $qNum = 1 + $passages->where('passage_number', '<', $passage->passage_number)->flatMap(fn($p) => $p->questionGroups->flatMap(fn($g) => $g->questions))->count() + $qi; @endphp
                                <div id="question-{{ $question->id }}" class="group/q rounded-2xl border border-slate-100 dark:border-slate-800 p-8 transition-all hover:bg-slate-50 dark:hover:bg-slate-800/30"
                                     :class="activeQuestion === {{ $question->id }} ? 'border-primary ring-1 ring-primary/20 bg-slate-50 dark:bg-slate-800/50 shadow-soft' : ''"
                                     @click="activeQuestion = {{ $question->id }}">
                                    
                                    <div class="flex items-start gap-4 mb-6">
                                        <div class="size-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-black text-slate-900 dark:text-white">{{ $qNum }}</span>
                                        </div>
                                        <div class="text-[17px] font-bold text-slate-900 dark:text-white leading-relaxed pt-1">
                                            {!! nl2br(e($question->question_text)) !!}
                                        </div>
                                        <button @click.stop="toggleFlag({{ $question->id }})" class="ml-auto transition-colors" :class="flags[{{ $question->id }}] ? 'text-rose-500' : 'text-slate-300 hover:text-slate-400'">
                                            <span class="material-symbols-outlined font-light" :class="flags[{{ $question->id }}] ? 'fill-1' : ''">flag</span>
                                        </button>
                                    </div>

                                    <!-- Answer Input Area -->
                                    <div class="pl-12">
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
                                        @elseif(in_array($question->question_type, ['true_false_not_given', 'yes_no_not_given']))
                                            <div class="flex flex-wrap gap-3">
                                                @php $opts = $question->question_type === 'true_false_not_given' ? ['True', 'False', 'Not Given'] : ['Yes', 'No', 'Not Given']; @endphp
                                                @foreach($opts as $opt)
                                                    <button @click="answers[{{ $question->id }}] = '{{ $opt }}'" 
                                                            class="px-6 py-2.5 rounded-xl border text-xs font-black uppercase tracking-widest transition-all"
                                                            :class="answers[{{ $question->id }}] === '{{ $opt }}' ? 'bg-primary border-primary text-white shadow-lg shadow-primary/20' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-500 hover:border-primary/50'">
                                                        {{ $opt }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @else
                                            <input type="text" x-model="answers[{{ $question->id }}]" 
                                                   class="w-full max-w-md bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                                                   placeholder="Write your answer here...">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <!-- Bottom: Answer Sheet Navigation -->
    <div class="h-20 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center px-8 z-30 shrink-0">
        <div class="flex-1 flex gap-2 overflow-x-auto py-2 custom-scrollbar">
            @php $qIdx = 1; @endphp
            @foreach($passages as $passage)
                @foreach($passage->questionGroups as $group)
                    @foreach($group->questions as $question)
                    <button @click="jumpToQuestion({{ $question->id }}, {{ $passage->passage_number }})"
                            class="size-10 rounded-xl border-2 flex items-center justify-center shrink-0 transition-all relative"
                            :class="[
                                activeQuestion === {{ $question->id }} ? 'border-primary ring-2 ring-primary/30 scale-110' : 'border-transparent',
                                answers[{{ $question->id }}] ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'
                            ]">
                        <span class="text-xs font-black">{{ $qIdx++ }}</span>
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
                        <span class="material-symbols-outlined text-4xl font-light">fact_check</span>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none mb-2">Review Summary</h2>
                        <p class="text-slate-500 font-bold text-xs uppercase tracking-widest">Please check all answers before final submission.</p>
                    </div>
                </div>
                <button @click="showReview = false" class="size-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-500 transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-12 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="p-8 rounded-[32px] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Completion</p>
                        <div class="flex items-end gap-3">
                            <h3 class="text-4xl font-black text-slate-900 dark:text-white leading-none" x-text="Object.keys(answers).filter(id => answers[id] != null && answers[id].trim() !== '').length"></h3>
                            <span class="text-slate-300 dark:text-slate-600 text-lg font-bold">/ {!! $passages->flatMap(fn($p) => $p->questionGroups->flatMap(fn($g) => $g->questions))->count() !!}</span>
                        </div>
                    </div>
                    <div class="p-8 rounded-[32px] bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-rose-500 mb-2">Flagged Items</p>
                        <h3 class="text-4xl font-black text-rose-600 leading-none" x-text="Object.keys(flags).filter(id => flags[id]).length"></h3>
                    </div>
                    <div x-data="readingTimer({{ $remainingSeconds }})" class="p-8 rounded-[32px] bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500 mb-2">Time Remaining</p>
                        <h3 class="text-4xl font-black text-indigo-700 leading-none x-mono" x-text="formattedTime"></h3>
                    </div>
                </div>

                <div class="space-y-12">
                    @foreach($passages as $p)
                        <div>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                <span>Reading Passage {{ $p->passage_number }}</span>
                                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800"></div>
                            </h4>
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                                @foreach($p->questionGroups->flatMap(fn($g) => $g->questions) as $q)
                                    @php $revNum = 1 + $passages->flatMap(fn($pp) => $pp->questionGroups->flatMap(fn($g) => $g->questions))->filter(fn($qq) => $qq->id < $q->id)->count(); @endphp
                                    <button @click="jumpToQuestion({{ $q->id }}, {{ $p->passage_number }}); showReview = false;" 
                                            class="aspect-square rounded-2xl border-2 flex flex-col items-center justify-center transition-all relative"
                                            :class="answers[{{ $q->id }}] ? 'bg-primary border-primary text-white' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400 hover:border-primary/30'">
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
                    Back to Questions
                </button>

                <form id="final-submit-form" :action="submitUrl" method="POST" class="flex items-center gap-4">
                    @csrf
                    <!-- Hidden inputs for data -->
                    <template x-for="(val, qId) in answers" :key="qId">
                        <input type="hidden" :name="'answers['+qId+']'" :value="val">
                    </template>
                    <template x-for="(val, qId) in flags" :key="qId">
                        <input x-if="val" type="hidden" :name="'flagged['+qId+']'" value="1">
                    </template>
                    
                    <button type="button" @click="confirmSubmit()" class="px-10 py-4 bg-primary text-white rounded-[24px] text-sm font-black uppercase tracking-[0.1em] shadow-xl shadow-primary/20 hover:-translate-y-1 transition-all active:scale-95">
                        Submit Test Final
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function readingTimer(initialSeconds) {
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

    function readingApp(passagesData, autosaveUrl, submitUrl) {
        return {
            currentPassage: 1,
            activeQuestion: null,
            answers: @json($savedAnswers),
            flags: @json($flaggedAnswers),
            showReview: false,
            autosaveUrl: autosaveUrl,
            submitUrl: submitUrl,
            
            init() {
                this.answers = this.answers || {};
                this.flags = this.flags || {};
                
                // Initialize default current passage based on first q?
                if (Object.keys(this.answers).length > 0) {
                    // find first q id in answers, then its passage
                }

                // Periodic autosave
                setInterval(() => this.autosave(), 20000);
            },
            
            toggleFlag(qId) {
                this.flags[qId] = !this.flags[qId];
                this.autosave();
            },
            
            jumpToQuestion(qId, passageNum) {
                this.currentPassage = passageNum;
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
                if (confirm("Final Submission: Your reading test will be closed for grading. Are you sure?")) {
                    document.getElementById('final-submit-form').submit();
                }
            }
        }
    }
</script>
@endpush
