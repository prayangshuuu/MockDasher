@props([
    'variant' => 'primary',
    'href' => null,
    'icon' => null,
    'size' => 'md'
])

@php
    $baseClasses = "inline-flex items-center justify-center gap-2 font-bold transition-all active:scale-95";
    
    $variants = [
        'primary' => 'bg-primary text-white shadow-lg shadow-primary/20 hover:scale-105',
        'secondary' => 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-lg hover:-translate-y-1',
        'outline' => 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-primary/50',
        'ghost' => 'bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-primary',
        'danger' => 'bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-red-500',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-xs rounded-lg',
        'md' => 'px-6 py-2.5 text-sm rounded-xl',
        'lg' => 'px-8 py-4 text-xs uppercase tracking-widest rounded-2xl',
        'icon' => 'size-12 rounded-xl',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <span class="material-symbols-outlined {{ $size === 'icon' ? 'text-xl' : 'text-lg' }}">{{ $icon }}</span> @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'submit']) }}>
        @if($icon) <span class="material-symbols-outlined {{ $size === 'icon' ? 'text-xl' : 'text-lg' }}">{{ $icon }}</span> @endif
        {{ $slot }}
    </button>
@endif
