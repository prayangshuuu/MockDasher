<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reading Test – {{ $test->title }} | MockDasher</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .passage-text { font-family: 'Inter', serif; font-size: 14px; line-height: 1.85; }
        .passage-text p    { margin-bottom: 1rem; }
        .passage-text h3   { font-weight: 700; font-size: 1.05rem; margin: 1.25rem 0 0.5rem; }
        .passage-text ul   { list-style: disc; padding-left: 1.5rem; margin-bottom: 1rem; }

        /* Scrollbars */
        .scroll-panel::-webkit-scrollbar       { width: 7px; }
        .scroll-panel::-webkit-scrollbar-track { background: var(--color-bg); }
        .scroll-panel::-webkit-scrollbar-thumb { background: var(--color-divider); border-radius: 4px; }

        /* Answer-sheet buttons */
        .ans-btn            { transition: all .15s; position: relative; }
        .ans-btn.answered   { background: var(--color-primary); color: white; border-color: var(--color-primary); }
        .ans-btn.active-q   { background: var(--color-primary); color: white; border-color: var(--color-primary); outline: 2px solid var(--color-primary); outline-offset: 2px; }
        
        /* Flag indicator on button */
        .ans-btn .flag-icon { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: absolute; top: -4px; right: -4px; color: var(--color-error); font-size: 10px; display: none; background: white; border-radius: 50%; width: 12px; height: 12px; line-height: 12px; text-align: center; box-shadow: 0 0 2px rgba(0,0,0,0.3); }
        .ans-btn.flagged .flag-icon { display: block; }

        /* Active question highlight */
        .question-block.active-q { border-left: 3px solid var(--color-primary); background: #fdfbf7; }

        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { animation: fadeIn .3s ease-out both; }

        /* Timer warning */
        .timer-warning { color: var(--color-error); animation: pulse 1s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
        
        /* Review overlay */
        .review-overlay { background: rgba(26,26,26,0.85); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-[var(--color-bg)] text-[var(--color-text)] min-h-screen overflow-hidden flex flex-col">

{{-- ═══════════════════════════════════════════════
     TOP BAR
═══════════════════════════════════════════════ --}}
<div id="top-bar" class="z-50 bg-white border-b border-[var(--color-divider)] text-[var(--color-text)] shadow-sm h-16 flex items-center justify-between px-6 flex-shrink-0">

    {{-- Left: Brand & Title --}}
    <div class="flex items-center gap-4 w-1/3">
        <div class="w-10 h-10 bg-[var(--color-primary)] rounded-md flex items-center justify-center font-bold text-white text-lg">M</div>
        <div>
            <p class="text-[10px] text-gray-500 font-bold leading-tight uppercase tracking-wider">IELTS Reading</p>
            <span class="text-sm font-bold truncate block max-w-[200px]">{{ $test->title }}</span>
        </div>
    </div>

    {{-- Center: Timer & Autosave --}}
    <div class="flex items-center justify-center gap-6 w-1/3">
        <div class="flex items-center gap-2 bg-[var(--color-bg)] border border-[var(--color-divider)] rounded-[var(--radius-base)] px-4 py-2 shadow-sm">
            <i class="fas fa-clock text-[var(--color-primary)] text-sm"></i>
            <div class="flex items-end gap-2">
                <p id="timer-display" class="text-lg font-bold font-mono leading-none tracking-widest text-[var(--color-text)]">60:00</p>
                <p class="text-[10px] text-gray-500 font-medium leading-tight mb-0.5">Left</p>
            </div>
        </div>
        <div id="autosave-indicator" class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 bg-[var(--color-bg)] px-3 py-1.5 rounded-full border border-[var(--color-divider)] shadow-sm">
            <i class="fas fa-check-circle text-[var(--color-success)] text-[10px]"></i> <span class="font-medium tracking-wide">Saved</span>
        </div>
    </div>
    
    {{-- Right: Progress & Profile --}}
    <div class="flex items-center justify-end gap-4 w-1/3">
        <div class="bg-[var(--color-bg)] px-4 py-2 rounded-[var(--radius-base)] border border-[var(--color-divider)] flex items-center gap-2 shadow-sm">
            <div class="w-2.5 h-2.5 rounded-full bg-[var(--color-primary)]"></div>
            <p class="text-xs text-gray-500 font-medium">Answered: <span id="progress-text" class="text-[var(--color-text)] font-bold ml-1">0 / 0</span></p>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-50 border border-[var(--color-divider)] flex items-center justify-center font-bold text-[var(--color-primary)] shadow-sm">
            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MAIN CONTENT AREA
═══════════════════════════════════════════════ --}}
<div class="flex flex-1 overflow-hidden">

    {{-- ─────────────────────────────────────
         LEFT PANEL: Passage
    ───────────────────────────────────── --}}
    <div class="w-1/2 xl:w-[52%] flex-shrink-0 flex flex-col border-r border-slate-200 bg-white">

        {{-- Passage Tabs & Header --}}
        <div class="border-b border-slate-200 bg-slate-50 flex flex-col">
            <div class="flex border-b border-slate-200">
                @foreach($passages as $p)
                    <button id="tab-p{{ $p->passage_number }}"
                        onclick="switchPassage({{ $p->passage_number }})"
                        class="passage-tab flex-1 py-2.5 text-sm font-semibold transition-colors
                            {{ $loop->first ? 'text-blue-700 bg-white border-b-2 border-blue-600 shadow-[inset_0_2px_0_0_#2563eb]' : 'text-slate-500 bg-slate-50 border-b-2 border-transparent hover:bg-slate-100 hover:text-slate-700' }}">
                        Reading Passage {{ $p->passage_number }}
                    </button>
                @endforeach
            </div>
            <div id="passage-header" class="px-6 py-4 bg-white hidden">
                <h2 id="passage-title-label" class="text-lg font-bold text-slate-800 leading-snug">
                    {{ $passages->first()->title }}
                </h2>
            </div>
        </div>

        {{-- Passage Content Panels --}}
        <div class="flex-1 overflow-hidden relative">
            @foreach($passages as $p)
                <div id="passage-panel-{{ $p->passage_number }}"
                     class="passage-panel scroll-panel absolute inset-0 overflow-y-auto px-8 py-8 fade-in
                            {{ !$loop->first ? 'hidden' : '' }}">
                    <h2 class="text-2xl font-bold text-slate-900 mb-6 font-serif">{{ $p->title }}</h2>
                    <div class="passage-text text-slate-800 max-w-none">
                        {!! $p->content !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─────────────────────────────────────
         RIGHT PANEL: Questions
    ───────────────────────────────────── --}}
    <div class="flex-1 flex flex-col bg-slate-50 overflow-hidden relative">
        
        {{-- Question Toolbar --}}
        <div class="border-b border-slate-200 bg-white px-5 py-3 flex items-center justify-between shadow-sm z-10 sticky top-0">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-slate-700">Question</span>
                <span id="current-question-display" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center font-bold shadow-sm border border-blue-200">1</span>
            </div>
            
            <button id="flag-btn" onclick="toggleFlag()" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-600 text-sm font-medium hover:bg-slate-50 hover:border-slate-400 transition-colors shadow-sm">
                <i class="far fa-flag" id="flag-icon"></i> <span id="flag-text">Flag for review</span>
            </button>
        </div>

        {{-- Questions Scroll Panel --}}
        <div class="flex-1 overflow-y-auto relative scroll-panel bg-white" id="questions-container">
            @foreach($passages as $p)
                <div id="questions-panel-{{ $p->passage_number }}"
                     class="px-8 py-6 fade-in
                            {{ !$loop->first ? 'hidden' : '' }}">
                    
                    @php
                        $passGlobalStart = 1;
                        foreach($passages as $pp) {
                            if ($pp->passage_number < $p->passage_number) {
                                foreach($pp->questionGroups as $gg) {
                                    $passGlobalStart += $gg->questions->count();
                                }
                            }
                        }
                        $qOffset = $passGlobalStart;
                    @endphp

                    @if($p->questionGroups->isEmpty())
                        <div class="text-center py-12 text-slate-400">
                            <i class="fas fa-layer-group text-4xl mb-3"></i>
                            <p>No question groups configured for this passage.</p>
                        </div>
                    @else
                        @foreach($p->questionGroups as $group)
                            {{-- Group instruction banner --}}
                            @if($group->group_instruction)
                                <div class="mb-5 p-4 bg-blue-50/50 border border-blue-100 rounded-xl relative overflow-hidden">
                                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-400"></div>
                                    <p class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-1">Instructions</p>
                                    <p class="text-sm text-slate-700 leading-relaxed font-medium">{{ $group->group_instruction }}</p>
                                </div>
                            @endif

                            {{-- Questions --}}
                            <div class="space-y-4 mb-8">
                                @foreach($group->questions as $qi => $question)
                                    @php $qNum = $qOffset + $qi; @endphp
                                    <div id="question-block-{{ $question->id }}"
                                         class="question-block border border-slate-200 rounded-xl p-5 transition-all bg-white shadow-sm"
                                         data-question-id="{{ $question->id }}"
                                         data-qnum="{{ $qNum }}">

                                        {{-- Q header --}}
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-start gap-4 flex-1">
                                                <div class="flex-shrink-0 w-8 h-8 rounded bg-slate-100 border border-slate-200 text-slate-700 flex flex-col items-center justify-center leading-none">
                                                    <span class="text-[10px] text-slate-400 font-semibold mb-0.5">Q</span>
                                                    <span class="text-sm font-bold">{{ $qNum }}</span>
                                                </div>
                                                <p class="text-[15px] font-medium text-slate-800 leading-relaxed pt-1">
                                                    {!! nl2br(e($question->question_text)) !!}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Answer input by type --}}
                                        <div class="ml-12">
                                            @if($question->question_type === 'multiple_choice')
                                                <div class="space-y-2">
                                                    @foreach($question->options as $oi => $opt)
                                                        <label class="flex items-center gap-3 cursor-pointer group hover:bg-slate-50 border border-transparent hover:border-slate-200 rounded-lg p-2.5 transition">
                                                            <input type="radio"
                                                                name="answer_{{ $question->id }}"
                                                                value="{{ $opt->option_text }}"
                                                                data-qid="{{ $question->id }}"
                                                                {{ ($savedAnswers[$question->id] ?? '') === $opt->option_text ? 'checked' : '' }}
                                                                onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 flex-shrink-0 cursor-pointer">
                                                            <span class="text-sm text-slate-700 flex-1">
                                                                <span class="inline-block w-6 text-slate-400 font-bold group-hover:text-blue-500 transition-colors">{{ chr(65+$oi) }}.</span>{{ $opt->option_text }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                            @elseif(in_array($question->question_type, ['true_false_not_given', 'yes_no_not_given']))
                                                @php
                                                    $tfOptions = $question->question_type === 'yes_no_not_given'
                                                        ? ['Yes', 'No', 'Not Given']
                                                        : ['True', 'False', 'Not Given'];
                                                @endphp
                                                <div class="flex gap-3 flex-wrap">
                                                    @foreach($tfOptions as $opt)
                                                        <label class="flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-200 hover:border-blue-300 hover:bg-blue-50 rounded-lg px-4 py-2.5 transition text-sm font-medium text-slate-700 flex-1 min-w-[100px] justify-center">
                                                            <input type="radio"
                                                                name="answer_{{ $question->id }}"
                                                                value="{{ $opt }}"
                                                                data-qid="{{ $question->id }}"
                                                                {{ ($savedAnswers[$question->id] ?? '') === $opt ? 'checked' : '' }}
                                                                onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer">
                                                            {{ $opt }}
                                                        </label>
                                                    @endforeach
                                                </div>

                                            @elseif(in_array($question->question_type, ['matching_headings', 'matching_information', 'matching_sentence_endings']))
                                                <select
                                                    id="answer-input-{{ $question->id }}"
                                                    data-qid="{{ $question->id }}"
                                                    onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                    class="w-full max-w-sm border border-slate-300 rounded-lg text-sm px-4 py-2.5 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm cursor-pointer">
                                                    <option value="">— Select your answer —</option>
                                                    @foreach($question->options as $opt)
                                                        <option value="{{ $opt->option_text }}"
                                                            {{ ($savedAnswers[$question->id] ?? '') === $opt->option_text ? 'selected' : '' }}>
                                                            {{ $opt->option_text }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            @else
                                                <input type="text"
                                                    id="answer-input-{{ $question->id }}"
                                                    data-qid="{{ $question->id }}"
                                                    value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                    placeholder="Write your answer..."
                                                    oninput="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                    class="w-full max-w-md border border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white px-4 py-2.5 text-sm rounded-lg shadow-sm transition outline-none">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @php $qOffset += $group->questions->count(); @endphp
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Next/Prev Toolbar --}}
        <div class="border-t border-slate-200 bg-white px-5 py-3 flex items-center justify-between shadow-[0_-2px_10px_-5px_rgba(0,0,0,0.1)] z-10 sticky bottom-0">
            <button onclick="navigateQuestion(-1)" class="flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 font-medium hover:bg-slate-50 transition-colors">
                <i class="fas fa-chevron-left text-xs text-slate-400"></i> Previous
            </button>
            <button onclick="navigateQuestion(1)" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors shadow-sm">
                Next <i class="fas fa-chevron-right text-xs text-blue-300"></i>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     BOTTOM PANEL: ANSWER SHEET NAVIGATION
