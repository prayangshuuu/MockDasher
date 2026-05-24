@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'Test Result — Attempt #' . $attempt->id)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90 opacity-50" alt=">" />
    <a href="{{ route('user.history.index') }}" class="hover:text-primary transition-colors">History</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90 opacity-50" alt=">" />
    <span class="font-semibold text-slate-900 dark:text-white">Attempt #{{ $attempt->id }}</span>
</nav>
@endsection

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- Score Hero --}}
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden p-6 md:p-8 hover:shadow-premium transition-all duration-200">
        <div class="flex flex-col items-center gap-8 md:flex-row">
            {{-- Score Circle --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="relative flex size-36 items-center justify-center rounded-full border-[6px] border-primary/20 dark:border-primary/10">
                    {{-- Active progress border ring --}}
                    <div class="absolute inset-0 rounded-full border-[6px] border-primary" style="clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%)"></div>
                    <div class="text-center">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Overall Band</p>
                        <p class="text-5xl font-black text-slate-900 dark:text-white mt-0.5 tabular-nums">
                            {{ $attempt->overall_band !== null ? number_format($attempt->overall_band, 1) : 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    @if($attempt->status === 'completed')
                        <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1 rounded-full border border-emerald-100 dark:border-emerald-800/40">
                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                            Completed
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-sky-500 bg-sky-50 dark:bg-sky-950/30 px-3 py-1 rounded-full border border-sky-100 dark:border-sky-800/40">
                            <span class="size-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                            {{ ucfirst($attempt->status) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Test Info --}}
            <div class="flex-1 text-center md:text-left">
                <div class="mb-3 flex items-center justify-center md:justify-start gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-primary bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-full border border-indigo-100 dark:border-indigo-800">
                        <img src="/storage/asset/icons/verified.svg" class="w-3 h-3" alt="✓" />
                        {{ $attempt->testSet->test->exam_type ?? 'Academic' }}
                    </span>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700/50">
                        Set {{ $attempt->testSet->set_number ?? '1' }}
                    </span>
                </div>

                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    IELTS {{ $attempt->testSet->test->exam_type ?? 'Academic' }} — Vol. {{ $attempt->testSet->test->book_number ?? '' }}
                </h2>
                <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-xl">
                    Performance summary and module-specific band scores generated in accordance with official Cambridge IELTS assessment rules.
                </p>

                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 border-t border-slate-200 dark:border-slate-800 pt-5 text-left">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Date &amp; Time Started</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ ($attempt->started_at ?? $attempt->created_at)->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Time Spent</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $attempt->time_spent ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Candidate Code</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-0.5">MD-{{ str_pad($attempt->user_id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Module Breakdown Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

        {{-- LISTENING CARD --}}
        <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden p-6 hover:shadow-premium hover:border-indigo-500/20 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50">
                        <img src="/storage/asset/icons/headphone.svg" class="w-5 h-5 filter-indigo-600 dark:invert" alt="Listening" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white">Listening</h4>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Comprehension</p>
                    </div>
                </div>
                <span class="text-2xl font-black text-indigo-500 dark:text-indigo-400 tabular-nums">
                    {{ $attempt->listening_band !== null ? number_format($attempt->listening_band, 1) : 'N/A' }}
                </span>
            </div>
            
            {{-- Progress bar --}}
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800/80">
                <div class="h-full rounded-full bg-indigo-500 transition-all duration-700" style="width: {{ ($attempt->listening_band ?? 0) * 11.11 }}%"></div>
            </div>
            
            <div class="mt-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span>Correct: {{ $attempt->listening_score !== null ? $attempt->listening_score . '/40' : 'N/A' }}</span>
                <span>Band {{ $attempt->listening_band !== null ? number_format($attempt->listening_band, 1) : '—' }}</span>
            </div>
        </div>

        {{-- READING CARD --}}
        <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden p-6 hover:shadow-premium hover:border-sky-500/20 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-sky-50 dark:bg-sky-950/40 text-sky-500 border border-sky-100 dark:border-sky-900/50">
                        <img src="/storage/asset/icons/library.svg" class="w-5 h-5 filter-sky-600 dark:invert" alt="Reading" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white">Reading</h4>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Analysis</p>
                    </div>
                </div>
                <span class="text-2xl font-black text-sky-500 dark:text-sky-400 tabular-nums">
                    {{ $attempt->reading_band !== null ? number_format($attempt->reading_band, 1) : 'N/A' }}
                </span>
            </div>
            
            {{-- Progress bar --}}
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800/80">
                <div class="h-full rounded-full bg-sky-500 transition-all duration-700" style="width: {{ ($attempt->reading_band ?? 0) * 11.11 }}%"></div>
            </div>
            
            <div class="mt-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span>Correct: {{ $attempt->reading_score !== null ? $attempt->reading_score . '/40' : 'N/A' }}</span>
                <span>Band {{ $attempt->reading_band !== null ? number_format($attempt->reading_band, 1) : '—' }}</span>
            </div>
        </div>

        {{-- WRITING CARD --}}
        <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden p-6 hover:shadow-premium hover:border-violet-500/20 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-950/40 text-violet-500 border border-violet-100 dark:border-violet-900/50">
                        <img src="/storage/asset/icons/edit.svg" class="w-5 h-5 filter-violet-600 dark:invert" alt="Writing" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white">Writing</h4>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">AI Task 1 & 2 Evaluation</p>
                    </div>
                </div>
                <span class="text-2xl font-black text-violet-500 dark:text-violet-400 tabular-nums">
                    @if($attempt->aiWritingEvaluation) 
                        {{ number_format($attempt->aiWritingEvaluation->band_score, 1) }} 
                    @else 
                        <span class="text-xs font-bold text-amber-500 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full border border-amber-100 dark:border-amber-900/40">Pending</span>
                    @endif
                </span>
            </div>
            
            {{-- Progress bar --}}
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800/80">
                <div class="h-full rounded-full bg-violet-500 transition-all duration-700" style="width: {{ ($attempt->aiWritingEvaluation->band_score ?? 0) * 11.11 }}%"></div>
            </div>
            
            <div class="mt-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span>Status: @if($attempt->aiWritingEvaluation) Scored @else Graded offline or pending @endif</span>
                <span>Band @if($attempt->aiWritingEvaluation) {{ number_format($attempt->aiWritingEvaluation->band_score, 1) }} @else — @endif</span>
            </div>
        </div>

        {{-- SPEAKING CARD --}}
        <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden p-6 hover:shadow-premium hover:border-emerald-500/20 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 border border-emerald-100 dark:border-emerald-900/50">
                        <img src="/storage/asset/icons/microphone.svg" class="w-5 h-5 filter-emerald-600 dark:invert" alt="Speaking" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white">Speaking</h4>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">AI Verbal Assessment</p>
                    </div>
                </div>
                <span class="text-2xl font-black text-emerald-500 dark:text-emerald-400 tabular-nums">
                    @if($attempt->aiSpeakingEvaluation) 
                        {{ number_format($attempt->aiSpeakingEvaluation->band_score, 1) }} 
                    @else 
                        <span class="text-xs font-bold text-amber-500 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full border border-amber-100 dark:border-amber-900/40">Pending</span>
                    @endif
                </span>
            </div>
            
            {{-- Progress bar --}}
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800/80">
                <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ ($attempt->aiSpeakingEvaluation->band_score ?? 0) * 11.11 }}%"></div>
            </div>
            
            <div class="mt-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span>Status: @if($attempt->aiSpeakingEvaluation) Scored @else Interview pending @endif</span>
                <span>Band @if($attempt->aiSpeakingEvaluation) {{ number_format($attempt->aiSpeakingEvaluation->band_score, 1) }} @else — @endif</span>
            </div>
        </div>
    </div>

    {{-- AI Examiner Detailed Feedback & Reports --}}
    @if($attempt->writingAnswers->count() > 0 || $attempt->aiSpeakingEvaluation)
    <div class="space-y-8 pt-2">
        
        {{-- WRITING MODULE REPORTS --}}
        @if($attempt->writingAnswers && $attempt->writingAnswers->count() > 0)
        <div class="space-y-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <img src="/storage/asset/icons/edit.svg" class="w-5 h-5 filter-indigo-600 dark:invert shrink-0" alt="Writing" />
                Writing Tasks &amp; AI Examiner Reports
            </h3>
            
            @foreach($attempt->writingAnswers->sortBy('writingTask.task_number') as $wa)
                @php
                    $task = $wa->writingTask;
                    $eval = $wa->evaluation_json ? json_decode($wa->evaluation_json, true) : null;
                @endphp
                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center gap-3">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Task {{ $task->task_number ?? '1' }} Answer &amp; Report</h4>
                        <span class="ml-auto text-base font-black text-violet-500 bg-violet-50 dark:bg-violet-950/40 px-2.5 py-1 rounded-lg border border-violet-100 dark:border-violet-900/50">
                            Band {{ $wa->band_score !== null ? number_format($wa->band_score, 1) : 'N/A' }}
                        </span>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        {{-- Candidate Essay --}}
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Candidate Response</p>
                            <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 text-sm text-slate-700 dark:text-slate-350 leading-relaxed whitespace-pre-line shadow-inner">
                                {{ $wa->answer_text }}
                            </div>
                            <div class="mt-2 text-right text-xs text-slate-450 dark:text-slate-500 font-semibold">
                                Word Count: {{ str_word_count(strip_tags((string) $wa->answer_text)) }} words
                            </div>
                        </div>

                        {{-- AI Examiner evaluation details --}}
                        @if($eval)
                            <div class="border-t border-slate-100 dark:border-slate-800/80 pt-6">
                                @include('user.writing-test._evaluation', ['eval' => $eval, 'taskNumber' => $task->task_number ?? 1, 'bandScore' => $wa->band_score])
                            </div>
                        @else
                            <div class="border-t border-slate-100 dark:border-slate-800/80 pt-6 flex items-start gap-2.5 text-xs text-amber-600 bg-amber-50 dark:bg-amber-950/20 p-4 rounded-xl border border-amber-100 dark:border-amber-900/40 font-semibold">
                                <img src="/storage/asset/icons/info.svg" class="w-4 h-4 shrink-0 opacity-80" alt="!" />
                                <span>No detailed AI grade report is compiled for this task. Ensure your Google Gemini API key is configured in Settings.</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- SPEAKING MODULE REPORTS --}}
        @if($attempt->aiSpeakingEvaluation && $attempt->aiSpeakingEvaluation->evaluation_json)
        @php
            $speakingEvals = json_decode($attempt->aiSpeakingEvaluation->evaluation_json, true) ?: [];
            $speakingAnswers = $attempt->speakingAnswers->keyBy('speaking_question_id');
        @endphp
        
        <div class="space-y-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <img src="/storage/asset/icons/microphone.svg" class="w-5 h-5 filter-emerald-600 dark:invert shrink-0" alt="Speaking" />
                Speaking Interview &amp; AI Examiner Reports
            </h3>
            
            <div class="space-y-6">
                @foreach($speakingEvals as $index => $se)
                    @php
                        $ans = $speakingAnswers->get($se['question_id']);
                    @endphp
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center gap-3">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Part {{ $se['part'] ?? '1' }} — Question #{{ $index + 1 }}</h4>
                            <span class="ml-auto text-base font-black text-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-100 dark:border-emerald-900/50">
                                Band {{ $se['band_score'] !== null ? number_format($se['band_score'], 1) : 'N/A' }}
                            </span>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            {{-- Interview Q&A --}}
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <span class="shrink-0 text-[10px] font-black uppercase text-indigo-500 mt-1 w-12">Prompt:</span>
                                    <p class="text-sm text-slate-900 dark:text-white font-extrabold">{{ $se['question'] }}</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="shrink-0 text-[10px] font-black uppercase text-emerald-500 mt-1 w-12">Speech:</span>
                                    <div class="flex-1">
                                        <p class="text-sm text-slate-700 dark:text-slate-350 leading-relaxed font-medium italic">
                                            "{{ $ans->transcript_text ?? 'No speech response saved.' }}"
                                        </p>
                                        @if($ans && $ans->audio_path)
                                            <div class="mt-3.5 flex items-center gap-2">
                                                <audio controls class="h-8 max-w-xs scale-90 -ml-8" src="{{ Storage::url($ans->audio_path) }}"></audio>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Detailed Speaking Question evaluation --}}
                            @if(!empty($se['evaluation']))
                                <div class="border-t border-slate-100 dark:border-slate-800/80 pt-6">
                                    @include('user.speaking-test._evaluation', ['eval' => $se['evaluation'], 'bandScore' => $se['band_score'], 'part' => $se['part']])
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
    @else
        {{-- Premium settings callout fallback if no AI feedback compiled --}}
        <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-200 dark:border-amber-800/60 p-6 flex items-start gap-4 shadow-soft">
            <img src="/storage/asset/icons/info.svg" class="w-6 h-6 text-amber-600 dark:text-amber-500 shrink-0 mt-0.5" alt="Info" />
            <div>
                <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300">Detailed AI Examiner Report Pending</h4>
                <p class="text-xs text-amber-700 dark:text-amber-400/80 leading-relaxed mt-1 font-medium">
                    To generate IELTS criterion breakdown scorecards, vocabularyRecommendations, and suggestions, add your Google <strong>Gemini API Key</strong> under Settings. Future attempts will sequentially compile AI assessments on final submission!
                </p>
                <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-1.5 mt-4 text-xs font-extrabold text-amber-700 dark:text-amber-400 hover:underline">
                    Configure Gemini API Key in Settings →
                </a>
            </div>
        </div>
    @endif

    {{-- Bottom CTA Buttons --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end pt-4">
        <a href="{{ route('user.history.index') }}" 
           class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-5 py-2.5 rounded-xl text-sm font-bold shadow-soft transition-all duration-200 border border-slate-200 dark:border-slate-700/30">
            <img src="/storage/asset/icons/history.svg" class="w-4 h-4 dark:invert opacity-75" alt="History" />
            View All Results
        </a>
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
            <img src="/storage/asset/icons/overview.svg" class="w-4 h-4 invert brightness-0" alt="Dashboard" />
            Back to Dashboard
        </a>
    </div>

</div>

@endsection
