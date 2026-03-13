@extends('layouts.admin')

@section('title', 'Edit Speaking Question')
@section('header', 'Edit Speaking Question')
@section('subheader', 'For test: ' . $speaking_question->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $speaking_question->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('admin.speaking-questions.update', $speaking_question->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Speaking Part</label>
                    <select name="part" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="1" {{ $speaking_question->part == 1 ? 'selected' : '' }}>Part 1 (Introduction/Interview)</option>
                        <option value="2" {{ $speaking_question->part == 2 ? 'selected' : '' }}>Part 2 (Cue Card)</option>
                        <option value="3" {{ $speaking_question->part == 3 ? 'selected' : '' }}>Part 3 (Discussion)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Time Limit (Seconds)</label>
                    <input type="number" name="time_limit" value="{{ $speaking_question->time_limit }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Question Text / Cue Card</label>
                <textarea name="question_text" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $speaking_question->question_text }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Preparation Instructions</label>
                <textarea name="preparation_instructions" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $speaking_question->preparation_instructions }}</textarea>
            </div>

            <div class="mb-8 border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 flex items-center justify-between">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">Update Examiner Audio</label>
                    <p class="text-xs text-gray-500 mb-3">Optional. Will replace existing audio.</p>
                    <input type="file" name="audio_file" accept=".mp3,.wav" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>
                
                @if($speaking_question->audio_path)
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-600">Current Audio:</span>
                        <audio controls class="h-8 w-64">
                            <source src="{{ Storage::url($speaking_question->audio_path) }}" type="audio/mpeg">
                        </audio>
                    </div>
                @endif
            </div>

            <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this speaking question?')) { document.getElementById('delete-question').submit(); }" class="text-red-600 hover:text-red-800 font-medium text-sm transition">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Question
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Question
                </button>
            </div>
        </form>

        <form id="delete-question" action="{{ route('admin.speaking-questions.destroy', $speaking_question->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
