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
    <div class="max-w-4xl">
        <form action="{{ route('admin.questions.update', $question->id) }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Question Type</label>
                <select name="question_type" id="question_type" class="w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="multiple_choice" {{ $question->question_type == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                    <option value="short_answer" {{ $question->question_type == 'short_answer' ? 'selected' : '' }}>Short Answer / Fill in the Blanks</option>
                    <option value="true_false_not_given" {{ $question->question_type == 'true_false_not_given' ? 'selected' : '' }}>True/False/Not Given (or Yes/No)</option>
                    <option value="matching" {{ $question->question_type == 'matching' ? 'selected' : '' }}>Matching</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Question Text</label>
                <textarea name="question_text" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>{{ $question->question_text }}</textarea>
            </div>

            <!-- Options Container -->
            <div id="options_container" class="mb-6 border border-gray-200 p-4 rounded bg-gray-50 {{ $question->question_type != 'multiple_choice' ? 'hidden' : '' }}">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Multiple Choice Options</label>
                <p class="text-xs text-gray-500 mb-4">Enter options and select the correct one.</p>
                
                <div class="space-y-3">
                    @php
                        $opts = $question->options;
                    @endphp
                    @for($i = 0; $i < max(4, $opts->count()); $i++)
                        @php
                            $opt = $opts->get($i);
                        @endphp
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="correct_option" value="{{ $i }}" {{ ($opt && $opt->is_correct) || (!$opts->count() && $i==0) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <input type="text" name="options[{{ $i }}]" value="{{ $opt ? $opt->option_text : '' }}" placeholder="Option {{ $i+1 }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    @endfor
                </div>
            </div>

            <div id="exact_answer_container" class="mb-6 {{ $question->question_type == 'multiple_choice' ? 'hidden' : '' }}">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Correct Answer (Exact Value)</label>
                <p class="text-xs text-gray-500 mb-2">For short answer, T/F/NG, or matching.</p>
                <input type="text" name="correct_answer" id="correct_answer_input" value="{{ $question->correct_answer }}" {{ $question->question_type != 'multiple_choice' ? 'required' : '' }} class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Explanation (Optional)</label>
                <textarea name="explanation" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $question->explanation }}</textarea>
            </div>

            <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this question?')) { document.getElementById('delete-question').submit(); }" class="text-red-600 hover:text-red-800 font-medium text-sm transition">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Question
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Question
                </button>
            </div>
        </form>

        <form id="delete-question" action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('question_type');
            const optionsContainer = document.getElementById('options_container');
            const exactAnswerContainer = document.getElementById('exact_answer_container');
            const exactAnswerInput = document.getElementById('correct_answer_input');

            function toggleFields() {
                if (typeSelect.value === 'multiple_choice') {
                    optionsContainer.classList.remove('hidden');
                    exactAnswerContainer.classList.add('hidden');
                    exactAnswerInput.removeAttribute('required');
                } else {
                    optionsContainer.classList.add('hidden');
                    exactAnswerContainer.classList.remove('hidden');
                    exactAnswerInput.setAttribute('required', 'required');
                }
            }

            typeSelect.addEventListener('change', toggleFields);
            // Don't call toggleFields on load to respect server-rendered state
        });
    </script>
@endsection
