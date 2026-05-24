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
        <h4 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Answer Assessment Summary — Part {{ $part }}</h4>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">AI IELTS Examiner Grade</p>
    </div>
    <div class="flex items-center justify-center size-16 rounded-2xl {{ $bandColor }} text-white shadow-lg shrink-0">
        <div class="text-center">
            <div class="text-2xl font-black leading-none">{{ $bandScore ?? '—' }}</div>
            <div class="text-[8px] font-bold uppercase tracking-wider opacity-80 mt-0.5">Band</div>
        </div>
    </div>
</div>

{{-- ── Criteria Scores Grid ────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    @foreach($criteria as $c)
        <div class="p-4 rounded-xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ $c['label'] }}</span>
                <span class="text-lg font-black {{ $scoreColor($c['score'] ?? 0) }}">{{ $c['score'] ?? '—' }}</span>
            </div>
            @if(!empty($c['feedback']))
                <p class="text-xs text-slate-500 dark:text-slate-455 leading-relaxed mt-1">{{ $c['feedback'] }}</p>
            @endif
        </div>
    @endforeach
</div>

@if($isNewSchema)

    {{-- ── Detailed Feedback ─────────────────────────────────────────────────── --}}
    @if(!empty($eval['detailed_feedback']))
        <div class="mb-4 p-4 rounded-xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 shadow-soft">
            <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Detailed Feedback
            </p>
            <p class="text-sm text-slate-700 dark:text-slate-350 leading-relaxed">{{ $eval['detailed_feedback'] }}</p>
        </div>
    @endif

    {{-- ── Vocabulary Corrections ────────────────────────────────────────────── --}}
    @if(!empty($eval['vocabulary_corrections']) && count($eval['vocabulary_corrections']) > 0)
        <div class="mb-4 p-4 rounded-xl bg-violet-50/50 dark:bg-violet-950/20 border border-violet-100 dark:border-violet-900/40 shadow-soft">
            <p class="text-[9px] font-black text-violet-500 uppercase tracking-widest mb-3">Vocabulary Improvements</p>
            <div class="space-y-2.5">
                @foreach($eval['vocabulary_corrections'] as $vc)
                    <div class="flex items-start gap-3 text-xs flex-wrap">
                        <span class="shrink-0 px-2 py-0.5 rounded bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 font-mono line-through">{{ $vc['incorrect'] ?? '' }}</span>
                        <span class="shrink-0 text-slate-400 dark:text-slate-550">→</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 font-mono font-bold">{{ $vc['suggested'] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Grammar Corrections ───────────────────────────────────────────────── --}}
    @if(!empty($eval['grammar_corrections']) && count($eval['grammar_corrections']) > 0)
        <div class="mb-4 p-4 rounded-xl bg-rose-50/50 dark:bg-rose-955/20 border border-rose-100 dark:border-rose-900/40 shadow-soft">
            <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest mb-3">Grammar Corrections</p>
            <div class="space-y-3">
                @foreach($eval['grammar_corrections'] as $gc)
                    <div class="space-y-1 text-xs">
                        <div class="flex items-start gap-2">
                            <span class="shrink-0 text-[9px] font-black uppercase text-rose-400 mt-0.5 w-12">Wrong:</span>
                            <span class="text-rose-700 dark:text-rose-400 italic">{{ $gc['incorrect'] ?? '' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="shrink-0 text-[9px] font-black uppercase text-emerald-500 mt-0.5 w-12">Better:</span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-bold">{{ $gc['suggested'] ?? '' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Suggestions for Improvement ──────────────────────────────────────── --}}
    @if(!empty($eval['suggestions_for_improvement']))
        <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 shadow-soft">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2.5 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7zm2 15h-4v-1h4v1zm0-2h-4v-1h4v1zm.69-2.31c-.34.24-.69.57-.69.96v.35h-4v-.35c0-.39-.35-.72-.69-.96A4.981 4.981 0 0 1 7 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.63-.78 3.07-2.31 4.14z"/></svg>
                Suggestions for Improvement
            </p>
            <p class="text-xs text-slate-600 dark:text-slate-405 leading-relaxed">{{ $eval['suggestions_for_improvement'] }}</p>
        </div>
    @endif

@else

    {{-- ── Legacy v1 schema rendering ──────────────────────────────────────── --}}
    @if(!empty($eval['overall_feedback']))
        <div class="p-4 rounded-xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 shadow-soft">
            <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Feedback
            </p>
            <p class="text-sm text-slate-700 dark:text-slate-350 leading-relaxed">{{ $eval['overall_feedback'] }}</p>
        </div>
    @endif

@endif
