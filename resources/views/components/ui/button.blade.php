{{-- ============================================================================
    UI Component: Button
    Usage: <x-ui.button variant="primary">Label</x-ui.button>
    Variants: primary | secondary | outline | danger
    Props: variant, type, disabled, loading, href (renders <a> if set)
    Uses .perfect-shape and .btn-active-state utility classes.
    ============================================================================ --}}
@props([
    'variant' => 'primary',
    'type'    => 'button',
    'loading' => false,
    'href'    => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold perfect-shape btn-active-state transition-ui focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-[var(--color-primary)] disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100 cursor-pointer';

    $variants = [
        'primary'   => 'bg-[var(--color-primary)] text-[var(--color-white)] hover:opacity-90',
        'secondary' => 'bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] border border-[var(--color-divider)] hover:bg-[var(--color-divider)]',
        'outline'   => 'bg-transparent text-[var(--color-primary)] border border-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-[var(--color-white)]',
        'danger'    => 'bg-[var(--color-error)] text-[var(--color-white)] hover:opacity-90',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($loading || $attributes->get('disabled')) disabled @endif
        x-data="{ loading: {{ $loading ? 'true' : 'false' }} }"
        x-on:submit.window="if ($el.closest('form')) loading = true"
        :disabled="loading"
    >
        {{-- Spinner --}}
        <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>

        <span :class="{ 'opacity-80': loading }">
            {{ $slot }}
        </span>
    </button>
@endif
