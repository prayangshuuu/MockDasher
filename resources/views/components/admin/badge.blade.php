@props([
    'type' => 'info',
    'label'
])

@php
    $types = [
        'success' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20',
        'warning' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20',
        'danger' => 'bg-red-50 text-red-600 dark:bg-red-900/20',
        'info' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20',
        'primary' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20',
    ];
    $typeClass = $types[$type] ?? $types['info'];
@endphp

<span {{ $attributes->merge(['class' => "px-2 py-0.5 rounded-lg $typeClass text-[10px] font-black uppercase tracking-widest border border-current opacity-70"]) }}>
    {{ $label }}
</span>
