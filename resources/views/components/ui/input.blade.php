{{-- ============================================================================
    UI Component: Input
    Usage: <x-ui.input name="email" label="Email" />
    Props: label, hint, hasError, hasSuccess, disabled
    ============================================================================ --}}
@props([
    'label'      => null,
    'hint'       => null,
    'hasError'   => false,
    'hasSuccess' => false,
    'disabled'   => false,
])

@php
    $ring = 'focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)]';
    if ($hasError) {
        $ring = 'border-[var(--color-error)] focus:ring-[var(--color-error)] focus:border-[var(--color-error)]';
    } elseif ($hasSuccess) {
        $ring = 'border-[var(--color-success)] focus:ring-[var(--color-success)] focus:border-[var(--color-success)]';
    }

    $inputClasses = "block w-full px-4 py-2.5 bg-[var(--color-bg-primary)] border border-[var(--color-divider)] rounded-[var(--radius-base)] text-sm text-[var(--color-text-primary)] placeholder:text-[var(--color-text-secondary)] focus:outline-none focus:ring-1 transition-ui disabled:opacity-50 disabled:cursor-not-allowed {$ring}";
@endphp

<div class="w-full">
    @if($label)
        <label
            @if($attributes->get('id')) for="{{ $attributes->get('id') }}" @endif
            class="block mb-1.5 text-sm font-medium text-[var(--color-text-primary)]"
        >
            {{ $label }}
        </label>
    @endif

    <input
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => $inputClasses]) !!}
    >

    @if($hint && !$hasError)
        <p class="mt-1.5 text-xs text-[var(--color-text-secondary)]">{{ $hint }}</p>
    @endif

    {{-- Auto-display validation error if name attribute is set --}}
    @if($attributes->get('name'))
        @error($attributes->get('name'))
            <p class="mt-1.5 text-xs text-[var(--color-error)]">{{ $message }}</p>
        @enderror
    @endif
</div>
