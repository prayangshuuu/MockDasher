<div class="w-full overflow-x-auto bg-[var(--color-bg-primary)]">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-[16px] text-[var(--color-text-primary)] border-collapse']) }}>
        @if(isset($header))
            <thead class="sticky top-0 bg-[var(--color-bg-secondary)] z-10 border-b border-[var(--color-divider)] font-bold text-[var(--color-text-primary)] uppercase text-[14px]">
                {{ $header }}
            </thead>
        @endif
        
        <tbody class="divide-y divide-[var(--color-divider)] bg-[var(--color-bg-primary)] [&>tr]:transition-colors [&>tr]:duration-150 [&>tr]:ease-out hover:[&>tr]:bg-[var(--color-bg-secondary)] cursor-pointer">
            {{ $slot }}
        </tbody>
    </table>
</div>
