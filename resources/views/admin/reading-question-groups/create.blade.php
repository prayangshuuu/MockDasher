@extends('layouts.admin')

@section('title', 'Add Question Group')
@section('header', 'Add Question Group')
@section('subheader', 'Passage ' . $passage->passage_number . ': ' . $passage->title)

@section('header_actions')
    <a href="{{ route('admin.reading-passages.edit', $passage->id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Passage
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Add Question Group" 
        description="Configure a new question group for: {{ $passage->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.reading-passages.edit', $passage->id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Passage
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.reading-question-groups.store', $passage->id) }}" method="POST" class="p-8 sm:p-10 space-y-8">
            @csrf

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Question Type</label>
                <select name="question_type" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                    <optgroup label="Completion">
                        <option value="sentence_completion">Sentence Completion</option>
                        <option value="summary_completion">Summary Completion</option>
                        <option value="table_completion">Table Completion</option>
                        <option value="flow_chart_completion">Flow-chart Completion</option>
                    </optgroup>
                    <optgroup label="Multiple Choice / T/F/NG">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false_not_given">True / False / Not Given</option>
                        <option value="yes_no_not_given">Yes / No / Not Given</option>
                    </optgroup>
                    <optgroup label="Matching">
                        <option value="matching_headings">Matching Headings</option>
                        <option value="matching_information">Matching Information</option>
                        <option value="matching_sentence_endings">Matching Sentence Endings</option>
                    </optgroup>
                    <optgroup label="Short Answer">
                        <option value="short_answer">Short Answer Questions</option>
                    </optgroup>
                </select>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Group Instruction</label>
                <p class="text-[10px] font-bold text-slate-400 italic mb-2">Displayed above each question group</p>
                <textarea name="group_instruction" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="Questions X–Y: …"></textarea>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                <input type="number" name="sort_order" value="0" min="0" class="w-32 px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 italic mt-2 ml-1">Lower numbers appear first.</p>
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <x-admin.button type="submit" size="lg" class="from-orange-500 to-orange-600">
                    Create Group
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection

@endsection
