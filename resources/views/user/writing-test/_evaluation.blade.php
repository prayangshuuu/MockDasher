{{--
    Writing Task Evaluation Partial
    Supports BOTH schemas:
      - New (v2): $eval has 'criteria_scores', 'detailed_feedback', 'vocabulary_corrections', 'grammar_corrections', 'suggestions_for_improvement'
      - Old (v1): $eval has 'task_achievement'/'task_response', 'coherence_cohesion', 'grammatical_errors', 'overall_review', 'improved_version'
--}}

@php
    $isNewSchema = isset($eval['criteria_scores']);

    // ── Criteria ──────────────────────────────────────────────────────────────
    if ($isNewSchema) {
        $cs = $eval['criteria_scores'] ?? [];
        $criteria = [
            ['label' => $taskNumber === 1 ? 'Task Achievement' : 'Task Response',
             'score' => $cs['task_achievement_or_response'] ?? null,
             'feedback' => null],
            ['label' => 'Coherence & Cohesion',
             'score' => $cs['coherence_and_cohesion'] ?? null,
             'feedback' => null],
            ['label' => 'Lexical Resource',
             'score' => $cs['lexical_resource'] ?? null,
             'feedback' => null],
            ['label' => 'Grammatical Range & Accuracy',
             'score' => $cs['grammatical_range_and_accuracy'] ?? null,
             'feedback' => null],
        ];
    } else {
        // Legacy schema — criteria are nested objects with score + feedback
        $firstCrit = $taskNumber === 1
            ? ['key' => 'task_achievement', 'label' => 'Task Achievement']
            : ['key' => 'task_response',   'label' => 'Task Response'];

        $legacyKeys = [$firstCrit,
            ['key' => 'coherence_cohesion',        'label' => 'Coherence & Cohesion'],
            ['key' => 'lexical_resource',           'label' => 'Lexical Resource'],
            ['key' => 'grammatical_range_accuracy', 'label' => 'Grammatical Range & Accuracy'],
        ];
        $criteria = array_map(fn($c) => [
            'label'    => $c['label'],
            'score'    => ($eval[$c['key']] ?? [])['score'] ?? null,
            'feedback' => ($eval[$c['key']] ?? [])['feedback'] ?? null,
        ], $legacyKeys);
    }

    // ── Color helpers ─────────────────────────────────────────────────────────
    $bandColor  = $bandScore >= 7 ? 'bg-emerald-500' : ($bandScore >= 5.5 ? 'bg-amber-500' : 'bg-rose-500');
    $scoreColor = fn($s) => $s >= 7 ? 'text-emerald-500' : ($s >= 5.5 ? 'text-amber-500' : 'text-rose-500');
@endphp