═══════════════════════════════════════════════ --}}
<div class="bg-white border-t border-[var(--color-divider)] px-6 py-4 flex items-center justify-between flex-shrink-0 z-20 shadow-[0_-2px_10px_-5px_rgba(0,0,0,0.05)]">
    <div class="flex-1 overflow-x-auto scroll-panel pb-2 mr-6">
        <div class="flex items-center gap-2 min-w-max" id="question-nav-chips">
            @php $globalNum = 1; @endphp
            @foreach($passages as $p)
                @foreach($p->questionGroups as $g)
                    @foreach($g->questions as $q)
                        @php 
                            $isFlagged = !empty($flaggedAnswers[$q->id]);
                            $isAns = !empty($savedAnswers[$q->id]);
                            $btnClass = "ans-btn relative w-9 h-9 flex items-center justify-center text-sm font-bold border rounded-[var(--radius-base)] hover:bg-[var(--color-bg)] hover:text-[var(--color-text)] cursor-pointer bg-white border-[var(--color-divider)] text-[var(--color-text)] transition-colors shadow-sm";
                            if($isAns) $btnClass .= " answered";
                            if($isFlagged) $btnClass .= " flagged";
                        @endphp
                        <button id="chip-{{ $q->id }}"
                            onclick="jumpToQuestion({{ $q->id }}, {{ $p->passage_number }}, this)"
                            class="{{ $btnClass }}"
                            title="Q{{ $globalNum }}">
                            {{ $globalNum }}
                            <i class="fas fa-flag flag-icon"></i>
                        </button>
                        @php $globalNum++; @endphp
                    @endforeach
                @endforeach
            @endforeach
        </div>
    </div>
    
    <div class="flex items-center gap-3 flex-shrink-0 border-l border-[var(--color-divider)] pl-6">
        <button onclick="showReviewPage()" class="px-6 py-2.5 rounded-[var(--radius-base)] bg-[var(--color-bg)] text-[var(--color-text)] font-bold hover:bg-[#e8e4dc] transition-colors border border-[var(--color-divider)] flex items-center gap-2 shadow-sm">
            <i class="fas fa-list-check text-[var(--color-primary)]"></i> Review & Submit
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     REVIEW OVERLAY
═══════════════════════════════════════════════ --}}
<div id="review-overlay" class="fixed inset-0 z-[100] hidden review-overlay flex flex-col pt-12">
    <div class="bg-white w-full max-w-4xl mx-auto rounded-t-2xl flex-1 flex flex-col shadow-2xl relative">
        <div class="border-b border-slate-200 px-8 py-5 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Review Answers</h2>
                <p class="text-slate-500 text-sm mt-1">Please check your answers before submitting the test.</p>
            </div>
            <button onclick="hideReviewPage()" class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors cursor-pointer">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <div class="p-8 flex-1 overflow-y-auto">
            {{-- Summary Stats --}}
            <div class="grid grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl"><i class="fas fa-check"></i></div>
                    <div>
                        <p class="text-3xl font-bold text-blue-800" id="review-answered-count">0</p>
                        <p class="text-blue-600 font-medium text-sm">Answered</p>
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-xl"><i class="fas fa-minus"></i></div>
                    <div>
                        <p class="text-3xl font-bold text-slate-700" id="review-unanswered-count">0</p>
                        <p class="text-slate-500 font-medium text-sm">Not Answered</p>
                    </div>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl"><i class="fas fa-flag"></i></div>
                    <div>
                        <p class="text-3xl font-bold text-red-700" id="review-flagged-count">0</p>
                        <p class="text-red-600 font-medium text-sm">Flagged</p>
                    </div>
                </div>
            </div>

            {{-- Grid of all questions --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <div class="bg-slate-50 px-6 py-3 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-slate-700">All Questions</h3>
                    <div class="flex items-center gap-4 text-xs font-medium">
                        <span class="flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 block rounded-sm bg-blue-500"></span> Answered</span>
                        <span class="flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 block rounded-sm bg-slate-200 border border-slate-300"></span> Not Answered</span>
                        <span class="flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 block rounded-full bg-white border border-slate-300 text-red-500 flex items-center justify-center text-[8px]"><i class="fas fa-flag"></i></span> Flagged</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-5 md:grid-cols-8 lg:grid-cols-10 gap-3" id="review-grid">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
            
            <div class="mt-8 bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-4 hidden" id="unanswered-warning">
                <i class="fas fa-exclamation-triangle text-amber-500 text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-bold text-amber-800">You have unanswered questions</h4>
                    <p class="text-sm text-amber-700 mt-1 pb-1">You will not lose points for incorrect answers. It is recommended to make an educated guess for all questions before submitting the test.</p>
                </div>
            </div>
        </div>
        
        <div class="border-t border-slate-200 px-8 py-5 bg-slate-50 rounded-b-2xl flex items-center justify-between sticky bottom-0 z-10">
            <button onclick="hideReviewPage()" class="px-6 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-700 font-medium hover:bg-slate-100 transition-colors">
                Return to Test
            </button>
            <form id="submit-form" action="{{ route('user.reading.submit', $attempt->id) }}" method="POST" onsubmit="return collectAndSubmit();">
                @csrf
                <div id="hidden-answers-container"></div>
                <div id="hidden-flags-container"></div>
                <button type="submit" id="final-submit-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane mr-1"></i> Submit Test Now
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     JS DATA
═══════════════════════════════════════════════ --}}
<script>
const ATTEMPT_ID     = {{ $attempt->id }};
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]').content;
const AUTOSAVE_URL   = '{{ route("user.reading.autosave", $attempt->id) }}';
const REMAINING_SECS = {{ $remainingSeconds }};
const SAVED_ANSWERS  = {!! json_encode($savedAnswers) !!};
const FLAGGED_ANSWERS = {!! json_encode($flaggedAnswers ?? []) !!};
const ALL_QUESTION_IDS = {!! json_encode($passages->flatMap(fn($p) => $p->questionGroups->flatMap(fn($g) => $g->questions->pluck('id')))) !!};
const PASSAGES_DATA = {!! json_encode($passages->map(fn($p) => ['number' => $p->passage_number, 'title' => $p->title, 'qIds' => $p->questionGroups->flatMap(fn($g) => $g->questions->pluck('id'))])) !!};
</script>

