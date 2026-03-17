@props([
    'icon' => 'fas fa-inbox',
    'title' => 'No Data Available',
    'message' => 'There is currently no data to display here.',
    'actionText' => null,
    'actionRoute' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-[64px] text-center bg-[var(--color-bg)] rounded-[var(--radius-base)] border border-[var(--color-divider)]']) }}>
    <div class="w-[64px] h-[64px] rounded-full bg-[var(--color-primary)] text-[var(--color-primary)] opacity-10 flex items-center justify-center text-[28px] mb-[24px] relative">
        <i class="{{ $icon }} absolute opacity-100"></i>
    </div>
    
    <h3 class="text-[20px] font-bold text-[var(--color-text)] mb-[8px]">{{ $title }}</h3>
    <p class="text-[16px] text-[var(--color-text)] opacity-70 max-w-sm mb-[32px]">{{ $message }}</p>
    
    @if($actionText && $actionRoute)
        <a href="{{ $actionRoute }}" class="inline-flex items-center justify-center px-[24px] py-[12px] bg-[var(--color-primary)] text-[var(--color-white)] text-[16px] font-medium rounded-[var(--radius-base)] hover:opacity-90 transition-ui btn-active-state">
            {{ $actionText }}
        </a>
    @endif
</div>
