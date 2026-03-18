@extends('layouts.admin')

@section('title', 'Edit Question')
@section('header', 'Edit Question')
@section('subheader', 'Update details for this question.')

@section('header_actions')
    <a href="{{ route($type == 'listening' ? 'admin.listening-sections.edit' : 'admin.reading-passages.edit', $question->questionable_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to {{ ucfirst($type) }}
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Edit Question" 
        description="Modify configuration for this question."
    >
        <x-slot:actions>
            <x-admin.button :href="route($type == 'listening' ? 'admin.listening-sections.edit' : 'admin.reading-passages.edit', $question->questionable_id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Part
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.questions.update', $question->id) }}" method="POST" class="p-8 sm:p-10 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Question Type</label>
                <select name="question_type" id="question_type" class="w-full md:w-1/2 px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                    <option value="multiple_choice" {{ $question->question_type == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                    <option value="short_answer" {{ $question->question_type == 'short_answer' ? 'selected' : '' }}>Short Answer / Fill in the Blanks</option>
                    <option value="true_false_not_given" {{ $question->question_type == 'true_false_not_given' ? 'selected' : '' }}>True/False/Not Given (or Yes/No)</option>
                    <option value="matching" {{ $question->question_type == 'matching' ? 'selected' : '' }}>Matching</option>
                </select>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Question Text</label>
                <textarea name="question_text" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>{{ $question->question_text }}</textarea>
            </div>

            <!-- Options Container -->
            <div id="options_container" class="p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 space-y-6 {{ $question->question_type != 'multiple_choice' ? 'hidden' : '' }}">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Multiple Choice Options</label>
                    <p class="text-[10px] font-bold text-slate-400 italic">Enter options and select the correct one.</p>
                </div>
                
                <div class="space-y-4">
                    @php
                        $opts = $question->options;
                    @endphp
                    @for($i = 0; $i < max(4, $opts->count()); $i++)
                        @php
                            $opt = $opts->get($i);
                        @endphp
                        <div class="flex items-center gap-4">
                            <input type="radio" name="correct_option" value="{{ $i }}" {{ ($opt && $opt->is_correct) || (!$opts->count() && $i==0) ? 'checked' : '' }} class="h-5 w-5 text-primary focus:ring-primary/20 border-slate-300 rounded-full">
                            <input type="text" name="options[{{ $i }}]" value="{{ $opt ? $opt->option_text : '' }}" placeholder="Option {{ $i+1 }}" class="w-full px-5 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                        </div>
                    @endfor
                </div>
            </div>

            <div id="exact_answer_container" class="space-y-3 {{ $question->question_type == 'multiple_choice' ? 'hidden' : '' }}">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Correct Answer (Exact Value)</label>
                <p class="text-[10px] font-bold text-slate-400 italic mb-2 ml-1">For short answer, T/F/NG, or matching.</p>
                <input type="text" name="correct_answer" id="correct_answer_input" value="{{ $question->correct_answer }}" {{ $question->question_type != 'multiple_choice' ? 'required' : '' }} class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Explanation <span class="text-slate-300 font-normal">(Optional)</span></label>
                <textarea name="explanation" rows="2" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ $question->explanation }}</textarea>
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this question?')) { document.getElementById('delete-question').submit(); }" class="text-red-500 hover:text-red-700 font-black text-xs uppercase tracking-widest transition-all">
                    Delete Question
                </button>
                <x-admin.button type="submit" size="lg">
                    Update Question
                </x-admin.button>
            </div>
        </form>

        <form id="delete-question" action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('question_type');
        const optionsContainer = document.getElementById('options_container');
        const exactAnswerContainer = document.getElementById('exact_answer_container');
        const exactAnswerInput = document.getElementById('correct_answer_input');

        function toggleFields() {
            if (typeSelect.value === 'multiple_choice') {
                optionsContainer.style.display = 'block';
                exactAnswerContainer.classList.add('hidden');
                exactAnswerInput.removeAttribute('required');
            } else {
                optionsContainer.style.display = 'none';
                exactAnswerContainer.classList.remove('hidden');
                exactAnswerInput.setAttribute('required', 'required');
            }
        }

        typeSelect.addEventListener('change', toggleFields);
    });
</script>
@endsection

@endsection
