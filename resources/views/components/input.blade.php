@props(['disabled' => false, 'hasError' => false, 'hasSuccess' => false])

@php
    $borderClass = 'border-[var(--color-divider)]';
    if ($hasError) {
        $borderClass = 'border-[var(--color-error)] focus:border-[var(--color-error)] focus:ring-[var(--color-error)]';
    } elseif ($hasSuccess) {
        $borderClass = 'border-[var(--color-success)] focus:border-[var(--color-success)] focus:ring-[var(--color-success)]';
    } else {
        $borderClass = 'border-[var(--color-divider)] focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]';
    }
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => "flex w-full p-[16px] bg-[var(--color-bg-primary)] border rounded-[var(--radius-base)] text-[var(--color-text-primary)] text-[16px] focus:outline-none focus:ring-1 disabled:opacity-50 disabled:cursor-not-allowed transition-ui {$borderClass}"
]) !!}>
