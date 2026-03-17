@extends('layouts.admin')

@section('title', 'Test Details')
@section('header', 'Manage Test: IELTS ' . $test->book_number)
@section('subheader', 'Book: ' . $test->book_number . ' | Year: ' . $test->year . ' | Type: ' . $test->exam_type)

@section('header_actions')
    <div class="flex gap-[16px]">
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.edit', $test->id) }}'">
            <i class="fas fa-cog mr-[8px]"></i> Test Settings
        </x-button>
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.index') }}'" class="border-transparent hover:bg-transparent">
            <i class="fas fa-arrow-left mr-[8px]"></i> Back to Tests
        </x-button>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-[24px]">
        @foreach($test->testSets->sortBy('set_number') as $testSet)
            <x-card class="flex flex-col h-full transition-colors border-[var(--color-divider)] hover:border-[var(--color-primary)]">
                <x-slot name="header">
                    <h3 class="text-[18px] font-bold text-[var(--color-text)] flex items-center">
                        <i class="fas fa-layer-group mr-[8px] text-[var(--color-primary)]"></i> Test Set {{ $testSet->set_number }}
                    </h3>
                </x-slot>
                
                <div class="flex-1 flex flex-col space-y-[16px]">
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-[var(--color-text)] opacity-70 font-medium"><i class="fas fa-pen-nib w-[24px] opacity-60"></i> Writing</span>
                        <x-badge variant="neutral">{{ collect($testSet->writingTasks ?? [])->count() }}</x-badge>
                    </div>
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-[var(--color-text)] opacity-70 font-medium"><i class="fas fa-microphone w-[24px] opacity-60"></i> Speaking</span>
                        <x-badge variant="neutral">{{ collect($testSet->speakingQuestions ?? [])->count() }}</x-badge>
                    </div>
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-[var(--color-text)] opacity-70 font-medium"><i class="fas fa-headphones w-[24px] opacity-60"></i> Listening</span>
                        <x-badge variant="neutral">{{ collect($testSet->listeningSections ?? [])->count() }}</x-badge>
                    </div>
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-[var(--color-text)] opacity-70 font-medium"><i class="fas fa-book-open w-[24px] opacity-60"></i> Reading</span>
                        <x-badge variant="neutral">{{ collect($testSet->readingPassages ?? [])->count() }}</x-badge>
                    </div>
                </div>
                
                <x-slot name="footer">
                    <x-button variant="primary" class="w-full" onclick="window.location.href='{{ route('admin.test_sets.show', $testSet->id) }}'">
                        Manage Modules
                    </x-button>
                </x-slot>
            </x-card>
        @endforeach
    </div>
@endsection
