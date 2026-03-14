@extends('layouts.admin')

@section('title', 'Test Details')
@section('header', 'Manage Test: ' . $test->title)
@section('subheader', 'Book: ' . $test->book_number . ' | Year: ' . $test->year . ' | Type: ' . $test->exam_type)

@section('header_actions')
    <div class="flex space-x-3">
        <a href="{{ route('admin.tests.edit', $test->id) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded shadow-sm transition flex items-center">
            <i class="fas fa-cog mr-2"></i> Test Settings
        </a>
        <a href="{{ route('admin.tests.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center px-4">
            <i class="fas fa-arrow-left mr-2"></i> Back to Tests
        </a>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($test->testSets->sortBy('set_number') as $testSet)
            <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden flex flex-col hover:shadow-md transition">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900"><i class="fas fa-layer-group mr-2"></i> Test Set {{ $testSet->set_number }}</h3>
                </div>
                <div class="p-6 flex-1 flex flex-col space-y-4">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span><i class="fas fa-pen-nib w-5"></i> Writing Tasks:</span>
                        <span class="font-medium">{{ $testSet->writingTasks->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span><i class="fas fa-microphone w-5"></i> Speaking Questions:</span>
                        <span class="font-medium">{{ $testSet->speakingQuestions->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span><i class="fas fa-headphones w-5"></i> Listening Sections:</span>
                        <span class="font-medium">{{ $testSet->listeningSections->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span><i class="fas fa-book-open w-5"></i> Reading Passages:</span>
                        <span class="font-medium">{{ $testSet->readingPassages->count() }}</span>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('admin.test_sets.show', $testSet->id) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition">
                        Manage Modules
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
