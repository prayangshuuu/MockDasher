<div {{ $attributes->merge(['class' => 'bg-[var(--color-dwimik-bg)] border border-[var(--color-dwimik-divider)] rounded-[var(--radius-dwimik)] shadow-sm overflow-hidden flex flex-col', 'style' => 'box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-[var(--color-dwimik-divider)] bg-white/50">
            {{ $header }}
        </div>
    @endif
    
    <div class="px-6 py-4 flex-grow">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-[var(--color-dwimik-divider)] bg-white/50 bg-gray-50/30">
            {{ $footer }}
        </div>
    @endif
</div>
