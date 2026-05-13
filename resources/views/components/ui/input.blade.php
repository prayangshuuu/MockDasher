{{-- ============================================================================
    UI Component: Input
    Usage: <x-ui.input name="email" label="Email" icon="mail" />
    Props: label, hint, icon, hasError, hasSuccess, disabled
    Focus ring uses --color-primary. Border uses --color-divider.
    ============================================================================ --}}
@props([
    'label'      => null,
    'hint'       => null,
    'icon'       => null,
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

    $inputClasses = "block w-full bg-[var(--color-bg-primary)] border border-[var(--color-divider)] rounded-[var(--radius-base)] text-sm text-[var(--color-text-primary)] placeholder:text-[var(--color-text-secondary)] focus:outline-none focus:ring-1 transition-ui disabled:opacity-50 disabled:cursor-not-allowed {$ring}";

    $padding = $icon ? 'pl-10 pr-4 py-2.5' : 'px-4 py-2.5';
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

    <div class="relative">
        @if($icon)
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-[var(--color-text-secondary)] pointer-events-none">{{ $icon }}</span>
        @endif

        <input
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge(['class' => $inputClasses . ' ' . $padding]) !!}
        >
    </div>

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
