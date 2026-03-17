<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Listening Test – {{ $test->title }} | MockDasher</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Audio progress bar */
        #audio-progress-bar { cursor: default; }
        #audio-progress-bar::-webkit-slider-thumb { cursor: default; }
        #audio-progress-bar::-moz-range-thumb   { cursor: default; }

        /* Scrollbars */
        .scroll-panel::-webkit-scrollbar       { width: 7px; }
        .scroll-panel::-webkit-scrollbar-track { background: var(--color-bg); }
        .scroll-panel::-webkit-scrollbar-thumb { background: var(--color-divider); border-radius: 4px; }

        /* Answer-sheet buttons */
        .ans-btn            { transition: all .15s; position: relative; }
        .ans-btn.answered   { background: var(--color-primary); color: white; border-color: var(--color-primary); } 
        .ans-btn.active-q   { background: var(--color-primary); color: white; border-color: var(--color-primary); outline: 2px solid var(--color-primary); outline-offset: 2px; }
        
        /* Flag indicator on button */
        .ans-btn .flag-icon { position: absolute; top: -4px; right: -4px; color: var(--color-error); font-size: 10px; display: none; background: white; border-radius: 50%; width: 12px; height: 12px; line-height: 12px; text-align: center; box-shadow: 0 0 2px rgba(0,0,0,0.3); }
        .ans-btn.flagged .flag-icon { display: block; }
        
        .ans-btn.locked { opacity: 0.3; cursor: not-allowed; }

        /* Transfer mode pulse */
        @keyframes pulseBorder { 0%,100% { border-color: #f59e0b; } 50% { border-color: #d97706; } }
        .transfer-pulse { animation: pulseBorder 2s ease-in-out infinite; }

        /* Active question highlight */
        .question-block.active-q { border-left: 3px solid var(--color-primary); background: #fdfbf7; }

        @keyframes fadeSlideIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        .fade-in { animation: fadeSlideIn 0.35s ease-out both; }
        
        /* Review overlay */
        .review-overlay { background: rgba(26,26,26,0.85); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-[var(--color-bg)] text-[var(--color-text)] min-h-screen overflow-hidden flex flex-col">

{{-- ════════════════════════════════════════════════════
     TOP BAR – Timer & Test Info
════════════════════════════════════════════════════ --}}
<div id="top-bar" class="z-50 bg-white border-b border-[var(--color-divider)] text-[var(--color-text)] shadow-sm h-16 flex items-center justify-between px-6 flex-shrink-0">

    {{-- Left: Brand & Title --}}
    <div class="flex items-center gap-4 w-1/3">
        <div class="w-10 h-10 bg-[var(--color-primary)] rounded-md flex items-center justify-center font-bold text-white text-lg">M</div>
        <div>
            <p class="text-[10px] text-gray-500 font-bold leading-tight uppercase tracking-wider">IELTS Listening</p>
            <span class="text-sm font-bold truncate block max-w-[200px]">{{ $test->title }}</span>
        </div>
    </div>

    {{-- Center: Timer & Autosave --}}
    <div class="flex items-center justify-center gap-6 w-1/3">
        <div class="flex items-center gap-2 bg-[var(--color-bg)] border border-[var(--color-divider)] rounded-[var(--radius-base)] px-4 py-2 shadow-sm">
            <i class="fas fa-clock text-[var(--color-primary)] text-sm"></i>
            <div class="flex items-end gap-2">
                <p id="timer-value" class="text-lg font-bold font-mono leading-none text-[var(--color-text)] tracking-widest">00:00</p>
                <p id="timer-label" class="text-[10px] text-gray-500 font-medium leading-tight mb-0.5">Elapsed</p>
            </div>
        </div>
        <div id="autosave-status" class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 bg-[var(--color-bg)] px-3 py-1.5 rounded-full border border-[var(--color-divider)] shadow-sm">
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

{{-- ════════════════════════════════════════════════════
     MAIN LAYOUT: Left Audio Panel | Right Questions Panel
════════════════════════════════════════════════════ --}}
<div class="flex flex-1 overflow-hidden">

    {{-- ──────────────────────────────────────
         LEFT PANEL: Audio Player
    ────────────────────────────────────── --}}
    <div class="w-72 xl:w-80 flex-shrink-0 bg-slate-900 flex flex-col border-r border-slate-700 shadow-xl z-10">

        {{-- Part indicator & Audio --}}
        <div class="p-6 border-b border-slate-700 bg-slate-800 flex-1">
            <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest mb-1">Now Playing</p>
            <h2 id="current-part-label" class="text-white text-2xl font-bold mb-1">Part 1</h2>
            <p id="current-part-desc" class="text-xs text-slate-400 mb-8 font-medium">Conversation — everyday social context</p>

            <audio id="exam-audio" preload="auto" class="hidden">
                @foreach($sections as $s)
                    @if($s->audio_path)
                        <source data-section="{{ $s->section_number }}" src="{{ Storage::url($s->audio_path) }}" type="audio/mpeg">
                    @endif
                @endforeach
            </audio>

            {{-- Waveform visual --}}
            <div class="w-full h-20 mb-6 bg-slate-900 rounded-xl flex items-center justify-center gap-1.5 px-4 overflow-hidden border border-slate-700/50 shadow-inner" id="waveform">
                @for($i=0;$i<30;$i++)
                    <div class="waveform-bar bg-blue-500 rounded-full opacity-50 transition-all duration-75"
                         style="width:4px; height:{{ rand(10,40) }}px;"></div>
                @endfor
            </div>

            {{-- Play/Pause --}}
            <div class="flex items-center justify-center mb-6">
                <button id="play-pause-btn" onclick="togglePlayPause()"
                    class="w-16 h-16 bg-blue-600 hover:bg-blue-500 text-white rounded-full flex items-center justify-center transition shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                    <i id="play-icon" class="fas fa-play text-2xl ml-1"></i>
                </button>
            </div>

            {{-- Time display & Progress bar --}}
            <div class="mb-8">
                <div class="flex justify-between text-xs text-slate-400 font-mono mb-2 px-1 font-medium">
                    <span id="current-time">0:00</span>
                    <span id="total-time">0:00</span>
                </div>
                <div class="relative">
                    <div class="w-full h-2.5 bg-slate-700 rounded-full overflow-hidden shadow-inner cursor-not-allowed">
                        <div id="audio-played" class="h-full bg-blue-500 rounded-full transition-all duration-300" style="width:0%"></div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 mt-2 text-center uppercase tracking-wider font-semibold">You may pause but cannot skip ahead</p>
            </div>

            {{-- Volume --}}
            <div class="flex items-center gap-3 bg-slate-900/50 py-2 px-4 rounded-lg border border-slate-700">
                <i class="fas fa-volume-up text-slate-400 text-sm"></i>
                <input type="range" id="volume-control" min="0" max="1" step="0.05" value="1"
                    onchange="document.getElementById('exam-audio').volume=this.value"
                    class="w-full h-1.5 accent-blue-500 cursor-pointer bg-slate-700 rounded-lg appearance-none">
            </div>
        </div>

        {{-- Section Navigation Status List --}}
        <div class="p-6 bg-slate-900 border-t border-slate-800">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Test Progress</h3>
            <div class="space-y-2">
                @foreach($sections as $s)
                    <div id="section-status-{{ $s->section_number }}"
                        class="flex items-center gap-3 text-sm rounded-lg p-3 transition-colors
                            {{ $attempt->current_section > $s->section_number ? 'bg-slate-800 text-green-400 border border-slate-700/50' : ($attempt->current_section == $s->section_number ? 'bg-blue-900/40 text-blue-300 border border-blue-800/50 shadow-inner' : 'bg-slate-800/40 text-slate-500 border border-transparent') }}">
                        @if($attempt->current_section > $s->section_number)
                            <i class="fas fa-check-circle text-green-500 w-4 text-center"></i>
                        @elseif($attempt->current_section == $s->section_number)
                            <div class="w-4 h-4 rounded-full bg-blue-500/20 flex items-center justify-center"><div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div></div>
                        @else
                            <i class="fas fa-lock text-slate-600 w-4 text-center"></i>
                        @endif
                        <span class="font-medium">Part {{ $s->section_number }}</span>
                        @if($attempt->current_section > $s->section_number)
                            <span class="ml-auto text-xs text-green-500 font-bold uppercase tracking-wider">Done</span>
                        @elseif($attempt->current_section == $s->section_number)
                            <span class="ml-auto text-xs font-bold uppercase tracking-wider text-blue-400">Active</span>
                        @else
                            <span class="ml-auto text-xs uppercase tracking-wider font-semibold">Locked</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────
         RIGHT PANEL: Questions
    ────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col bg-slate-50 overflow-hidden relative">
        
        {{-- Question Toolbar --}}
        <div class="border-b border-slate-200 bg-white px-6 py-3 flex items-center justify-between shadow-sm z-10 sticky top-0 hidden" id="question-toolbar">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-slate-700">Part <span id="toolbar-part-display">1</span></span>
                <div class="w-px h-5 bg-slate-300"></div>
                <span class="text-sm font-semibold text-slate-700">Question</span>
                <span id="current-question-display" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center font-bold shadow-sm border border-blue-200">1</span>
            </div>
            
            <button id="flag-btn" onclick="toggleFlag()" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-600 text-sm font-medium hover:bg-slate-50 hover:border-slate-400 transition-colors shadow-sm">
                <i class="far fa-flag" id="flag-icon"></i> <span id="flag-text">Flag for review</span>
            </button>
        </div>

        {{-- Instruction Banner --}}
        <div id="instruction-banner" class="border-b border-slate-200 bg-blue-50/50 px-8 py-4 z-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-400"></div>
            <p class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-1">Instructions</p>
            <p id="instruction-text" class="text-sm text-slate-700 font-medium">
                {{ $sections->firstWhere('section_number', $attempt->current_section)?->instruction_text ?? 'Listen carefully and answer the questions below.' }}
            </p>
        </div>

        {{-- Questions Scroll Panel --}}
        <div class="flex-1 overflow-y-auto relative scroll-panel bg-white" id="questions-container">
            @foreach($sections as $section)
                <div id="section-questions-{{ $section->section_number }}"
                    class="px-8 py-8 fade-in {{ $section->section_number != $attempt->current_section ? 'hidden' : '' }}">

                    @if($section->questions->isEmpty())
                        <div class="text-center py-16 text-slate-400 bg-slate-50 rounded-2xl border border-slate-200 border-dashed">
                            <i class="fas fa-headphones text-4xl mb-4 text-slate-300"></i>
                            <p class="font-medium text-slate-500">No questions configured for this section yet.</p>
                        </div>
                    @else
                        @php $qNum = $section->section_number == 1 ? 1 : $sections->where('section_number', '<', $section->section_number)->sum(fn($s) => $s->questions->count()) + 1; @endphp
                        <div class="space-y-6">
                            @foreach($section->questions as $qi => $question)
                                @php $globalNum = $qNum + $qi; @endphp
                                <div id="question-block-{{ $question->id }}"
                                     class="question-block border border-slate-200 rounded-xl p-6 mb-4 transition-all bg-white shadow-sm"
                                     data-question-id="{{ $question->id }}"
                                     data-qnum="{{ $globalNum }}">

                                    {{-- Q header --}}
                                    <div class="flex items-start justify-between mb-5">
                                        <div class="flex items-start gap-4 flex-1">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 flex flex-col items-center justify-center leading-none shadow-sm">
                                                <span class="text-[10px] text-slate-400 font-semibold mb-0.5 uppercase">Q</span>
                                                <span class="text-sm font-bold">{{ $globalNum }}</span>
                                            </div>
                                            <p class="text-[15px] font-medium text-slate-800 leading-relaxed pt-1.5">
                                                {!! nl2br(e($question->question_text)) !!}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Answer Input - varies by type --}}
                                    <div class="ml-14">
                                        @if($question->question_type === 'multiple_choice')
                                            <div class="space-y-2">
                                                @foreach($question->options as $oi => $opt)
                                                    <label class="flex items-center gap-3 cursor-pointer group hover:bg-slate-50 border border-transparent hover:border-slate-200 rounded-lg p-3 transition">
                                                        <input type="radio"
                                                            name="answer_{{ $question->id }}"
                                                            value="{{ $opt->option_text }}"
                                                            data-question-id="{{ $question->id }}"
                                                            {{ (($savedAnswers[$question->id] ?? '') === $opt->option_text) ? 'checked' : '' }}
                                                            onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 flex-shrink-0 cursor-pointer">
                                                        <span class="text-sm text-slate-700 flex-1 font-medium">
                                                            <span class="inline-block w-6 text-slate-400 font-bold group-hover:text-blue-500 transition-colors">{{ chr(65+$oi) }}.</span>{{ $opt->option_text }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>

                                        @elseif(in_array($question->question_type, ['form_completion', 'table_completion', 'sentence_completion', 'short_answer']))
                                            <input type="text"
                                                id="answer-input-{{ $question->id }}"
                                                data-question-id="{{ $question->id }}"
                                                value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                placeholder="{{ $question->question_type === 'form_completion' ? 'Write your answer here...' : 'Write ONE WORD AND/OR A NUMBER' }}"
                                                oninput="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                class="w-full max-w-md border border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white px-4 py-3 text-sm rounded-lg shadow-sm transition outline-none">

                                        @elseif($question->question_type === 'matching')
                                            <select
                                                id="answer-input-{{ $question->id }}"
                                                data-question-id="{{ $question->id }}"
                                                onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                class="w-full max-w-sm border border-slate-300 rounded-lg text-sm px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm cursor-pointer">
                                                <option value="">— Select your answer —</option>
                                                @foreach($question->options as $opt)
                                                    <option value="{{ $opt->option_text }}" {{ ($savedAnswers[$question->id] ?? '') === $opt->option_text ? 'selected' : '' }}>
                                                        {{ $opt->option_text }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text"
                                                id="answer-input-{{ $question->id }}"
                                                data-question-id="{{ $question->id }}"
                                                value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                placeholder="Your answer..."
                                                oninput="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                class="w-full max-w-md border border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white px-4 py-3 text-sm rounded-lg shadow-sm transition outline-none">
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
            <div class="h-16"></div> <!-- Padding bottom -->
        </div>

        {{-- Next/Prev Toolbar (Hidden initially until first question is selected) --}}
        <div class="border-t border-slate-200 bg-white px-5 py-3 flex items-center justify-between shadow-[0_-2px_10px_-5px_rgba(0,0,0,0.1)] z-10 sticky bottom-0">
            <button onclick="navigateQuestion(-1)" class="flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 font-medium hover:bg-slate-100 transition-colors">
                <i class="fas fa-chevron-left text-xs text-slate-400"></i> Previous
            </button>
            <div class="flex gap-3">
                <button type="button" id="next-section-btn" onclick="requestNextSection()" class="hidden bg-slate-800 hover:bg-slate-900 text-white font-semibold px-6 py-2.5 rounded-lg shadow-md transition-colors flex items-center gap-2 border border-slate-700">
                    Next Part <i class="fas fa-arrow-right text-xs text-slate-400"></i>
                </button>
            </div>
            <button onclick="navigateQuestion(1)" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-50 text-blue-700 font-medium hover:bg-blue-100 border border-blue-200 transition-colors shadow-sm">
                Next <i class="fas fa-chevron-right text-xs text-blue-400"></i>
            </button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     BOTTOM PANEL: ANSWER SHEET NAVIGATION
════════════════════════════════════════════════════ --}}
<div class="bg-white border-t border-[var(--color-divider)] px-6 py-4 flex items-center justify-between flex-shrink-0 z-20 shadow-[0_-2px_10px_-5px_rgba(0,0,0,0.05)]">
    <div class="flex-1 overflow-x-auto scroll-panel pb-2 mr-6">
        <div class="flex items-center gap-2 min-w-max" id="question-nav-chips">
            @php $globalNum = 1; @endphp
            @foreach($sections as $s)
                @foreach($s->questions as $q)
                    @php 
                        $isFlagged = !empty($flaggedAnswers[$q->id]);
                        $isAns = !empty($savedAnswers[$q->id]);
                        $isLocked = $s->section_number > $attempt->current_section;
                        $btnClass = "ans-btn relative w-9 h-9 flex items-center justify-center text-sm font-bold border rounded-[var(--radius-base)] transition-colors ";
                        
                        // Default vs locked styling
                        if ($isLocked) {
                            $btnClass .= "locked bg-[var(--color-bg)] border-[var(--color-divider)] text-gray-400";
                        } else {
                            $btnClass .= "hover:bg-[var(--color-bg)] hover:text-[var(--color-text)] cursor-pointer bg-white border-[var(--color-divider)] text-[var(--color-text)] shadow-sm ";
                            if($isAns) $btnClass .= " answered";
                            if($isFlagged) $btnClass .= " flagged";
                        }
                    @endphp
                    <button id="chip-{{ $q->id }}"
                        onclick="{{ $isLocked ? 'return false;' : "jumpToQuestion({$q->id}, {$s->section_number}, this)" }}"
                        class="{{ $btnClass }}"
                        title="Q{{ $globalNum }}">
                        {{ $globalNum }}
                        @if(!$isLocked) <i class="fas fa-flag flag-icon"></i> @endif
                        @if($isLocked) <i class="fas fa-lock absolute text-[8px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-20"></i> @endif
                    </button>
                    @php $globalNum++; @endphp
                @endforeach
            @endforeach
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     TRANSFER PHASE / REVIEW OVERLAY
════════════════════════════════════════════════════ --}}
<div id="transfer-overlay" class="fixed inset-0 z-[100] hidden review-overlay flex flex-col pt-12 transition-opacity">
    <div class="bg-white w-full max-w-4xl mx-auto rounded-t-2xl flex-1 flex flex-col shadow-2xl relative border border-slate-200">
        {{-- Custom Header for Transfer Mode --}}
        <div class="bg-amber-50 border-b border-amber-200 px-8 py-5 flex items-center justify-between sticky top-0 rounded-t-[15px] z-10 transfer-pulse shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 border border-amber-200 shadow-sm">
                    <i class="fas fa-exchange-alt text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-amber-800">Answer Transfer & Review Phase</h2>
                    <p class="text-amber-700 text-sm font-medium mt-0.5">Please check and finalize your answers.</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs text-amber-600 font-bold uppercase tracking-wider block mb-1">Time Remaining</span>
                <div class="text-3xl font-mono font-bold text-amber-700 bg-amber-100/50 px-4 py-1.5 rounded-lg border border-amber-200/50" id="transfer-countdown">10:00</div>
            </div>
        </div>
        
        <div class="p-8 flex-1 overflow-y-auto bg-slate-50/50">
            {{-- Summary Stats --}}
            <div class="grid grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl border border-blue-200 shadow-inner"><i class="fas fa-check"></i></div>
                    <div>
                        <p class="text-3xl font-bold text-blue-800" id="review-answered-count">0</p>
                        <p class="text-blue-600 font-semibold text-sm">Answered</p>
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-xl border border-slate-300 shadow-inner"><i class="fas fa-minus"></i></div>
                    <div>
                        <p class="text-3xl font-bold text-slate-700" id="review-unanswered-count">0</p>
                        <p class="text-slate-500 font-semibold text-sm">Not Answered</p>
                    </div>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl border border-red-200 shadow-inner"><i class="fas fa-flag"></i></div>
                    <div>
                        <p class="text-3xl font-bold text-red-700" id="review-flagged-count">0</p>
                        <p class="text-red-600 font-semibold text-sm">Flagged</p>
                    </div>
                </div>
            </div>

            {{-- Grid of all questions --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fas fa-list-check text-slate-400"></i> All Questions</h3>
                    <div class="flex items-center gap-5 text-xs font-semibold">
                        <span class="flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 block rounded shadow-sm bg-blue-500 border border-blue-600"></span> Answered</span>
                        <span class="flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 block rounded shadow-sm bg-slate-50 border border-slate-300"></span> Not Answered</span>
                        <span class="flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 block rounded shadow-sm bg-white border border-red-300 text-red-500 flex items-center justify-center text-[8px]"><i class="fas fa-flag"></i></span> Flagged</span>
                    </div>
                </div>
                <div class="p-8 bg-slate-50/30">
                    <div class="grid grid-cols-5 md:grid-cols-8 lg:grid-cols-10 gap-4" id="review-grid">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
            
            <div class="mt-8 bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-4 hidden shadow-sm" id="unanswered-warning">
                <i class="fas fa-exclamation-triangle text-amber-500 text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-bold text-amber-800">You have unanswered questions</h4>
                    <p class="text-sm text-amber-700 mt-1 pb-1 font-medium">You will not lose points for incorrect answers. It is recommended to make an educated guess for all questions before the transfer time ends.</p>
                </div>
            </div>
        </div>
        
        <div class="border-t border-slate-200 px-8 py-5 bg-white rounded-b-2xl flex items-center justify-between sticky bottom-0 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <button onclick="closeTransfer()" class="px-6 py-3 rounded-xl border border-slate-300 bg-white text-slate-700 font-semibold hover:bg-slate-50 hover:border-slate-400 transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-arrow-left text-slate-400"></i> Return to Answers
            </button>
            <form id="final-submit-form" action="{{ route('user.listening.submit', $attempt->id) }}" method="POST" onsubmit="return collectAndSubmit('final-submit-form');">
                @csrf
                <div id="hidden-answers-container-2"></div>
                <div id="hidden-flags-container"></div>
                <button type="submit" id="final-submit-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-md transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane mr-1 text-blue-200"></i> Final Submit Now
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     DATA PASSED TO JS
════════════════════════════════════════════════════ --}}
<script>
const ATTEMPT_ID    = {{ $attempt->id }};
const CURRENT_SEC   = {{ $attempt->current_section }};
const ATTEMPT_STATUS = '{{ $attempt->status }}';
const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]').content;
const AUTOSAVE_URL  = '{{ route('user.listening.autosave', $attempt->id) }}';
const COMPLETE_URL  = '{{ route('user.listening.completeSection', $attempt->id) }}';
const SECTIONS_DATA = {!! json_encode($sections->map(fn($s) => [
    'id'             => $s->id,
    'number'         => $s->section_number,
    'audio_path'     => $s->audio_path ? Storage::url($s->audio_path) : null,
    'instruction'    => $s->instruction_text ?? 'Listen carefully and answer the questions below.',
    'q_count'        => $s->questions->count(),
    'question_ids'   => $s->questions->pluck('id'),
])) !!};
const TRANSFER_REMAINING = {{ $transferRemainingSeconds ?? 'null' }};
const ALL_QUESTION_IDS   = {!! json_encode($sections->flatMap(fn($s) => $s->questions->pluck('id'))) !!};
const TOTAL_QUESTIONS    = ALL_QUESTION_IDS.length;
const SAVED_ANSWERS      = {!! json_encode($savedAnswers ?? []) !!};
const FLAGGED_ANSWERS    = {!! json_encode($flaggedAnswers ?? []) !!};
</script>

