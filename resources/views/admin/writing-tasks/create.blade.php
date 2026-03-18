@extends('layouts.admin')

@section('title', 'Add Writing Task')
@section('header', 'Add Writing Task')
@section('subheader', 'For test: ' . $testSet->test->title)

@section('header_actions')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Add Writing Task" 
        description="Configure a new writing task for: {{ $testSet->test->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $testSet->id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.writing-tasks.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Task Type</label>
                    <select name="task_number" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                        <option value="1">Task 1 (Description & Image)</option>
                        <option value="2">Task 2 (Essay Prompt)</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Min Word Count</label>
                    <input type="number" name="minimum_word_count" value="150" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Task Title</label>
                <input type="text" name="task_title" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="e.g., The chart below shows..." required>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Instruction Text</label>
                <textarea name="instruction_text" rows="2" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="Summarise the information by selecting and reporting the main features..."></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Task 1 Description</label>
                    <textarea name="task_description" rows="4" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm"></textarea>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Task 2 Prompt</label>
                    <textarea name="task_prompt" rows="4" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm"></textarea>
                </div>
            </div>

            <div class="p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 text-center">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Task Image (Optional - For Task 1)</label>
                <input type="file" name="task_image" class="text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-primary file:text-white hover:file:opacity-90 transition-all">
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <x-admin.button type="submit" size="lg">
                    Save Writing Task
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection
