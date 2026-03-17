@props([
    'variant' => 'primary', // primary, success, error, neutral
])

@php
    $baseClasses = 'inline-flex items-center px-[8px] py-[4px] rounded-[var(--radius-base)] text-[14px] font-medium';

    $variants = [
        'primary' => 'bg-[var(--color-primary)] text-[var(--color-white)]',
        'success' => 'bg-[var(--color-success)] text-[var(--color-white)]',
        'error' => 'bg-[var(--color-error)] text-[var(--color-white)]',
        'neutral' => 'bg-[var(--color-bg)] text-[var(--color-text)] border border-[var(--color-divider)]',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