<script>
// ════════════════════════════════════════════════════
// STATE
// ════════════════════════════════════════════════════
let currentSection = CURRENT_SEC;
let currentQuestionId = null;
let currentQuestionIndex = -1;
let autosaveTimer  = null;
let autosavePending = false;
let transferInterval = null;
let isAudioPlayed  = false;
let maxReachedAudio = 0;
let isSectionAudioDone = false;
let flaggedState = {...FLAGGED_ANSWERS};

const audio = document.getElementById('exam-audio');
const partDescriptions = {
    1: 'Conversation — everyday social context',
    2: 'Monologue — everyday social context',
    3: 'Conversation — educational/training context',
    4: 'Monologue — academic subject',
};

// ════════════════════════════════════════════════════
// AUDIO SYSTEM
// ════════════════════════════════════════════════════
function loadSectionAudio(sectionNum) {
    const sec = SECTIONS_DATA.find(s => s.number === sectionNum);
    if (!sec || !sec.audio_path) {
        document.getElementById('current-part-label').textContent = `Part ${sectionNum}`;
        document.getElementById('current-part-desc').textContent = partDescriptions[sectionNum] || '';
        markSectionAudioDone();
        return;
    }
    audio.src = sec.audio_path;
    audio.load();
    document.getElementById('current-part-label').textContent = `Part ${sectionNum}`;
    document.getElementById('current-part-desc').textContent = partDescriptions[sectionNum] || '';
    maxReachedAudio = 0;
    isSectionAudioDone = false;
    audio.play().catch(() => {});
}

