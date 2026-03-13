@extends('layouts.admin')

@section('title', 'Add Reading Passage')
@section('header', 'Add Reading Passage')
@section('subheader', 'For test: ' . $test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $test->id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-5xl">
        <form action="{{ route('admin.reading-passages.store', $test->id) }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Passage Number</label>
                    <select name="passage_number" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <option value="1">Passage 1</option>
                        <option value="2">Passage 2</option>
                        <option value="3">Passage 3</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Passage Title / Heading</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" required placeholder="e.g. The History of the Bicycle">
                </div>
            </div>

            <div class="mb-8 border border-gray-100 p-4 rounded bg-gray-50">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Passage Content</label>
                <p class="text-xs text-gray-500 mb-3">Include HTML tags (like &lt;p&gt;, &lt;h3&gt;, &lt;strong&gt;) for formatting.</p>
                <textarea name="content" rows="20" class="w-full font-mono text-sm border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" required></textarea>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Passage
                </button>
            </div>
        </form>
    </div>
@endsection
