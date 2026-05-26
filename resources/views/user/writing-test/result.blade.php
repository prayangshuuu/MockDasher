@extends('layouts.exam')

@section('title', 'Writing Results - ' . optional($attempt->testSet->test)->book_number)
@section('test_type', 'IELTS Writing')
@section('test_title', 'IELTS ' . optional($attempt->testSet->test)->book_number)

@section('timer_area')
<div class="flex items-center gap-2 {{ (!$evaluation || in_array($evaluation->evaluation_status, ['pending', 'evaluating'])) ? 'bg-indigo-50 dark:bg-indigo-950/30 border-indigo-100 dark:border-indigo-900/40' : 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-100 dark:border-emerald-900/40' }} px-4 py-1.5 rounded-full shadow-soft">
    @if(!$evaluation || in_array($evaluation->evaluation_status, ['pending', 'evaluating']))
        <span class="material-symbols-outlined text-[16px] text-indigo-500 animate-spin">sync</span>
        <span class="text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Evaluating</span>
    @else
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 fill-current" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Completed</span>
    @endif
</div>
@endsection

@section('top_right_actions')
<a href="{{ route('dashboard') }}" 
   class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 px-4 py-2 rounded-xl text-xs font-bold shadow-soft transition-all duration-150 focus:outline-none">
    <img src="/storage/asset/icons/overview.svg" class="w-4 h-4 invert dark:invert-0" alt="Dashboard" />
    Dashboard
</a>
@endsection

@section('content')
<div class="flex-1 flex flex-col overflow-hidden custom-scrollbar bg-slate-50 dark:bg-slate-900/40 p-6 sm:p-10">
    <div class="max-w-4xl mx-auto space-y-10 w-full">
        
        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-500">sync</span>
                Writing Evaluation Status
            </h2>
            @if(!$evaluation)
                <p class="text-sm text-slate-500">Evaluation not started.</p>
            @elseif($evaluation->evaluation_status === 'pending' || $evaluation->evaluation_status === 'evaluating')
                <div class="flex items-center gap-3 text-indigo-600 dark:text-indigo-400">
                    <span class="material-symbols-outlined animate-spin">sync</span>
                    <span class="font-bold">AI is currently evaluating your answers. Please refresh this page in a few minutes.</span>
                </div>
            @elseif($evaluation->evaluation_status === 'failed')
                <p class="text-sm text-rose-600 dark:text-rose-400 font-bold">Evaluation failed: {{ $evaluation->failure_reason }}</p>
            @elseif($evaluation->evaluation_status === 'completed')
                <div class="flex items-center gap-4">
                    <div class="size-16 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($evaluation->band_score, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Overall Band Score</p>
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Your writing tasks have been evaluated successfully.</p>
                    </div>
                </div>
            @endif
        </div>

        @if($evaluation && $evaluation->evaluation_status === 'completed')
            @php
                $evalData = json_decode($evaluation->evaluation_text, true);
            @endphp
            <div class="space-y-8">
                @foreach($tasks as $index => $task)
                    <div class="bg-white dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-soft">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                Task {{ $task->task_number }}
                            </h3>
                            @php
                                $ans = $answers->get($task->id);
                                $wordCount = $ans ? $ans->word_count : 0;
                            @endphp
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white dark:bg-surface-dark px-2.5 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                {{ $wordCount }} Words
                            </span>
                        </div>
                        
                        <div class="p-6 text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-wrap font-medium">
                            {{ $ans->answer_text ?? 'No response submitted.' }}
                        </div>

                        @php
                            $taskFeedback = null;
                            if ($ans && $ans->evaluation_json) {
                                $taskFeedback = json_decode($ans->evaluation_json, true);
                            }
                            if (!$taskFeedback && $evalData) {
                                $taskFeedback = $evalData["task_{$task->task_number}"] ?? null;
                            }
                            $bandScore = $ans->band_score ?? ($taskFeedback ? ($taskFeedback['overall_band_score'] ?? $taskFeedback['band_score'] ?? 0) : 0);
                        @endphp

                        @if($taskFeedback && $bandScore > 0)
                            <div class="border-t border-slate-200 dark:border-slate-800 p-6 bg-slate-50/30 dark:bg-slate-900/10">
                                @include('user.writing-test._evaluation', ['eval' => $taskFeedback, 'taskNumber' => $task->task_number, 'bandScore' => $bandScore])
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
