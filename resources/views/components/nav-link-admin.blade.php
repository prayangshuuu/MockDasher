@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors group {{ $active ? 'bg-primary/10 text-primary' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
    <span class="material-symbols-outlined {{ $active ? 'fill-1' : '' }}">{{ $icon }}</span>
    <span class="text-sm font-medium">{{ $slot }}</span>
</a>
