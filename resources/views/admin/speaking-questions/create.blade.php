@extends('layouts.admin')

@section('title', 'Add Speaking Question')
@section('header', 'Add Speaking Question')
@section('subheader', 'For test: ' . $test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $test->id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('admin.speaking-questions.store', $test->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Speaking Part</label>
                    <select name="part" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="1">Part 1 (Introduction/Interview)</option>
                        <option value="2">Part 2 (Cue Card)</option>
                        <option value="3">Part 3 (Discussion)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Time Limit (Seconds)</label>
                    <input type="number" name="time_limit" placeholder="e.g. 120 (for 2 minutes)" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Question Text / Cue Card</label>
                <textarea name="question_text" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Text for the question or instructions..."></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Preparation Instructions (mostly for Part 2)</label>
                <textarea name="preparation_instructions" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="You have 1 minute to prepare..."></textarea>
            </div>

            <div class="mb-8 border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 text-center">
                <label class="block text-gray-700 text-sm font-semibold mb-3">Examiner Audio (Optional)</label>
                <input type="file" name="audio_file" accept=".mp3,.wav" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Question
                </button>
            </div>
        </form>
    </div>
@endsection
