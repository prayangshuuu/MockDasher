@props([
    'classes' => 'h-[24px] w-full'
])

<div {{ $attributes->merge(['class' => 'animate-pulse bg-[var(--color-divider)] opacity-30 rounded-[var(--radius-base)] ' . $classes]) }}></div>
