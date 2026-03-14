@extends('layouts.admin')

@section('title', 'IELTS Collections')
@section('header', 'IELTS Collections')
@section('subheader', 'Manage all IELTS book collections and their associated tests.')
@section('header_actions')
    <a href="{{ route('admin.collections.create') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition">
        <i class="fas fa-plus mr-2"></i> Create New Collection
    </a>
@endsection

@section('content')
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        @if($collections->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-folder-open text-5xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium">No collections available.</p>
                <p class="text-sm mt-1">Get started by creating your first IELTS collection.</p>
                <a href="{{ route('admin.collections.create') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-5 rounded shadow-sm transition">
                    <i class="fas fa-plus mr-1"></i> Create Collection
                </a>
            </div>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($collections as $collection)
                    <li class="p-6 hover:bg-gray-50 transition">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $collection->title }}</h3>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $collection->exam_type === 'Academic' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $collection->exam_type }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                        <i class="fas fa-file-alt mr-1"></i>{{ $collection->tests_count }} Tests
                                    </span>
                                </div>
                                @if($collection->description)
                                    <p class="text-sm text-gray-500 mb-2">{{ \Illuminate\Support\Str::limit($collection->description, 120) }}</p>
                                @endif
                                <div class="flex items-center space-x-3 text-xs font-medium text-gray-400">
                                    @if($collection->year)
                                        <span><i class="fas fa-calendar-alt mr-1"></i> {{ $collection->year }}</span>
                                    @endif
                                    <span><i class="fas fa-clock mr-1"></i> Created {{ $collection->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('admin.collections.show', $collection->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                                    <i class="fas fa-cog mr-1"></i> Manage Tests
                                </a>
                                <a href="{{ route('admin.collections.edit', $collection->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.collections.destroy', $collection->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this collection? Tests inside it will become standalone. Tasks and sections will NOT be deleted.');">
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
@endsection
