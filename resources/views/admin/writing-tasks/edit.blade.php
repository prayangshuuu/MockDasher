@extends('layouts.admin')

@section('title', 'Edit Writing Task')
@section('header', 'Edit Writing Task')
@section('subheader', 'For test: ' . $writing_task->testSet->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $writing_task->testSet->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Edit Writing Task" 
        description="Modify the configuration for task in: {{ $writing_task->testSet->test->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $writing_task->test_set_id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.writing-tasks.update', $writing_task->id) }}" method="POST" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Task Type</label>
                    <select name="task_number" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                        <option value="1" {{ $writing_task->task_number == 1 ? 'selected' : '' }}>Task 1 (Description & Image)</option>
                        <option value="2" {{ $writing_task->task_number == 2 ? 'selected' : '' }}>Task 2 (Essay Prompt)</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Min Word Count</label>
                    <input type="number" name="minimum_word_count" value="{{ $writing_task->minimum_word_count }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Task Title</label>
                <input type="text" name="task_title" value="{{ $writing_task->task_title }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Instruction Text</label>
                <textarea name="instruction_text" rows="2" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ $writing_task->instruction_text }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Task 1 Description</label>
                    <textarea name="task_description" rows="5" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ $writing_task->task_description }}</textarea>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Task 2 Prompt</label>
                    <textarea name="task_prompt" rows="5" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ $writing_task->task_prompt }}</textarea>
                </div>
            </div>

            <div class="p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex-1">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Upload New Task Image</label>
                    <p class="text-[10px] font-bold text-slate-400 italic mb-4">Optional. Will replace any existing image.</p>
                    <input type="file" name="task_image" class="text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-primary file:text-white hover:file:opacity-90 transition-all">
                </div>
                
                @if($writing_task->images->isNotEmpty())
                    <div class="w-48 shrink-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 text-center">Current Image</p>
                        <img src="{{ Storage::url($writing_task->images->first()->image_path) }}" alt="Task Image" class="w-full h-auto rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    </div>
                @endif
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this writing task?')) { document.getElementById('delete-task').submit(); }" class="text-red-500 hover:text-red-700 font-black text-xs uppercase tracking-widest transition-all">
                    Delete Task
                </button>
                <x-admin.button type="submit" size="lg">
                    Update Task
                </x-admin.button>
            </div>
        </form>

        <form id="delete-task" action="{{ route('admin.writing-tasks.destroy', $writing_task->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
