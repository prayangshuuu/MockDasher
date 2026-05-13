{{-- ============================================================================
    UI Component: Card
    Usage: <x-ui.card> or <x-ui.card :flush="true">
    Slots: $header (optional), $slot (main content), $footer (optional)
    Adheres strictly to --color-bg-primary, --color-divider, --radius-xl
    ============================================================================ --}}
@props([
    'flush' => false,
])

<div {{ $attributes->merge([
    'class' => 'bg-[var(--color-bg-primary)] border border-[var(--color-divider)] rounded-[var(--radius-xl)] flex flex-col w-full transition-ui hover:shadow-[var(--shadow-soft)]'
        . ($flush ? '' : ' p-5 sm:p-6')
]) }}>

    {{-- Optional Header Slot --}}
    @if(isset($header))
        <div @class([
            'border-b border-[var(--color-divider)] pb-4 mb-4',
            'px-5 sm:px-6 pt-5 sm:pt-6' => $flush,
        ])>
            {{ $header }}
        </div>
    @endif

    {{-- Main Content --}}
    <div @class([
        'flex-grow min-w-0',
        'px-5 sm:px-6' => $flush,
    ])>
        {{ $slot }}
    </div>

    {{-- Optional Footer Slot --}}
    @if(isset($footer))
        <div @class([
            'border-t border-[var(--color-divider)] pt-4 mt-4',
            'px-5 sm:px-6 pb-5 sm:pb-6' => $flush,
        ])>
            {{ $footer }}
        </div>
    @endif
</div>
