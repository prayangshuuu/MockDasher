@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-base transition-colors group {{ $active ? 'bg-primary/10 text-primary shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
    <div class="size-8 flex items-center justify-center rounded-base {{ $active ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 dark:bg-slate-800 text-slate-400' }}">
        <span class="material-symbols-outlined text-[18px] {{ $active ? 'fill-1' : '' }}">{{ $icon }}</span>
    </div>
    <span class="text-sm font-bold tracking-tight">{{ $slot }}</span>
</a>