function togglePlayPause() {
    if (audio.paused) {
        audio.play();
    } else {
        audio.pause();
    }
}

audio.addEventListener('play', () => {
    document.getElementById('play-icon').className = 'fas fa-pause text-2xl';
    animateWaveform(true);
});
audio.addEventListener('pause', () => {
    document.getElementById('play-icon').className = 'fas fa-play text-2xl ml-1';
    animateWaveform(false);
});

audio.addEventListener('timeupdate', () => {
    const cur = audio.currentTime;
    const dur = audio.duration || 1;
    if (cur > maxReachedAudio) maxReachedAudio = cur;

    const pct = (cur / dur) * 100;
    document.getElementById('audio-played').style.width = pct + '%';
    document.getElementById('current-time').textContent = formatTime(cur);
    document.getElementById('total-time').textContent   = formatTime(dur);
});

audio.addEventListener('seeking', () => {
    if (audio.currentTime > maxReachedAudio + 1) {
        audio.currentTime = maxReachedAudio;
    }
});

audio.addEventListener('ended', () => markSectionAudioDone());

audio.addEventListener('loadedmetadata', () => {
    document.getElementById('total-time').textContent = formatTime(audio.duration);
});

let waveformAnimFrame = null;
function animateWaveform(isPlaying) {
    const bars = document.querySelectorAll('.waveform-bar');
    if (!isPlaying) {
        bars.forEach(b => { b.style.transform = 'scaleY(1)'; b.style.opacity = '0.3'; });
        if (waveformAnimFrame) cancelAnimationFrame(waveformAnimFrame);
        return;
    }
    function step() {
        bars.forEach(b => {
            const h = 0.4 + Math.random() * 0.8;
            b.style.transform = `scaleY(${h})`;
            b.style.opacity = 0.5 + Math.random() * 0.5;
        });
        waveformAnimFrame = requestAnimationFrame(() => setTimeout(step, 80));
    }
    step();
}