<script>
// ─────────────────────────────── STATE
let currentPassage = 1;
let currentQuestionId = ALL_QUESTION_IDS[0];
let currentQuestionIndex = 0;
let autosavePending = false;
let autosaveTimer = null;
const totalQuestions = ALL_QUESTION_IDS.length;
let flaggedState = {...FLAGGED_ANSWERS};

// ─────────────────────────────── TIMER
let timeLeft = REMAINING_SECS;
const timerEl = document.getElementById('timer-display');

const timerInterval = setInterval(() => {
    timeLeft--;
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        document.getElementById('submit-form').submit();
        return;
    }
    const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
    const s = (timeLeft % 60).toString().padStart(2, '0');
    timerEl.textContent = `${m}:${s}`;
    if (timeLeft <= 300) timerEl.classList.add('timer-warning');
}, 1000);

// Initial display
(function() {
    const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
    const s = (timeLeft % 60).toString().padStart(2, '0');
    timerEl.textContent = `${m}:${s}`;
})();

// ─────────────────────────────── PASSAGE SWITCHING
function switchPassage(num) {
    if (num === currentPassage) return;

    // Hide all panels
    document.querySelectorAll('.passage-panel').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="questions-panel-"]').forEach(el => el.classList.add('hidden'));

    // Show selected
    const pp = document.getElementById(`passage-panel-${num}`);
    const qp = document.getElementById(`questions-panel-${num}`);
    if (pp) { pp.classList.remove('hidden'); pp.classList.add('fade-in'); }
    if (qp) { qp.classList.remove('hidden'); qp.classList.add('fade-in'); }

    currentPassage = num;

    // Update tabs
    document.querySelectorAll('.passage-tab').forEach(tab => {
        const pNum = Number(tab.id.replace('tab-p', ''));
        const isActive = pNum === num;
        if(isActive) {
            tab.className = "passage-tab flex-1 py-2.5 text-sm font-semibold transition-colors text-blue-700 bg-white border-b-2 border-blue-600 shadow-[inset_0_2px_0_0_#2563eb]";
        } else {
            tab.className = "passage-tab flex-1 py-2.5 text-sm font-semibold transition-colors text-slate-500 bg-slate-50 border-b-2 border-transparent hover:bg-slate-100 hover:text-slate-700";
        }
    });
}

