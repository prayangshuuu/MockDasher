@extends('layouts.admin')

@section('title', 'Writing Tasks Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    {{-- Page Header --}}
    <x-admin.page-header
        title="Writing Module Manager"
        :description="'IELTS ' . $testSet->test->book_number . ' · ' . $testSet->test->exam_type . ' ' . $testSet->test->year . ' · Set ' . $testSet->set_number"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $testSet->id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Stats Strip --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tasks</p>
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $tasks->count() }}<span class="text-lg text-slate-300">/2</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Min Words (Total)</p>
            <p class="text-3xl font-black text-primary">{{ $tasks->sum('minimum_word_count') }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
            @if($tasks->count() >= 2)
                <p class="text-lg font-black text-emerald-500 flex items-center gap-2"><span class="material-symbols-outlined">check_circle</span> Ready</p>
            @else
                <p class="text-lg font-black text-amber-500 flex items-center gap-2"><span class="material-symbols-outlined">pending</span> Incomplete</p>
            @endif
        </div>
    </div>

    {{-- Existing Tasks --}}
    @if($tasks->isNotEmpty())
    <div class="space-y-6">
        @foreach($tasks as $task)
        <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-2xl flex items-center justify-center shrink-0 {{ $task->task_number === 1 ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-500' : 'bg-violet-50 dark:bg-violet-900/20 text-violet-500' }}">
                        <span class="material-symbols-outlined text-2xl">{{ $task->task_number === 1 ? 'bar_chart' : 'edit_note' }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $task->task_title }}</h3>
                        <p class="text-sm text-slate-500 font-bold">{{ $task->task_description }} · Min {{ $task->minimum_word_count }} words</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-admin.button :href="route('admin.writing-tasks.edit', $task->id)" variant="secondary" size="sm">
                        <span class="material-symbols-outlined text-sm mr-1">edit</span> Edit
                    </x-admin.button>
                    <form action="{{ route('admin.writing-tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-xl border border-rose-200 text-rose-500 text-xs font-black uppercase tracking-widest hover:bg-rose-50 transition-all">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="p-6 sm:p-8 space-y-4">
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Prompt</span>
                    <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium whitespace-pre-line bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-100 dark:border-slate-800">{{ $task->task_prompt }}</div>
                </div>
                @if($task->images->isNotEmpty())
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Attached Image</span>
                    <div class="flex gap-4 flex-wrap">
                        @foreach($task->images as $img)
                            <img src="{{ Storage::url($img->image_path) }}" alt="Task {{ $task->task_number }} Image" class="h-40 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm object-cover">
                        @endforeach
                    </div>
                </div>
                @endif
                @if($task->instruction_text)
                <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800">
                    <span class="material-symbols-outlined text-amber-500 text-lg">info</span>
                    <span class="text-sm font-bold text-amber-700 dark:text-amber-400">{{ $task->instruction_text }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Add New Task Form --}}
    @if($tasks->count() < 2)
    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-500">add_circle</span>
                Add New Writing Task
            </h3>
            <p class="text-sm text-slate-500 mt-1">{{ 2 - $tasks->count() }} task slot(s) remaining</p>
        </div>
        <form action="{{ route('admin.writing-tasks.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 text-sm font-bold">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Task Number</label>
                    <select name="task_number" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                        @for($i = 1; $i <= 2; $i++)
                            @if(!$tasks->contains('task_number', $i))
                                <option value="{{ $i }}" {{ old('task_number') == $i ? 'selected' : '' }}>Task {{ $i }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Title</label>
                    <input type="text" name="task_title" value="{{ old('task_title') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required placeholder="e.g. Writing Task 1">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Min Word Count</label>
                    <input type="number" name="minimum_word_count" value="{{ old('minimum_word_count', 150) }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Description</label>
                <input type="text" name="task_description" value="{{ old('task_description') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="e.g. You should spend about 20 minutes on this task.">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Prompt</label>
                <textarea name="task_prompt" rows="5" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>{{ old('task_prompt') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Instruction Text</label>
                    <input type="text" name="instruction_text" value="{{ old('instruction_text') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="e.g. Write at least 150 words.">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Task Image (optional)</label>
                    <input type="file" name="task_image" accept="image/*" class="w-full px-5 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary/10 file:text-primary file:font-black file:text-xs">
                </div>
            </div>
            <div class="flex justify-end">
                <x-admin.button type="submit" size="lg">
                    <span class="material-symbols-outlined text-sm mr-2">add</span> Create Task
                </x-admin.button>
            </div>
        </form>
    </div>
    @else
    <div class="p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800 flex items-center gap-4">
        <span class="material-symbols-outlined text-emerald-500 text-2xl">check_circle</span>
        <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">Both writing tasks have been configured. Edit existing tasks using the buttons above.</p>
    </div>
    @endif
</div>
@endsection
