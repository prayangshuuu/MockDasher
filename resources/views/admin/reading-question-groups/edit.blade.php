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
<div class="max-w-4xl space-y-8">

    {{-- ── Group Settings ── --}}
    <form action="{{ route('admin.reading-question-groups.update', $group->id) }}" method="POST"
          class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Question Type</label>
            <select name="question_type" class="w-full md:w-2/3 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
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

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Group Instruction</label>
            <textarea name="group_instruction" rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">{{ $group->group_instruction }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Sort Order</label>
            <input type="number" name="sort_order" value="{{ $group->sort_order }}" min="0"
                class="w-24 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
        </div>

        <div class="flex justify-between items-center border-t border-gray-100 pt-6">
            <button type="button"
                onclick="if(confirm('Delete this question group and all its questions?')) document.getElementById('delete-group').submit();"
                class="text-red-600 hover:text-red-800 font-medium text-sm">
                <i class="fas fa-trash-alt mr-1"></i> Delete Group
            </button>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                <i class="fas fa-save mr-2"></i> Update Group
            </button>
        </div>
    </form>

    <form id="delete-group" action="{{ route('admin.reading-question-groups.destroy', $group->id) }}" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>

    {{-- ── Questions in this group ── --}}
    <div>
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Questions</h3>
                <p class="text-sm text-gray-500">{{ $group->questions->count() }} question(s) in this group.</p>
            </div>
            <a href="{{ route('admin.questions.create', ['type' => 'reading_group', 'id' => $group->id]) }}"
               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded shadow-sm transition text-sm">
                <i class="fas fa-plus mr-2"></i> Add Question
            </a>
        </div>

        @if($group->questions->isEmpty())
            <div class="bg-gray-50 border border-gray-200 rounded p-6 text-center text-gray-500">
                No questions yet. Click "Add Question" to begin.
            </div>
        @else
            <ul class="space-y-3">
                @foreach($group->questions as $qi => $q)
                    <li class="bg-white border border-gray-200 rounded p-4 flex justify-between items-start hover:shadow-sm transition">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 truncate">Q{{ $qi + 1 }}. {{ \Illuminate\Support\Str::limit($q->question_text, 90) }}</p>
                            <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500">
                                <span class="bg-orange-50 text-orange-700 border border-orange-200 px-2 py-0.5 rounded">{{ ucwords(str_replace('_', ' ', $q->question_type)) }}</span>
                                @if($q->correct_answer)
                                    <span>Answer: <strong class="text-gray-700">{{ $q->correct_answer }}</strong></span>
                                @elseif($q->options->isNotEmpty())
                                    <span>{{ $q->options->count() }} options</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('admin.questions.edit', $q->id) }}"
                           class="ml-4 text-blue-600 hover:text-blue-800 text-sm font-medium flex-shrink-0">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
