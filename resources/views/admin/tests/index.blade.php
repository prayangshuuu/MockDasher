@extends('layouts.admin')

@section('title', 'Manage Tests')
@section('header', 'Manage Tests')
@section('subheader', 'Manage all standalone and collection-based tests.')

@section('header_actions')
    <x-button variant="primary" onclick="window.location.href='{{ route('admin.tests.create') }}'">
        <i class="fas fa-plus mr-2"></i> Create New Test
    </x-button>
@endsection

@section('content')
<x-card class="p-0 overflow-hidden" style="padding: 0;">
    <!-- Toolbar (Search & Filters) -->
    <div class="p-4 border-b border-[var(--color-dwimik-divider)] bg-white flex flex-col md:flex-row gap-4 justify-between items-center">
        <form method="GET" action="{{ route('admin.tests.index') }}" class="w-full md:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <x-input type="text" name="search" value="{{ request('search') }}" placeholder="Search tests by book or year..." class="pl-10 h-10 w-full" />
        </form>
        
        <div class="flex gap-2 w-full md:w-auto">
            <select name="type" class="h-10 px-3 bg-[var(--color-dwimik-bg)] border border-[var(--color-dwimik-divider)] rounded-[var(--radius-dwimik)] text-[var(--color-dwimik-text)] text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-dwimik-primary)] focus:border-[var(--color-dwimik-primary)]">
                <option value="">All Types</option>
                <option value="Academic" {{ request('type') == 'Academic' ? 'selected' : '' }}>Academic</option>
                <option value="General" {{ request('type') == 'General' ? 'selected' : '' }}>General</option>
            </select>
            <x-button variant="secondary" type="submit" class="h-10 px-4 whitespace-nowrap">
                <i class="fas fa-filter mr-2 text-gray-400"></i> Filter
            </x-button>
        </div>
    </div>

    <!-- Table -->
    @if($tests->isEmpty())
        <div class="p-16 text-center text-gray-500 bg-white">
            <i class="fas fa-file-alt text-5xl text-[var(--color-dwimik-divider)] mb-4"></i>
            <p class="text-lg font-bold text-[var(--color-dwimik-text)]">No tests available.</p>
            <p class="text-sm mt-1">Get started by creating your first test.</p>
            <x-button variant="primary" class="mt-6" onclick="window.location.href='{{ route('admin.tests.create') }}'">
                <i class="fas fa-plus mr-2"></i> Create Test
            </x-button>
        </div>
    @else
        <div class="overflow-x-auto w-full">
            <x-table class="border-0 rounded-none w-full border-b-0 min-w-max">
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
                        <x-td class="font-bold text-[var(--color-dwimik-text)]">
                            IELTS Book {{ $test->book_number }}
                        </x-td>
                        <x-td>{{ $test->year }}</x-td>
                        <x-td>{{ $test->exam_type }}</x-td>
                        <x-td>
                            <x-badge variant="neutral" class="bg-[var(--color-dwimik-bg)] text-xs border-[var(--color-dwimik-divider)]">
                                <i class="fas fa-layer-group mr-1.5 opacity-60"></i> {{ $test->test_sets_count ?? collect($test->testSets ?? [])->count() ?? 0 }}
                            </x-badge>
                        </x-td>
                        <x-td>
                            <x-badge variant="{{ $test->status === 'published' ? 'success' : 'neutral' }}">
                                {{ ucfirst($test->status) }}
                            </x-badge>
                        </x-td>
                        <x-td class="text-right whitespace-nowrap">
                            <div class="flex gap-2 justify-end">
                                <x-button variant="secondary" class="!px-3 !py-1 text-xs" onclick="window.location.href='{{ route('admin.tests.show', $test->id) }}'">
                                    Manage
                                </x-button>
                                <x-button variant="secondary" class="!px-3 !py-1 text-xs text-gray-600 border-gray-200" onclick="window.location.href='{{ route('admin.tests.edit', $test->id) }}'">
                                    <i class="fas fa-edit"></i>
                                </x-button>
                                <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this test?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-[var(--radius-dwimik)] transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </x-td>
                    </x-tr>
                @endforeach
            </x-table>
        </div>
        
        @if($tests->hasPages())
            <div class="px-6 py-4 border-t border-[var(--color-dwimik-divider)] bg-white">
                {{ $tests->links() }}
            </div>
        @endif
    @endif
</x-card>
@endsection
