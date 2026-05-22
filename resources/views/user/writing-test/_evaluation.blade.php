@php
    $firstCrit = $taskNumber === 1
        ? ['key' => 'task_achievement', 'label' => 'Task Achievement']
        : ['key' => 'task_response',   'label' => 'Task Response'];

    $criteria = [
        $firstCrit,
        ['key' => 'coherence_cohesion',         'label' => 'Coherence & Cohesion'],
        ['key' => 'lexical_resource',            'label' => 'Lexical Resource'],
        ['key' => 'grammatical_range_accuracy',  'label' => 'Grammatical Range & Accuracy'],
    ];

    $bandColor = $bandScore >= 7 ? 'bg-emerald-500' : ($bandScore >= 5.5 ? 'bg-amber-500' : 'bg-rose-500');
    $scoreColor = fn($s) => $s >= 7 ? 'text-emerald-500' : ($s >= 5.5 ? 'text-amber-500' : 'text-rose-500');
@endphp

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

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    @foreach($criteria as $c)
        @php $d = $eval[$c['key']] ?? []; @endphp
        <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)]">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase tracking-widest text-[var(--color-text-secondary)]">{{ $c['label'] }}</span>
                <span class="text-xl font-black {{ $scoreColor($d['score'] ?? 0) }}">{{ $d['score'] ?? '—' }}</span>
            </div>
            <p class="text-sm text-[var(--color-text-secondary)] leading-relaxed">{{ $d['feedback'] ?? '' }}</p>
        </div>
    @endforeach
</div>

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
