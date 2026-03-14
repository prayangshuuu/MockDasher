@extends('layouts.admin')

@section('title', 'Manage Tests')
@section('header', 'Manage Tests')
@section('subheader', 'Manage all standalone and collection-based tests.')

@section('header_actions')
    <a href="{{ route('admin.tests.create') }}" class="inline-flex items-center bg-dwimik-primary hover:opacity-90 text-white font-medium py-2 px-4 rounded-dwimik transition">
        <i class="fas fa-plus mr-2"></i> Create New Test
    </a>
@endsection

@section('content')
    <div class="bg-white border border-dwimik-divider rounded-dwimik overflow-hidden">
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
            <ul class="divide-y divide-dwimik-divider">
                @foreach($tests as $test)
                    <li class="p-6 hover:bg-dwimik-bg transition">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-bold text-dwimik-text">IELTS {{ $test->book_number }} {{ $test->exam_type }} {{ $test->year }}</h3>
                                    
                                    @if($test->status === 'published')
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-dwimik-success bg-opacity-10 text-dwimik-success">
                                            Published
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-4 mt-3 text-sm font-medium text-gray-500">
                                    <span title="Test Sets"><i class="fas fa-layer-group mr-1"></i> {{ $test->test_sets_count }} Test Sets</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('admin.tests.show', $test->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-dwimik text-white bg-dwimik-primary hover:opacity-90 transition">
                                    <i class="fas fa-cog mr-1"></i> Manage
                                </a>
                                <a href="{{ route('admin.tests.edit', $test->id) }}" class="inline-flex items-center px-3 py-2 border border-dwimik-divider text-sm font-medium rounded-dwimik text-dwimik-text bg-transparent hover:bg-gray-50 transition">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this test? All tasks, sections, and questions will be permanently deleted.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-dwimik-error text-sm font-medium rounded-dwimik text-dwimik-error bg-transparent hover:bg-red-50 transition">
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
