{{--
    Speaking Question Evaluation Partial
    Supports BOTH schemas:
      - New (v2): $eval has 'criteria_scores', 'detailed_feedback', 'vocabulary_corrections', 'grammar_corrections', 'suggestions_for_improvement'
      - Old (v1): $eval has 'fluency_coherence', 'lexical_resource', 'grammatical_range_accuracy', 'pronunciation', 'overall_feedback'
--}}

@php
    $isNewSchema = isset($eval['criteria_scores']);

    // ── Criteria ──────────────────────────────────────────────────────────────
    if ($isNewSchema) {
        $cs = $eval['criteria_scores'] ?? [];
        $criteria = [
            ['label' => 'Fluency & Coherence',           'score' => $cs['fluency_and_coherence'] ?? null,            'feedback' => null],
            ['label' => 'Lexical Resource',               'score' => $cs['lexical_resource'] ?? null,                 'feedback' => null],
            ['label' => 'Grammatical Range & Accuracy',   'score' => $cs['grammatical_range_and_accuracy'] ?? null,   'feedback' => null],
            ['label' => 'Pronunciation (estimated)',       'score' => $cs['pronunciation'] ?? null,                    'feedback' => null],
        ];
    } else {
        // Legacy schema — criteria are nested objects with score + feedback
        $legacyKeys = [
            ['key' => 'fluency_coherence',         'label' => 'Fluency & Coherence'],
            ['key' => 'lexical_resource',          'label' => 'Lexical Resource'],
            ['key' => 'grammatical_range_accuracy','label' => 'Grammatical Range & Accuracy'],
            ['key' => 'pronunciation',             'label' => 'Pronunciation (estimated)'],
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
<div class="flex items-center justify-between mb-6">
    <div>
        <h4 class="text-lg font-black text-[var(--color-text-primary)]">Answer Evaluation — Part {{ $part }}</h4>
        <p class="text-xs text-[var(--color-text-secondary)] mt-0.5">AI IELTS Band Assessment</p>
    </div>
    <div class="flex items-center justify-center size-16 rounded-[var(--radius-xl)] {{ $bandColor }} text-white shadow-lg shrink-0">
        <div class="text-center">
            <div class="text-2xl font-black leading-none">{{ $bandScore ?? '—' }}</div>
            <div class="text-[8px] font-bold uppercase tracking-wider opacity-80 mt-0.5">Band</div>
        </div>
    </div>
</div>

{{-- ── Criteria Scores Grid ────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    @foreach($criteria as $c)
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--color-bg-primary)] border border-[var(--color-divider)]">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-black uppercase tracking-widest text-[var(--color-text-secondary)]">{{ $c['label'] }}</span>
                <span class="text-lg font-black {{ $scoreColor($c['score'] ?? 0) }}">{{ $c['score'] ?? '—' }}</span>
            </div>
            @if(!empty($c['feedback']))
                <p class="text-xs text-[var(--color-text-secondary)] leading-relaxed">{{ $c['feedback'] }}</p>
            @endif
        </div>
    @endforeach
</div>

@if($isNewSchema)

    {{-- ── Detailed Feedback ─────────────────────────────────────────────────── --}}
    @if(!empty($eval['detailed_feedback']))
        <div class="mb-3 p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
            <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Detailed Feedback</p>
            <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">{{ $eval['detailed_feedback'] }}</p>
        </div>
    @endif

    {{-- ── Vocabulary Corrections ────────────────────────────────────────────── --}}
    @if(!empty($eval['vocabulary_corrections']) && count($eval['vocabulary_corrections']) > 0)
        <div class="mb-3 p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#8B5CF6_5%,transparent)] border border-[color-mix(in_srgb,#8B5CF6_20%,transparent)]">
            <p class="text-[10px] font-black text-violet-500 uppercase tracking-widest mb-3">Vocabulary Improvements</p>
            <div class="space-y-2">
                @foreach($eval['vocabulary_corrections'] as $vc)
                    <div class="flex items-start gap-3 text-xs">
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
        <div class="mb-3 p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#EF4444_5%,transparent)] border border-[color-mix(in_srgb,#EF4444_20%,transparent)]">
            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3">Grammar Corrections</p>
            <div class="space-y-3">
                @foreach($eval['grammar_corrections'] as $gc)
                    <div class="space-y-1 text-xs">
                        <div class="flex items-start gap-2">
                            <span class="shrink-0 text-[10px] font-bold uppercase text-rose-400 mt-0.5 w-12">Wrong:</span>
                            <span class="text-rose-700 dark:text-rose-400 italic">{{ $gc['incorrect'] ?? '' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="shrink-0 text-[10px] font-bold uppercase text-emerald-500 mt-0.5 w-12">Better:</span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-semibold">{{ $gc['suggested'] ?? '' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Suggestions for Improvement ──────────────────────────────────────── --}}
    @if(!empty($eval['suggestions_for_improvement']))
        <div class="p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,#10B981_5%,transparent)] border border-[color-mix(in_srgb,#10B981_20%,transparent)]">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">
                <span class="material-symbols-outlined text-xs align-middle mr-1">lightbulb</span>
                Suggestions for Improvement
            </p>
            <p class="text-xs text-[var(--color-text-primary)] leading-relaxed">{{ $eval['suggestions_for_improvement'] }}</p>
        </div>
    @endif

@else

    {{-- ── Legacy v1 schema rendering ──────────────────────────────────────── --}}
    @if(!empty($eval['overall_feedback']))
        <div class="p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
            <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Feedback</p>
            <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">{{ $eval['overall_feedback'] }}</p>
        </div>
    @endif

@endif