function markSectionAudioDone() {
    isSectionAudioDone = true;
    const isLastSection = currentSection >= SECTIONS_DATA.length;
    if (isLastSection) {
        // Trigger answer transfer phase remotely or automatically 
        requestNextSection(); // Complete final part
    } else {
        document.getElementById('next-section-btn').classList.remove('hidden');
    }
}

// ════════════════════════════════════════════════════
// NAVIGATION LOGIC
// ════════════════════════════════════════════════════
function jumpToQuestion(qId, sectionNum, btnElement) {
    if(sectionNum > currentSection) return; // Locked
    
    // Switch UI panels if section changes
    if(sectionNum !== currentSection) {
        document.querySelectorAll('[id^="section-questions-"]').forEach(el => el.classList.add('hidden'));
        const target = document.getElementById(`section-questions-${sectionNum}`);
        if(target) { target.classList.remove('hidden'); target.classList.add('fade-in'); }
    }
    
    currentQuestionId = qId;
    currentQuestionIndex = ALL_QUESTION_IDS.indexOf(qId);
    
    // Expose Toolbar
    document.getElementById('question-toolbar').classList.remove('hidden');
    
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
        
        const container = document.getElementById('questions-container');
        const headerOffset = 60; // Approximate height of the toolbar and instruction banner
        const elementPosition = block.getBoundingClientRect().top;
        const offsetPosition = elementPosition + container.scrollTop - container.getBoundingClientRect().top - headerOffset - 20;
        
        container.scrollTo({ top: offsetPosition, behavior: "smooth" });
        updateQuestionToolbar(sectionNum);
    }, 50);
}