// ─────────────────────────────── QUESTION NAVIGATION
function jumpToQuestion(qId, passageNum, btnElement) {
    if (passageNum !== currentPassage) switchPassage(passageNum);
    
    currentQuestionId = qId;
    currentQuestionIndex = ALL_QUESTION_IDS.indexOf(qId);
    
    // Update active state in bottom nav
    document.querySelectorAll('.ans-btn').forEach(b => b.classList.remove('active-q'));
    const chip = document.getElementById(`chip-${qId}`);
    if (chip) chip.classList.add('active-q');
    
    // Scroll to question
    setTimeout(() => {
        const block = document.getElementById(`question-block-${qId}`);
        if (!block) return;
        document.querySelectorAll('.question-block').forEach(b => b.classList.remove('active-q'));
        block.classList.add('active-q');
        
        // Custom scroll logic to account for sticky header
        const container = document.getElementById('questions-container');
        const headerOffset = 56; // approximate height of sticky header
        const elementPosition = block.getBoundingClientRect().top;
        const offsetPosition = elementPosition + container.scrollTop - container.getBoundingClientRect().top - headerOffset - 20;
        
        container.scrollTo({
            top: offsetPosition,
            behavior: "smooth"
        });
        
        updateQuestionToolbar();
    }, 50);
}

