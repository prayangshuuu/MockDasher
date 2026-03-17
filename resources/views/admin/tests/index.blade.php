@extends('layouts.admin')

@section('title', 'Manage Tests')
@section('header', 'Manage Tests')
@section('subheader', 'Manage all standalone and collection-based tests.')

@section('header_actions')
    <x-button variant="primary" onclick="window.location.href='{{ route('admin.tests.create') }}'">
        <i class="fas fa-plus mr-[8px]"></i> Create New Test
    </x-button>
@endsection

@section('content')
<x-card>
    <!-- Toolbar (Search & Filters) -->
    <div class="pb-[24px] mb-[24px] border-b border-[var(--color-divider)] flex flex-col md:flex-row gap-[16px] justify-between items-center">
        <form method="GET" action="{{ route('admin.tests.index') }}" class="w-full md:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-[16px] flex items-center pointer-events-none">
                <i class="fas fa-search text-[var(--color-text)] opacity-50"></i>
            </div>
            <x-input type="text" name="search" value="{{ request('search') }}" placeholder="Search tests by book or year..." class="pl-[40px] w-full" />
        </form>
        
        <div class="flex gap-[8px] w-full md:w-auto">
            <select name="type" class="p-[16px] bg-[var(--color-bg)] border border-[var(--color-divider)] rounded-[var(--radius-base)] text-[var(--color-text)] text-[16px] focus:outline-none focus:border-[var(--color-primary)]">
                <option value="">All Types</option>
                <option value="Academic" {{ request('type') == 'Academic' ? 'selected' : '' }}>Academic</option>
                <option value="General" {{ request('type') == 'General' ? 'selected' : '' }}>General</option>
            </select>
            <x-button variant="secondary" type="submit" class="whitespace-nowrap">
                <i class="fas fa-filter mr-[8px] opacity-60"></i> Filter
            </x-button>
        </div>
    </div>

    <!-- Grouped Book Cards -->
    @if($tests->isEmpty())
        <x-card>
            <div class="px-[24px]">
                <x-empty-state 
                    icon="fas fa-file-alt" 
                    title="No tests available" 
                    message="Get started by creating your first test." 
                    actionText="Create Test" 
                    actionRoute="{{ route('admin.tests.create') }}" />
            </div>
        </x-card>
    @else
        <div class="space-y-[24px]">
            @foreach($tests as $test)
                <x-card class="!p-0 overflow-hidden">
                    <!-- Book Header -->
                    <div class="p-[24px] border-b border-[var(--color-divider)] flex flex-col md:flex-row md:items-center justify-between gap-[16px] bg-[var(--color-bg)]">
                        <div>
                            <div class="flex items-center gap-[12px] mb-[4px]">
                                <h3 class="text-[18px] font-bold text-[var(--color-text)]">IELTS Book {{ $test->book_number }}</h3>
                                <x-badge variant="{{ $test->status === 'published' ? 'success' : 'neutral' }}">
                                    {{ ucfirst($test->status) }}
                                </x-badge>
                            </div>
                            <div class="text-[14px] text-[var(--color-text)] opacity-70 flex items-center gap-[12px]">
                                <span>{{ $test->year }}</span>
                                <span>&bull;</span>
                                <span>{{ $test->exam_type }}</span>
                                <span>&bull;</span>
                                <span>{{ $test->testSets->count() }} Test{{ $test->testSets->count() !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-[8px]">
                            <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.show', $test->id) }}'">
                                Manage Book
                            </x-button>
                            <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.edit', $test->id) }}'">
                                <i class="fas fa-edit"></i>
                            </x-button>
                            <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this complete book?');">
                                @csrf
                                @method('DELETE')
                                <x-button variant="danger" type="submit">
                                    <i class="fas fa-trash"></i>
                                </x-button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Children Test Sets -->
                    @if($test->testSets->isNotEmpty())
                        <div class="divide-y divide-[var(--color-divider)] bg-[var(--color-bg)]">
                            @foreach($test->testSets as $set)
                                <div class="px-[24px] py-[16px] flex justify-between items-center transition-ui hover:bg-black/5 cursor-pointer" onclick="window.location.href='{{ route('admin.tests.show', $test->id) }}'">
                                    <div class="flex items-center gap-[16px]">
                                        <div class="w-[32px] h-[32px] rounded-[var(--radius-base)] border border-[var(--color-divider)] flex items-center justify-center text-[12px] font-bold text-[var(--color-text)] opacity-60 bg-[var(--color-bg)] shrink-0">
                                            {{ $set->set_number }}
                                        </div>
                                        <span class="text-[14px] font-medium text-[var(--color-text)]">Test Set {{ $set->set_number }}</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-[12px] text-[var(--color-text)] opacity-40"></i>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-[24px] py-[32px] text-center text-[14px] text-[var(--color-text)] opacity-60 bg-black/5">
                            No test sets generated.
                        </div>
                    @endif
                </x-card>
            @endforeach
        </div>
        
        @if($tests->hasPages())
            <div class="mt-[24px] pt-[24px] border-t border-[var(--color-divider)]">
                {{ $tests->links() }}
            </div>
        @endif
    @endif
@endsection
