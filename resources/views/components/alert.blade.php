@props([
    'variant' => 'success', // success, error, warning, info
])

@php
    $baseClasses = 'p-[16px] rounded-[var(--radius-base)] border text-[14px] leading-relaxed';

    $variants = [
        'success' => 'bg-[var(--color-success)]/10 border-[var(--color-success)]/20 text-[var(--color-success)]',
        'error' => 'bg-[var(--color-error)]/10 border-[var(--color-error)]/20 text-[var(--color-error)]',
        'warning' => 'bg-yellow-100 border-yellow-200 text-yellow-800',
        'info' => 'bg-[var(--color-primary)]/10 border-[var(--color-primary)]/20 text-[var(--color-primary)]',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['info']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>
