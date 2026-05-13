@extends('layouts.admin')

@section('title', 'Create New Exam')

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)]">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-[var(--color-primary)] transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <a href="{{ route('admin.tests.index') }}" class="hover:text-[var(--color-primary)] transition-colors">Exams & Tests</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="font-semibold text-[var(--color-text-primary)]">Create Exam</span>
    </nav>
@endsection

@section('content')

<div class="max-w-5xl mx-auto mb-10">
    <div class="flex items-center mb-8">
        <a href="{{ route('admin.tests.index') }}" class="flex items-center gap-1.5 text-sm font-semibold text-[var(--color-text-secondary)] hover:text-[var(--color-primary)] transition-colors">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Back to Exams
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         CREATE EXAM FORM - 2 COLUMN GRID
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        
        {{-- Left: Details --}}
        <div>
            <h3 class="text-lg font-bold text-[var(--color-text-primary)]">Exam Details</h3>
            <p class="mt-1 text-sm text-[var(--color-text-secondary)]">Provide the basic information for this test. Students will see these details before starting.</p>
        </div>

        {{-- Right: Form Card --}}
        <div class="lg:col-span-2">
            <x-ui.card>
                <form action="{{ route('admin.tests.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        
                        {{-- Book Number --}}
                        <div>
                            <x-ui.input 
                                name="book_number" 
                                label="IELTS Book Number" 
                                type="number" 
                                placeholder="e.g., 20" 
                                min="1"
                                required 
                            />
                        </div>

                        {{-- Year --}}
                        <div>
                            <x-ui.input 
                                name="year" 
                                label="Publication Year" 
                                type="number" 
                                placeholder="e.g., 2025" 
                                min="1990"
                                required 
                            />
                        </div>

                        {{-- Exam Type Select --}}
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-[var(--color-text-primary)]">Exam Type</label>
                            <div class="relative">
                                <select name="exam_type" class="block w-full appearance-none bg-[var(--color-bg-primary)] border border-[var(--color-divider)] rounded-[var(--radius-base)] px-4 py-2.5 text-sm text-[var(--color-text-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-ui cursor-pointer">
                                    <option value="Academic">Academic</option>
                                    <option value="General Training">General Training</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-[var(--color-text-secondary)] pointer-events-none">expand_more</span>
                            </div>
                        </div>

                        {{-- Status Select/Toggle --}}
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-[var(--color-text-primary)]">Status</label>
                            <div class="relative">
                                <select name="status" class="block w-full appearance-none bg-[var(--color-bg-primary)] border border-[var(--color-divider)] rounded-[var(--radius-base)] px-4 py-2.5 text-sm text-[var(--color-text-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-ui cursor-pointer">
                                    <option value="draft">Draft (Hidden)</option>
                                    <option value="published">Published (Visible)</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-[var(--color-text-secondary)] pointer-events-none">expand_more</span>
                            </div>
                        </div>

                    </div>

                    {{-- Info Note --}}
                    <div class="mt-6 flex items-start gap-3 rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_5%,transparent)] border border-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] p-4">
                        <span class="material-symbols-outlined text-[var(--color-primary)] text-lg">info</span>
                        <div>
                            <p class="text-sm font-bold text-[var(--color-primary)]">Automatic Test Sets</p>
                            <p class="text-xs text-[var(--color-text-secondary)] mt-0.5">Creating this exam will automatically generate 4 empty Test Sets (Set 1–4) for you to configure with module content.</p>
                        </div>
                    </div>

                    {{-- Form Footer --}}
                    <div class="mt-8 pt-6 border-t border-[var(--color-divider)] flex items-center justify-end gap-3">
                        <x-ui.button type="button" variant="secondary" href="{{ route('admin.tests.index') }}">Cancel</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Save Exam</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</div>

@endsection
