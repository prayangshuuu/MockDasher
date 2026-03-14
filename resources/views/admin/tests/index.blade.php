@extends('layouts.admin')

@section('title', 'Manage Tests')
@section('header', 'Manage Tests')
@section('subheader', 'Manage all standalone and collection-based tests.')

@section('header_actions')
    <a href="{{ route('admin.tests.create') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition">
        <i class="fas fa-plus mr-2"></i> Create New Test
    </a>
@endsection

@section('content')
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        @if($tests->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-file-alt text-5xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium">No tests available.</p>
                <p class="text-sm mt-1">Get started by creating your first test.</p>
                <a href="{{ route('admin.tests.create') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-5 rounded shadow-sm transition">
                    <i class="fas fa-plus mr-1"></i> Create Test
                </a>
            </div>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($tests as $test)
                    <li class="p-6 hover:bg-gray-50 transition">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $test->title }} (Test {{ $test->number }})</h3>
                                    
                                    @if($test->status === 'published')
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                    @endif
                                    
                                    @if($test->collection)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fas fa-folder mr-1"></i> {{ $test->collection->title }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-50 text-gray-600 border border-gray-200">
                                            <i class="fas fa-unlink mr-1"></i> Standalone
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-4 mt-3 text-sm font-medium text-gray-500">
                                    <span title="Writing Tasks"><i class="fas fa-pen-nib mr-1"></i> {{ $test->writing_tasks_count }}</span>
                                    <span title="Speaking Parts"><i class="fas fa-microphone mr-1"></i> {{ $test->speaking_questions_count }}</span>
                                    <span title="Listening Sections"><i class="fas fa-headphones mr-1"></i> {{ $test->listening_sections_count }}</span>
                                    <span title="Reading Passages"><i class="fas fa-book-open mr-1"></i> {{ $test->reading_passages_count }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('admin.tests.show', $test->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                                    <i class="fas fa-cog mr-1"></i> Manage
                                </a>
                                <a href="{{ route('admin.tests.edit', $test->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this test? All tasks, sections, and questions will be permanently deleted.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-600 bg-white hover:bg-red-50 shadow-sm transition">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if($tests->hasPages())
        <div class="mt-6">
            {{ $tests->links() }}
        </div>
    @endif
@endsection