function navigateQuestion(direction) {
    const newIndex = currentQuestionIndex + direction;
    if (newIndex >= 0 && newIndex < TOTAL_QUESTIONS) {
        const targetQid = ALL_QUESTION_IDS[newIndex];
        const secData = SECTIONS_DATA.find(s => s.question_ids.includes(targetQid));
        if (secData && secData.number <= currentSection) {
            jumpToQuestion(targetQid, secData.number, document.getElementById(`chip-${targetQid}`));
        }
    }
}

function updateQuestionToolbar(sectionNum) {
    const qNum = currentQuestionIndex + 1;
    document.getElementById('current-question-display').textContent = qNum;
    document.getElementById('toolbar-part-display').textContent = sectionNum;
    
    // Load instruction for the question's section
    const secData = SECTIONS_DATA.find(s => s.number === sectionNum);
    if(secData) {
        document.getElementById('instruction-text').textContent = secData.instruction;
    }
    
    // Flag Button State
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
    if(!currentQuestionId) return;
    flaggedState[currentQuestionId] = !flaggedState[currentQuestionId];
    
    const chip = document.getElementById(`chip-${currentQuestionId}`);
    if (chip) {
        if (flaggedState[currentQuestionId]) chip.classList.add('flagged');
        else chip.classList.remove('flagged');
    }
    
    const secData = SECTIONS_DATA.find(s => s.question_ids.includes(currentQuestionId));
    if(secData) updateQuestionToolbar(secData.number);
    scheduleAutosave();
}

