@props([
    'variant' => 'success', // success, error, warning, info
])

@php
    $baseClasses = 'p-4 rounded-[var(--radius-dwimik)] border text-sm';

    $variants = [
        'success' => 'bg-[var(--color-dwimik-success)]/10 border-[var(--color-dwimik-success)]/20 text-[var(--color-dwimik-success)]',
        'error' => 'bg-[var(--color-dwimik-error)]/10 border-[var(--color-dwimik-error)]/20 text-[var(--color-dwimik-error)]',
        'warning' => 'bg-yellow-100 border-yellow-200 text-yellow-800',
        'info' => 'bg-[var(--color-dwimik-primary)]/10 border-[var(--color-dwimik-primary)]/20 text-[var(--color-dwimik-primary)]',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['info']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>
