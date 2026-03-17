@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center px-[16px] py-[8px] text-[16px] font-medium rounded-[var(--radius-base)] transition-opacity focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-[var(--color-primary)] text-[var(--color-white)] hover:opacity-90',
        'secondary' => 'bg-transparent border border-[var(--color-divider)] text-[var(--color-text)] hover:opacity-90',
        'danger' => 'bg-[var(--color-error)] text-[var(--color-white)] hover:opacity-90',
        'success' => 'bg-[var(--color-success)] text-[var(--color-white)] hover:opacity-90',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>