// ════════════════════════════════════════════════════
// SECTION UNLOCKING & COMPLETION
// ════════════════════════════════════════════════════
function requestNextSection() {
    if (!isSectionAudioDone) return;
    autosaveNow();

    fetch(COMPLETE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ section: currentSection }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'next') unlockSection(data.next_section);
        else if (data.status === 'transfer') startTransferPhase(data.transfer_seconds);
    });
}

function unlockSection(nextNum) {
    currentSection = nextNum;

    // Update left panel status
    const prevStatus = document.getElementById(`section-status-${nextNum-1}`);
    if (prevStatus) {
        prevStatus.className = 'flex items-center gap-3 text-sm rounded-lg p-3 transition-colors bg-slate-800 text-green-400 border border-slate-700/50';
        prevStatus.innerHTML = `<i class="fas fa-check-circle text-green-500 w-4 text-center"></i><span class="font-medium">Part ${nextNum-1}</span><span class="ml-auto text-xs text-green-500 font-bold uppercase tracking-wider">Done</span>`;
    }
    const curStatus = document.getElementById(`section-status-${nextNum}`);
    if (curStatus) {
        curStatus.className = 'flex items-center gap-3 text-sm rounded-lg p-3 transition-colors bg-blue-900/40 text-blue-300 border border-blue-800/50 shadow-inner';
        curStatus.innerHTML = `<div class="w-4 h-4 rounded-full bg-blue-500/20 flex items-center justify-center"><div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div></div><span class="font-medium">Part ${nextNum}</span><span class="ml-auto text-xs font-bold uppercase tracking-wider text-blue-400">Active</span>`;
    }

    // Refresh chips (unlock)
    const secData = SECTIONS_DATA.find(s => s.number === nextNum);
    if(secData) {
        secData.question_ids.forEach(qId => {
            const chip = document.getElementById(`chip-${qId}`);
            if(chip) {
                chip.classList.remove('locked', 'bg-slate-700', 'border-slate-600', 'text-slate-400');
                chip.classList.add('hover:bg-slate-600', 'hover:text-white', 'cursor-pointer', 'text-slate-300', 'bg-slate-700', 'border-slate-600');
                chip.setAttribute('onclick', `jumpToQuestion(${qId}, ${nextNum}, this)`);
                const lockIcon = chip.querySelector('.fa-lock');
                if(lockIcon) lockIcon.remove();
                chip.innerHTML += `<i class="fas fa-flag flag-icon"></i>`;
                
                // If it was somehow answered before or retrieved
                if(getAnswerValue(qId)) chip.classList.add('answered');
                if(flaggedState[qId]) chip.classList.add('flagged');
            }
        });
    }

    // Switch panels
    document.querySelectorAll('[id^="section-questions-"]').forEach(el => el.classList.add('hidden'));
    const target = document.getElementById(`section-questions-${nextNum}`);
    if (target) { target.classList.remove('hidden'); target.classList.add('fade-in'); }
    
    // Update toolbar if there's an active question. Let's select the first question of new section
    if(secData && secData.question_ids.length > 0) {
        jumpToQuestion(secData.question_ids[0], nextNum, document.getElementById(`chip-${secData.question_ids[0]}`));
    } else {
        document.getElementById('question-toolbar').classList.add('hidden');
    }

    isSectionAudioDone = false;
    document.getElementById('next-section-btn').classList.add('hidden');
    loadSectionAudio(nextNum);
}

