<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>My IELTS Practice Report — #{{ $attempt->id }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }

@page {
    size: A4;
    margin: 0mm 0mm 18mm 0mm;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #1e293b;
    line-height: 1.5;
    background: #fff;
}

/* ── Fixed footer ─────────────────────────────────────────────────────── */
.page-footer {
    position: fixed;
    bottom: -14mm;
    left: 0; right: 0;
    height: 13mm;
    background: #f8fafc;
    border-top: 2px solid #e2e8f0;
    padding: 0 14mm;
}
.footer-inner { height:100%; width:100%; border-collapse:collapse; }
.footer-inner td { vertical-align:middle; font-size:7pt; color:#94a3b8; }
.footer-right { text-align:right; }

/* ── Accent strip ─────────────────────────────────────────────────────── */
.cover-strip { background: linear-gradient(90deg, #4F46E5, #7c3aed); height:8px; width:100%; }

.page-wrap { padding:12mm 14mm 6mm 14mm; }

/* ── Header ──────────────────────────────────────────────────────────── */
.header-table { width:100%; border-collapse:collapse; margin-bottom:14px; }
.brand-logo { width:40px; height:40px; vertical-align:middle; }
.brand-name { font-size:20pt; font-weight:900; color:#4F46E5; letter-spacing:-0.5px; vertical-align:middle; padding-left:8px; }
.report-title { font-size:14pt; font-weight:900; color:#0f172a; }
.report-subtitle { font-size:7.5pt; color:#64748b; margin-top:2px; }
.header-divider { border:none; border-top:2px solid #4F46E5; margin-bottom:16px; }

/* ── Section headings ────────────────────────────────────────────────── */
.section-heading {
    font-size:8.5pt; font-weight:900; text-transform:uppercase;
    letter-spacing:0.09em; color:#fff; background:#4F46E5;
    padding:5px 12px; border-radius:4px;
    margin-bottom:12px; margin-top:20px; display:block;
}

/* ── Candidate box ───────────────────────────────────────────────────── */
.candidate-wrap { border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; margin-bottom:18px; }
.candidate-header { background:#f1f5f9; border-bottom:1px solid #e2e8f0; padding:7px 14px; font-size:8pt; font-weight:900; text-transform:uppercase; letter-spacing:0.08em; color:#475569; }
.candidate-body { padding:12px 14px; }
.info-table { width:100%; border-collapse:collapse; }
.info-table td { padding:3px 8px 3px 0; font-size:8.5pt; vertical-align:top; }
.info-label { color:#64748b; font-weight:700; width:120px; white-space:nowrap; }
.info-value { color:#0f172a; font-weight:600; }
.info-col { width:50%; vertical-align:top; padding-right:10px; }

.avatar { width:52px; height:52px; border-radius:50%; background:#4F46E5; color:#fff; font-size:18pt; font-weight:900; text-align:center; line-height:52px; display:inline-block; margin-right:14px; vertical-align:middle; }

/* ── Overall Score ───────────────────────────────────────────────────── */
.overall-section { text-align:center; margin:16px 0 12px; }
.overall-outer { display:inline-block; border:3px solid #4F46E5; border-radius:14px; overflow:hidden; }
.overall-top { background:#4F46E5; padding:6px 40px 4px; }
.overall-label { font-size:7pt; font-weight:900; text-transform:uppercase; letter-spacing:0.15em; color:rgba(255,255,255,0.8); display:block; }
.overall-score { font-size:44pt; font-weight:900; line-height:1; color:#fff; display:block; letter-spacing:-1px; }
.overall-bottom { background:#fff; padding:5px 16px 6px; }
.cefr-badge { display:inline-block; font-size:8.5pt; font-weight:900; color:#4F46E5; letter-spacing:0.03em; }
.cefr-level { font-size:7.5pt; color:#64748b; margin-left:6px; font-weight:600; }

/* ── Module Grid ─────────────────────────────────────────────────────── */
.module-table { width:100%; border-collapse:separate; border-spacing:6px; margin:10px 0; }
.module-cell { width:25%; border:1.5px solid #e2e8f0; border-radius:10px; text-align:center; padding:12px 6px 10px; background:#fff; }
.module-name { font-size:7pt; font-weight:900; text-transform:uppercase; letter-spacing:0.1em; color:#64748b; display:block; margin-bottom:6px; }
.module-band { font-size:22pt; font-weight:900; line-height:1; display:block; margin-bottom:4px; }
.module-cefr { font-size:7pt; font-weight:700; color:#64748b; display:block; margin-bottom:6px; }
.band-high  { color:#10b981; }
.band-mid   { color:#f59e0b; }
.band-low   { color:#ef4444; }
.band-na    { color:#cbd5e1; }
.module-bar-bg { background:#f1f5f9; height:5px; border-radius:3px; overflow:hidden; margin:0 10px; }
.module-bar-fill { height:5px; border-radius:3px; }
.bar-high   { background:#10b981; }
.bar-mid    { background:#f59e0b; }
.bar-low    { background:#ef4444; }
.bar-na     { background:#e2e8f0; }
.module-raw { font-size:7pt; color:#94a3b8; font-weight:600; margin-top:4px; display:block; }

/* ── Legend ──────────────────────────────────────────────────────────── */
.legend-table { width:100%; border-collapse:collapse; margin-top:14px; border:1px solid #e2e8f0; border-radius:6px; overflow:hidden; }
.legend-table th { background:#f8fafc; font-size:7pt; font-weight:900; text-transform:uppercase; letter-spacing:0.07em; color:#475569; padding:5px 10px; border-bottom:1px solid #e2e8f0; text-align:left; }
.legend-table td { font-size:7.5pt; padding:4px 10px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
.legend-table tr:last-child td { border-bottom:none; }
.legend-dot { display:inline-block; width:9px; height:9px; border-radius:50%; vertical-align:middle; margin-right:5px; }

/* ── Task Card ───────────────────────────────────────────────────────── */
.task-card { border:1.5px solid #e2e8f0; border-radius:10px; margin-bottom:16px; page-break-inside:avoid; overflow:hidden; }
.task-card-header { background:#f8fafc; border-bottom:1.5px solid #e2e8f0; padding:8px 14px; }
.task-card-header-table { width:100%; border-collapse:collapse; }
.task-title { font-size:10pt; font-weight:900; color:#0f172a; }
.task-meta { font-size:7.5pt; color:#64748b; text-align:right; vertical-align:middle; }
.task-band-badge { display:inline-block; font-size:8pt; font-weight:900; padding:3px 12px; border-radius:20px; color:#fff; }
.task-card-body { padding:12px 14px; }

.answer-label { font-size:7pt; font-weight:900; text-transform:uppercase; letter-spacing:0.08em; color:#94a3b8; margin-bottom:5px; }
.answer-text { font-size:8.5pt; color:#334155; line-height:1.65; white-space:pre-wrap; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 12px; }

.ai-report-header { border-top:1.5px dashed #e2e8f0; margin-top:12px; padding-top:12px; }
.ai-report-heading { font-size:8pt; font-weight:900; text-transform:uppercase; letter-spacing:0.07em; color:#4F46E5; }

/* ── Criteria ────────────────────────────────────────────────────────── */
.criteria-table { width:100%; border-collapse:collapse; margin-bottom:10px; }
.criteria-table tr:nth-child(odd) td { background:#f8fafc; }
.criteria-table td { padding:5px 8px; font-size:8.5pt; vertical-align:middle; border-bottom:1px solid #f1f5f9; }
.criteria-label { color:#475569; font-weight:700; width:55%; }
.criteria-bar-cell { width:30%; }
.criteria-bar-bg { background:#f1f5f9; height:6px; border-radius:3px; overflow:hidden; }
.criteria-bar-fill { height:6px; border-radius:3px; }
.criteria-score-cell { font-weight:900; text-align:right; width:15%; font-size:9pt; }

/* ── Feedback boxes ──────────────────────────────────────────────────── */
.fb-box { border-radius:6px; padding:8px 12px; margin-bottom:8px; }
.fb-label { font-size:7pt; font-weight:900; text-transform:uppercase; letter-spacing:0.09em; margin-bottom:5px; }
.fb-body  { font-size:8.5pt; line-height:1.6; }
.fb-indigo  { background:#eef2ff; border-left:3px solid #6366f1; }
.fb-indigo .fb-label  { color:#4338ca; }
.fb-violet  { background:#f5f3ff; border-left:3px solid #8b5cf6; }
.fb-violet .fb-label  { color:#6d28d9; }
.fb-rose    { background:#fff1f2; border-left:3px solid #f43f5e; }
.fb-rose .fb-label    { color:#be123c; }
.fb-emerald { background:#ecfdf5; border-left:3px solid #10b981; }
.fb-emerald .fb-label { color:#065f46; }

/* ── Corrections ─────────────────────────────────────────────────────── */
.corr-row { margin-bottom:4px; font-size:8pt; line-height:1.4; }
.wrong { color:#be123c; text-decoration:line-through; }
.right { color:#065f46; font-weight:700; }
.arrow { color:#94a3b8; padding:0 5px; }
.more  { font-size:7.5pt; font-style:italic; color:#94a3b8; margin-top:3px; }

/* ── Speaking ────────────────────────────────────────────────────────── */
.speak-card { border:1.5px solid #e2e8f0; border-radius:10px; margin-bottom:14px; page-break-inside:avoid; overflow:hidden; }
.speak-card-header { background:#f8fafc; border-bottom:1.5px solid #e2e8f0; padding:7px 14px; }
.speak-header-table { width:100%; border-collapse:collapse; }
.speak-part-label { font-size:8pt; font-weight:900; color:#4F46E5; }
.speak-band-cell  { text-align:right; font-size:8pt; font-weight:900; }
.speak-card-body  { padding:10px 14px; }
.q-label { font-size:7pt; font-weight:900; text-transform:uppercase; color:#4F46E5; margin-bottom:2px; }
.q-text  { font-size:9pt; font-weight:700; color:#0f172a; margin-bottom:8px; }
.a-label { font-size:7pt; font-weight:900; text-transform:uppercase; color:#10b981; margin-bottom:2px; }
.a-text  { font-size:8.5pt; color:#334155; font-style:italic; line-height:1.6; }

.pending-badge { display:inline-block; background:#faf5ff; border:1px solid #e9d5ff; border-radius:20px; padding:4px 12px; font-size:7.5pt; font-weight:700; color:#7c3aed; }

/* ── Disclaimer ──────────────────────────────────────────────────────── */
.disclaimer { margin-top:20px; padding:10px 14px; border:1px solid #fee2e2; border-radius:8px; background:#fff7f7; font-size:7.5pt; color:#b91c1c; text-align:center; }

.clearfix::after { content:""; display:table; clear:both; }
</style>
</head>
<body>

{{-- ── Fixed Footer ─────────────────────────────────────────────────────────── --}}
<div class="page-footer">
    <table class="footer-inner">
        <tr>
            <td>MockDasher &bull; Your Personal IELTS Practice Report &bull; Attempt #{{ $attempt->id }}</td>
            <td class="footer-right">Generated {{ now()->format('d M Y, H:i') }} &bull; Practice Use Only</td>
        </tr>
    </table>
</div>

{{-- ── Accent strip ──────────────────────────────────────────────────────────── --}}
<div class="cover-strip"></div>

<div class="page-wrap">

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<table class="header-table">
    <tr>
        <td style="vertical-align:middle;">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="brand-logo" alt="MockDasher" />
            @endif
            <span class="brand-name">MockDasher</span>
        </td>
        <td style="text-align:right; vertical-align:bottom;">
            <div class="report-title">Your IELTS Practice Report</div>
            <div class="report-subtitle">Personal Performance Analysis &bull; {{ now()->format('d F Y') }}</div>
        </td>
    </tr>
</table>
<hr class="header-divider">

{{-- ── Candidate Information ────────────────────────────────────────────────── --}}
@php
    $candidateName  = optional($attempt->user)->name ?? 'Unknown Candidate';
    $candidateEmail = optional($attempt->user)->email ?? '—';
    $initials       = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $candidateName), 0, 2))));
    $test           = $attempt->testSet?->test;
    $examType       = $test?->exam_type ?? 'Academic';
    $testTitle      = $test ? 'IELTS ' . $examType . ' — Vol. ' . ($test->book_number ?? '') : 'IELTS Mock Test';
    $candidateCode  = 'MD-' . str_pad($attempt->user_id, 5, '0', STR_PAD_LEFT);

    $cefrLevel = function(?float $band): string {
        if ($band === null) return '—';
        if ($band >= 8.5) return 'C2';
        if ($band >= 7.0) return 'C1';
        if ($band >= 5.5) return 'B2';
        if ($band >= 4.0) return 'B1';
        if ($band >= 3.0) return 'A2';
        return 'A1';
    };
    $cefrDesc = function(?float $band): string {
        if ($band === null) return '';
        if ($band >= 8.5) return 'Mastery';
        if ($band >= 7.0) return 'Advanced';
        if ($band >= 5.5) return 'Upper Intermediate';
        if ($band >= 4.0) return 'Intermediate';
        if ($band >= 3.0) return 'Elementary';
        return 'Beginner';
    };
    $bandColorStyle = function(?float $band): string {
        if ($band === null) return 'color:#cbd5e1;';
        if ($band >= 7) return 'color:#10b981;';
        if ($band >= 5.5) return 'color:#f59e0b;';
        return 'color:#ef4444;';
    };
    $barColorClass = function(?float $band): string {
        if ($band === null) return 'bar-na';
        if ($band >= 7) return 'bar-high';
        if ($band >= 5.5) return 'bar-mid';
        return 'bar-low';
    };
    $barWidth = function(?float $band): string {
        return ($band !== null ? round(($band / 9) * 100) : 0) . '%';
    };
@endphp

<div class="candidate-wrap">
    <div class="candidate-header">Your Details</div>
    <div class="candidate-body">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:56px; vertical-align:middle; padding-right:12px;">
                    <div class="avatar">{{ $initials }}</div>
                </td>
                <td style="vertical-align:middle;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td class="info-col">
                                <table class="info-table">
                                    <tr><td class="info-label">Name</td><td class="info-value">{{ $candidateName }}</td></tr>
                                    <tr><td class="info-label">Email</td><td class="info-value">{{ $candidateEmail }}</td></tr>
                                    <tr><td class="info-label">Your Code</td><td class="info-value">{{ $candidateCode }}</td></tr>
                                </table>
                            </td>
                            <td class="info-col">
                                <table class="info-table">
                                    <tr><td class="info-label">Test</td><td class="info-value">{{ $testTitle }}</td></tr>
                                    <tr><td class="info-label">Exam Type</td><td class="info-value">IELTS {{ $examType }}</td></tr>
                                    <tr><td class="info-label">Set Number</td><td class="info-value">Set {{ $attempt->testSet?->set_number ?? '1' }}</td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-col">
                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Started</td>
                                        <td class="info-value">{{ $attempt->started_at ? $attempt->started_at->format('d M Y, H:i') : 'N/A' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="info-col">
                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Completed</td>
                                        <td class="info-value">
                                            {{ $attempt->completed_at ? $attempt->completed_at->format('d M Y, H:i') : 'In Progress' }}
                                            @if($attempt->time_spent)
                                                <span style="color:#64748b; font-weight:400;"> ({{ $attempt->time_spent }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- ── Overall Band Score ───────────────────────────────────────────────────── --}}
@php $overall = $attempt->overall_band; @endphp

<div class="overall-section">
    <div class="overall-outer">
        <div class="overall-top">
            <span class="overall-label">Overall Band Score</span>
            @if($overall !== null)
                <span class="overall-score">{{ number_format($overall, 1) }}</span>
            @else
                <span class="overall-score" style="font-size:24pt;">N / A</span>
            @endif
        </div>
        <div class="overall-bottom">
            @if($overall !== null)
                <span class="cefr-badge">{{ $cefrLevel($overall) }}</span>
                <span class="cefr-level">{{ $cefrDesc($overall) }}</span>
            @else
                <span style="font-size:7.5pt; color:#94a3b8;">Complete all 4 modules to receive your overall band</span>
            @endif
        </div>
    </div>
</div>

{{-- ── Module Performance ───────────────────────────────────────────────────── --}}
@php
    $la = $attempt->listeningAttempt;
    $ra = $attempt->readingAttempt;
    $modules = [
        ['name' => 'Listening', 'band' => $attempt->listening_band, 'raw' => ($la && $la->total_correct !== null) ? $la->total_correct . '/40' : null],
        ['name' => 'Reading',   'band' => $attempt->reading_band,   'raw' => ($ra && $ra->total_correct !== null) ? $ra->total_correct . '/40' : null],
        ['name' => 'Writing',   'band' => $attempt->writing_band,   'raw' => null],
        ['name' => 'Speaking',  'band' => $attempt->speaking_band,  'raw' => null],
    ];
@endphp

<table class="module-table">
    <tr>
        @foreach($modules as $mod)
        <td class="module-cell">
            <span class="module-name">{{ $mod['name'] }}</span>
            <span class="module-band {{ $mod['band'] !== null ? ($mod['band'] >= 7 ? 'band-high' : ($mod['band'] >= 5.5 ? 'band-mid' : 'band-low')) : 'band-na' }}">
                {{ $mod['band'] !== null ? number_format($mod['band'], 1) : '—' }}
            </span>
            <span class="module-cefr">{{ $cefrLevel($mod['band']) }}{{ $mod['band'] !== null ? ' · ' . $cefrDesc($mod['band']) : '' }}</span>
            <div class="module-bar-bg">
                <div class="module-bar-fill {{ $barColorClass($mod['band']) }}" style="width:{{ $barWidth($mod['band']) }};"></div>
            </div>
            @if($mod['raw'])
            <span class="module-raw">{{ $mod['raw'] }} correct</span>
            @endif
        </td>
        @endforeach
    </tr>
</table>

{{-- ── Band Scale Legend ────────────────────────────────────────────────────── --}}
<table class="legend-table">
    <tr>
        <th>Band</th><th>CEFR</th><th>Level</th>
        <th>Band</th><th>CEFR</th><th>Level</th>
    </tr>
    <tr>
        <td><span class="legend-dot" style="background:#10b981;"></span><strong>8.5 – 9.0</strong></td>
        <td><strong>C2</strong></td><td>Mastery / Expert</td>
        <td><span class="legend-dot" style="background:#f59e0b;"></span><strong>5.5 – 6.5</strong></td>
        <td><strong>B2</strong></td><td>Upper Intermediate</td>
    </tr>
    <tr>
        <td><span class="legend-dot" style="background:#10b981;"></span><strong>7.0 – 8.0</strong></td>
        <td><strong>C1</strong></td><td>Advanced</td>
        <td><span class="legend-dot" style="background:#ef4444;"></span><strong>4.0 – 5.0</strong></td>
        <td><strong>B1</strong></td><td>Intermediate</td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- ── Writing Module ──────────────────────────────────────────────────────── --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
@if($attempt->writingAnswers && $attempt->writingAnswers->count() > 0)

<span class="section-heading" style="page-break-before:always;">Writing Module — AI Examiner Report</span>

@php
    $renderCriteriaBar = function(?float $s) use ($barColorClass, $barWidth): string {
        $cls   = $barColorClass($s);
        $width = $barWidth($s);
        return "<div class=\"criteria-bar-bg\"><div class=\"criteria-bar-fill {$cls}\" style=\"width:{$width};\"></div></div>";
    };
@endphp

@foreach($attempt->writingAnswers->sortBy('writingTask.task_number') as $wIdx => $wAnswer)
@php
    $taskNumber = optional($wAnswer->writingTask)->task_number ?? ($wIdx + 1);
    $taskTitle  = optional($wAnswer->writingTask)->task_title ?? "Writing Task {$taskNumber}";
    $wordCount  = str_word_count(strip_tags($wAnswer->answer_text ?? ''));

    $wEval  = $wAnswer->evaluation_json ? json_decode($wAnswer->evaluation_json, true) : null;
    $wBand  = $wAnswer->band_score ?? ($wEval ? ($wEval['overall_band_score'] ?? $wEval['band_score'] ?? null) : null);
    $wCs    = $wEval['criteria_scores'] ?? [];

    $wCriteria = [];
    if (!empty($wCs)) {
        $wCriteria = [
            ['label' => $taskNumber == 1 ? 'Task Achievement' : 'Task Response',
             'score' => $wCs['task_achievement_or_response'] ?? null],
            ['label' => 'Coherence & Cohesion',
             'score' => $wCs['coherence_and_cohesion'] ?? null],
            ['label' => 'Lexical Resource',
             'score' => $wCs['lexical_resource'] ?? null],
            ['label' => 'Grammatical Range & Accuracy',
             'score' => $wCs['grammatical_range_and_accuracy'] ?? null],
        ];
    }
@endphp

<div class="task-card">
    <div class="task-card-header">
        <table class="task-card-header-table">
            <tr>
                <td class="task-title">Task {{ $taskNumber }}: {{ $taskTitle }}</td>
                <td class="task-meta">
                    <span style="margin-right:8px; color:#64748b;">{{ $wordCount }} words</span>
                    @if($wBand !== null)
                        <span class="task-band-badge" style="{{ $wBand >= 7 ? 'background:#10b981' : ($wBand >= 5.5 ? 'background:#f59e0b' : 'background:#ef4444') }}">
                            Band {{ number_format($wBand, 1) }}
                        </span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="task-card-body">

        <div class="answer-label">Your Written Response</div>
        <div class="answer-text">{{ $wAnswer->answer_text ?? 'No response submitted.' }}</div>

        @if($wEval && $wBand !== null)
        <div class="ai-report-header">
            <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                <tr>
                    <td class="ai-report-heading">AI Examiner Feedback</td>
                    <td style="text-align:right; font-size:7.5pt; color:#64748b;">{{ $cefrLevel($wBand) }} — {{ $cefrDesc($wBand) }}</td>
                </tr>
            </table>

            @if(!empty($wCriteria))
            <table class="criteria-table">
                @foreach($wCriteria as $c)
                <tr>
                    <td class="criteria-label">{{ $c['label'] }}</td>
                    <td class="criteria-bar-cell">{!! $renderCriteriaBar($c['score'] !== null ? (float)$c['score'] : null) !!}</td>
                    <td class="criteria-score-cell" style="{{ $bandColorStyle($c['score'] !== null ? (float)$c['score'] : null) }}">
                        {{ $c['score'] !== null ? number_format((float)$c['score'], 1) : '—' }}
                    </td>
                </tr>
                @endforeach
            </table>
            @endif

            @if(!empty($wEval['detailed_feedback']))
            <div class="fb-box fb-indigo">
                <div class="fb-label">What the Examiner Observed</div>
                <div class="fb-body">{{ $wEval['detailed_feedback'] }}</div>
            </div>
            @endif

            @if(!empty($wEval['suggestions_for_improvement']))
            <div class="fb-box fb-emerald">
                <div class="fb-label">How to Improve Your Score</div>
                <div class="fb-body">{{ mb_substr($wEval['suggestions_for_improvement'], 0, 700) }}{{ mb_strlen($wEval['suggestions_for_improvement']) > 700 ? '…' : '' }}</div>
            </div>
            @endif

            @if(!empty($wEval['vocabulary_corrections']))
            <div class="fb-box fb-violet">
                <div class="fb-label">Vocabulary to Strengthen</div>
                @foreach(array_slice($wEval['vocabulary_corrections'], 0, 8) as $vc)
                <div class="corr-row">
                    <span class="wrong">{{ $vc['incorrect'] ?? '' }}</span>
                    <span class="arrow">→</span>
                    <span class="right">{{ $vc['suggested'] ?? '' }}</span>
                </div>
                @endforeach
                @if(count($wEval['vocabulary_corrections']) > 8)
                <div class="more">+{{ count($wEval['vocabulary_corrections']) - 8 }} more corrections available</div>
                @endif
            </div>
            @endif

            @if(!empty($wEval['grammar_corrections']))
            <div class="fb-box fb-rose">
                <div class="fb-label">Grammar to Review</div>
                @foreach(array_slice($wEval['grammar_corrections'], 0, 6) as $gc)
                <div class="corr-row">
                    <span class="wrong">{{ $gc['incorrect'] ?? '' }}</span>
                    <span class="arrow">→</span>
                    <span class="right">{{ $gc['suggested'] ?? '' }}</span>
                </div>
                @endforeach
                @if(count($wEval['grammar_corrections']) > 6)
                <div class="more">+{{ count($wEval['grammar_corrections']) - 6 }} more corrections available</div>
                @endif
            </div>
            @endif
        </div>
        @else
            <div class="pending-badge" style="margin-top:12px;">AI Evaluation Pending</div>
        @endif

    </div>
</div>
@endforeach
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- ── Speaking Module ─────────────────────────────────────────────────────── --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
@php
    $speakingEvals   = [];
    $speakingAnswers = collect();
    if ($attempt->aiSpeakingEvaluation && $attempt->aiSpeakingEvaluation->evaluation_json) {
        $rawSE = json_decode($attempt->aiSpeakingEvaluation->evaluation_json, true) ?: [];
        $speakingEvals   = array_values(array_filter($rawSE, fn($i) => is_array($i) && isset($i['question_id'])));
        $speakingAnswers = $attempt->speakingAnswers->keyBy('speaking_question_id');
    }
@endphp

@if(count($speakingEvals) > 0)

<span class="section-heading" style="page-break-before:always;">Speaking Module — AI Examiner Report</span>

@php
    $speakBand = $attempt->aiSpeakingEvaluation?->band_score;
@endphp
@if($speakBand !== null)
<div style="background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:8px; padding:10px 14px; margin-bottom:14px;">
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="font-size:9pt; font-weight:700; color:#0f172a;">Your Overall Speaking Score</td>
            <td style="text-align:right;">
                <span style="font-size:13pt; font-weight:900; {{ $bandColorStyle($speakBand) }}">{{ number_format($speakBand, 1) }}</span>
                <span style="font-size:8pt; color:#64748b; margin-left:6px;">{{ $cefrLevel($speakBand) }} — {{ $cefrDesc($speakBand) }}</span>
            </td>
        </tr>
    </table>
</div>
@endif

@foreach($speakingEvals as $sIdx => $se)
@php
    $sAns    = $speakingAnswers->get($se['question_id'] ?? null);
    $sBand   = $se['band_score'] ?? null;
    $sEval   = $se['evaluation'] ?? [];
    $sCs     = $sEval['criteria_scores'] ?? [];

    $sCriteria = [];
    if (!empty($sCs)) {
        $sCriteria = [
            ['label' => 'Fluency & Coherence',          'score' => $sCs['fluency_and_coherence'] ?? null],
            ['label' => 'Lexical Resource',             'score' => $sCs['lexical_resource'] ?? null],
            ['label' => 'Grammatical Range & Accuracy', 'score' => $sCs['grammatical_range_and_accuracy'] ?? null],
            ['label' => 'Pronunciation (estimated)',    'score' => $sCs['pronunciation'] ?? null],
        ];
    }
@endphp

<div class="speak-card">
    <div class="speak-card-header">
        <table class="speak-header-table">
            <tr>
                <td class="speak-part-label">Part {{ $se['part'] ?? 1 }} &mdash; Question {{ $sIdx + 1 }}</td>
                <td class="speak-band-cell">
                    @if($sBand !== null)
                        <span style="{{ $bandColorStyle((float)$sBand) }}">Band {{ number_format((float)$sBand, 1) }}</span>
                        <span style="font-size:7pt; color:#94a3b8; font-weight:600; margin-left:4px;">{{ $cefrLevel((float)$sBand) }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="speak-card-body">

        <div class="q-label">Question</div>
        <div class="q-text">{{ $se['question'] ?? '' }}</div>

        <div class="a-label">Your Response</div>
        <div class="a-text">"{{ $sAns?->transcript_text ?: 'No speech transcript recorded.' }}"</div>

        @if(!empty($sEval) && $sBand !== null)
        <div class="ai-report-header">
            <div class="ai-report-heading" style="margin-bottom:8px;">AI Examiner Feedback</div>

            @if(!empty($sCriteria))
            <table class="criteria-table">
                @foreach($sCriteria as $sc)
                <tr>
                    <td class="criteria-label">{{ $sc['label'] }}</td>
                    <td class="criteria-bar-cell">{!! $renderCriteriaBar($sc['score'] !== null ? (float)$sc['score'] : null) !!}</td>
                    <td class="criteria-score-cell" style="{{ $bandColorStyle($sc['score'] !== null ? (float)$sc['score'] : null) }}">
                        {{ $sc['score'] !== null ? number_format((float)$sc['score'], 1) : '—' }}
                    </td>
                </tr>
                @endforeach
            </table>
            @endif

            @if(!empty($sEval['detailed_feedback']))
            <div class="fb-box fb-indigo">
                <div class="fb-label">What the Examiner Observed</div>
                <div class="fb-body">{{ $sEval['detailed_feedback'] }}</div>
            </div>
            @endif

            @if(!empty($sEval['suggestions_for_improvement']))
            <div class="fb-box fb-emerald">
                <div class="fb-label">How to Improve Your Score</div>
                <div class="fb-body">{{ mb_substr($sEval['suggestions_for_improvement'], 0, 400) }}{{ mb_strlen($sEval['suggestions_for_improvement']) > 400 ? '…' : '' }}</div>
            </div>
            @endif

            @if(!empty($sEval['vocabulary_corrections']))
            <div class="fb-box fb-violet">
                <div class="fb-label">Vocabulary to Strengthen</div>
                @foreach(array_slice($sEval['vocabulary_corrections'], 0, 5) as $vc)
                <div class="corr-row">
                    <span class="wrong">{{ $vc['incorrect'] ?? '' }}</span>
                    <span class="arrow">→</span>
                    <span class="right">{{ $vc['suggested'] ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(!empty($sEval['grammar_corrections']))
            <div class="fb-box fb-rose">
                <div class="fb-label">Grammar to Review</div>
                @foreach(array_slice($sEval['grammar_corrections'], 0, 5) as $gc)
                <div class="corr-row">
                    <span class="wrong">{{ $gc['incorrect'] ?? '' }}</span>
                    <span class="arrow">→</span>
                    <span class="right">{{ $gc['suggested'] ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

    </div>
</div>
@endforeach
@endif

{{-- ── Disclaimer ───────────────────────────────────────────────────────────── --}}
<div class="disclaimer">
    <strong>Practice Report Only:</strong> This report was generated by MockDasher using the Gemini AI Examiner.
    Scores are for <strong>self-improvement purposes only</strong> and are not official IELTS results.
    They cannot be used for university admissions, visa applications, or any official purpose.
</div>

</div>{{-- end .page-wrap --}}
</body>
</html>
