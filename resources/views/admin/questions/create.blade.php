@extends('layouts.admin')

@section('title', 'Add Question')
@section('header', 'Add Question')
@section('subheader', 'For ' . ($type == 'listening' ? 'Listening Section '.$parent->section_number : 'Reading Passage '.$parent->passage_number))

@section('header_actions')
    <a href="{{ route($type == 'listening' ? 'admin.listening-sections.edit' : 'admin.reading-passages.edit', $parent->id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to {{ ucfirst($type) }}
    </a>
@endsection

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('admin.questions.store', ['type' => $type, 'id' => $parent->id]) }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Question Type</label>
                <select name="question_type" id="question_type" class="w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="short_answer">Short Answer / Fill in the Blanks</option>
                    <option value="true_false_not_given">True/False/Not Given (or Yes/No)</option>
                    <option value="matching">Matching</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Question Text</label>
                <textarea name="question_text" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required></textarea>
            </div>

            <!-- Options Container (Dynamic) -->
            <div id="options_container" class="mb-6 border border-gray-200 p-4 rounded bg-gray-50">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Multiple Choice Options</label>
                <p class="text-xs text-gray-500 mb-4">Enter options and select the correct one.</p>
                
                <div class="space-y-3">
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="correct_option" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <input type="text" name="options[{{ $i }}]" placeholder="Option {{ $i+1 }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    @endfor
                </div>
            </div>

            <div id="exact_answer_container" class="mb-6 hidden">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Correct Answer (Exact Value)</label>
                <p class="text-xs text-gray-500 mb-2">For short answer, T/F/NG, or matching. Multiple valid answers can be separated by commas if needed (handle logic in frontend).</p>
                <input type="text" name="correct_answer" id="correct_answer_input" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Explanation (Optional)</label>
                <textarea name="explanation" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Explain why the answer is correct..."></textarea>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Question
                </button>
            </div>
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
            toggleFields(); // Initial load
        });
    </script>
@endsection