// ════════════════════════════════════════════════════
// TRANSFER MODE / REVIEW OVERLAY
// ════════════════════════════════════════════════════
function startTransferPhase(seconds) {
    const totalSecs = seconds || 600;
    
    // Change timer text in header
    document.getElementById('timer-label').textContent = 'Transfer Time';
    document.getElementById('timer-value').style.color = '#f59e0b';
    
    let remaining = totalSecs;
    document.getElementById('transfer-countdown').textContent = formatTimer(remaining);
    
    // Hide audio-specific controls in left panel
    document.getElementById('play-pause-btn').classList.add('hidden');
    document.getElementById('waveform').classList.add('hidden');
    document.getElementById('current-part-label').textContent = "Transfer Phase";
    document.getElementById('current-part-desc').innerHTML = "<i class='fas fa-exclamation-triangle text-amber-500'></i> Audio has ended.";
    
    transferInterval = setInterval(() => {
        remaining--;
        document.getElementById('transfer-countdown').textContent = formatTimer(remaining);
        document.getElementById('timer-value').textContent = formatTimer(remaining);
        if (remaining <= 0) {
            clearInterval(transferInterval);
            document.getElementById('final-submit-form').submit();
        }
    }, 1000);
    
    // Prepare and show Review page
    showTransferReviewPage();
}