function navigateQuestion(direction) {
    const newIndex = currentQuestionIndex + direction;
    if (newIndex >= 0 && newIndex < totalQuestions) {
        const targetQid = ALL_QUESTION_IDS[newIndex];
        const passageData = PASSAGES_DATA.find(p => p.qIds.includes(targetQid));
        if (passageData) {
            jumpToQuestion(targetQid, passageData.number, document.getElementById(`chip-${targetQid}`));
        }
    }
}

function updateQuestionToolbar() {
    // Update question number display
    const qNum = currentQuestionIndex + 1;
    document.getElementById('current-question-display').textContent = qNum;
    
    // Update flag button state
    const flagBtn = document.getElementById('flag-btn');
    const flagIcon = document.getElementById('flag-icon');
    const flagText = document.getElementById('flag-text');
    
    if (flaggedState[currentQuestionId]) {
        flagBtn.classList.add('bg-red-50', 'border-red-200', 'text-red-600');
        flagBtn.classList.remove('bg-white', 'border-slate-300', 'text-slate-600', 'hover:bg-slate-50');
        flagIcon.classList.remove('far');
        flagIcon.classList.add('fas');
        flagText.textContent = 'Flagged';
    } else {
        flagBtn.classList.remove('bg-red-50', 'border-red-200', 'text-red-600');
        flagBtn.classList.add('bg-white', 'border-slate-300', 'text-slate-600', 'hover:bg-slate-50');
        flagIcon.classList.remove('fas');
        flagIcon.classList.add('far');
        flagText.textContent = 'Flag for review';
    }
}

