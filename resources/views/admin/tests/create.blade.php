@extends('layouts.admin')

@section('title', 'Create Test')
@section('header', 'Create New Test')
@section('subheader', 'Adding to collection: ' . $collection->title)

@section('header_actions')
    <a href="{{ route('admin.collections.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Collections
    </a>
@endsection

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.tests.store', $collection->id) }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Test Title</label>
                <input type="text" name="title" placeholder="e.g., Academic Reading & Writing Test 1" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Test Number</label>
                <input type="number" name="number" value="1" min="1" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                <p class="text-xs text-gray-500 mt-1">Numerical identifier for ordering (e.g., Test 1, Test 2).</p>
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="draft">Draft (Hidden from users)</option>
                    <option value="published">Published (Visible to users)</option>
                </select>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Test
                </button>
            </div>
        </form>
    </div>
@endsection
