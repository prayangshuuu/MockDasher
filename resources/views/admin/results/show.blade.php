@extends('layouts.admin')

@section('title', 'Attempt Details - ' . optional($result->user)->name)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.results.index') }}" class="hover:text-primary transition-colors">Results</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
    <span class="text-slate-900 dark:text-white font-medium">Attempt Details</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start gap-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Attempt Analysis</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">In-depth breakdown of the candidate's performance.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold uppercase tracking-widest text-slate-500">ID: #{{ $result->id }}</span>
            <button class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-primary-hover transition-colors px-4 py-2 rounded-lg border border-primary/20 hover:bg-primary/5">
                <img src="/storage/asset/icons/pdf.svg" class="w-4 h-4" alt="PDF" />
                Export PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-5">
            <div class="size-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                <img src="/storage/asset/icons/name.svg" class="w-6 h-6" alt="User" />
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Candidate</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white truncate">{{ optional($result->user)->name ?? 'Unknown' }}</p>
            </div>
        </div>

        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-5">
            <div class="size-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                <img src="/storage/asset/icons/info.svg" class="w-6 h-6" alt="Info" />
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Test Info</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white truncate">{{ optional($result->test)->title ?? 'IELTS Mock' }}</p>
            </div>
        </div>

        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-5">
            <div class="size-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                <img src="/storage/asset/icons/verified.svg" class="w-6 h-6" alt="Verified" />
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Overall Outcome</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $result->overall_band ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl shadow-soft overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Module Performance Breakdown</h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <x-admin.stat-card label="Reading" :value="$result->reading_band ?? 'N/A'" icon="/storage/asset/icons/menu.svg" />
                <x-admin.stat-card label="Listening" :value="$result->listening_band ?? 'N/A'" icon="/storage/asset/icons/headphone.svg" />
                <x-admin.stat-card label="Writing" :value="$result->writing_band ?? 'N/A'" icon="/storage/asset/icons/edit.svg" />
                <x-admin.stat-card label="Speaking" :value="$result->speaking_band ?? 'N/A'" icon="/storage/asset/icons/microphone.svg" />
            </div>

            @if($result->writingAnswers && $result->writingAnswers->count() > 0)
                <div class="pt-6 border-t border-slate-200 dark:border-slate-800 space-y-6">
                    <h4 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-3">
                        <img src="/storage/asset/icons/history.svg" class="w-5 h-5" alt="History" />
                        Detailed Writing Responses
                    </h4>

                    @php
                        $evaluation = null;
                        if ($result->aiWritingEvaluation && $result->aiWritingEvaluation->evaluation_text) {
                            $evaluation = json_decode($result->aiWritingEvaluation->evaluation_text, true);
                        }
                    @endphp

                    <div class="space-y-6">
                        @foreach($result->writingAnswers as $index => $answer)
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <div class="size-7 rounded-md bg-primary text-white flex items-center justify-center font-bold text-xs">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                            {{ optional($answer->writingTask)->task_title ?? 'Writing Task' }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white dark:bg-surface-dark px-2.5 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">{{ str_word_count($answer->answer_text ?? '') }} Words</span>
                                </div>
                                <div class="p-6 text-sm text-slate-600 dark:text-slate-400 leading-relaxed max-h-80 overflow-y-auto whitespace-pre-wrap font-medium">
                                    {{ $answer->answer_text ?? 'No response submitted.' }}
                                </div>

                                @php
                                    $taskNumber = optional($answer->writingTask)->task_number ?? ($index + 1);
                                    $taskFeedback = $evaluation ? ($evaluation["task_{$taskNumber}"] ?? null) : null;
                                @endphp

                                @if($taskFeedback && isset($taskFeedback['overall_band_score']) && $taskFeedback['overall_band_score'] > 0)
                                    <div class="border-t border-slate-200 dark:border-slate-800 p-6 bg-slate-50/30 dark:bg-slate-900/10 space-y-6">
                                        {{-- Header with Score --}}
                                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                            <div>
                                                <h5 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-violet-600 dark:text-violet-400 text-lg">auto_awesome</span>
                                                    AI Examiner Report
                                                </h5>
                                                <p class="text-xs text-slate-500 mt-0.5">Evaluated strictly according to official IELTS band descriptors.</p>
                                            </div>
                                            <div class="px-4 py-2 rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-800 font-extrabold text-sm flex items-center gap-1.5 shadow-sm shrink-0">
                                                <span>Band Score</span>
                                                <span class="text-base text-violet-800 dark:text-violet-200">{{ number_format($taskFeedback['overall_band_score'] ?? 0, 1) }}</span>
                                            </div>
                                        </div>

                                        {{-- Criteria Grid --}}
                                        @if(isset($taskFeedback['criteria_scores']))
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                @foreach($taskFeedback['criteria_scores'] as $criterion => $score)
                                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider truncate" title="{{ ucwords(str_replace('_', ' ', $criterion)) }}">
                                                            {{ ucwords(str_replace('_', ' ', $criterion)) }}
                                                        </p>
                                                        <p class="text-base font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($score, 1) }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Detailed Feedback --}}
                                        @if(isset($taskFeedback['detailed_feedback']))
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Detailed Analysis</p>
                                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium whitespace-pre-line bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-inner">
                                                    {{ $taskFeedback['detailed_feedback'] }}
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Suggestions for Improvement --}}
                                        @if(isset($taskFeedback['suggestions_for_improvement']))
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Suggestions for Improvement</p>
                                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium whitespace-pre-line bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-inner">
                                                    {{ $taskFeedback['suggestions_for_improvement'] }}
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Corrections (Grammar/Vocabulary) --}}
                                        @if((isset($taskFeedback['grammar_corrections']) && count($taskFeedback['grammar_corrections']) > 0) || (isset($taskFeedback['vocabulary_corrections']) && count($taskFeedback['vocabulary_corrections']) > 0))
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                {{-- Grammar --}}
                                                @if(isset($taskFeedback['grammar_corrections']) && count($taskFeedback['grammar_corrections']) > 0)
                                                    <div class="space-y-3">
                                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Grammar Corrections</p>
                                                        <div class="space-y-2.5">
                                                            @foreach($taskFeedback['grammar_corrections'] as $correction)
                                                                <div class="p-3 rounded-xl bg-red-50/50 dark:bg-red-950/10 border border-red-100 dark:border-red-900/30 text-xs font-medium space-y-1 shadow-sm">
                                                                    <div class="flex items-start gap-2">
                                                                        <span class="text-red-500 font-bold shrink-0">Incorrect:</span>
                                                                        <span class="text-slate-600 dark:text-slate-400 italic">"{{ $correction['incorrect'] }}"</span>
                                                                    </div>
                                                                    <div class="flex items-start gap-2 border-t border-red-100/50 dark:border-red-900/20 pt-1 mt-1">
                                                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold shrink-0">Suggested:</span>
                                                                        <span class="text-slate-800 dark:text-slate-200 font-bold">"{{ $correction['suggested'] }}"</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Vocabulary --}}
                                                @if(isset($taskFeedback['vocabulary_corrections']) && count($taskFeedback['vocabulary_corrections']) > 0)
                                                    <div class="space-y-3">
                                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Vocabulary Corrections</p>
                                                        <div class="space-y-2.5">
                                                            @foreach($taskFeedback['vocabulary_corrections'] as $correction)
                                                                <div class="p-3 rounded-xl bg-amber-50/50 dark:bg-amber-950/10 border border-amber-100 dark:border-amber-900/30 text-xs font-medium space-y-1 shadow-sm">
                                                                    <div class="flex items-start gap-2">
                                                                        <span class="text-amber-600 dark:text-amber-500 font-bold shrink-0">Incorrect:</span>
                                                                        <span class="text-slate-600 dark:text-slate-400 italic">"{{ $correction['incorrect'] }}"</span>
                                                                    </div>
                                                                    <div class="flex items-start gap-2 border-t border-amber-100/50 dark:border-amber-900/20 pt-1 mt-1">
                                                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold shrink-0">Suggested:</span>
                                                                        <span class="text-slate-800 dark:text-slate-200 font-bold">"{{ $correction['suggested'] }}"</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="px-6 py-4 bg-violet-50/40 dark:bg-violet-950/5 border-t border-violet-100 dark:border-violet-900/30 flex items-center justify-between">
                                        <span class="text-xs font-bold text-violet-600 dark:text-violet-400 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm animate-pulse">pending</span>
                                            AI Feedback: Analysis Pending
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Expected shortly</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any page-specific scripts here
</script>
@endpush
