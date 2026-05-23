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
        'primary' => 'bg-indigo-50 dark:bg-indigo-900/30 text-primary border-indigo-100 dark:border-indigo-800',
        'blue' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-500 border-blue-100 dark:border-blue-800',
        'purple' => 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 border-purple-100 dark:border-purple-800',
        'orange' => 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 border-orange-100 dark:border-orange-800',
        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 border-emerald-100 dark:border-emerald-800',
    ];
    $colorClass = $iconColors[$iconColor] ?? $iconColors['primary'];
@endphp

<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft group hover:shadow-premium hover:-translate-y-1 transition-all duration-300">
    <div class="flex justify-between items-start mb-4">
        <div class="size-10 rounded-xl {{ $colorClass }} flex items-center justify-center border shadow-sm group-hover:scale-110 transition-transform">
            @if(Str::endsWith($icon, '.svg'))
                <img src="{{ $icon }}" class="w-5 h-5" alt="{{ $label }} Icon" />
            @else
                <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
            @endif
        </div>
        @if($trend)
            <div class="flex flex-col items-end">
                <span class="text-xs font-semibold {{ $trendType === 'success' ? 'text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300' : 'text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-300' }} px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $trend }}</span>
            </div>
        @endif
    </div>
    <h3 class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider mb-1">{{ $label }}</h3>
    <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $value }}</p>
</div>
