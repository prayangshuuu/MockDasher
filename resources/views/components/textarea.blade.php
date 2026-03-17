@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'flex w-full p-[16px] bg-[var(--color-bg)] border border-[var(--color-divider)] rounded-[var(--radius-base)] text-[var(--color-text)] text-[16px] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] disabled:opacity-50 disabled:cursor-not-allowed transition-colors min-h-[100px]']) !!}>{{ $slot }}</textarea>
