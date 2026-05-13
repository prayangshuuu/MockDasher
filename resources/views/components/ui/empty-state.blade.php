{{-- ============================================================================
    UI Component: Empty State
    Usage: <x-ui.empty-state icon="inbox" title="No Data" description="...">
               <x-slot:action>...</x-slot:action>
           </x-ui.empty-state>
    Centered column with icon circle, title, description, and action slot.
    ============================================================================ --}}
@props([
    'icon'        => 'inbox',
    'title'       => 'No Data Available',
    'description' => 'There is currently no data to display here.',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 text-center']) }}>
    {{-- Icon Circle --}}
    <div class="flex size-16 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-primary)_8%,transparent)] mb-5">
        <span class="material-symbols-outlined text-3xl text-[var(--color-primary)]">{{ $icon }}</span>
    </div>

    {{-- Title --}}
    <h3 class="text-body-large font-semibold text-[var(--color-text-primary)] mb-1.5">{{ $title }}</h3>

    {{-- Description --}}
    <p class="text-sm text-[var(--color-text-secondary)] max-w-sm mb-6">{{ $description }}</p>

    {{-- Action Slot --}}
    @if(isset($action))
        <div>
            {{ $action }}
        </div>
    @endif
</div>