function showTransferReviewPage() {
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
        
        let btnClasses = "relative w-full aspect-square flex flex-col items-center justify-center font-bold text-sm rounded-xl border-2 transition-transform hover:scale-105 cursor-pointer shadow-sm";
        let iconHtml = "";
        
        if (isAns) btnClasses += " bg-blue-50 border-blue-500 text-blue-700";
        else btnClasses += " bg-white border-slate-200 text-slate-500";
        
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
    
    document.getElementById('review-answered-count').textContent = answeredCount;
    document.getElementById('review-unanswered-count').textContent = TOTAL_QUESTIONS - answeredCount;
    document.getElementById('review-flagged-count').textContent = flaggedCount;
    
    const warningEl = document.getElementById('unanswered-warning');
    if (answeredCount < TOTAL_QUESTIONS) {
        warningEl.classList.remove('hidden');
    } else {
        warningEl.classList.add('hidden');
    }
    
    const overlay = document.getElementById('transfer-overlay');
    overlay.classList.remove('hidden');
    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
    document.body.style.overflow = 'hidden';
}

function closeTransfer() {
    document.getElementById('transfer-overlay').classList.add('hidden');
    document.body.style.overflow = '';
}

function returnToQuestion(qId) {
    closeTransfer();
    const secData = SECTIONS_DATA.find(s => s.question_ids.includes(qId));
    if (secData) {
        jumpToQuestion(qId, secData.number, document.getElementById(`chip-${qId}`));
    }
}

// ════════════════════════════════════════════════════
// ANSWERS & AUTOSAVE
// ════════════════════════════════════════════════════
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

function markAnswered(qId) {
    const chip = document.getElementById(`chip-${qId}`);
    if (chip) {
        const val = getAnswerValue(qId);
        if (val !== '') chip.classList.add('answered');
        else chip.classList.remove('answered');
    }
    updateProgressTracker();
}

function updateProgressTracker() {
    let answeredCount = 0;
    ALL_QUESTION_IDS.forEach(id => {
        if (getAnswerValue(id) !== '') answeredCount++;
    });
    document.getElementById('progress-text').textContent = `${answeredCount} / ${TOTAL_QUESTIONS}`;
}

function scheduleAutosave() {
    if (autosavePending) return;
    autosavePending = true;
    autosaveTimer = setTimeout(autosaveNow, 5000);
}

function autosaveNow() {
    clearTimeout(autosaveTimer);
    autosavePending = false;
    const answersPayload = collectAllAnswers();
    
    if (Object.keys(answersPayload).length === 0 && Object.keys(flaggedState).length === 0) return;

    const ind = document.getElementById('autosave-status');
    ind.classList.remove('hidden');
    ind.innerHTML = '<i class="fas fa-circle-notch fa-spin text-yellow-400 text-[10px]"></i> <span class="tracking-wide">Saving...</span>';

    fetch(AUTOSAVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ answers: answersPayload, flagged: flaggedState }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            ind.innerHTML = `<i class="fas fa-check-circle text-green-400 text-[10px]"></i> <span class="tracking-wide text-slate-300">Saved</span>`;
            setTimeout(() => { if(!autosavePending) ind.classList.add('hidden'); }, 3000);
        }
    })
    .catch(() => ind.innerHTML = '<i class="fas fa-exclamation-circle text-red-400 text-[10px]"></i> <span class="text-red-400">Failed</span>');
}

setInterval(autosaveNow, 20000);

function collectAndSubmit(formId) {
    const form = document.getElementById(formId || 'submit-form');
    let container = form.querySelector('[id^="hidden-answers-container"]');
    container.innerHTML = '';
    const answers = collectAllAnswers();
    Object.entries(answers).forEach(([qId, val]) => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = `answers[${qId}]`;
        inp.value = val;
        container.appendChild(inp);
    });
    
    let flagContainer = form.querySelector('#hidden-flags-container');
    if(flagContainer) {
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
    }
    return true;
}

// ════════════════════════════════════════════════════
// TIMER & INIT
// ════════════════════════════════════════════════════
let elapsedSeconds = 0;
const timerEl = document.getElementById('timer-value');
setInterval(() => {
    elapsedSeconds++;
    timerEl.textContent = formatTimer(elapsedSeconds);
}, 1000);

function formatTime(s) {
    if (isNaN(s)) return '0:00';
    const mins = Math.floor(s / 60);
    const secs = Math.floor(s % 60).toString().padStart(2, '0');
    return `${mins}:${secs}`;
}

function formatTimer(s) {
    const m = Math.floor(Math.abs(s) / 60).toString().padStart(2, '0');
    const ss = (Math.abs(s) % 60).toString().padStart(2, '0');
    return `${m}:${ss}`;
}

document.addEventListener('DOMContentLoaded', () => {
    if (ATTEMPT_STATUS === 'transfer' && TRANSFER_REMAINING !== null) {
        startTransferPhase(TRANSFER_REMAINING);
        
        // Show questions panel for final part just in case they return
        document.querySelectorAll('[id^="section-questions-"]').forEach(el => el.classList.add('hidden'));
        if(SECTIONS_DATA.length > 0) {
            const finalPart = SECTIONS_DATA[SECTIONS_DATA.length - 1].number;
            const target = document.getElementById(`section-questions-${finalPart}`);
            if (target) { target.classList.remove('hidden'); target.classList.add('fade-in'); }
        }
    } else if (ATTEMPT_STATUS === 'completed') {
        window.location = '{{ route("dashboard") }}';
    } else {
        loadSectionAudio(currentSection);
    }

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
