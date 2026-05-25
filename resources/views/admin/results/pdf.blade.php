<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>IELTS Result — #{{ $result->id }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page {
        size: A4;
        margin: 16mm 14mm 20mm 14mm;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 9.5pt;
        color: #1e293b;
        line-height: 1.5;
        background: #fff;
    }

    /* ── Page Footer ─────────────────────────────────────────────────────── */
    .page-footer {
        position: fixed;
        bottom: -14mm;
        left: 0; right: 0;
        height: 12mm;
        border-top: 1px solid #e2e8f0;
        text-align: center;
        font-size: 7.5pt;
        color: #94a3b8;
        padding-top: 3mm;
    }

    /* ── Header ──────────────────────────────────────────────────────────── */
    .report-header {
        border-bottom: 2px solid #4F46E5;
        padding-bottom: 10px;
        margin-bottom: 14px;
    }
    .header-table { width: 100%; border-collapse: collapse; }
    .header-logo { width: 44px; height: 44px; }
    .header-brand { font-size: 18pt; font-weight: 900; color: #4F46E5; letter-spacing: -0.5px; vertical-align: middle; padding-left: 8px; }
    .header-right { text-align: right; vertical-align: bottom; }
    .header-title { font-size: 13pt; font-weight: 700; color: #1e293b; }
    .header-sub { font-size: 8pt; color: #64748b; margin-top: 2px; }

    /* ── Section headings ────────────────────────────────────────────────── */
    .section-heading {
        font-size: 9pt;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #4F46E5;
        border-bottom: 1px solid #e0e7ff;
        padding-bottom: 4px;
        margin-bottom: 10px;
        margin-top: 18px;
    }

    /* ── Info grid ───────────────────────────────────────────────────────── */
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
    .info-table td { padding: 4px 6px; font-size: 9pt; vertical-align: top; }
    .info-label { color: #64748b; font-weight: 700; width: 34%; white-space: nowrap; }
    .info-value { color: #0f172a; font-weight: 400; }
    .info-block { width: 50%; vertical-align: top; }

    /* ── Overall Band ─────────────────────────────────────────────────────── */
    .overall-wrap {
        text-align: center;
        margin: 14px 0 10px;
    }
    .overall-box {
        display: inline-block;
        background: #4F46E5;
        color: #fff;
        border-radius: 12px;
        padding: 10px 28px 8px;
        min-width: 120px;
    }
    .overall-label {
        font-size: 7.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        opacity: 0.85;
        display: block;
        margin-bottom: 2px;
    }
    .overall-score {
        font-size: 36pt;
        font-weight: 900;
        line-height: 1;
        display: block;
    }
    .overall-na {
        font-size: 16pt;
        font-weight: 900;
        line-height: 1.3;
    }
    .overall-note {
        font-size: 7.5pt;
        color: #64748b;
        margin-top: 5px;
    }

    /* ── Module score cards ──────────────────────────────────────────────── */
    .module-table { width: 100%; border-collapse: separate; border-spacing: 5px; }
    .module-cell {
        width: 25%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        padding: 10px 4px;
    }
    .module-name {
        font-size: 7.5pt;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        display: block;
        margin-bottom: 3px;
    }
    .module-band {
        font-size: 18pt;
        font-weight: 900;
        line-height: 1;
        display: block;
    }
    .band-high  { color: #10b981; }
    .band-mid   { color: #f59e0b; }
    .band-low   { color: #ef4444; }
    .band-na    { color: #94a3b8; }

    /* ── Task card ───────────────────────────────────────────────────────── */
    .task-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 14px;
        page-break-inside: avoid;
        overflow: hidden;
    }
    .task-card-header {
        background: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        padding: 7px 12px;
    }
    .task-card-title {
        font-size: 9.5pt;
        font-weight: 900;
        color: #0f172a;
        display: inline;
    }
    .task-card-meta {
        font-size: 8pt;
        color: #64748b;
        float: right;
    }
    .task-card-body {
        padding: 10px 12px;
    }
    .answer-text {
        font-size: 8.5pt;
        color: #334155;
        line-height: 1.6;
        white-space: pre-wrap;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 10px;
        max-height: 160pt;
        overflow: hidden;
    }

    /* ── Evaluation section ──────────────────────────────────────────────── */
    .eval-wrap {
        margin-top: 10px;
        border-top: 1px dashed #e2e8f0;
        padding-top: 10px;
    }
    .eval-header-row { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    .eval-heading {
        font-size: 8pt;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #4F46E5;
    }
    .eval-band-badge {
        float: right;
        font-size: 8pt;
        font-weight: 900;
        padding: 2px 10px;
        border-radius: 20px;
        background: #4F46E5;
        color: #fff;
    }

    /* ── Criteria table ──────────────────────────────────────────────────── */
    .criteria-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .criteria-table td {
        padding: 5px 8px;
        font-size: 8.5pt;
        border-bottom: 1px solid #f1f5f9;
    }
    .criteria-label { color: #475569; font-weight: 700; }
    .criteria-score { font-weight: 900; text-align: center; width: 40px; }

    /* ── Feedback boxes ──────────────────────────────────────────────────── */
    .fb-box {
        border-radius: 6px;
        padding: 7px 10px;
        margin-bottom: 7px;
        font-size: 8.5pt;
    }
    .fb-label {
        font-size: 7pt;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .fb-indigo { background: #eef2ff; border-left: 3px solid #6366f1; }
    .fb-indigo .fb-label { color: #4338ca; }
    .fb-rose   { background: #fff1f2; border-left: 3px solid #f43f5e; }
    .fb-rose .fb-label   { color: #be123c; }
    .fb-violet { background: #f5f3ff; border-left: 3px solid #8b5cf6; }
    .fb-violet .fb-label { color: #6d28d9; }
    .fb-emerald { background: #ecfdf5; border-left: 3px solid #10b981; }
    .fb-emerald .fb-label { color: #065f46; }

    /* ── Corrections ─────────────────────────────────────────────────────── */
    .correction-row { margin-bottom: 4px; font-size: 8pt; }
    .wrong { color: #be123c; text-decoration: line-through; }
    .right { color: #065f46; font-weight: 700; }
    .arrow { color: #94a3b8; padding: 0 4px; }

    /* ── Speaking Q cards ────────────────────────────────────────────────── */
    .speaking-q-header {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px 6px 0 0;
        border-bottom: none;
        padding: 6px 12px;
    }
    .speaking-q-body {
        border: 1px solid #e2e8f0;
        border-radius: 0 0 6px 6px;
        padding: 8px 12px;
        margin-bottom: 12px;
        page-break-inside: avoid;
    }
    .q-label { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #4F46E5; }
    .q-text  { font-size: 9pt; font-weight: 700; color: #0f172a; }
    .a-label { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #10b981; margin-top: 6px; }
    .a-text  { font-size: 8.5pt; color: #334155; font-style: italic; line-height: 1.55; }

    /* ── Pending badge ───────────────────────────────────────────────────── */
    .pending-badge {
        display: inline-block;
        background: #faf5ff;
        border: 1px solid #e9d5ff;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 7.5pt;
        font-weight: 700;
        color: #7c3aed;
        margin-top: 6px;
    }

    .clearfix::after { content: ""; display: table; clear: both; }
    .mt8 { margin-top: 8px; }
    .no-data { color: #94a3b8; font-style: italic; font-size: 8.5pt; }
</style>
</head>
<body>

{{-- ── Page Footer ─────────────────────────────────────────────────────────── --}}
<div class="page-footer">
    MockDasher &mdash; AI-Powered IELTS Preparation Platform &nbsp;&bull;&nbsp; Generated {{ now()->format('d M Y, H:i') }} &nbsp;&bull;&nbsp; Attempt #{{ $result->id }}
</div>

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="report-header">
    <table class="header-table">
        <tr>
            <td style="vertical-align:middle; width:56px;">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" class="header-logo" alt="MockDasher" />
                @endif
            </td>
            <td style="vertical-align:middle;">
                <span class="header-brand">MockDasher</span>
            </td>
            <td class="header-right">
                <div class="header-title">IELTS Mock Test Report</div>
                <div class="header-sub">Generated {{ now()->format('d F Y') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── Candidate & Test Info ────────────────────────────────────────────────── --}}
<div class="section-heading" style="margin-top:0;">Candidate Information</div>
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td class="info-block">
            <table class="info-table">
                <tr>
                    <td class="info-label">Candidate</td>
                    <td class="info-value">{{ optional($result->user)->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Email</td>
                    <td class="info-value">{{ optional($result->user)->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Test</td>
                    <td class="info-value">{{ optional($result->test)->title ?? optional($result->testSet)->name ?? 'IELTS Mock Test' }}</td>
                </tr>
            </table>
        </td>
        <td class="info-block">
            <table class="info-table">
                <tr>
                    <td class="info-label">Attempt ID</td>
                    <td class="info-value">#{{ $result->id }}</td>
                </tr>
                <tr>
                    <td class="info-label">Started</td>
                    <td class="info-value">{{ $result->started_at ? $result->started_at->format('d M Y, H:i') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Completed</td>
                    <td class="info-value">
                        {{ $result->completed_at ? $result->completed_at->format('d M Y, H:i') : 'In Progress' }}
                        @if($result->time_spent)
                            <span style="color:#64748b;">({{ $result->time_spent }})</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Violations</td>
                    <td class="info-value">{{ $result->proctoring_violations ?? 0 }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ── Overall Band Score ───────────────────────────────────────────────────── --}}
<div class="overall-wrap">
    @php $overall = $result->overall_band; @endphp
    <div class="overall-box">
        <span class="overall-label">Overall Band Score</span>
        @if($overall !== null)
            <span class="overall-score">{{ number_format($overall, 1) }}</span>
        @else
            <span class="overall-na">N/A</span>
        @endif
    </div>
    @if($overall === null)
        <div class="overall-note">Overall band requires all 4 modules to be completed and evaluated.</div>
    @endif
</div>

{{-- ── Module Breakdown ────────────────────────────────────────────────────── --}}
<div class="section-heading">Module Performance</div>
@php
    $modules = [
        ['name' => 'Listening', 'band' => $result->listening_band],
        ['name' => 'Reading',   'band' => $result->reading_band],
        ['name' => 'Writing',   'band' => $result->writing_band],
        ['name' => 'Speaking',  'band' => $result->speaking_band],
    ];
    $bandClass = function($b) {
        if ($b === null) return 'band-na';
        if ($b >= 7) return 'band-high';
        if ($b >= 5.5) return 'band-mid';
        return 'band-low';
    };
@endphp
<table class="module-table">
    <tr>
        @foreach($modules as $mod)
        <td class="module-cell">
            <span class="module-name">{{ $mod['name'] }}</span>
            <span class="module-band {{ $bandClass($mod['band']) }}">
                {{ $mod['band'] !== null ? number_format($mod['band'], 1) : '—' }}
            </span>
        </td>
        @endforeach
    </tr>
</table>

@php
    $ra = $result->readingAttempt;
    $la = $result->listeningAttempt;
@endphp
@if(($ra && $ra->score !== null) || ($la && $la->score !== null))
<table style="width:100%; border-collapse:collapse; margin-top:6px;">
    <tr>
        @if($la && $la->score !== null)
        <td style="width:50%; font-size:8pt; color:#64748b; padding:2px 4px;">
            Listening raw score: <strong style="color:#0f172a;">{{ $la->score }}/40</strong>
        </td>
        @endif
        @if($ra && $ra->score !== null)
        <td style="width:50%; font-size:8pt; color:#64748b; padding:2px 4px;">
            Reading raw score: <strong style="color:#0f172a;">{{ $ra->score }}/40</strong>
        </td>
        @endif
    </tr>
</table>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- ── Writing Tasks ──────────────────────────────────────────────────────── --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
@if($result->writingAnswers && $result->writingAnswers->count() > 0)

<div class="section-heading" style="page-break-before:always;">Writing Module</div>

@php
    $writingEvalTop = null;
    if ($result->aiWritingEvaluation && $result->aiWritingEvaluation->evaluation_text) {
        $writingEvalTop = json_decode($result->aiWritingEvaluation->evaluation_text, true);
    }
@endphp

@foreach($result->writingAnswers as $wIdx => $wAnswer)
@php
    $taskNumber = optional($wAnswer->writingTask)->task_number ?? ($wIdx + 1);
    $taskTitle  = optional($wAnswer->writingTask)->task_title ?? "Writing Task {$taskNumber}";
    $wordCount  = str_word_count($wAnswer->answer_text ?? '');

    $wEval = $wAnswer->evaluation_json ? json_decode($wAnswer->evaluation_json, true) : null;
    if (!$wEval && $writingEvalTop) {
        $wEval = $writingEvalTop["task_{$taskNumber}"] ?? null;
    }
    $wBand = $wAnswer->band_score ?? ($wEval ? ($wEval['overall_band_score'] ?? $wEval['band_score'] ?? null) : null);

    $wIsNew = isset($wEval['criteria_scores']);
    $wCs    = $wEval['criteria_scores'] ?? [];

    if ($wIsNew) {
        $wCriteria = [
            ['label' => $taskNumber === 1 ? 'Task Achievement' : 'Task Response',
             'score' => $wCs['task_achievement_or_response'] ?? null],
            ['label' => 'Coherence & Cohesion',
             'score' => $wCs['coherence_and_cohesion'] ?? null],
            ['label' => 'Lexical Resource',
             'score' => $wCs['lexical_resource'] ?? null],
            ['label' => 'Grammatical Range & Accuracy',
             'score' => $wCs['grammatical_range_and_accuracy'] ?? null],
        ];
    } elseif ($wEval) {
        $firstKey = $taskNumber === 1 ? 'task_achievement' : 'task_response';
        $firstLbl = $taskNumber === 1 ? 'Task Achievement' : 'Task Response';
        $wCriteria = [
            ['label' => $firstLbl,                      'score' => ($wEval[$firstKey] ?? [])['score'] ?? null],
            ['label' => 'Coherence & Cohesion',         'score' => ($wEval['coherence_cohesion'] ?? [])['score'] ?? null],
            ['label' => 'Lexical Resource',             'score' => ($wEval['lexical_resource'] ?? [])['score'] ?? null],
            ['label' => 'Grammatical Range & Accuracy', 'score' => ($wEval['grammatical_range_accuracy'] ?? [])['score'] ?? null],
        ];
    } else {
        $wCriteria = [];
    }

    $bClass = function($s) { return $s === null ? 'band-na' : ($s >= 7 ? 'band-high' : ($s >= 5.5 ? 'band-mid' : 'band-low')); };
@endphp

<div class="task-card">
    <div class="task-card-header clearfix">
        <span class="task-card-title">Task {{ $taskNumber }}: {{ $taskTitle }}</span>
        <span class="task-card-meta">{{ $wordCount }} words</span>
    </div>
    <div class="task-card-body">

        {{-- Answer Text --}}
        <div style="font-size:7.5pt; font-weight:900; text-transform:uppercase; color:#64748b; letter-spacing:0.07em; margin-bottom:4px;">
            Candidate's Response
        </div>
        <div class="answer-text">{{ $wAnswer->answer_text ?? 'No response submitted.' }}</div>

        {{-- AI Evaluation --}}
        @if($wEval && $wBand)
        <div class="eval-wrap">
            <div class="clearfix" style="margin-bottom:8px;">
                <span class="eval-heading">AI Examiner Report</span>
                <span class="eval-band-badge {{ $wBand >= 7 ? 'band-high' : ($wBand >= 5.5 ? 'band-mid' : 'band-low') }}"
                      style="{{ $wBand >= 7 ? 'background:#10b981' : ($wBand >= 5.5 ? 'background:#f59e0b' : 'background:#ef4444') }}">
                    Band {{ number_format($wBand, 1) }}
                </span>
            </div>

            {{-- Criteria Scores --}}
            @if(!empty($wCriteria))
            <table class="criteria-table">
                @foreach($wCriteria as $crit)
                <tr>
                    <td class="criteria-label">{{ $crit['label'] }}</td>
                    <td class="criteria-score {{ $bClass($crit['score']) }}">
                        {{ $crit['score'] !== null ? number_format((float)$crit['score'], 1) : '—' }}
                    </td>
                </tr>
                @endforeach
            </table>
            @endif

            {{-- Detailed Feedback --}}
            @php
                $wFeedback = $wIsNew
                    ? ($wEval['detailed_feedback'] ?? null)
                    : ($wEval['overall_review'] ?? null);
            @endphp
            @if($wFeedback)
            <div class="fb-box fb-indigo">
                <div class="fb-label">Examiner Feedback</div>
                <div>{{ $wFeedback }}</div>
            </div>
            @endif

            {{-- Vocab Corrections --}}
            @if(!empty($wEval['vocabulary_corrections']))
            <div class="fb-box fb-violet">
                <div class="fb-label">Vocabulary Improvements</div>
                @foreach(array_slice($wEval['vocabulary_corrections'], 0, 8) as $vc)
                <div class="correction-row">
                    <span class="wrong">{{ $vc['incorrect'] ?? '' }}</span>
                    <span class="arrow">→</span>
                    <span class="right">{{ $vc['suggested'] ?? '' }}</span>
                </div>
                @endforeach
                @if(count($wEval['vocabulary_corrections']) > 8)
                    <div style="font-size:7.5pt; color:#7c3aed; margin-top:3px;">+{{ count($wEval['vocabulary_corrections']) - 8 }} more…</div>
                @endif
            </div>
            @endif

            {{-- Grammar Corrections --}}
            @if(!empty($wEval['grammar_corrections']))
            <div class="fb-box fb-rose">
                <div class="fb-label">Grammar Corrections</div>
                @foreach(array_slice($wEval['grammar_corrections'], 0, 6) as $gc)
                <div class="correction-row">
                    <span class="wrong">{{ $gc['incorrect'] ?? '' }}</span>
                    <span class="arrow">→</span>
                    <span class="right">{{ $gc['suggested'] ?? '' }}</span>
                </div>
                @endforeach
                @if(count($wEval['grammar_corrections']) > 6)
                    <div style="font-size:7.5pt; color:#be123c; margin-top:3px;">+{{ count($wEval['grammar_corrections']) - 6 }} more…</div>
                @endif
            </div>
            @endif

            {{-- Suggestions / Improved Version --}}
            @php
                $wSuggestions = $wIsNew
                    ? ($wEval['suggestions_for_improvement'] ?? null)
                    : ($wEval['improved_version'] ?? null);
                $wSugLabel = $wIsNew ? 'Suggestions for Improvement' : 'Improved Version (excerpt)';
            @endphp
            @if($wSuggestions)
            <div class="fb-box fb-emerald">
                <div class="fb-label">{{ $wSugLabel }}</div>
                <div>{{ mb_substr($wSuggestions, 0, 600) }}{{ mb_strlen($wSuggestions) > 600 ? '…' : '' }}</div>
            </div>
            @endif
        </div>
        @else
            <div class="pending-badge">AI Evaluation Pending</div>
        @endif

    </div>
</div>
@endforeach
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- ── Speaking Questions ──────────────────────────────────────────────────── --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
@php
    $speakingEvals   = [];
    $speakingAnswers = collect();
    if ($result->aiSpeakingEvaluation && $result->aiSpeakingEvaluation->evaluation_json) {
        $speakingEvals   = json_decode($result->aiSpeakingEvaluation->evaluation_json, true) ?: [];
        $speakingAnswers = $result->speakingAnswers->keyBy('speaking_question_id');
    }
@endphp

@if(count($speakingEvals) > 0)

<div class="section-heading" style="page-break-before:always;">Speaking Module</div>

@foreach($speakingEvals as $sIdx => $se)
@php
    $sAns    = $speakingAnswers->get($se['question_id'] ?? null);
    $sBand   = $se['band_score'] ?? null;
    $sEval   = $se['evaluation'] ?? [];
    $sIsNew  = isset($sEval['criteria_scores']);
    $sCs     = $sEval['criteria_scores'] ?? [];

    if ($sIsNew) {
        $sCriteria = [
            ['label' => 'Fluency & Coherence',          'score' => $sCs['fluency_and_coherence'] ?? null],
            ['label' => 'Lexical Resource',             'score' => $sCs['lexical_resource'] ?? null],
            ['label' => 'Grammatical Range & Accuracy', 'score' => $sCs['grammatical_range_and_accuracy'] ?? null],
            ['label' => 'Pronunciation (estimated)',    'score' => $sCs['pronunciation'] ?? null],
        ];
    } elseif (!empty($sEval)) {
        $sCriteria = [
            ['label' => 'Fluency & Coherence',          'score' => ($sEval['fluency_coherence'] ?? [])['score'] ?? null],
            ['label' => 'Lexical Resource',             'score' => ($sEval['lexical_resource'] ?? [])['score'] ?? null],
            ['label' => 'Grammatical Range & Accuracy', 'score' => ($sEval['grammatical_range_accuracy'] ?? [])['score'] ?? null],
            ['label' => 'Pronunciation (estimated)',    'score' => ($sEval['pronunciation'] ?? [])['score'] ?? null],
        ];
    } else {
        $sCriteria = [];
    }
@endphp

<div style="margin-bottom:14px; page-break-inside:avoid;">
    <div class="speaking-q-header clearfix">
        <span style="font-size:8pt; font-weight:900; color:#4F46E5;">Part {{ $se['part'] ?? 1 }} &mdash; Question {{ $sIdx + 1 }}</span>
        @if($sBand !== null)
        <span style="float:right; font-size:8pt; font-weight:900; {{ $sBand >= 7 ? 'color:#10b981' : ($sBand >= 5.5 ? 'color:#f59e0b' : 'color:#ef4444') }}">
            Band {{ number_format((float)$sBand, 1) }}
        </span>
        @endif
    </div>
    <div class="speaking-q-body">

        {{-- Question --}}
        <div class="q-label">Prompt</div>
        <div class="q-text">{{ $se['question'] ?? '' }}</div>

        {{-- Transcript --}}
        <div class="a-label">Candidate's Response</div>
        <div class="a-text">"{{ $sAns?->transcript_text ?: 'No speech transcript recorded.' }}"</div>

        {{-- Evaluation --}}
        @if(!empty($sEval) && $sBand !== null)
        <div class="eval-wrap">
            <div class="eval-heading" style="margin-bottom:7px;">AI Examiner Assessment</div>

            @if(!empty($sCriteria))
            <table class="criteria-table">
                @foreach($sCriteria as $sc)
                <tr>
                    <td class="criteria-label">{{ $sc['label'] }}</td>
                    <td class="criteria-score {{ $sc['score'] === null ? 'band-na' : ($sc['score'] >= 7 ? 'band-high' : ($sc['score'] >= 5.5 ? 'band-mid' : 'band-low')) }}">
                        {{ $sc['score'] !== null ? number_format((float)$sc['score'], 1) : '—' }}
                    </td>
                </tr>
                @endforeach
            </table>
            @endif

            @php
                $sFeedback = $sIsNew
                    ? ($sEval['detailed_feedback'] ?? null)
                    : ($sEval['overall_feedback'] ?? null);
            @endphp
            @if($sFeedback)
            <div class="fb-box fb-indigo">
                <div class="fb-label">Detailed Feedback</div>
                <div>{{ $sFeedback }}</div>
            </div>
            @endif

            @if(!empty($sEval['vocabulary_corrections']))
            <div class="fb-box fb-violet">
                <div class="fb-label">Vocabulary Improvements</div>
                @foreach(array_slice($sEval['vocabulary_corrections'], 0, 6) as $vc)
                <div class="correction-row">
                    <span class="wrong">{{ $vc['incorrect'] ?? '' }}</span>
                    <span class="arrow">→</span>
                    <span class="right">{{ $vc['suggested'] ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(!empty($sEval['grammar_corrections']))
            <div class="fb-box fb-rose">
                <div class="fb-label">Grammar Corrections</div>
                @foreach(array_slice($sEval['grammar_corrections'], 0, 5) as $gc)
                <div class="correction-row">
                    <span class="wrong">{{ $gc['incorrect'] ?? '' }}</span>
                    <span class="arrow">→</span>
                    <span class="right">{{ $gc['suggested'] ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(!empty($sEval['suggestions_for_improvement']))
            <div class="fb-box fb-emerald">
                <div class="fb-label">Suggestions for Improvement</div>
                <div>{{ mb_substr($sEval['suggestions_for_improvement'], 0, 400) }}{{ mb_strlen($sEval['suggestions_for_improvement']) > 400 ? '…' : '' }}</div>
            </div>
            @endif
        </div>
        @endif

    </div>
</div>
@endforeach
@endif

{{-- ── Final Footer Strip ────────────────────────────────────────────────────── --}}
<div style="margin-top:20px; padding-top:10px; border-top:1px solid #e2e8f0; text-align:center; font-size:7.5pt; color:#94a3b8;">
    This report was generated by <strong>MockDasher</strong>, an AI-powered IELTS preparation platform.<br>
    Band scores are produced by the Gemini AI Examiner and are for practice purposes only. They do not constitute official IELTS results.
</div>

</body>
</html>
