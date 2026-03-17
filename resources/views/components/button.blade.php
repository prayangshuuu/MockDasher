@props([
    'variant' => 'primary',
    'type' => 'button',
    'loading' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center px-[20px] py-[10px] text-[16px] font-medium rounded-[var(--radius-base)] transition-ui btn-active-state focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100';

    $variants = [
        'primary' => 'bg-[var(--color-primary)] text-[var(--color-white)] hover:opacity-90 shadow-sm',
        'secondary' => 'bg-[var(--color-bg-primary)] border border-[var(--color-divider)] text-[var(--color-text-primary)] hover:bg-black/5 cursor-pointer',
        'danger' => 'bg-[var(--color-error)] text-[var(--color-white)] hover:opacity-90',
        'success' => 'bg-[var(--color-success)] text-[var(--color-white)] hover:opacity-90',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }} 
    @if($loading) disabled @endif
    x-data="{ loading: {{ $loading ? 'true' : 'false' }} }"
    x-on:submit.window="if ($el.closest('form')) loading = true"
    :disabled="loading">
    
    <span x-show="loading" class="mr-2" style="display: none;">
        <i class="fas fa-spinner fa-spin"></i>
    </span>
    
    <span :class="{ 'opacity-80': loading }">
        {{ $slot }}
    </span>
</button>
