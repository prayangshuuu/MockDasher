@props([
    'label',
    'value',
    'icon',
    'trend' => null,
    'trendType' => 'success', // success or danger
    'iconColor' => 'primary'
])

@php
    $iconColors = [
        'primary' => 'bg-indigo-50 dark:bg-indigo-900/40 text-primary',
        'blue' => 'bg-blue-50 dark:bg-blue-900/40 text-blue-500',
        'purple' => 'bg-purple-50 dark:bg-purple-900/40 text-purple-600',
        'orange' => 'bg-orange-50 dark:bg-orange-900/40 text-orange-600',
        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600',
    ];
    $colorClass = $iconColors[$iconColor] ?? $iconColors['primary'];
@endphp

<div class="glass-card p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft group hover:border-primary/50 transition-all">
    <div class="flex justify-between items-start mb-6">
        <div class="size-12 rounded-2xl {{ $colorClass }} flex items-center justify-center group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-2xl">{{ $icon }}</span>
        </div>
        @if($trend)
            <span class="text-[10px] font-black {{ $trendType === 'success' ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30' : 'text-red-600 bg-red-50 dark:bg-red-900/30' }} px-2 py-1 rounded-lg uppercase tracking-widest">{{ $trend }}</span>
        @endif
    </div>
    <h3 class="text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-widest mb-1">{{ $label }}</h3>
    <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $value }}</p>
</div>
