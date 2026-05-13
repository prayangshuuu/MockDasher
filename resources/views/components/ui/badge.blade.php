{{-- ============================================================================
    UI Component: Badge
    Usage: <x-ui.badge variant="success">Active</x-ui.badge>
    Variants: success | error | pending | primary | neutral
    Pill shape with leading dot indicator for status variants.
    ============================================================================ --}}
@props([
    'variant' => 'neutral',
])

@php
    $base = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium leading-5 whitespace-nowrap';

    $variants = [
        'success' => 'bg-[color-mix(in_srgb,var(--color-success)_12%,transparent)] text-[var(--color-success)]',
        'error'   => 'bg-[color-mix(in_srgb,var(--color-error)_12%,transparent)] text-[var(--color-error)]',
        'pending' => 'bg-[color-mix(in_srgb,#F59E0B_12%,transparent)] text-[#B45309]',
        'primary' => 'bg-[color-mix(in_srgb,var(--color-primary)_12%,transparent)] text-[var(--color-primary)]',
        'neutral' => 'bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)] border border-[var(--color-divider)]',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{-- Leading dot indicator for status variants --}}
    @if(in_array($variant, ['success', 'error', 'pending']))
        <span @class([
            'inline-block w-1.5 h-1.5 rounded-full shrink-0',
            'bg-[var(--color-success)]' => $variant === 'success',
            'bg-[var(--color-error)]'   => $variant === 'error',
            'bg-[#F59E0B]'              => $variant === 'pending',
        ])></span>
    @endif

    {{ $slot }}
</span>
