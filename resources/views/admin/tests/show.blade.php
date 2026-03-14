@extends('layouts.admin')

@section('title', 'Test Details')
@section('header', 'Manage Test: ' . $test->title)
@section('subheader', ($test->collection ? 'Part of ' . $test->collection->title : 'Standalone test') . ' • Status: ' . ucfirst($test->status))

@section('header_actions')
    <div class="flex space-x-3">
        <a href="{{ route('admin.tests.edit', $test->id) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded shadow-sm transition flex items-center">
            <i class="fas fa-cog mr-2"></i> Test Settings
        </a>
        @if($test->ielts_collection_id)
            <a href="{{ route('admin.collections.show', $test->ielts_collection_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center px-4">
                <i class="fas fa-arrow-left mr-2"></i> Back to Collection
            </a>
        @else
            <a href="{{ route('admin.tests.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center px-4">
                <i class="fas fa-arrow-left mr-2"></i> Back to Tests
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Writing Module Summary -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 bg-blue-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-blue-900"><i class="fas fa-pen-nib mr-2"></i> Writing Tasks</h3>
                <a href="{{ route('admin.writing-tasks.create', $test->id) }}" class="text-sm font-medium text-blue-700 hover:bg-blue-100 px-3 py-1 rounded transition">Add Task</a>
            </div>
            <div class="p-6 flex-1">
                @if($test->writingTasks->isEmpty())
                    <p class="text-gray-500 text-sm text-center py-4">No writing tasks added yet.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($test->writingTasks as $task)
                            <li class="border border-gray-100 rounded p-4 hover:shadow-sm transition">
                                <div class="flex justify-between">
                                    <h4 class="font-semibold text-gray-800">Task {{ $task->task_number }}: {{ $task->task_title }}</h4>
                                    <a href="{{ route('admin.writing-tasks.edit', $task->id) }}" class="text-blue-600 hover:text-blue-800 text-sm"><i class="fas fa-edit"></i> Edit</a>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Min. Words: {{ $task->minimum_word_count }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Speaking Module Summary -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 bg-green-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-green-900"><i class="fas fa-microphone mr-2"></i> Speaking Parts</h3>
                <a href="{{ route('admin.speaking-questions.create', $test->id) }}" class="text-sm font-medium text-green-700 hover:bg-green-100 px-3 py-1 rounded transition">Add Question</a>
            </div>
            <div class="p-6 flex-1">
                @if($test->speakingQuestions->isEmpty())
                    <p class="text-gray-500 text-sm text-center py-4">No speaking questions added yet.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($test->speakingQuestions->sortBy('part') as $q)
                            <li class="border border-gray-100 rounded p-4 hover:shadow-sm transition">
                                <div class="flex justify-between">
                                    <h4 class="font-semibold text-gray-800">Part {{ $q->part }}</h4>
                                    <a href="{{ route('admin.speaking-questions.edit', $q->id) }}" class="text-green-600 hover:text-green-800 text-sm"><i class="fas fa-edit"></i> Edit</a>
                                </div>
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $q->question_text }}</p>
                                @if($q->audio_path)
                                    <div class="mt-2 text-xs font-semibold text-gray-400"><i class="fas fa-volume-up"></i> Audio Attached</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Listening Module Summary -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 bg-purple-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-purple-900"><i class="fas fa-headphones mr-2"></i> Listening Sections</h3>
                <a href="{{ route('admin.listening-sections.create', $test->id) }}" class="text-sm font-medium text-purple-700 hover:bg-purple-100 px-3 py-1 rounded transition">Add Section</a>
            </div>
            <div class="p-6 flex-1">
                @if($test->listeningSections->isEmpty())
                    <p class="text-gray-500 text-sm text-center py-4">No listening sections added yet.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($test->listeningSections->sortBy('section_number') as $sec)
                            <li class="border border-gray-100 rounded p-4 hover:shadow-sm transition">
                                <div class="flex justify-between">
                                    <h4 class="font-semibold text-gray-800">Section {{ $sec->section_number }}</h4>
                                    <a href="{{ route('admin.listening-sections.edit', $sec->id) }}" class="text-purple-600 hover:text-purple-800 text-sm"><i class="fas fa-edit"></i> Edit</a>
                                </div>
                                @if($sec->audio_path)
                                    <div class="mt-2 text-xs font-semibold text-gray-500"><i class="fas fa-volume-up mr-1 text-purple-500"></i> Audio Present</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Reading Module Summary -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 bg-orange-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-orange-900"><i class="fas fa-book-open mr-2"></i> Reading Passages</h3>
                <a href="{{ route('admin.reading-passages.create', $test->id) }}" class="text-sm font-medium text-orange-700 hover:bg-orange-100 px-3 py-1 rounded transition">Add Passage</a>
            </div>
            <div class="p-6 flex-1">
                @if($test->readingPassages->isEmpty())
                    <p class="text-gray-500 text-sm text-center py-4">No reading passages added yet.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($test->readingPassages->sortBy('passage_number') as $passage)
                            <li class="border border-gray-100 rounded p-4 hover:shadow-sm transition">
                                <div class="flex justify-between">
                                    <h4 class="font-semibold text-gray-800">Passage {{ $passage->passage_number }}</h4>
                                    <a href="{{ route('admin.reading-passages.edit', $passage->id) }}" class="text-orange-600 hover:text-orange-800 text-sm"><i class="fas fa-edit"></i> Edit</a>
                                </div>
                                <p class="text-sm font-medium text-gray-700 mt-1">{{ $passage->title }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>
@endsection
