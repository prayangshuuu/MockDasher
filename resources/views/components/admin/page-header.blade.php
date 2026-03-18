@props(['title', 'description'])

<div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
    <div>
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $title }}</h2>
        @if($description)
            <p class="text-slate-500 dark:text-slate-400 text-base">{{ $description }}</p>
        @endif
    </div>
    <div class="flex flex-wrap items-center gap-4">
        {{ $actions ?? '' }}
    </div>
</div>
