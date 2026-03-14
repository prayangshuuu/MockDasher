@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-[var(--radius-dwimik)] transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-[var(--color-dwimik-primary)] text-white hover:bg-opacity-90 focus:ring-[var(--color-dwimik-primary)]',
        'secondary' => 'bg-transparent border border-[var(--color-dwimik-divider)] text-[var(--color-dwimik-text)] hover:bg-gray-50 focus:ring-[var(--color-dwimik-divider)]',
        'danger' => 'bg-[var(--color-dwimik-error)] text-white hover:bg-opacity-90 focus:ring-[var(--color-dwimik-error)]',
        'success' => 'bg-[var(--color-dwimik-success)] text-white hover:bg-opacity-90 focus:ring-[var(--color-dwimik-success)]',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>
