@extends('layouts.admin')

@section('title', 'Create Test')
@section('header', 'Create New Test')
@section('subheader', 'Add an official IELTS book as a test')

@section('header_actions')
    <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.index') }}'">
        <i class="fas fa-arrow-left mr-[8px]"></i> Back to Tests
    </x-button>
@endsection

@section('content')
    <div class="max-w-3xl">
        <x-card>
            <form action="{{ route('admin.tests.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-[24px] mb-[24px]">
                    <div>
                        <label class="block text-[var(--color-text)] text-[14px] font-bold mb-[8px]">Book Number</label>
                        <x-input type="number" name="book_number" placeholder="e.g., 20" min="1" required />
                    </div>
                    <div>
                        <label class="block text-[var(--color-text)] text-[14px] font-bold mb-[8px]">Year</label>
                        <x-input type="number" name="year" placeholder="e.g., 2025" min="1990" required />
                    </div>
                </div>

                <div class="mb-[24px]">
                    <label class="block text-[var(--color-text)] text-[14px] font-bold mb-[8px]">Exam Type</label>
                    <select name="exam_type" class="w-full p-[16px] bg-[var(--color-bg)] text-[var(--color-text)] border border-[var(--color-divider)] rounded-[var(--radius-base)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-ui" required>
                        <option value="" disabled selected>Select Exam Type...</option>
                        <option value="Academic">Academic</option>
                        <option value="General">General</option>
                    </select>
                </div>

                <div class="mb-[32px]">
                    <label class="block text-[var(--color-text)] text-[14px] font-bold mb-[8px]">Status</label>
                    <select name="status" class="w-full p-[16px] bg-[var(--color-bg)] text-[var(--color-text)] border border-[var(--color-divider)] rounded-[var(--radius-base)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-ui">
                        <option value="draft">Draft (Hidden from users)</option>
                        <option value="published">Published (Visible to users)</option>
                    </select>
                </div>

                <div class="flex justify-between items-center border-t border-[var(--color-divider)] pt-[24px] mt-[16px]">
                    <p class="text-[12px] text-[var(--color-text)] opacity-60 flex items-center">
                        <i class="fas fa-magic mr-[8px]"></i> 4 Test Sets auto-generated
                    </p>
                    <x-button variant="primary" type="submit" loading="false">
                        <i class="fas fa-save mr-[8px]"></i> Save Test
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
