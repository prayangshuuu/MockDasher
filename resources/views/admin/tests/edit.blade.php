@extends('layouts.admin')

@section('title', 'Edit Test')
@section('header', 'Edit Test: ' . $test->title)
@section('subheader', 'Book: ' . $test->book_number . ' | Year: ' . $test->year . ' | Type: ' . $test->exam_type)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $test->id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.tests.update', $test->id) }}" method="POST" class="bg-white border border-dwimik-divider rounded-dwimik p-8 shadow-sm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-dwimik-text text-sm font-semibold mb-2">Book Number</label>
                    <input type="number" name="book_number" value="{{ $test->book_number }}" min="1" class="w-full border-dwimik-divider rounded-dwimik shadow-sm focus:ring-dwimik-primary focus:border-dwimik-primary sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-dwimik-text text-sm font-semibold mb-2">Year</label>
                    <input type="number" name="year" value="{{ $test->year }}" min="1990" class="w-full border-dwimik-divider rounded-dwimik shadow-sm focus:ring-dwimik-primary focus:border-dwimik-primary sm:text-sm" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-dwimik-text text-sm font-semibold mb-2">Exam Type</label>
                <select name="exam_type" class="w-full border-dwimik-divider rounded-dwimik shadow-sm focus:ring-dwimik-primary focus:border-dwimik-primary sm:text-sm" required>
                    <option value="Academic" {{ $test->exam_type === 'Academic' ? 'selected' : '' }}>Academic</option>
                    <option value="General" {{ $test->exam_type === 'General' ? 'selected' : '' }}>General</option>
                </select>
            </div>

            <div class="mb-8">
                <label class="block text-dwimik-text text-sm font-semibold mb-2">Status</label>
                <select name="status" class="w-full border-dwimik-divider rounded-dwimik shadow-sm focus:ring-dwimik-primary focus:border-dwimik-primary sm:text-sm">
                    <option value="draft" {{ $test->status === 'draft' ? 'selected' : '' }}>Draft (Hidden from users)</option>
                    <option value="published" {{ $test->status === 'published' ? 'selected' : '' }}>Published (Visible to users)</option>
                </select>
            </div>

            <div class="flex justify-between items-center border-t border-dwimik-divider pt-6 mt-4">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this test? All tasks, sections, and questions will be permanently deleted.')) { document.getElementById('delete-test').submit(); }" class="text-dwimik-error hover:opacity-80 font-medium text-sm transition">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Test
                </button>
                <button type="submit" class="bg-dwimik-primary hover:opacity-90 text-white font-medium py-2 px-6 rounded-dwimik transition flex items-center">
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
