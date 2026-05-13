@extends('layouts.admin')

@section('title', 'Add Question Group')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
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

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 text-sm font-bold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Question Type</label>
                <select name="question_type" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                    <optgroup label="True/False & Yes/No">
                        <option value="true_false_not_given" {{ old('question_type') == 'true_false_not_given' ? 'selected' : '' }}>True / False / Not Given</option>
                        <option value="yes_no_not_given" {{ old('question_type') == 'yes_no_not_given' ? 'selected' : '' }}>Yes / No / Not Given</option>
                    </optgroup>
                    <optgroup label="Multiple Choice">
                        <option value="multiple_choice" {{ old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice (MCQ)</option>
                    </optgroup>
                    <optgroup label="Completion / Fill in the Blanks">
                        <option value="short_answer" {{ old('question_type') == 'short_answer' ? 'selected' : '' }}>Fill in the Blanks / Short Answer</option>
                        <option value="sentence_completion" {{ old('question_type') == 'sentence_completion' ? 'selected' : '' }}>Sentence Completion</option>
                        <option value="summary_completion" {{ old('question_type') == 'summary_completion' ? 'selected' : '' }}>Summary Completion</option>
                        <option value="table_completion" {{ old('question_type') == 'table_completion' ? 'selected' : '' }}>Table Completion</option>
                        <option value="flow_chart_completion" {{ old('question_type') == 'flow_chart_completion' ? 'selected' : '' }}>Flow-chart Completion</option>
                    </optgroup>
                    <optgroup label="Matching">
                        <option value="matching_headings" {{ old('question_type') == 'matching_headings' ? 'selected' : '' }}>Matching Headings</option>
                        <option value="matching_information" {{ old('question_type') == 'matching_information' ? 'selected' : '' }}>Matching Information</option>
                        <option value="matching_sentence_endings" {{ old('question_type') == 'matching_sentence_endings' ? 'selected' : '' }}>Matching Sentence Endings</option>
                    </optgroup>
                </select>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Group Instruction</label>
                <p class="text-[10px] font-bold text-slate-400 italic mb-2">Displayed above each question group (e.g. "Questions 1–6: Write TRUE, FALSE or NOT GIVEN.")</p>
                <textarea name="group_instruction" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="Questions X–Y: …">{{ old('group_instruction') }}</textarea>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-32 px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
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
