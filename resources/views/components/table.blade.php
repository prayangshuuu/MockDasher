<div class="w-full overflow-x-auto bg-[var(--color-bg)]">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-[16px] text-[var(--color-text)] border-collapse']) }}>
        @if(isset($header))
            <thead class="border-b border-[var(--color-divider)] font-bold">
                {{ $header }}
            </thead>
        @endif
        
        <tbody class="divide-y divide-[var(--color-divider)] bg-[var(--color-bg)] [&>tr]:transition-colors [&>tr]:duration-150 [&>tr]:ease-out hover:[&>tr]:bg-black/5 cursor-pointer">
            {{ $slot }}
        </tbody>
    </table>
</div>
