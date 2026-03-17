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

    <!-- Table -->
    @if($tests->isEmpty())
        <div class="px-[24px]">
            <x-empty-state 
                icon="fas fa-file-alt" 
                title="No tests available" 
                message="Get started by creating your first test." 
                actionText="Create Test" 
                actionRoute="{{ route('admin.tests.create') }}" />
        </div>
    @else
        <div class="w-full">
            <x-table>
                <x-slot name="header">
                    <x-tr>
                        <x-th>Book</x-th>
                        <x-th>Year</x-th>
                        <x-th>Exam Type</x-th>
                        <x-th>Test Sets</x-th>
                        <x-th>Status</x-th>
                        <x-th class="text-right">Actions</x-th>
                    </x-tr>
                </x-slot>
                
                @foreach($tests as $test)
                    <x-tr>
                        <x-td>
                            <span class="font-bold text-[var(--color-text)]">IELTS Book {{ $test->book_number }}</span>
                        </x-td>
                        <x-td>{{ $test->year }}</x-td>
                        <x-td>{{ $test->exam_type }}</x-td>
                        <x-td>
                            <x-badge variant="neutral" class="bg-[var(--color-bg)] border-[var(--color-divider)]">
                                <i class="fas fa-layer-group mr-[8px] opacity-60"></i> {{ $test->test_sets_count ?? collect($test->testSets ?? [])->count() ?? 0 }}
                            </x-badge>
                        </x-td>
                        <x-td>
                            <x-badge variant="{{ $test->status === 'published' ? 'success' : 'neutral' }}">
                                {{ ucfirst($test->status) }}
                            </x-badge>
                        </x-td>
                        <x-td class="text-right whitespace-nowrap">
                            <div class="flex gap-[8px] justify-end">
                                <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.show', $test->id) }}'">
                                    Manage
                                </x-button>
                                <x-button variant="secondary" onclick="window.location.href='{{ route('admin.tests.edit', $test->id) }}'">
                                    <i class="fas fa-edit"></i>
                                </x-button>
                                <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this test?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="danger" type="submit">
                                        <i class="fas fa-trash"></i>
                                    </x-button>
                                </form>
                            </div>
                        </x-td>
                    </x-tr>
                @endforeach
            </x-table>
        </div>
        
        @if($tests->hasPages())
            <div class="mt-[24px] pt-[24px] border-t border-[var(--color-divider)]">
                {{ $tests->links() }}
            </div>
        @endif
    @endif
</x-card>
@endsection
