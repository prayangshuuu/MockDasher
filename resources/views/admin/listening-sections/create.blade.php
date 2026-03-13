@extends('layouts.admin')

@section('title', 'Add Listening Section')
@section('header', 'Add Listening Section')
@section('subheader', 'For test: ' . $test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $test->id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('admin.listening-sections.store', $test->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Section Number</label>
                <select name="section_number" class="w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <option value="1">Part 1 (Conversation, everyday social context)</option>
                    <option value="2">Part 2 (Monologue, everyday social context)</option>
                    <option value="3">Part 3 (Conversation, educational/training context)</option>
                    <option value="4">Part 4 (Monologue, academic subject)</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Section Instructions</label>
                <p class="text-xs text-gray-500 mb-2">Displayed to test-takers above the questions (e.g., "Questions 1–10. Complete the form below. Write ONE WORD AND/OR A NUMBER for each answer.")</p>
                <textarea name="instruction_text" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="e.g. Questions 1–10. Complete the form below. Write ONE WORD AND/OR A NUMBER for each answer."></textarea>
            </div>

            <div class="mb-8 border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 text-center">
                <label class="block text-gray-700 text-sm font-semibold mb-3">Audio Recording <span class="text-red-500">*</span></label>
                <input type="file" name="audio_file" accept=".mp3,.wav" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" required>
                <p class="text-xs text-gray-400 mt-2">MP3 or WAV — max 10MB</p>
            </div>

            <div class="mb-6 border border-gray-100 p-4 rounded bg-gray-50">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Transcript / Passage Text <span class="text-gray-400 font-normal">(Optional)</span></label>
                <p class="text-xs text-gray-500 mb-2">For admin reference. Not shown to test-takers during the exam.</p>
                <textarea name="passage_text" rows="8" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Section
                </button>
            </div>
        </form>
    </div>
@endsection
