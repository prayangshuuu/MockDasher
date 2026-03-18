@props([
    'title',
    'description',
    'icon' => 'inventory_2',
    'actionHref' => null,
    'actionLabel' => null,
    'actionIcon' => 'add'
])

<div class="bg-white dark:bg-slate-900 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800 p-20 text-center">
    <div class="size-20 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
        <span class="material-symbols-outlined text-4xl text-slate-300">{{ $icon }}</span>
    </div>
    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">{{ $title }}</h3>
    <p class="text-slate-500 font-medium mb-8">{{ $description }}</p>
    
    @if($actionHref && $actionLabel)
        <x-admin.button :href="$actionHref" :icon="$actionIcon" size="lg">
            {{ $actionLabel }}
        </x-admin.button>
    @endif
</div>