function toggleFlag() {
    flaggedState[currentQuestionId] = !flaggedState[currentQuestionId];
    
    // Update bottom chip
    const chip = document.getElementById(`chip-${currentQuestionId}`);
    if (chip) {
        if (flaggedState[currentQuestionId]) {
            chip.classList.add('flagged');
            const icon = chip.querySelector('.flag-icon');
            if(icon) {
                icon.style.transform = 'scale(1.5)';
                setTimeout(() => icon.style.transform = 'scale(1)', 150);
            }
        } else {
            chip.classList.remove('flagged');
        }
    }
    
    updateQuestionToolbar();
    scheduleAutosave();
}

// ─────────────────────────────── ANSWERED STATE & PROGRESS
function markAnswered(qId) {
    const chip = document.getElementById(`chip-${qId}`);
    if (chip) {
        // Double check it actually has a value before marking answered
        const val = getAnswerValue(qId);
        if (val !== '') {
            chip.classList.add('answered');
        } else {
            chip.classList.remove('answered');
        }
    }
    updateProgressTracker();
}

function updateProgressTracker() {
    let answeredCount = 0;
    ALL_QUESTION_IDS.forEach(id => {
        if (getAnswerValue(id) !== '') answeredCount++;
    });
    document.getElementById('progress-text').textContent = `${answeredCount} / ${totalQuestions}`;
}

