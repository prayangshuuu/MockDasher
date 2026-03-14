@props([
    'active' => false,
    'href' => '#'
])

@php
    $classes = $active
        ? 'border-[var(--color-dwimik-primary)] text-[var(--color-dwimik-primary)] whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
        : 'border-transparent text-gray-500 hover:text-[var(--color-dwimik-text)] hover:border-[var(--color-dwimik-divider)] whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
