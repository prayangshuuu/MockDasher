@extends('layouts.admin')

@section('title', 'Collection Details')
@section('header', $collection->title)
@section('subheader', $collection->exam_type . ' • ' . ($collection->year ?? 'No Year'))

@section('header_actions')
    <div class="flex space-x-3">
        <a href="{{ route('admin.collections.edit', $collection->id) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded shadow-sm transition flex items-center">
            <i class="fas fa-edit mr-2"></i> Edit Collection
        </a>
        <a href="{{ route('admin.tests.create', $collection->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-sm transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Create Test
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden mb-8 p-6">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h3>
        <p class="text-gray-800">{{ $collection->description }}</p>
    </div>

    <!-- Tests List -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Tests in this Collection</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $tests->count() }} Tests</span>
        </div>
        
        <ul class="divide-y divide-gray-200">
            @forelse($tests as $test)
                <li class="p-6 hover:bg-gray-50 transition flex justify-between items-center">
                    <div>
                        <div class="flex items-center space-x-3 mb-1">
                            <h4 class="text-md font-bold text-gray-900">{{ $test->title }}</h4>
                            @if($test->status === 'published')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">Test #{{ $test->number }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.tests.show', $test->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                            <i class="fas fa-cog mr-2"></i> Manage Test
                        </a>
                    </div>
                </li>
            @empty
                <li class="p-8 text-center text-gray-500">
                    <p class="text-md mb-2">No tests have been created in this collection yet.</p>
                    <a href="{{ route('admin.tests.create', $collection->id) }}" class="text-blue-600 font-medium hover:underline">Add the first test</a>
                </li>
            @endforelse
        </ul>
    </div>
@endsection