{{-- ── Header: title + band badge ─────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h3 class="text-2xl font-black text-[var(--color-text-primary)]">Task {{ $taskNumber }} Evaluation</h3>
        <p class="text-sm text-[var(--color-text-secondary)] mt-1">AI-powered IELTS band assessment</p>
    </div>
    <div class="flex items-center justify-center size-20 rounded-[var(--radius-xl)] {{ $bandColor }} text-white shadow-lg shrink-0">
        <div class="text-center">
            <div class="text-3xl font-black leading-none">{{ $bandScore ?? '—' }}</div>
            <div class="text-[9px] font-bold uppercase tracking-wider opacity-80 mt-1">Band</div>
        </div>
    </div>
</div>

{{-- ── Criteria Scores Grid ────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    @foreach($criteria as $c)
        <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)]">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase tracking-widest text-[var(--color-text-secondary)]">{{ $c['label'] }}</span>
                <span class="text-xl font-black {{ $scoreColor($c['score'] ?? 0) }}">{{ $c['score'] ?? '—' }}</span>
            </div>
            @if(!empty($c['feedback']))
                <p class="text-sm text-[var(--color-text-secondary)] leading-relaxed">{{ $c['feedback'] }}</p>
            @endif
        </div>
    @endforeach
</div>

@if($isNewSchema)

    {{-- ── Detailed Feedback ─────────────────────────────────────────────────── --}}
    @if(!empty($eval['detailed_feedback']))
        <div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
            <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Detailed Feedback</p>
            <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">{{ $eval['detailed_feedback'] }}</p>
        </div>
    @endif

    {{-- ── Vocabulary Corrections ────────────────────────────────────────────── --}}
    @if(!empty($eval['vocabulary_corrections']) && count($eval['vocabulary_corrections']) > 0)
        <div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#8B5CF6_5%,transparent)] border border-[color-mix(in_srgb,#8B5CF6_20%,transparent)]">
            <p class="text-[10px] font-black text-violet-500 uppercase tracking-widest mb-3">Vocabulary Improvements</p>
            <div class="space-y-2">
                @foreach($eval['vocabulary_corrections'] as $vc)
                    <div class="flex items-start gap-3 text-sm">
                        <span class="shrink-0 px-2 py-0.5 rounded bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 font-mono line-through">{{ $vc['incorrect'] ?? '' }}</span>
                        <span class="shrink-0 text-[var(--color-text-secondary)]">→</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 font-mono font-semibold">{{ $vc['suggested'] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Grammar Corrections ───────────────────────────────────────────────── --}}
    @if(!empty($eval['grammar_corrections']) && count($eval['grammar_corrections']) > 0)
        <div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#EF4444_5%,transparent)] border border-[color-mix(in_srgb,#EF4444_20%,transparent)]">
            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3">Grammar Corrections</p>
            <div class="space-y-3">
                @foreach($eval['grammar_corrections'] as $gc)
                    <div class="space-y-1 text-sm">
                        <div class="flex items-start gap-2">
                            <span class="shrink-0 text-[10px] font-bold uppercase text-rose-400 mt-0.5 w-16">Wrong:</span>
                            <span class="text-rose-700 dark:text-rose-400 italic">{{ $gc['incorrect'] ?? '' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="shrink-0 text-[10px] font-bold uppercase text-emerald-500 mt-0.5 w-16">Better:</span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-semibold">{{ $gc['suggested'] ?? '' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Suggestions for Improvement ──────────────────────────────────────── --}}
    @if(!empty($eval['suggestions_for_improvement']))
        <details class="group">
            <summary class="flex items-center gap-2 cursor-pointer text-xs font-black uppercase tracking-widest text-[var(--color-primary)] select-none list-none p-4 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)] hover:opacity-80 transition-opacity">
                <span class="material-symbols-outlined text-sm group-open:rotate-90 transition-transform">chevron_right</span>
                Suggestions for Improvement
            </summary>
            <div class="mt-3 p-6 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] text-sm text-[var(--color-text-primary)] leading-relaxed whitespace-pre-line">{{ $eval['suggestions_for_improvement'] }}</div>
        </details>
    @endif

@else

    {{-- ── Legacy v1 schema rendering ──────────────────────────────────────── --}}
    @if(!empty($eval['grammatical_errors']))
        <div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#EF4444_5%,transparent)] border border-[color-mix(in_srgb,#EF4444_20%,transparent)]">
            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3">Grammatical Errors Found</p>
            <ul class="space-y-1 list-disc list-inside">
                @foreach($eval['grammatical_errors'] as $err)
                    <li class="text-sm text-rose-700 dark:text-rose-400">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($eval['overall_review']))
        <div class="mb-4 p-5 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
            <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Overall Review</p>
            <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">{{ $eval['overall_review'] }}</p>
        </div>
    @endif

    @if(!empty($eval['improved_version']))
        <details class="group">
            <summary class="flex items-center gap-2 cursor-pointer text-xs font-black uppercase tracking-widest text-[var(--color-primary)] select-none list-none p-4 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)] hover:opacity-80 transition-opacity">
                <span class="material-symbols-outlined text-sm group-open:rotate-90 transition-transform">chevron_right</span>
                View Improved Version
            </summary>
            <div class="mt-3 p-6 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] text-sm text-[var(--color-text-primary)] leading-relaxed whitespace-pre-line">{{ $eval['improved_version'] }}</div>
        </details>
    @endif

@endif
