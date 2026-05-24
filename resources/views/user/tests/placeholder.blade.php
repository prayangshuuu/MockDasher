@extends('layouts.student')

@section('title', 'Start Test — IELTS ' . $test->book_number)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90 opacity-50" alt=">" />
    <a href="{{ route('user.tests.index') }}" class="hover:text-primary transition-colors">Mock Tests</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90 opacity-50" alt=">" />
    <span class="font-semibold text-slate-900 dark:text-white">IELTS {{ $test->book_number }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="mb-3 flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-primary bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-full border border-indigo-100 dark:border-indigo-800">
                    <img src="/storage/asset/icons/verified.svg" class="w-3 h-3" alt="✓" />
                    {{ $test->exam_type ?? 'Academic' }}
                </span>
                @if($test->year)
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700/50">Series {{ $test->year }}</span>
                @endif
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                IELTS {{ $test->exam_type ?? 'Academic' }} — Vol. {{ $test->book_number ?? '' }}
            </h1>
            <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 mt-2 max-w-2xl leading-relaxed">
                Choose one of the core modules below to start your practice session. Each module simulates actual IELTS exam constraints to provide highly realistic training.
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('user.tests.index') }}" 
               class="inline-flex items-center gap-2 bg-surface-light hover:bg-slate-100 dark:bg-surface-dark dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-sm font-bold shadow-soft border border-slate-200 dark:border-slate-800 transition-all duration-200">
                <img src="/storage/asset/icons/arrowback.svg" class="w-4 h-4 opacity-75 dark:invert" alt="Back" />
                Back to Tests
            </a>
        </div>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        {{-- LISTENING CARD --}}
        <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col hover:shadow-premium hover:border-indigo-500/30 dark:hover:border-indigo-500/30 transition-all duration-200">
            <div class="h-1 bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
            <div class="p-6 flex flex-col flex-1">
                <div class="mb-5 flex size-12 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50 group-hover:scale-105 transition-transform duration-200">
                    <img src="/storage/asset/icons/headphone.svg" class="w-6 h-6 filter-indigo-600 dark:invert" alt="Listening" />
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors">Listening</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">Practice comprehensive listening skills across 4 sections containing conversations and monologues.</p>
                
                <div class="space-y-2 mb-6 mt-auto">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                        <img src="/storage/asset/icons/section.svg" class="w-4 h-4 opacity-60 dark:invert" alt="Questions" />
                        <span>40 Questions</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                        <img src="/storage/asset/icons/history.svg" class="w-4 h-4 opacity-60 dark:invert" alt="Duration" />
                        <span>30 Minutes</span>
                    </div>
                </div>

                @if($listeningStatus === 'completed')
                    <div class="w-full text-center bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-emerald-600 dark:text-emerald-400 py-2.5 rounded-xl text-sm font-bold shadow-soft">
                        Completed (Band {{ number_format($listeningBand ?? 0.0, 1) }})
                    </div>
                @else
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="module" value="listening">
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 {{ $listeningStatus === 'in_progress' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-indigo-500 hover:bg-indigo-600' }} text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
                            <img src="/storage/asset/icons/start.svg" class="w-4 h-4 invert brightness-0" alt="Start" />
                            {{ $listeningStatus === 'in_progress' ? 'Resume Listening' : 'Start Listening' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- READING CARD --}}
        <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col hover:shadow-premium hover:border-sky-500/30 dark:hover:border-sky-500/30 transition-all duration-200">
            <div class="h-1 bg-gradient-to-r from-sky-500 to-sky-600"></div>
            <div class="p-6 flex flex-col flex-1">
                <div class="mb-5 flex size-12 items-center justify-center rounded-xl bg-sky-50 dark:bg-sky-950/40 text-sky-500 border border-sky-100 dark:border-sky-900/50 group-hover:scale-105 transition-transform duration-200">
                    <img src="/storage/asset/icons/library.svg" class="w-6 h-6 filter-sky-600 dark:invert" alt="Reading" />
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-sky-500 dark:group-hover:text-sky-400 transition-colors">Reading</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">Read complex academic articles and complete matching, labeling, and short-answer tasks.</p>
                
                <div class="space-y-2 mb-6 mt-auto">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                        <img src="/storage/asset/icons/instruction.svg" class="w-4 h-4 opacity-60 dark:invert" alt="Passages" />
                        <span>3 Passages</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                        <img src="/storage/asset/icons/history.svg" class="w-4 h-4 opacity-60 dark:invert" alt="Duration" />
                        <span>60 Minutes</span>
                    </div>
                </div>

                @if($readingStatus === 'completed')
                    <div class="flex flex-col gap-2 w-full">
                        <div class="w-full text-center bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-emerald-600 dark:text-emerald-400 py-2.5 rounded-xl text-sm font-bold shadow-soft">
                            Completed (Band {{ number_format($readingBand ?? 0.0, 1) }})
                        </div>
                        @if($testAttempt && $testAttempt->readingAttempt)
                            <a href="{{ route('user.reading.result', $testAttempt->readingAttempt->id) }}" 
                               class="w-full text-center text-xs font-bold text-sky-500 hover:underline">
                                View Reading Results
                            </a>
                        @endif
                    </div>
                @else
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="module" value="reading">
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 {{ $readingStatus === 'in_progress' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-sky-500 hover:bg-sky-600' }} text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
                            <img src="/storage/asset/icons/start.svg" class="w-4 h-4 invert brightness-0" alt="Start" />
                            {{ $readingStatus === 'in_progress' ? 'Resume Reading' : 'Start Reading' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- WRITING CARD --}}
        <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col hover:shadow-premium hover:border-violet-500/30 dark:hover:border-violet-500/30 transition-all duration-200">
            <div class="h-1 bg-gradient-to-r from-violet-500 to-violet-600"></div>
            <div class="p-6 flex flex-col flex-1">
                <div class="mb-5 flex size-12 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-950/40 text-violet-500 border border-violet-100 dark:border-violet-900/50 group-hover:scale-105 transition-transform duration-200">
                    <img src="/storage/asset/icons/edit.svg" class="w-6 h-6 filter-violet-600 dark:invert" alt="Writing" />
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-violet-500 dark:group-hover:text-violet-400 transition-colors">Writing</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">Submit Task 1 and Task 2 essays with professional, instantaneous AI band score feedback.</p>
                
                <div class="space-y-2 mb-6 mt-auto">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                        <img src="/storage/asset/icons/create.svg" class="w-4 h-4 opacity-60 dark:invert" alt="Tasks" />
                        <span>2 Tasks</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                        <img src="/storage/asset/icons/history.svg" class="w-4 h-4 opacity-60 dark:invert" alt="Duration" />
                        <span>60 Minutes</span>
                    </div>
                </div>

                @if($writingStatus === 'completed')
                    <div class="w-full text-center bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-emerald-600 dark:text-emerald-400 py-2.5 rounded-xl text-sm font-bold shadow-soft">
                        Completed (Band {{ number_format($writingBand ?? 0.0, 1) }})
                    </div>
                @else
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="module" value="writing">
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 {{ $writingStatus === 'in_progress' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-violet-500 hover:bg-violet-600' }} text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
                            <img src="/storage/asset/icons/start.svg" class="w-4 h-4 invert brightness-0" alt="Start" />
                            {{ $writingStatus === 'in_progress' ? 'Resume Writing' : 'Start Writing' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- SPEAKING CARD --}}
        <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col hover:shadow-premium hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all duration-200">
            <div class="h-1 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
            <div class="p-6 flex flex-col flex-1">
                <div class="mb-5 flex size-12 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 border border-emerald-100 dark:border-emerald-900/50 group-hover:scale-105 transition-transform duration-200">
                    <img src="/storage/asset/icons/microphone.svg" class="w-6 h-6 filter-emerald-600 dark:invert" alt="Speaking" />
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors">Speaking</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">Engage in interactive Part 1, 2, and 3 voice interviews evaluated directly by Gemini AI.</p>
                
                <div class="space-y-2 mb-6 mt-auto">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                        <img src="/storage/asset/icons/chat.svg" class="w-4 h-4 opacity-60 dark:invert" alt="Parts" />
                        <span>3 Parts</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                        <img src="/storage/asset/icons/history.svg" class="w-4 h-4 opacity-60 dark:invert" alt="Duration" />
                        <span>~15 Minutes</span>
                    </div>
                </div>

                @if($speakingStatus === 'completed')
                    <div class="w-full text-center bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-emerald-600 dark:text-emerald-400 py-2.5 rounded-xl text-sm font-bold shadow-soft">
                        Completed (Band {{ number_format($speakingBand ?? 0.0, 1) }})
                    </div>
                @else
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="module" value="speaking">
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 {{ $speakingStatus === 'in_progress' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }} text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
                            <img src="/storage/asset/icons/start.svg" class="w-4 h-4 invert brightness-0" alt="Start" />
                            {{ $speakingStatus === 'in_progress' ? 'Resume Speaking' : 'Start Speaking' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Finish Exam Section --}}
    @if($testAttempt && !$testAttempt->completed_at)
    <div class="mb-12 bg-white dark:bg-slate-900 border-2 border-indigo-500/20 dark:border-indigo-500/30 rounded-2xl p-6 sm:p-8 shadow-premium flex flex-col md:flex-row md:items-center justify-between gap-6 animate-pulse-subtle">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 border border-indigo-100 dark:border-indigo-900/50 rounded-xl shrink-0">
                <img src="/storage/asset/icons/verified.svg" class="w-6 h-6 filter-indigo-600 dark:invert" alt="Exam sitting" />
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Active Exam Session</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xl">
                    You can take the modules in any order. Once you have finished taking your desired modules, click <strong>"Finish Full Exam"</strong> to lock in your scores. Any modules you have not attempted will receive a band score of <strong>0.0</strong>.
                </p>
            </div>
        </div>
        <form action="{{ route('user.tests.finish', $testAttempt->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to finish the entire exam? This will submit and grade any un-evaluated work, and assign a band score of 0.0 to any unsubmitted modules. This action cannot be undone.');">
            @csrf
            <button type="submit" 
                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3.5 rounded-xl text-sm font-bold shadow-premium transition-all duration-200">
                <img src="/storage/asset/icons/verified.svg" class="w-5 h-5 invert brightness-0" alt="Finish" />
                Finish Full Exam
            </button>
        </form>
    </div>
    @endif

    {{-- Practice Tips Section --}}
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden p-6 md:p-8 flex flex-col md:flex-row items-center gap-6">
        <div class="size-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center text-primary shrink-0 border border-indigo-100 dark:border-indigo-900/50">
            <img src="/storage/asset/icons/info.svg" class="w-6 h-6 filter-indigo-600" alt="Tips" />
        </div>
        <div class="flex-1 text-center md:text-left">
            <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">Important Practice Guidelines</h4>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Ensure that you are in a quiet room with a stable internet connection before starting. We highly recommend wearing headphones during the Listening module for optimal audio clarity. Your progress is saved automatically, allowing you to resume whenever needed.
            </p>
        </div>
    </div>
</div>
@endsection
