@extends('layouts.admin')

@section('title', 'Edit Test')
@section('header', 'Edit Test: ' . $test->title)
@section('subheader', $test->collection ? 'Within collection: ' . $test->collection->title : 'Standalone test')

@section('header_actions')
    <a href="{{ route('admin.tests.show', $test->id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.tests.update', $test->id) }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            @method('PUT')


            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Test Title</label>
                <input type="text" name="title" value="{{ $test->title }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Test Number</label>
                <input type="number" name="number" value="{{ $test->number }}" min="1" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="draft" {{ $test->status === 'draft' ? 'selected' : '' }}>Draft (Hidden from users)</option>
                    <option value="published" {{ $test->status === 'published' ? 'selected' : '' }}>Published (Visible to users)</option>
                </select>
            </div>

            <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this test? All tasks, sections, and questions will be permanently deleted.')) { document.getElementById('delete-test').submit(); }" class="text-red-600 hover:text-red-800 font-medium text-sm transition">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Test
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Test
                </button>
            </div>
        </form>

        <form id="delete-test" action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
