@extends('layouts.admin')

@section('title', 'Add Writing Task')
@section('header', 'Add Writing Task')
@section('subheader', 'For test: ' . $test->title)

@section('header_actions')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
    </a>
@endsection

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('admin.writing-tasks.store', $test->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Task Type</label>
                    <select name="task_number" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="1">Task 1 (Requires Description & Optional Image)</option>
                        <option value="2">Task 2 (Requires Prompt)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Minimum Word Count</label>
                    <input type="number" name="minimum_word_count" value="150" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Task Title</label>
                <input type="text" name="task_title" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g., The chart below shows..." required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Instruction Text</label>
                <textarea name="instruction_text" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Summarise the information by selecting and reporting the main features..."></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Task Description <span class="text-xs font-normal text-gray-500">(For Task 1)</span></label>
                    <textarea name="task_description" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Task Prompt <span class="text-xs font-normal text-gray-500">(For Task 2)</span></label>
                    <textarea name="task_prompt" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>
            </div>

            <div class="mb-8 border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 text-center">
                <label class="block text-gray-700 text-sm font-semibold mb-3">Task Image (Optional - For Task 1)</label>
                <input type="file" name="task_image" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Writing Task
                </button>
            </div>
        </form>
    </div>
@endsection
