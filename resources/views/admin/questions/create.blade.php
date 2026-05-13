@extends('layouts.admin')

@section('title', isset($question) ? 'Edit Question' : 'Add Question')

@section('content')
@php
    $isEditing = isset($question);
    $parentLabel = match($type) {
        'listening' => 'Listening Section ' . ($parent->section_number ?? ''),
        'reading_group' => 'Question Group #' . ($parent->id ?? ''),
        default => 'Reading Passage ' . ($parent->passage_number ?? ''),
    };
    $backRoute = match($type) {
        'listening' => route('admin.listening-sections.edit', $isEditing ? $question->questionable_id : $parent->id),
        'reading_group' => route('admin.reading-question-groups.edit', $isEditing ? $question->questionable_id : $parent->id),
        default => route('admin.reading-passages.edit', $isEditing ? $question->questionable_id : $parent->id),
    };
@endphp

<div x-data="questionAdder()" class="max-w-4xl mx-auto space-y-10">
    <x-admin.page-header
        :title="$isEditing ? 'Edit Question' : 'Add Question'"
        :description="'Configure for: ' . $parentLabel"
    >
        <x-slot:actions>
            <x-admin.button href="{{ $backRoute }}" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ $isEditing ? route('admin.questions.update', $question->id) : route('admin.questions.store', ['type' => $type, 'id' => ($isEditing ? $question->questionable_id : $parent->id)]) }}" method="POST" class="p-8 sm:p-10 space-y-8">
            @csrf
            @if($isEditing) @method('PUT') @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 text-sm font-bold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Question Type Selector --}}
            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Question Type</label>
                <select name="question_type" x-model="questionType" class="w-full md:w-2/3 px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                    <optgroup label="True/False & Yes/No">
                        <option value="true_false_not_given">True / False / Not Given</option>
                        <option value="yes_no_not_given">Yes / No / Not Given</option>
                    </optgroup>
                    <optgroup label="Multiple Choice">
                        <option value="multiple_choice">Multiple Choice (A/B/C/D)</option>
                    </optgroup>
                    <optgroup label="Completion / Fill in the Blanks">
                        <option value="short_answer">Fill in the Blanks / Short Answer</option>
                        <option value="sentence_completion">Sentence Completion</option>
                        <option value="summary_completion">Summary Completion</option>
                        <option value="table_completion">Table Completion</option>
                        <option value="form_completion">Form Completion</option>
                        <option value="flow_chart_completion">Flow-chart Completion</option>
                    </optgroup>
                    <optgroup label="Matching">
                        <option value="matching_headings">Matching Headings</option>
                        <option value="matching_information">Matching Information</option>
                        <option value="matching_sentence_endings">Matching Sentence Endings</option>
                        <option value="matching">Matching</option>
                    </optgroup>
                </select>
            </div>

            {{-- Visual Type Indicator --}}
            <div class="flex items-center gap-3 p-4 rounded-2xl border border-dashed transition-all"
                 :class="{
                    'border-blue-300 bg-blue-50 dark:bg-blue-900/10': questionType === 'true_false_not_given' || questionType === 'yes_no_not_given',
                    'border-violet-300 bg-violet-50 dark:bg-violet-900/10': questionType === 'multiple_choice',
                    'border-amber-300 bg-amber-50 dark:bg-amber-900/10': ['short_answer','sentence_completion','summary_completion','table_completion','form_completion','flow_chart_completion'].includes(questionType),
                    'border-emerald-300 bg-emerald-50 dark:bg-emerald-900/10': ['matching_headings','matching_information','matching_sentence_endings','matching'].includes(questionType),
                 }">
                <span class="material-symbols-outlined text-xl"
                      :class="{
                        'text-blue-500': questionType === 'true_false_not_given' || questionType === 'yes_no_not_given',
                        'text-violet-500': questionType === 'multiple_choice',
                        'text-amber-500': ['short_answer','sentence_completion','summary_completion','table_completion','form_completion','flow_chart_completion'].includes(questionType),
                        'text-emerald-500': ['matching_headings','matching_information','matching_sentence_endings','matching'].includes(questionType),
                      }">
                    <span x-show="questionType === 'true_false_not_given' || questionType === 'yes_no_not_given'">rule</span>
                    <span x-show="questionType === 'multiple_choice'">radio_button_checked</span>
                    <span x-show="['short_answer','sentence_completion','summary_completion','table_completion','form_completion','flow_chart_completion'].includes(questionType)">edit_note</span>
                    <span x-show="['matching_headings','matching_information','matching_sentence_endings','matching'].includes(questionType)">join_inner</span>
                </span>
                <span class="text-xs font-black uppercase tracking-widest opacity-70" x-text="typeLabel"></span>
            </div>

            {{-- Question Text --}}
            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Question Text</label>
                <textarea name="question_text" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required placeholder="Enter the question or statement...">{{ $isEditing ? $question->question_text : old('question_text') }}</textarea>
            </div>

            {{-- MCQ Options (only for multiple_choice) --}}
            <div x-show="questionType === 'multiple_choice'" x-cloak class="p-8 rounded-[2rem] bg-violet-50/50 dark:bg-violet-900/10 border border-violet-200 dark:border-violet-800/50 space-y-6 transition-all">
                <div>
                    <label class="block text-xs font-black text-violet-500 uppercase tracking-widest mb-1 ml-1">Multiple Choice Options</label>
                    <p class="text-[10px] font-bold text-slate-400 italic">Enter options and select the correct one. Leave blank to skip unused slots.</p>
                </div>

                <div class="space-y-4">
                    @for($i = 0; $i < 7; $i++)
                        <div class="flex items-center gap-4 group/opt">
                            <input type="radio" name="correct_option" value="{{ $i }}"
                                   {{ ($isEditing && isset($question) && $question->options->get($i)?->is_correct) ? 'checked' : (!$isEditing && $i == 0 ? 'checked' : '') }}
                                   class="h-5 w-5 text-primary focus:ring-primary/20 border-slate-300 rounded-full shrink-0">
                            <div class="flex items-center gap-3 flex-1">
                                <span class="size-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center text-xs font-black text-violet-600 shrink-0">{{ chr(65+$i) }}</span>
                                <input type="text" name="options[{{ $i }}]"
                                       value="{{ $isEditing ? ($question->options->get($i)?->option_text ?? '') : old('options.'.$i) }}"
                                       placeholder="Option {{ chr(65+$i) }}"
                                       class="w-full px-5 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Correct Answer (for TFNG, YNNG, short answer, matching) --}}
            <div x-show="questionType !== 'multiple_choice'" x-cloak class="space-y-3 transition-all">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Correct Answer</label>

                {{-- TFNG Buttons --}}
                <div x-show="questionType === 'true_false_not_given'" class="flex flex-wrap gap-3">
                    @foreach(['TRUE', 'FALSE', 'NOT GIVEN'] as $opt)
                        <label class="cursor-pointer">
                            <input type="radio" name="correct_answer" value="{{ $opt }}" class="peer hidden"
                                   {{ ($isEditing && strtoupper($question->correct_answer ?? '') === $opt) ? 'checked' : '' }}>
                            <span class="block px-6 py-2.5 rounded-xl border-2 text-xs font-black uppercase tracking-widest transition-all
                                         peer-checked:bg-primary peer-checked:border-primary peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-primary/20
                                         border-slate-200 dark:border-slate-700 text-slate-500 hover:border-primary/50">
                                {{ $opt }}
                            </span>
                        </label>
                    @endforeach
                </div>

                {{-- YNNG Buttons --}}
                <div x-show="questionType === 'yes_no_not_given'" class="flex flex-wrap gap-3">
                    @foreach(['YES', 'NO', 'NOT GIVEN'] as $opt)
                        <label class="cursor-pointer">
                            <input type="radio" name="correct_answer" value="{{ $opt }}" class="peer hidden"
                                   {{ ($isEditing && strtoupper($question->correct_answer ?? '') === $opt) ? 'checked' : '' }}>
                            <span class="block px-6 py-2.5 rounded-xl border-2 text-xs font-black uppercase tracking-widest transition-all
                                         peer-checked:bg-primary peer-checked:border-primary peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-primary/20
                                         border-slate-200 dark:border-slate-700 text-slate-500 hover:border-primary/50">
                                {{ $opt }}
                            </span>
                        </label>
                    @endforeach
                </div>

                {{-- Text input for short answer / matching / completion --}}
                <div x-show="questionType !== 'true_false_not_given' && questionType !== 'yes_no_not_given'">
                    <p class="text-[10px] font-bold text-slate-400 italic mb-2 ml-1">For multiple valid answers, separate with pipe | (e.g. "answer1|answer2")</p>
                    <input type="text" name="correct_answer"
                           value="{{ $isEditing ? $question->correct_answer : old('correct_answer') }}"
                           class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm"
                           placeholder="Enter the correct answer...">
                </div>
            </div>

            {{-- Explanation --}}
            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Explanation <span class="text-slate-300 font-normal">(Optional)</span></label>
                <textarea name="explanation" rows="2" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="Explain why the answer is correct...">{{ $isEditing ? $question->explanation : old('explanation') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                @if($isEditing)
                    <button type="button" onclick="if(confirm('Delete this question?')) document.getElementById('delete-q-form').submit();" class="text-red-500 hover:text-red-700 font-black text-xs uppercase tracking-widest transition-all">
                        Delete Question
                    </button>
                @else
                    <div></div>
                @endif
                <x-admin.button type="submit" size="lg">
                    {{ $isEditing ? 'Update Question' : 'Save Question' }}
                </x-admin.button>
            </div>
        </form>

        @if($isEditing)
            <form id="delete-q-form" action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function questionAdder() {
        return {
            questionType: '{{ $isEditing ? $question->question_type : old("question_type", "true_false_not_given") }}',
            get typeLabel() {
                const labels = {
                    'true_false_not_given': 'True / False / Not Given — Select the correct classification',
                    'yes_no_not_given': 'Yes / No / Not Given — Select the correct classification',
                    'multiple_choice': 'Multiple Choice — Define options and mark the correct one',
                    'short_answer': 'Fill in the Blanks — Type the exact word/number answer',
                    'sentence_completion': 'Sentence Completion — Type the missing word(s)',
                    'summary_completion': 'Summary Completion — Fill in summary gaps',
                    'table_completion': 'Table Completion — Fill in table cells',
                    'form_completion': 'Form Completion — Fill in form fields',
                    'flow_chart_completion': 'Flow-chart Completion — Complete the flow-chart',
                    'matching_headings': 'Matching Headings — Match headings to paragraphs',
                    'matching_information': 'Matching Information — Match information to paragraphs',
                    'matching_sentence_endings': 'Matching Sentence Endings — Complete the sentences',
                    'matching': 'Matching — Match items from two lists',
                };
                return labels[this.questionType] || 'Select a question type';
            }
        }
    }
</script>
@endpush
@endsection
