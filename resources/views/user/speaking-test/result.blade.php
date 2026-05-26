@extends('layouts.exam')

@section('title', 'Speaking Results - ' . optional($attempt->testSet->test)->book_number)
@section('test_type', 'IELTS Speaking')
@section('test_title', 'IELTS ' . optional($attempt->testSet->test)->book_number)

@section('timer_area')
<div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 px-4 py-1.5 rounded-full shadow-soft">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 fill-current" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
    </svg>
    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Completed</span>
</div>
@endsection

@section('top_right_actions')
<div class="flex items-center gap-3">
    <a href="{{ route('user.tests.start', $attempt->testSet->test_id) }}" 
       class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-soft transition-all duration-150 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Back to Test
    </a>
    <a href="{{ route('dashboard') }}" 
       class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 px-4 py-2 rounded-xl text-xs font-bold shadow-soft transition-all duration-150 focus:outline-none">
        <img src="/storage/asset/icons/overview.svg" class="w-4 h-4 invert dark:invert-0" alt="Dashboard" />
        Dashboard
    </a>
</div>
@endsection

@section('content')
<div class="flex-1 flex flex-col overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/40 p-6 sm:p-10">
    <div class="max-w-4xl mx-auto space-y-10 w-full">
        
        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <img src="/storage/asset/icons/ai.svg" class="w-6 h-6" alt="AI" />
                Speaking Evaluation Status
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
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Your speaking tasks have been evaluated successfully.</p>
                    </div>
                </div>
            @endif
        </div>

        @if($evaluation && $evaluation->evaluation_status === 'completed')
            @php
                $evalData = json_decode($evaluation->evaluation_json, true) ?: [];
            @endphp
            <div class="space-y-8">
                @foreach($evalData as $index => $se)
                    @php
                        $ans = $existingAnswers->get($se['question_id']);
                    @endphp
                    <div class="bg-white dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-soft">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                            <h5 class="text-sm font-bold text-slate-700 dark:text-slate-200">Part {{ $se['part'] ?? '1' }} — Question #{{ $index + 1 }}</h5>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white dark:bg-surface-dark px-2.5 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">Band {{ $se['band_score'] !== null ? number_format($se['band_score'], 1) : 'N/A' }}</span>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="shrink-0 text-[10px] font-black uppercase text-indigo-500 mt-1 w-12">Prompt:</span>
                                <p class="text-sm text-slate-900 dark:text-white font-extrabold">{{ $se['question'] }}</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="shrink-0 text-[10px] font-black uppercase text-emerald-500 mt-1 w-12">Speech:</span>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-750 dark:text-slate-350 leading-relaxed font-medium italic">
                                        "{{ $ans->transcript_text ?? 'No speech response saved.' }}"
                                    </p>
                                    @if($ans && $ans->audio_path)
                                        <div class="mt-3 flex items-center gap-2">
                                            <audio controls class="h-8 max-w-xs scale-90 -ml-8" src="{{ Storage::url($ans->audio_path) }}"></audio>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(!empty($se['evaluation']))
                            <div class="border-t border-slate-200 dark:border-slate-800 p-6 bg-slate-50/30 dark:bg-slate-900/10">
                                @include('user.speaking-test._evaluation', ['eval' => $se['evaluation'], 'bandScore' => $se['band_score'], 'part' => $se['part'] ?? 1])
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
