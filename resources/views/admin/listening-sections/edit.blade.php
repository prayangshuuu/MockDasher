@extends('layouts.admin')

@section('title', 'Edit Listening Section')
@section('header', 'Edit Listening Section')
@section('subheader', 'For test: ' . $listening_section->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $listening_section->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('admin.listening-sections.update', $listening_section->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Section Number</label>
                <select name="section_number" class="w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <option value="1" {{ $listening_section->section_number == 1 ? 'selected' : '' }}>Section 1 (Conversation, everyday context)</option>
                    <option value="2" {{ $listening_section->section_number == 2 ? 'selected' : '' }}>Section 2 (Monologue, everyday context)</option>
                    <option value="3" {{ $listening_section->section_number == 3 ? 'selected' : '' }}>Section 3 (Conversation, educational context)</option>
                    <option value="4" {{ $listening_section->section_number == 4 ? 'selected' : '' }}>Section 4 (Monologue, academic subject)</option>
                </select>
            </div>

            <div class="mb-8 border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">Update Audio Recording</label>
                    <p class="text-xs text-gray-500 mb-3">Optional. Will replace existing audio.</p>
                    <input type="file" name="audio_file" accept=".mp3,.wav" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                </div>
                
                @if($listening_section->audio_path)
                    <div class="bg-white p-3 rounded shadow-sm border border-gray-200 w-full md:w-auto">
                        <span class="block text-xs font-medium text-gray-600 mb-2">Current Audio:</span>
                        <audio controls class="h-8 w-full md:w-64">
                            <source src="{{ Storage::url($listening_section->audio_path) }}" type="audio/mpeg">
                        </audio>
                    </div>
                @endif
            </div>

            <div class="mb-6 border border-gray-100 p-4 rounded bg-gray-50">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Transcript / Passage Text</label>
                <textarea name="passage_text" rows="8" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">{{ $listening_section->passage_text }}</textarea>
            </div>

            <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this listening section? All associated questions will be deleted.')) { document.getElementById('delete-section').submit(); }" class="text-red-600 hover:text-red-800 font-medium text-sm transition">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Section
                </button>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Section
                </button>
            </div>
        </form>

        <form id="delete-section" action="{{ route('admin.listening-sections.destroy', $listening_section->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Questions</h3>
                    <p class="text-sm text-gray-600">Manage the questions associated with this listening section.</p>
                </div>
                <a href="{{ route('admin.questions.create', ['type' => 'listening', 'id' => $listening_section->id]) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded shadow-sm transition">
                    <i class="fas fa-plus mr-2"></i> Add Question
                </a>
            </div>

            @if($listening_section->questions->isEmpty())
                <div class="bg-gray-50 border border-gray-200 rounded p-6 text-center text-gray-500">
                    No questions added yet.
                </div>
            @else
                <ul class="space-y-3">
                    @foreach($listening_section->questions as $index => $q)
                        <li class="bg-white border border-gray-200 rounded p-4 flex justify-between items-start hover:shadow-sm transition">
                            <div>
                                <h4 class="font-medium text-gray-800">Q{{ $index + 1 }}. {{ \Illuminate\Support\Str::limit($q->question_text, 80) }}</h4>
                                <div class="flex items-center space-x-3 mt-2 text-xs text-gray-500">
                                    <span class="bg-gray-100 px-2 py-1 rounded">{{ ucwords(str_replace('_', ' ', $q->question_type)) }}</span>
                                    @if($q->question_type == 'multiple_choice')
                                        <span>{{ $q->options->count() }} options</span>
                                    @else
                                        <span>Answer: <strong class="text-gray-700">{{ $q->correct_answer }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.questions.edit', $q->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium"><i class="fas fa-edit"></i> Edit</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
