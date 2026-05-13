@extends('layouts.exam')

@section('title', 'Reading Results - ' . $test->book_number)
@section('test_type', 'IELTS Reading')
@section('test_title', 'IELTS ' . $test->book_number)

@section('timer_area')
<div class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 border-emerald-200 dark:border-emerald-800 px-4 py-2 rounded-2xl shadow-sm">
    <span class="material-symbols-outlined text-xl text-emerald-500">verified</span>
    <span class="text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Completed</span>
</div>
@endsection

@section('top_right_actions')
<a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg">
    <span class="material-symbols-outlined text-sm">home</span>
    Dashboard
</a>
@endsection

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Score Summary Banner --}}
    <div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 px-8 py-8 shrink-0">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center gap-10">
            {{-- Band Score --}}
            <div class="flex items-center gap-6">
                <div class="size-24 rounded-[2rem] bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                    <span class="text-5xl font-black text-white tracking-tight">{{ number_format($attempt->band_score, 1) }}</span>
                </div>
                <div>
                    <p class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Band Score</p>
                    <p class="text-white text-lg font-bold">
                        @if($attempt->band_score >= 8.0) Expert User
                        @elseif($attempt->band_score >= 7.0) Good User
                        @elseif($attempt->band_score >= 6.0) Competent User
                        @elseif($attempt->band_score >= 5.0) Modest User
                        @else Limited User
                        @endif
                    </p>
                </div>
            </div>

            <div class="h-16 w-px bg-white/20 hidden md:block"></div>

            {{-- Raw Score --}}
            <div>
                <p class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Correct Answers</p>
                <p class="text-white text-3xl font-black">{{ $attempt->total_correct }} <span class="text-lg text-white/40">/ {{ $totalQuestions }}</span></p>
            </div>

            <div class="h-16 w-px bg-white/20 hidden md:block"></div>

            {{-- Accuracy --}}
            <div>
                <p class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Accuracy</p>
                <p class="text-white text-3xl font-black">{{ $totalQuestions > 0 ? round(($attempt->total_correct / $totalQuestions) * 100) : 0 }}%</p>
            </div>
        </div>
    </div>

    {{-- Detailed Answer Review --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-950 p-8 md:p-12">
        <div class="max-w-5xl mx-auto space-y-12">
            @php $globalQ = 0; @endphp
            @foreach($passages as $passage)
                <div>
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                        <span class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/50">Passage {{ $passage->passage_number }}</span>
                        <span>{{ $passage->title }}</span>
                        <div class="flex-1 h-px bg-slate-200 dark:bg-slate-800"></div>
                    </h3>

                    <div class="space-y-4">
                        @foreach($passage->questionGroups as $group)
                            @foreach($group->questions as $question)
                                @php
                                    $globalQ++;
                                    $userAnswer = $answers->get($question->id);
                                    $userText = $userAnswer ? trim($userAnswer->answer_text ?? '') : '';
                                    $correctText = trim($question->correct_answer ?? '');
                                    $validAnswers = array_map(fn($a) => strtolower(trim($a)), explode('|', $correctText));
                                    $isCorrect = in_array(strtolower($userText), $validAnswers);
                                    $isUnanswered = $userText === '';
                                @endphp

                                <div class="flex items-start gap-4 p-5 rounded-2xl border transition-all
                                    {{ $isCorrect ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-800/50' : ($isUnanswered ? 'bg-slate-100 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700' : 'bg-rose-50 dark:bg-rose-900/10 border-rose-200 dark:border-rose-800/50') }}">

                                    {{-- Number Badge --}}
                                    <div class="size-10 rounded-xl flex items-center justify-center shrink-0 text-xs font-black
                                        {{ $isCorrect ? 'bg-emerald-500 text-white' : ($isUnanswered ? 'bg-slate-300 dark:bg-slate-600 text-white' : 'bg-rose-500 text-white') }}">
                                        {{ $globalQ }}
                                    </div>

                                    {{-- Question & Answer --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200 leading-relaxed mb-2">
                                            {{ $question->question_text }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-4 text-xs">
                                            @if(!$isUnanswered)
                                                <span class="font-bold {{ $isCorrect ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                    Your answer: {{ $userText }}
                                                </span>
                                            @else
                                                <span class="font-bold text-slate-400 italic">Not answered</span>
                                            @endif

                                            @if(!$isCorrect)
                                                <span class="font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm">check</span>
                                                    Correct: {{ $correctText }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Status Icon --}}
                                    <div class="shrink-0">
                                        @if($isCorrect)
                                            <span class="material-symbols-outlined text-emerald-500 text-2xl">check_circle</span>
                                        @elseif($isUnanswered)
                                            <span class="material-symbols-outlined text-slate-400 text-2xl">remove_circle_outline</span>
                                        @else
                                            <span class="material-symbols-outlined text-rose-500 text-2xl">cancel</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
