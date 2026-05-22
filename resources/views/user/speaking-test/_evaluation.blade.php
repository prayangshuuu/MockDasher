@php
    $bandColor  = $bandScore >= 7 ? 'bg-emerald-500' : ($bandScore >= 5.5 ? 'bg-amber-500' : 'bg-rose-500');
    $scoreColor = fn($s) => $s >= 7 ? 'text-emerald-500' : ($s >= 5.5 ? 'text-amber-500' : 'text-rose-500');

    $criteria = [
        ['key' => 'fluency_coherence',         'label' => 'Fluency & Coherence'],
        ['key' => 'lexical_resource',           'label' => 'Lexical Resource'],
        ['key' => 'grammatical_range_accuracy', 'label' => 'Grammatical Range & Accuracy'],
        ['key' => 'pronunciation',              'label' => 'Pronunciation (estimated)'],
    ];
@endphp

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

<div class="grid grid-cols-2 gap-3 mb-4">
    @foreach($criteria as $c)
        @php $d = $eval[$c['key']] ?? []; @endphp
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--color-bg-primary)] border border-[var(--color-divider)]">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-black uppercase tracking-widest text-[var(--color-text-secondary)]">{{ $c['label'] }}</span>
                <span class="text-lg font-black {{ $scoreColor($d['score'] ?? 0) }}">{{ $d['score'] ?? '—' }}</span>
            </div>
            <p class="text-xs text-[var(--color-text-secondary)] leading-relaxed">{{ $d['feedback'] ?? '' }}</p>
        </div>
    @endforeach
</div>

@if(!empty($eval['overall_feedback']))
    <div class="p-4 rounded-[var(--radius-lg)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_15%,transparent)]">
        <p class="text-[10px] font-black text-[var(--color-primary)] uppercase tracking-widest mb-2">Feedback</p>
        <p class="text-sm text-[var(--color-text-primary)] leading-relaxed">{{ $eval['overall_feedback'] }}</p>
    </div>
@endif
