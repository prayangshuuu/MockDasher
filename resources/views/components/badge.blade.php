@props([
    'variant' => 'primary', // primary, success, error, neutral
])

@php
    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-[var(--radius-dwimik)] text-xs font-medium';

    $variants = [
        'primary' => 'bg-[var(--color-dwimik-primary)]/10 text-[var(--color-dwimik-primary)]',
        'success' => 'bg-[var(--color-dwimik-success)]/10 text-[var(--color-dwimik-success)]',
        'error' => 'bg-[var(--color-dwimik-error)]/10 text-[var(--color-dwimik-error)]',
        'neutral' => 'bg-gray-100 text-[var(--color-dwimik-text)] border border-[var(--color-dwimik-divider)]',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
