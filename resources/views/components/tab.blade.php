@props([
    'active' => false,
    'href' => '#'
])

@php
    $classes = $active
        ? 'border-[var(--color-primary)] text-[var(--color-primary)] whitespace-nowrap py-[16px] px-[8px] border-b-2 font-bold text-[16px] transition-colors'
        : 'border-transparent text-[var(--color-text)]/70 hover:text-[var(--color-text)] hover:border-[var(--color-divider)] whitespace-nowrap py-[16px] px-[8px] border-b-2 font-medium text-[16px] transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