// ─────────────────────────────── ANSWER COLLECTION
function getAnswerValue(qId) {
    const radio = document.querySelector(`input[name="answer_${qId}"]:checked`);
    if (radio) return radio.value;
    const inp = document.getElementById(`answer-input-${qId}`);
    if (inp) return inp.value.trim();
    return '';
}

function collectAllAnswers() {
    const out = {};
    ALL_QUESTION_IDS.forEach(id => {
        const val = getAnswerValue(id);
        if (val !== '') out[id] = val;
    });
    return out;
}

// ─────────────────────────────── AUTOSAVE
function scheduleAutosave() {
    if (autosavePending) return;
    autosavePending = true;
    autosaveTimer = setTimeout(autosaveNow, 5000);
}

function autosaveNow() {
    clearTimeout(autosaveTimer);
    autosavePending = false;
    const answersPayload = collectAllAnswers();
    
    // Even if no answers, we might have flags to save
    if (Object.keys(answersPayload).length === 0 && Object.keys(flaggedState).length === 0) return;

    const indicator = document.getElementById('autosave-indicator');
    indicator.classList.remove('hidden');
    indicator.innerHTML = '<i class="fas fa-circle-notch fa-spin text-yellow-400 text-[10px]"></i> <span class="tracking-wide">Saving...</span>';

    fetch(AUTOSAVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ 
            answers: answersPayload, 
            flagged: flaggedState 
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            indicator.innerHTML = `<i class="fas fa-check-circle text-green-400 text-[10px]"></i> <span class="tracking-wide text-slate-300">Saved</span>`;
            setTimeout(() => {
                if(!autosavePending) indicator.classList.add('hidden');
            }, 3000);
        }
    })
    .catch(() => {
        indicator.innerHTML = '<i class="fas fa-exclamation-circle text-red-400 text-[10px]"></i> <span class="text-red-400">Failed</span>';
    });
}

// Periodic autosave every 20s as fallback
setInterval(autosaveNow, 20000);

