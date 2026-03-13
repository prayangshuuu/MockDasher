<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reading Test – {{ $test->title }} | MockDasher</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap');

        body          { font-family: 'Inter', sans-serif; }
        .passage-text { font-family: 'Merriweather', serif; font-size: 14px; line-height: 1.85; }
        .passage-text p    { margin-bottom: 1rem; }
        .passage-text h3   { font-weight: 700; font-size: 1.05rem; margin: 1.25rem 0 0.5rem; }
        .passage-text ul   { list-style: disc; padding-left: 1.5rem; margin-bottom: 1rem; }

        /* Scrollbars */
        .scroll-panel::-webkit-scrollbar       { width: 7px; }
        .scroll-panel::-webkit-scrollbar-track { background: #f1f5f9; }
        .scroll-panel::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        /* Answer-sheet buttons */
        .ans-btn            { transition: all .15s; }
        .ans-btn.answered   { background: #2563eb; color: white; border-color: #2563eb; }
        .ans-btn.active-q   { outline: 2px solid #2563eb; outline-offset: 2px; }

        /* Active question highlight */
        .question-block.active-q { border-left: 3px solid #ea580c; background: #fff7ed; }

        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { animation: fadeIn .3s ease-out both; }

        /* Timer warning */
        .timer-warning { color: #ef4444; animation: pulse 1s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
    </style>
</head>
<body class="bg-slate-100 min-h-screen overflow-hidden">

{{-- ═══════════════════════════════════════════════
     TOP BAR
═══════════════════════════════════════════════ --}}
<div id="top-bar" class="fixed top-0 left-0 right-0 z-50 bg-slate-900 text-white shadow-lg h-12 flex items-center px-4 gap-4">

    {{-- Brand --}}
    <div class="flex items-center gap-2 flex-shrink-0">
        <div class="w-7 h-7 bg-orange-500 rounded flex items-center justify-center font-bold text-xs">MD</div>
        <span class="text-sm font-semibold hidden sm:block truncate max-w-[180px]">{{ $test->title }}</span>
    </div>

    {{-- Passage tabs --}}
    <div class="flex items-center gap-1 flex-1 justify-center">
        @foreach($passages as $p)
            <button id="tab-p{{ $p->passage_number }}"
                onclick="switchPassage({{ $p->passage_number }})"
                data-passage="{{ $p->passage_number }}"
                class="passage-tab px-3 py-1 rounded text-xs font-semibold transition
                    {{ $loop->first ? 'bg-orange-500 text-white' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                Passage {{ $p->passage_number }}
            </button>
        @endforeach
    </div>

    {{-- Timer --}}
    <div class="flex items-center gap-2 flex-shrink-0 bg-slate-800 rounded-lg px-3 py-1">
        <i class="fas fa-clock text-orange-400 text-xs"></i>
        <div>
            <p class="text-[10px] text-slate-400 leading-none">Time Left</p>
            <p id="timer-display" class="text-sm font-bold font-mono leading-tight text-white">60:00</p>
        </div>
    </div>

    {{-- Autosave indicator --}}
    <div id="autosave-indicator" class="hidden sm:flex items-center gap-1 text-xs text-slate-400 flex-shrink-0">
        <i class="fas fa-check-circle text-green-400 text-xs"></i> Saved
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MAIN LAYOUT (full viewport minus top bar)
═══════════════════════════════════════════════ --}}
<div class="flex mt-12" style="height: calc(100vh - 48px);">

    {{-- ─────────────────────────────────────
         LEFT PANEL: Passage
    ───────────────────────────────────── --}}
    <div class="w-1/2 xl:w-[52%] flex-shrink-0 flex flex-col border-r border-slate-200 bg-white">

        {{-- Passage header --}}
        <div id="passage-header" class="border-b border-slate-200 bg-slate-50 px-6 py-3">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-0.5">Reading Passage <span id="passage-num-label">1</span></p>
            <h2 id="passage-title-label" class="text-base font-bold text-slate-800 leading-snug">
                {{ $passages->first()->title }}
            </h2>
        </div>

        {{-- Passage content panels (one per passage, switched via JS) --}}
        <div class="flex-1 overflow-hidden relative">
            @foreach($passages as $p)
                <div id="passage-panel-{{ $p->passage_number }}"
                     class="passage-panel scroll-panel absolute inset-0 overflow-y-auto px-8 py-6 fade-in
                            {{ !$loop->first ? 'hidden' : '' }}">
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
    <div class="flex-1 flex flex-col bg-white overflow-hidden">

        {{-- Question navigation chips --}}
        <div class="border-b border-slate-200 bg-slate-50 px-4 py-2 flex items-center gap-2 flex-wrap">
            <span class="text-xs text-slate-500 font-semibold mr-1">Questions:</span>
            <div id="question-nav-chips" class="flex flex-wrap gap-1">
                @php $globalNum = 1; @endphp
                @foreach($passages as $p)
                    @foreach($p->questionGroups as $g)
                        @foreach($g->questions as $q)
                            <button id="chip-{{ $q->id }}"
                                onclick="jumpToQuestion({{ $q->id }}, {{ $p->passage_number }})"
                                class="ans-btn w-7 h-7 text-[11px] font-bold border border-slate-300 rounded hover:border-orange-400 hover:bg-orange-50
                                       bg-white text-slate-600 {{ !empty($savedAnswers[$q->id]) ? 'answered' : '' }}"
                                title="Q{{ $globalNum }}">
                                {{ $globalNum++ }}
                            </button>
                        @endforeach
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Question panels (one per passage) --}}
        <div class="flex-1 overflow-hidden relative">
            @foreach($passages as $p)
                <div id="questions-panel-{{ $p->passage_number }}"
                     class="scroll-panel absolute inset-0 overflow-y-auto px-5 py-4 fade-in
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
                                <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                    <p class="text-xs font-bold text-orange-700 uppercase tracking-wide mb-0.5">Instructions</p>
                                    <p class="text-sm text-orange-900 leading-relaxed">{{ $group->group_instruction }}</p>
                                </div>
                            @endif

                            {{-- Questions --}}
                            <div class="space-y-3 mb-6">
                                @foreach($group->questions as $qi => $question)
                                    @php $qNum = $qOffset + $qi; @endphp
                                    <div id="question-block-{{ $question->id }}"
                                         class="question-block border border-slate-200 rounded-lg p-4 transition-all"
                                         data-question-id="{{ $question->id }}">

                                        {{-- Q header --}}
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-start gap-2 flex-1">
                                                <span class="flex-shrink-0 w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5">
                                                    {{ $qNum }}
                                                </span>
                                                <p class="text-sm font-medium text-slate-800 leading-snug">
                                                    {!! nl2br(e($question->question_text)) !!}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Answer input by type --}}
                                        @if($question->question_type === 'multiple_choice')
                                            <div class="space-y-1.5 ml-8">
                                                @foreach($question->options as $oi => $opt)
                                                    <label class="flex items-center gap-2 cursor-pointer hover:bg-orange-50 rounded p-1.5 -mx-2 transition">
                                                        <input type="radio"
                                                            name="answer_{{ $question->id }}"
                                                            value="{{ $opt->option_text }}"
                                                            data-qid="{{ $question->id }}"
                                                            {{ ($savedAnswers[$question->id] ?? '') === $opt->option_text ? 'checked' : '' }}
                                                            onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                            class="h-4 w-4 text-orange-500 focus:ring-orange-400 border-gray-300 flex-shrink-0">
                                                        <span class="text-sm text-slate-700">
                                                            <strong class="text-orange-600 mr-1">{{ chr(65+$oi) }}.</strong>{{ $opt->option_text }}
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
                                            <div class="flex gap-2 ml-8 flex-wrap">
                                                @foreach($tfOptions as $opt)
                                                    <label class="flex items-center gap-1.5 cursor-pointer bg-slate-50 border border-slate-200 hover:border-orange-400 hover:bg-orange-50 rounded-lg px-3 py-1.5 transition text-sm font-medium text-slate-700">
                                                        <input type="radio"
                                                            name="answer_{{ $question->id }}"
                                                            value="{{ $opt }}"
                                                            data-qid="{{ $question->id }}"
                                                            {{ ($savedAnswers[$question->id] ?? '') === $opt ? 'checked' : '' }}
                                                            onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                            class="h-3.5 w-3.5 text-orange-500 focus:ring-orange-400 border-gray-300">
                                                        {{ $opt }}
                                                    </label>
                                                @endforeach
                                            </div>

                                        @elseif(in_array($question->question_type, ['matching_headings', 'matching_information', 'matching_sentence_endings']))
                                            <div class="ml-8">
                                                <select
                                                    id="answer-input-{{ $question->id }}"
                                                    data-qid="{{ $question->id }}"
                                                    onchange="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                    class="w-full max-w-xs border border-slate-300 rounded-md text-sm px-3 py-2 focus:ring-orange-400 focus:border-orange-400 bg-white">
                                                    <option value="">— Select a match —</option>
                                                    @foreach($question->options as $opt)
                                                        <option value="{{ $opt->option_text }}"
                                                            {{ ($savedAnswers[$question->id] ?? '') === $opt->option_text ? 'selected' : '' }}>
                                                            {{ $opt->option_text }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        @else
                                            {{-- sentence_completion, summary_completion, table_completion, flow_chart_completion, short_answer --}}
                                            <div class="ml-8">
                                                <input type="text"
                                                    id="answer-input-{{ $question->id }}"
                                                    data-qid="{{ $question->id }}"
                                                    value="{{ $savedAnswers[$question->id] ?? '' }}"
                                                    placeholder="Write your answer (ONE WORD AND/OR A NUMBER)"
                                                    oninput="markAnswered({{ $question->id }}); scheduleAutosave();"
                                                    class="w-full max-w-sm border-b-2 border-slate-300 focus:border-orange-500 bg-slate-50 focus:bg-white px-3 py-2 text-sm rounded-t transition outline-none">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @php $qOffset += $group->questions->count(); @endphp
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Bottom bar: passage nav + submit --}}
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-2.5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                @foreach($passages as $p)
                    <button onclick="switchPassage({{ $p->passage_number }})"
                        class="passage-bottom-btn text-xs font-semibold px-3 py-1.5 rounded border transition
                               {{ $loop->first ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-slate-600 border-slate-300 hover:border-orange-400 hover:text-orange-600' }}">
                        Passage {{ $p->passage_number }}
                    </button>
                @endforeach
            </div>

            <form id="submit-form" action="{{ route('user.reading.submit', $attempt->id) }}" method="POST" onsubmit="return collectAndSubmit();">
                @csrf
                <div id="hidden-answers-container"></div>
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Submit Test
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
const ALL_QUESTION_IDS = {!! json_encode($passages->flatMap(fn($p) => $p->questionGroups->flatMap(fn($g) => $g->questions->pluck('id')))) !!};
const PASSAGES_DATA = {!! json_encode($passages->map(fn($p) => ['number' => $p->passage_number, 'title' => $p->title])) !!};
</script>

<script>
// ─────────────────────────────── STATE
let currentPassage = 1;
let autosavePending = false;
let autosaveTimer = null;

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

    // Update header
    const passData = PASSAGES_DATA.find(p => p.number === num);
    if (passData) {
        document.getElementById('passage-num-label').textContent = num;
        document.getElementById('passage-title-label').textContent = passData.title;
    }

    // Update tabs
    document.querySelectorAll('.passage-tab').forEach(tab => {
        const isActive = parseInt(tab.dataset.passage) === num;
        tab.className = tab.className
            .replace('bg-orange-500 text-white', '')
            .replace('bg-slate-700 text-slate-300 hover:bg-slate-600', '')
            .trim();
        tab.className += isActive
            ? ' bg-orange-500 text-white'
            : ' bg-slate-700 text-slate-300 hover:bg-slate-600';
    });

    document.querySelectorAll('.passage-bottom-btn').forEach((btn, i) => {
        const isActive = (i + 1) === num;
        btn.className = btn.className
            .replace('bg-orange-500 text-white border-orange-500', '')
            .replace('bg-white text-slate-600 border-slate-300 hover:border-orange-400 hover:text-orange-600', '')
            .trim();
        btn.className += isActive
            ? ' bg-orange-500 text-white border-orange-500'
            : ' bg-white text-slate-600 border-slate-300 hover:border-orange-400 hover:text-orange-600';
    });
}

// ─────────────────────────────── QUESTION NAVIGATION
function jumpToQuestion(qId, passageNum) {
    if (passageNum !== currentPassage) switchPassage(passageNum);
    setTimeout(() => {
        const block = document.getElementById(`question-block-${qId}`);
        if (!block) return;
        document.querySelectorAll('.question-block').forEach(b => b.classList.remove('active-q'));
        block.classList.add('active-q');
        block.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);
}

// ─────────────────────────────── ANSWERED STATE
function markAnswered(qId) {
    const chip = document.getElementById(`chip-${qId}`);
    if (chip) {
        chip.classList.add('answered');
        chip.classList.remove('bg-white', 'text-slate-600');
    }
}

// ─────────────────────────────── ANSWER COLLECTION
function getAnswerValue(qId) {
    const radio = document.querySelector(`input[name="answer_${qId}"]:checked`);
    if (radio) return radio.value;
    const inp = document.getElementById(`answer-input-${qId}`);
    if (inp) return inp.value;
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
    autosaveTimer = setTimeout(autosaveNow, 8000);
}

function autosaveNow() {
    clearTimeout(autosaveTimer);
    autosavePending = false;
    const payload = collectAllAnswers();
    if (Object.keys(payload).length === 0) return;

    const indicator = document.getElementById('autosave-indicator');
    indicator.innerHTML = '<i class="fas fa-circle-notch fa-spin text-yellow-400 text-xs"></i> Saving…';

    fetch(AUTOSAVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ answers: payload }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            indicator.innerHTML = `<i class="fas fa-check-circle text-green-400 text-xs"></i> Saved ${data.saved_at}`;
        }
    })
    .catch(() => {
        indicator.innerHTML = '<i class="fas fa-exclamation-circle text-red-400 text-xs"></i> Save failed';
    });
}

// Periodic autosave every 10s
setInterval(autosaveNow, 10000);

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
    return true;
}

// ─────────────────────────────── INIT
document.addEventListener('DOMContentLoaded', () => {
    // Pre-mark already answered chips
    ALL_QUESTION_IDS.forEach(id => {
        if (SAVED_ANSWERS[id]) markAnswered(id);
    });

    // Pre-populate inputs from saved answers
    ALL_QUESTION_IDS.forEach(id => {
        const saved = SAVED_ANSWERS[id];
        if (!saved) return;
        const inp = document.getElementById(`answer-input-${id}`);
        if (inp) inp.value = saved;
        const radio = document.querySelector(`input[name="answer_${id}"][value="${CSS.escape ? CSS.escape(saved) : saved}"]`);
        if (radio) radio.checked = true;
    });
});
</script>

</body>
</html>
