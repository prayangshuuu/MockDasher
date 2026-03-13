@extends('layouts.admin')

@section('title', 'Edit Writing Task')
@section('header', 'Edit Writing Task')
@section('subheader', 'For test: ' . $writing_task->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $writing_task->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('admin.writing-tasks.update', $writing_task->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Task Type</label>
                    <select name="task_number" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="1" {{ $writing_task->task_number == 1 ? 'selected' : '' }}>Task 1 (Requires Description & Optional Image)</option>
                        <option value="2" {{ $writing_task->task_number == 2 ? 'selected' : '' }}>Task 2 (Requires Prompt)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Minimum Word Count</label>
                    <input type="number" name="minimum_word_count" value="{{ $writing_task->minimum_word_count }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Task Title</label>
                <input type="text" name="task_title" value="{{ $writing_task->task_title }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Instruction Text</label>
                <textarea name="instruction_text" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $writing_task->instruction_text }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Task Description <span class="text-xs font-normal text-gray-500">(For Task 1)</span></label>
                    <textarea name="task_description" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $writing_task->task_description }}</textarea>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Task Prompt <span class="text-xs font-normal text-gray-500">(For Task 2)</span></label>
                    <textarea name="task_prompt" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $writing_task->task_prompt }}</textarea>
                </div>
            </div>

            <div class="mb-8 border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 flex items-center justify-between">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">Upload New Task Image</label>
                    <p class="text-xs text-gray-500 mb-3">Optional. Will replace any existing image.</p>
                    <input type="file" name="task_image" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                
                @if($writing_task->images->isNotEmpty())
                    <div class="w-48">
                        <p class="text-xs font-semibold text-gray-500 mb-2">Current Image:</p>
                        <img src="{{ Storage::url($writing_task->images->first()->image_path) }}" alt="Task Image" class="w-full h-auto rounded border border-gray-200">
                    </div>
                @endif
            </div>

            <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this writing task?')) { document.getElementById('delete-task').submit(); }" class="text-red-600 hover:text-red-800 font-medium text-sm transition">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Task
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Task
                </button>
            </div>
        </form>

        <form id="delete-task" action="{{ route('admin.writing-tasks.destroy', $writing_task->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
