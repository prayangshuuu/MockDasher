<div {{ $attributes->merge(['class' => 'bg-[var(--color-bg)] border border-[var(--color-divider)] rounded-[var(--radius-base)] flex flex-col p-[24px]']) }}>
    @if(isset($header))
        <div class="mb-[24px] border-b border-[var(--color-divider)] pb-[16px]">
            {{ $header }}
        </div>
    @endif
    
    <div class="flex-grow">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="mt-[24px] border-t border-[var(--color-divider)] pt-[16px]">
            {{ $footer }}
        </div>
    @endif
</div>
