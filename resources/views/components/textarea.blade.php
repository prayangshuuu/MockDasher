@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'flex w-full px-4 py-2 bg-[var(--color-dwimik-bg)] border border-[var(--color-dwimik-divider)] rounded-[var(--radius-dwimik)] text-[var(--color-dwimik-text)] text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-dwimik-primary)] focus:border-[var(--color-dwimik-primary)] disabled:opacity-50 disabled:cursor-not-allowed transition-colors min-h-[100px]']) !!}>{{ $slot }}</textarea>
