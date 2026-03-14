<div {{ $attributes->merge(['class' => 'border-b border-[var(--color-dwimik-divider)]']) }}>
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        {{ $slot }}
    </nav>
</div>
