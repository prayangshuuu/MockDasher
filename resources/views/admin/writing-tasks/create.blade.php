@extends('layouts.admin')

@section('title', 'Writing Tasks Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Writing Module Manager</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">IELTS {{ $testSet->test->book_number }} · {{ $testSet->test->exam_type }} {{ $testSet->test->year }} · Set {{ $testSet->set_number }}</p>
        </div>
        <a href="{{ route('admin.test_sets.show', $testSet->id) }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors shadow-sm">
            <img src="/storage/asset/icons/arrowback.svg" class="w-4 h-4 opacity-70" alt="Back" />
            Back to Set
        </a>
    </div>

    {{-- Stats Strip --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Tasks</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $tasks->count() }}<span class="text-lg text-slate-400 font-medium">/2</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Min Words (Total)</p>
            <p class="text-3xl font-bold text-primary">{{ $tasks->sum('minimum_word_count') }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Status</p>
            @if($tasks->count() >= 2)
                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                    <img src="/storage/asset/icons/check-circle.svg" class="w-5 h-5 text-emerald-500" alt="Ready" style="filter: invert(56%) sepia(54%) saturate(464%) hue-rotate(107deg) brightness(97%) contrast(92%);" />
                    Ready
                </p>
            @else
                <p class="text-sm font-bold text-amber-600 dark:text-amber-400 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">pending</span> Incomplete
                </p>
            @endif
        </div>
    </div>

    {{-- Existing Tasks --}}
    @if($tasks->isNotEmpty())
    <div class="space-y-6">
        @foreach($tasks as $task)
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-xl flex items-center justify-center shrink-0 border {{ $task->task_number === 1 ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-100 dark:border-indigo-800' : 'bg-violet-50 dark:bg-violet-900/30 border-violet-100 dark:border-violet-800' }}">
                        <img src="{{ $task->task_number === 1 ? '/storage/asset/icons/bar-chart.svg' : '/storage/asset/icons/edit.svg' }}" class="w-6 h-6 opacity-60" alt="Task Icon" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $task->task_title }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">{{ $task->task_description }} · Min {{ $task->minimum_word_count }} words</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.writing-tasks.edit', $task->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm">
                        <img src="/storage/asset/icons/edit.svg" class="w-4 h-4 opacity-70" alt="Edit" />
                        Edit
                    </a>
                    <form action="{{ route('admin.writing-tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex size-10 items-center justify-center rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 transition-colors border border-red-100 dark:border-red-800 shadow-sm" title="Delete">
                            <img src="/storage/asset/icons/delete.svg" class="w-5 h-5 opacity-70" alt="Delete" />
                        </button>
                    </form>
                </div>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest block mb-2">Prompt</span>
                    <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium whitespace-pre-line bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-inner">{{ $task->task_prompt }}</div>
                </div>
                @if($task->images->isNotEmpty())
                <div>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest block mb-2">Attached Image</span>
                    <div class="flex gap-4 flex-wrap">
                        @foreach($task->images as $img)
                            <img src="{{ Storage::url($img->image_path) }}" alt="Task {{ $task->task_number }} Image" class="h-40 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm object-cover">
                        @endforeach
                    </div>
                </div>
                @endif
                @if($task->instruction_text)
                <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 shadow-sm">
                    <img src="/storage/asset/icons/info.svg" class="w-5 h-5 text-amber-500 opacity-70" alt="Info" />
                    <span class="text-sm font-bold text-amber-800 dark:text-amber-300">{{ $task->instruction_text }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Add New Task Form --}}
    @if($tasks->count() < 2)
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden mt-8">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <img src="/storage/asset/icons/create.svg" class="w-6 h-6 text-primary" alt="Add" style="filter: invert(30%) sepia(85%) saturate(2716%) hue-rotate(231deg) brightness(97%) contrast(97%);" />
                Add New Writing Task
            </h3>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700">{{ 2 - $tasks->count() }} task slot(s) remaining</p>
        </div>
        <form action="{{ route('admin.writing-tasks.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Task Number</label>
                    <div class="relative">
                        <select name="task_number" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm appearance-none cursor-pointer">
                            @for($i = 1; $i <= 2; $i++)
                                @if(!$tasks->contains('task_number', $i))
                                    <option value="{{ $i }}" {{ old('task_number') == $i ? 'selected' : '' }}>Task {{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                        <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Title</label>
                    <input type="text" name="task_title" value="{{ old('task_title') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required placeholder="e.g. Writing Task 1">
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Min Word Count</label>
                    <input type="number" name="minimum_word_count" value="{{ old('minimum_word_count', 150) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                </div>
            </div>
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Description</label>
                <input type="text" name="task_description" value="{{ old('task_description') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" placeholder="e.g. You should spend about 20 minutes on this task.">
            </div>
            <div class="space-y-3 bg-slate-50 dark:bg-slate-900/30 p-5 rounded-xl border border-slate-200 dark:border-slate-700" id="precontext-field">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">
                    Task 1 Data Context <span class="text-primary">(AI Evaluation Context)</span>
                </label>
                <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 mb-2">
                    For Task 1 only. The image you upload is shown to students as usual. This text is what gets sent to Gemini for AI scoring — describe the data the image shows in detail. Example: "A bar chart showing the percentage of households with internet access in five countries between 2000 and 2020."
                </p>
                <textarea name="precontext" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-surface-dark text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-sm" placeholder="Describe the visual data here...">{{ old('precontext') }}</textarea>
            </div>
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Prompt</label>
                <textarea name="task_prompt" rows="5" class="w-full px-4 py-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>{{ old('task_prompt') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Instruction Text</label>
                    <input type="text" name="instruction_text" value="{{ old('instruction_text') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" placeholder="e.g. Write at least 150 words.">
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Task Image (optional)</label>
                    <input type="file" name="task_image" accept="image/*" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-bold file:text-xs file:cursor-pointer hover:file:bg-primary/20">
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                    <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Create" />
                    Create Task
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="mt-8 p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 flex items-center gap-4 shadow-sm">
        <img src="/storage/asset/icons/check-circle.svg" class="w-8 h-8 text-emerald-500" alt="Complete" style="filter: invert(56%) sepia(54%) saturate(464%) hue-rotate(107deg) brightness(97%) contrast(92%);" />
        <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Both writing tasks have been configured. Edit existing tasks using the buttons above.</p>
    </div>
    @endif
</div>
@endsection
