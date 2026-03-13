@extends('layouts.admin')

@section('title', 'Create Collection')
@section('header', 'Create IELTS Collection')
@section('subheader', 'Add a new book or collection to organize Mock IELTS tests.')

@section('header_actions')
    <a href="{{ route('admin.collections.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Collections
    </a>
@endsection

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.collections.store') }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Collection Title</label>
                <input type="text" name="title" placeholder="e.g., Cambridge IELTS 18" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Exam Type</label>
                    <select name="exam_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="Academic">Academic</option>
                        <option value="General">General Training</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Year Published (Optional)</label>
                    <input type="number" name="year" placeholder="2023" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Description / Notes</label>
                <textarea name="description" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Collection
                </button>
            </div>
        </form>
    </div>
@endsection
