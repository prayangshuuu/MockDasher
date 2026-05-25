@extends('layouts.exam')

@section('title', 'Listening Results - ' . $test->book_number)
@section('test_type', 'IELTS Listening')
@section('test_title', 'IELTS ' . $test->book_number)

@section('timer_area')
<div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 px-4 py-1.5 rounded-full shadow-soft">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 fill-current" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
    </svg>
    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Completed</span>
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
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Score Summary Banner --}}
    <div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 px-8 py-8 shrink-0 shadow-md">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center gap-10">
            {{-- Band Score --}}
            <div class="flex items-center gap-6">
                <div class="size-24 rounded-[2rem] bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center shadow-inner">
                    <span class="text-5xl font-black text-white tracking-tight">{{ number_format($attempt->band_score, 1) }}</span>
                </div>
                <div>
                    <p class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em] mb-1 leading-none">Band Score</p>
                    <p class="text-white text-lg font-extrabold mt-0.5">
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
                <p class="text-white text-3xl font-black mt-0.5">{{ $attempt->total_correct }} <span class="text-lg text-white/40 font-bold">/ {{ $totalQuestions }}</span></p>
            </div>

            <div class="h-16 w-px bg-white/20 hidden md:block"></div>

            {{-- Accuracy --}}
            <div>
                <p class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Accuracy</p>
                <p class="text-white text-3xl font-black mt-0.5">{{ $totalQuestions > 0 ? round(($attempt->total_correct / $totalQuestions) * 100) : 0 }}%</p>
            </div>
        </div>
    </div>

    {{-- Detailed Answer Review --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50 dark:bg-slate-900/40 p-6 sm:p-10">
        <div class="max-w-4xl mx-auto space-y-10">
            @php $globalQ = 0; @endphp
            @foreach($sections as $section)
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50">Section {{ $section->section_number }}</span>
                        <span class="text-slate-700 dark:text-slate-350 font-extrabold">{{ $section->title }}</span>
                        <div class="flex-1 h-px bg-slate-200 dark:bg-slate-800"></div>
                    </h3>

                    <div class="space-y-4">
                        @foreach($section->questions as $question)
                            @php
                                $globalQ++;
                                $userAnswer = $answers->get($question->id);
                                $userText = $userAnswer ? trim($userAnswer->answer_text ?? '') : '';
                                $correctText = trim($question->correct_answer ?? '');
                                $validAnswers = array_map(fn($a) => strtolower(trim($a)), explode('|', $correctText));
                                $isCorrect = in_array(strtolower($userText), $validAnswers);
                                $isUnanswered = $userText === '';
                            @endphp

                            <div class="flex items-start gap-4 p-5 rounded-2xl border shadow-soft transition-all
                                {{ $isCorrect ? 'bg-emerald-50/50 dark:bg-emerald-950/15 border-emerald-200 dark:border-emerald-800/40' : ($isUnanswered ? 'bg-surface-light dark:bg-surface-dark border-slate-200 dark:border-slate-800' : 'bg-rose-50/50 dark:bg-rose-950/15 border-rose-200 dark:border-rose-800/40') }}">

                                {{-- Number Badge --}}
                                <div class="size-10 rounded-xl flex items-center justify-center shrink-0 text-xs font-black shadow-sm
                                    {{ $isCorrect ? 'bg-emerald-500 text-white' : ($isUnanswered ? 'bg-slate-300 dark:bg-slate-700 text-white' : 'bg-rose-500 text-white') }}">
                                    {{ $globalQ }}
                                </div>

                                {{-- Question & Answer --}}
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <p class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200 leading-relaxed mb-2.5">
                                        {{ $question->question_text }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs">
                                        @if(!$isUnanswered)
                                            <span class="font-bold {{ $isCorrect ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                Your answer: <span class="underline underline-offset-2 decoration-2">{{ $userText }}</span>
                                            </span>
                                        @else
                                            <span class="font-semibold text-slate-400 dark:text-slate-500 italic">Not answered</span>
                                        @endif

                                        @if(!$isCorrect)
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                                Correct: <span class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-800 px-2 py-0.5 rounded font-black">{{ $correctText }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Status Icon --}}
                                <div class="shrink-0 pt-0.5">
                                    @if($isCorrect)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-500 fill-current" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                    @elseif($isUnanswered)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-400 dark:text-slate-600 fill-current" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H7v-2h10v2z"/>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-rose-500 fill-current" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
