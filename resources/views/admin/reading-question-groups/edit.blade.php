@extends('layouts.admin')

@section('title', 'Edit Question Group')
@section('header', 'Edit Question Group')
@section('subheader', 'Passage ' . $group->passage->passage_number . ': ' . $group->passage->title)

@section('header_actions')
    <a href="{{ route('admin.reading-passages.edit', $group->reading_passage_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Passage
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-12">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Edit Question Group" 
        description="Modify the configuration for group in: {{ $group->passage->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.reading-passages.edit', $group->reading_passage_id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Passage
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.reading-question-groups.update', $group->id) }}" method="POST" class="p-8 sm:p-10 space-y-8">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Question Type</label>
                <select name="question_type" class="w-full md:w-2/3 px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                    @php
                    $types = [
                        'Completion' => ['sentence_completion'=>'Sentence Completion','summary_completion'=>'Summary Completion','table_completion'=>'Table Completion','flow_chart_completion'=>'Flow-chart Completion'],
                        'MCQ / T/F/NG' => ['multiple_choice'=>'Multiple Choice','true_false_not_given'=>'True / False / Not Given','yes_no_not_given'=>'Yes / No / Not Given'],
                        'Matching' => ['matching_headings'=>'Matching Headings','matching_information'=>'Matching Information','matching_sentence_endings'=>'Matching Sentence Endings'],
                        'Short Answer' => ['short_answer'=>'Short Answer Questions'],
                    ];
                    @endphp
                    @foreach($types as $optgroup => $options)
                        <optgroup label="{{ $optgroup }}">
                            @foreach($options as $val => $label)
                                <option value="{{ $val }}" {{ $group->question_type === $val ? 'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Group Instruction</label>
                <textarea name="group_instruction" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ $group->group_instruction }}</textarea>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ $group->sort_order }}" min="0" class="w-32 px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <button type="button" onclick="if(confirm('Delete this question group and all its questions?')) document.getElementById('delete-group').submit();" class="text-red-500 hover:text-red-700 font-black text-xs uppercase tracking-widest transition-all">
                    Delete Group
                </button>
                <x-admin.button type="submit" size="lg" class="from-orange-500 to-orange-600">
                    Update Group
                </x-admin.button>
            </div>
        </form>

        <form id="delete-group" action="{{ route('admin.reading-question-groups.destroy', $group->id) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>

        {{-- ── Questions in this group ── --}}
        <div class="p-8 sm:p-10 border-t border-slate-100 dark:border-slate-800 space-y-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Questions</h3>
                    <p class="text-sm font-bold text-slate-400 italic mt-1">{{ $group->questions->count() }} question(s) in this group.</p>
                </div>
                <x-admin.button :href="route('admin.questions.create', ['type' => 'reading_group', 'id' => $group->id])" size="sm">
                    <span class="material-symbols-outlined text-lg mr-2">add</span>
                    Add Question
                </x-admin.button>
            </div>

            @if($group->questions->isEmpty())
                <div class="p-12 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 text-center">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl text-slate-400">quiz</span>
                    </div>
                    <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No questions yet</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach($group->questions as $qi => $q)
                        <div class="group flex items-center justify-between p-6 rounded-3xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary hover:shadow-soft transition-all duration-300">
                            <div class="flex items-center gap-6 overflow-hidden">
                                <div class="w-12 h-12 shrink-0 rounded-2xl bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center text-primary font-black text-lg">
                                    {{ $qi + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-white truncate pr-8 leading-tight">{{ $q->question_text }}</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-900/30 text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest border border-orange-100 dark:border-orange-800/50">
                                            {{ str_replace('_', ' ', $q->question_type) }}
                                        </span>
                                        @if($q->correct_answer)
                                            <span class="text-[10px] font-bold text-slate-400 italic">
                                                Ans: <span class="text-slate-600 dark:text-slate-300">{{ $q->correct_answer }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <x-admin.button :href="route('admin.questions.edit', $q->id)" variant="secondary" size="sm">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </x-admin.button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