// ─────────────────────────────── REVIEW PAGE
function showReviewPage() {
    // Generate grid
    const grid = document.getElementById('review-grid');
    grid.innerHTML = '';
    
    let answeredCount = 0;
    let flaggedCount = 0;
    
    ALL_QUESTION_IDS.forEach((id, index) => {
        const qNum = index + 1;
        const isAns = getAnswerValue(id) !== '';
        const isFlag = !!flaggedState[id];
        
        if (isAns) answeredCount++;
        if (isFlag) flaggedCount++;
        
        // Build styling based on state
        let btnClasses = "relative w-full aspect-square flex flex-col items-center justify-center font-bold text-sm rounded-lg border-2 transition-transform hover:scale-105 cursor-pointer shadow-sm";
        let iconHtml = "";
        
        if (isAns) {
            btnClasses += " bg-blue-50 border-blue-500 text-blue-700";
        } else {
            btnClasses += " bg-white border-slate-200 text-slate-500";
        }
        
        if (isFlag) {
            iconHtml = '<div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-white border border-slate-200 rounded-full flex items-center justify-center text-red-500 text-[10px] shadow-sm"><i class="fas fa-flag"></i></div>';
            if(!isAns) btnClasses = btnClasses.replace("border-slate-200", "border-red-300");
        }
        
        grid.innerHTML += `
            <div onclick="returnToQuestion(${id})" class="${btnClasses}">
                ${qNum}
                ${iconHtml}
            </div>
        `;
    });
    
    // Update stats
    document.getElementById('review-answered-count').textContent = answeredCount;
    document.getElementById('review-unanswered-count').textContent = totalQuestions - answeredCount;
    document.getElementById('review-flagged-count').textContent = flaggedCount;
    
    const warningEl = document.getElementById('unanswered-warning');
    if (answeredCount < totalQuestions) {
        warningEl.classList.remove('hidden');
        document.getElementById('final-submit-btn').innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Submit with Unanswered Questions';
        document.getElementById('final-submit-btn').classList.replace('bg-blue-600', 'bg-amber-600');
        document.getElementById('final-submit-btn').classList.replace('hover:bg-blue-700', 'hover:bg-amber-700');
    } else {
        warningEl.classList.add('hidden');
        document.getElementById('final-submit-btn').innerHTML = '<i class="fas fa-check-circle mr-1"></i> Confirm & Submit';
        document.getElementById('final-submit-btn').classList.replace('bg-amber-600', 'bg-green-600');
        document.getElementById('final-submit-btn').classList.replace('hover:bg-amber-700', 'hover:bg-green-700');
    }
    
    document.getElementById('review-overlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideReviewPage() {
    document.getElementById('review-overlay').classList.add('hidden');
    document.body.style.overflow = '';
}

function returnToQuestion(qId) {
    hideReviewPage();
    const passageData = PASSAGES_DATA.find(p => p.qIds.includes(qId));
    if (passageData) {
        jumpToQuestion(qId, passageData.number, document.getElementById(`chip-${qId}`));
    }
}

// ─────────────────────────────── SUBMISSION
function collectAndSubmit() {
    const container = document.getElementById('hidden-answers-container');
    container.innerHTML = '';
    const answers = collectAllAnswers();
    Object.entries(answers).forEach(([qId, val]) => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = `answers[${qId}]`;
        inp.value = val;
        container.appendChild(inp);
    });
    
    const flagContainer = document.getElementById('hidden-flags-container');
    flagContainer.innerHTML = '';
    Object.entries(flaggedState).forEach(([qId, val]) => {
        if(val) {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = `flagged[${qId}]`;
            inp.value = "1";
            flagContainer.appendChild(inp);
        }
    });
    return true;
}

// ─────────────────────────────── INIT
document.addEventListener('DOMContentLoaded', () => {
    // Select first question
    jumpToQuestion(ALL_QUESTION_IDS[0], 1, document.getElementById(`chip-${ALL_QUESTION_IDS[0]}`));

    // Pre-mark already answered chips & fill inputs
    ALL_QUESTION_IDS.forEach(id => {
        const saved = SAVED_ANSWERS[id];
        if (saved) {
            const inp = document.getElementById(`answer-input-${id}`);
            if (inp) inp.value = saved;
            const radio = document.querySelector(`input[name="answer_${id}"][value="${CSS.escape ? CSS.escape(saved) : saved}"]`);
            if (radio) radio.checked = true;
            markAnswered(id);
        }
    });
    
    updateProgressTracker();
});
</script>

</body>
</html>
