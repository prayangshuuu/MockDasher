@extends('layouts.admin')

@section('title', 'Test Details')
@section('header', 'Manage Test: IELTS ' . $test->book_number)
@section('subheader', 'Book: ' . $test->book_number . ' | Year: ' . $test->year . ' | Type: ' . $test->exam_type)

@section('header_actions')
    <div class="flex space-x-3">
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.edit', $test->id) }}'">
            <i class="fas fa-cog mr-2"></i> Test Settings
        </x-button>
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.index') }}'" class="border-transparent hover:bg-transparent">
            <i class="fas fa-arrow-left mr-2"></i> Back to Tests
        </x-button>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($test->testSets->sortBy('set_number') as $testSet)
            <x-card class="flex flex-col h-full hover:border-[var(--color-dwimik-primary)] transition-colors p-0" style="padding: 0;">
                <div class="px-6 py-4 border-b border-[var(--color-dwimik-divider)] bg-[var(--color-dwimik-bg)] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-[var(--color-dwimik-text)]"><i class="fas fa-layer-group mr-2 text-[var(--color-dwimik-primary)]"></i> Test Set {{ $testSet->set_number }}</h3>
                </div>
                
                <div class="p-6 flex-1 flex flex-col space-y-4 bg-white">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium"><i class="fas fa-pen-nib w-5 text-gray-400"></i> Writing</span>
                        <x-badge variant="neutral">{{ collect($testSet->writingTasks ?? [])->count() }}</x-badge>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium"><i class="fas fa-microphone w-5 text-gray-400"></i> Speaking</span>
                        <x-badge variant="neutral">{{ collect($testSet->speakingQuestions ?? [])->count() }}</x-badge>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium"><i class="fas fa-headphones w-5 text-gray-400"></i> Listening</span>
                        <x-badge variant="neutral">{{ collect($testSet->listeningSections ?? [])->count() }}</x-badge>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium"><i class="fas fa-book-open w-5 text-gray-400"></i> Reading</span>
                        <x-badge variant="neutral">{{ collect($testSet->readingPassages ?? [])->count() }}</x-badge>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-[var(--color-dwimik-divider)] bg-gray-50 mt-auto">
                    <x-button variant="primary" class="w-full" onclick="window.location.href='{{ route('admin.test_sets.show', $testSet->id) }}'">
                        Manage Modules
                    </x-button>
                </div>
            </x-card>
        @endforeach
    </div>
@endsection
