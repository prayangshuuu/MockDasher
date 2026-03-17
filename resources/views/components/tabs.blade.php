<div {{ $attributes->merge(['class' => 'border-b border-[var(--color-divider)]']) }}>
    <nav class="-mb-px flex space-x-[24px]" aria-label="Tabs">
        {{ $slot }}
    </nav>
</div>
