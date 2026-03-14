@extends('layouts.admin')

@section('title', 'Create Test')
@section('header', 'Create New Test')
@section('subheader', 'Add an official IELTS book as a test')

@section('header_actions')
    <a href="{{ route('admin.tests.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Tests
    </a>
@endsection

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.tests.store') }}" method="POST" class="bg-white border border-dwimik-divider rounded-dwimik p-8 shadow-sm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-dwimik-text text-sm font-semibold mb-2">Book Number</label>
                    <input type="number" name="book_number" placeholder="e.g., 20" min="1" class="w-full border-dwimik-divider rounded-dwimik shadow-sm focus:ring-dwimik-primary focus:border-dwimik-primary sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-dwimik-text text-sm font-semibold mb-2">Year</label>
                    <input type="number" name="year" placeholder="e.g., 2025" min="1990" class="w-full border-dwimik-divider rounded-dwimik shadow-sm focus:ring-dwimik-primary focus:border-dwimik-primary sm:text-sm" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-dwimik-text text-sm font-semibold mb-2">Exam Type</label>
                <select name="exam_type" class="w-full border-dwimik-divider rounded-dwimik shadow-sm focus:ring-dwimik-primary focus:border-dwimik-primary sm:text-sm" required>
                    <option value="" disabled selected>Select Exam Type...</option>
                    <option value="Academic">Academic</option>
                    <option value="General">General</option>
                </select>
            </div>

            <div class="mb-8">
                <label class="block text-dwimik-text text-sm font-semibold mb-2">Status</label>
                <select name="status" class="w-full border-dwimik-divider rounded-dwimik shadow-sm focus:ring-dwimik-primary focus:border-dwimik-primary sm:text-sm">
                    <option value="draft">Draft (Hidden from users)</option>
                    <option value="published">Published (Visible to users)</option>
                </select>
            </div>

            <div class="flex justify-end border-t border-dwimik-divider pt-6 mt-4">
                <button type="submit" class="bg-dwimik-primary hover:opacity-90 text-white font-medium py-2 px-6 rounded-dwimik transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Test
                </button>
            </div>
            
            <p class="text-xs text-gray-500 text-center mt-6 p-3 bg-dwimik-bg rounded">
                <i class="fas fa-info-circle mr-1 text-dwimik-primary"></i> 
                4 Test Sets will be automatically generated upon creation.
            </p>
        </form>
    </div>
@endsection
