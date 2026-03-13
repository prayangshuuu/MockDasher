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
<div class="max-w-3xl">
    <form action="{{ route('admin.reading-question-groups.store', $passage->id) }}" method="POST"
          class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
        @csrf

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Question Type</label>
            <select name="question_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
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

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Group Instruction</label>
            <p class="text-xs text-gray-500 mb-2">Displayed above each question group, e.g. "Questions 1–7: Do the following statements agree with the information in the reading passage?"</p>
            <textarea name="group_instruction" rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                placeholder="Questions X–Y: …"></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Sort Order</label>
            <input type="number" name="sort_order" value="0" min="0"
                class="w-24 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first.</p>
        </div>

        <div class="flex justify-end border-t border-gray-100 pt-6">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Create Group
            </button>
        </div>
    </form>
</div>
@endsection
