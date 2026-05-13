@extends('layouts.admin')

@section('title', 'Manage Test: IELTS ' . $test->book_number)

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)]">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-[var(--color-primary)] transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <a href="{{ route('admin.tests.index') }}" class="hover:text-[var(--color-primary)] transition-colors">Exams & Tests</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="font-semibold text-[var(--color-text-primary)]">IELTS {{ $test->book_number }}</span>
    </nav>
@endsection

@section('content')
<div class="mb-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[var(--color-text-primary)]">
                IELTS {{ $test->book_number }} ({{ $test->year }}) - {{ $test->exam_type }}
            </h2>
            <p class="text-small mt-1 text-[var(--color-text-secondary)]">Manage test details and underlying test sets.</p>
        </div>
        <x-ui.button variant="secondary" href="{{ route('admin.tests.index') }}">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to List
        </x-ui.button>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        
        {{-- Left: Edit Test Details Form --}}
        <div class="lg:col-span-1">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-base font-bold text-[var(--color-text-primary)]">Update Test Details</h3>
                </x-slot:header>

                <form action="{{ route('admin.tests.update', $test) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-5">
                        {{-- Book Number --}}
                        <x-ui.input 
                            name="book_number" 
                            label="IELTS Book Number" 
                            type="number" 
                            value="{{ $test->book_number }}"
                            required 
                        />

                        {{-- Year --}}
                        <x-ui.input 
                            name="year" 
                            label="Publication Year" 
                            type="number" 
                            value="{{ $test->year }}"
                            required 
                        />

                        {{-- Exam Type --}}
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-[var(--color-text-primary)]">Exam Type</label>
                            <div class="relative">
                                <select name="exam_type" class="block w-full appearance-none bg-[var(--color-bg-primary)] border border-[var(--color-divider)] rounded-[var(--radius-base)] px-4 py-2.5 text-sm text-[var(--color-text-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-ui cursor-pointer">
                                    <option value="Academic" {{ $test->exam_type === 'Academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="General Training" {{ $test->exam_type === 'General Training' ? 'selected' : '' }}>General Training</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-[var(--color-text-secondary)] pointer-events-none">expand_more</span>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-[var(--color-text-primary)]">Status</label>
                            <div class="relative">
                                <select name="status" class="block w-full appearance-none bg-[var(--color-bg-primary)] border border-[var(--color-divider)] rounded-[var(--radius-base)] px-4 py-2.5 text-sm text-[var(--color-text-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-ui cursor-pointer">
                                    <option value="draft" {{ $test->status === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                                    <option value="published" {{ $test->status === 'published' ? 'selected' : '' }}>Published (Visible)</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-[var(--color-text-secondary)] pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-[var(--color-divider)] flex justify-end">
                        <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        {{-- Right: Test Sets --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-[var(--color-text-primary)]">Test Sets (Modules)</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($test->testSets->sortBy('set_number') as $testSet)
                    <x-ui.card class="relative overflow-hidden group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)]">
                                    <span class="text-lg font-bold">0{{ $testSet->set_number }}</span>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-[var(--color-text-primary)]">Set {{ $testSet->set_number }}</h4>
                                    <p class="text-xs text-[var(--color-text-secondary)]">4 Modules</p>
                                </div>
                            </div>
                            <x-ui.badge variant="success">Active</x-ui.badge>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-6">
                            <div class="p-2 rounded bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] text-center">
                                <p class="text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">Listening</p>
                                <p class="text-sm font-bold text-[var(--color-text-primary)]">{{ collect($testSet->listeningSections ?? [])->count() }}</p>
                            </div>
                            <div class="p-2 rounded bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] text-center">
                                <p class="text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">Reading</p>
                                <p class="text-sm font-bold text-[var(--color-text-primary)]">{{ collect($testSet->readingPassages ?? [])->count() }}</p>
                            </div>
                            <div class="p-2 rounded bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] text-center">
                                <p class="text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">Writing</p>
                                <p class="text-sm font-bold text-[var(--color-text-primary)]">{{ collect($testSet->writingTasks ?? [])->count() }}</p>
                            </div>
                            <div class="p-2 rounded bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] text-center">
                                <p class="text-[10px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">Speaking</p>
                                <p class="text-sm font-bold text-[var(--color-text-primary)]">{{ collect($testSet->speakingQuestions ?? [])->count() }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-[var(--color-divider)] pt-4 mt-auto">
                            <button class="text-xs font-semibold text-[var(--color-error)] hover:opacity-80 transition-opacity">Delete</button>
                            <x-ui.button href="{{ route('admin.test_sets.show', $testSet->id) }}" variant="outline" class="text-xs px-3 py-1">
                                Manage Set
                            </x-ui.button>
                        </div>
                    </x-ui.card>
                @empty
                    <div class="md:col-span-2">
                        <x-ui.empty-state
                            icon="layers_clear"
                            title="No Test Sets"
                            description="This exam doesn't have any test sets yet."
                        />
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
