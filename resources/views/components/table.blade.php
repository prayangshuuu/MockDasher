<div class="w-full overflow-x-auto border border-[var(--color-dwimik-divider)] rounded-[var(--radius-dwimik)] bg-white">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-[var(--color-dwimik-text)] border-collapse']) }}>
        @if(isset($header))
            <thead class="bg-[#F9F8F6] border-b border-[var(--color-dwimik-divider)]">
                {{ $header }}
            </thead>
        @endif
        
        <tbody class="divide-y divide-[var(--color-dwimik-divider)] bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
