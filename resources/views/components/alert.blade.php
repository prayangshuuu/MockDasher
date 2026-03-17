@props([
    'variant' => 'success', // success, error, warning, info
])

@php
    $baseClasses = 'p-[16px] rounded-[var(--radius-base)] border text-[14px] leading-relaxed';

    $variants = [
        'success' => 'bg-[var(--color-bg-primary)] border border-l-4 border-l-[var(--color-success)] border-[var(--color-divider)] text-[var(--color-text-primary)] shadow-sm',
        'error' => 'bg-[var(--color-bg-primary)] border border-l-4 border-l-[var(--color-error)] border-[var(--color-divider)] text-[var(--color-text-primary)] shadow-sm',
        'warning' => 'bg-[var(--color-bg-primary)] border border-l-4 border-l-yellow-500 border-[var(--color-divider)] text-[var(--color-text-primary)] shadow-sm',
        'info' => 'bg-[var(--color-bg-primary)] border border-l-4 border-l-[var(--color-primary)] border-[var(--color-divider)] text-[var(--color-text-primary)] shadow-sm',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['info']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>
