@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Overview')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-[24px] mb-[32px]">
        <!-- Stats Card 1 -->
        <x-card>
            <div class="flex flex-col justify-center">
                <p class="text-[14px] font-medium text-[var(--color-text)] opacity-70 mb-[8px]">Total Tests</p>
                <p class="text-[28px] font-bold text-[var(--color-text)]">{{ $stats['total_tests'] }}</p>
            </div>
        </x-card>

        <!-- Stats Card 2 -->
        <x-card>
            <div class="flex flex-col justify-center">
                <p class="text-[14px] font-medium text-[var(--color-text)] opacity-70 mb-[8px]">Total Test Sets</p>
                <p class="text-[28px] font-bold text-[var(--color-text)]">{{ $stats['total_test_sets'] }}</p>
            </div>
        </x-card>

        <!-- Stats Card 3 -->
        <x-card>
            <div class="flex flex-col justify-center">
                <p class="text-[14px] font-medium text-[var(--color-text)] opacity-70 mb-[8px]">Total Users</p>
                <p class="text-[28px] font-bold text-[var(--color-text)]">{{ $stats['users'] ?? 0 }}</p>
            </div>
        </x-card>

        <!-- Stats Card 4 -->
        <x-card>
            <div class="flex flex-col justify-center">
                <p class="text-[14px] font-medium text-[var(--color-text)] opacity-70 mb-[8px]">Total Attempts</p>
                <p class="text-[28px] font-bold text-[var(--color-text)]">{{ $stats['attempts'] ?? 0 }}</p>
            </div>
        </x-card>
    </div>

    <!-- Recent Tests Table -->
    <x-card>
        <x-slot name="header">
            <div class="flex justify-between items-center w-full">
                <h3 class="text-[18px] font-bold text-[var(--color-text)]">Recent Tests Overview</h3>
                <a href="{{ route('admin.tests.index') }}" class="text-[14px] text-[var(--color-primary)] hover:underline font-bold">View All &rarr;</a>
            </div>
        </x-slot>
        
        <x-table>
            <x-slot name="header">
                <x-tr>
                    <x-th>Test Info</x-th>
                    <x-th>Status</x-th>
                    <x-th class="text-right">Actions</x-th>
                </x-tr>
            </x-slot>
            
            @forelse($tests as $test)
                <x-tr>
                    <x-td>
                        <div class="font-bold">IELTS {{ $test->book_number }} {{ $test->exam_type }} {{ $test->year }}</div>
                        <div class="text-[var(--color-text)] opacity-60 mt-[4px]">Book #{{ $test->book_number }}</div>
                    </x-td>

                    <x-td>
                        <x-badge variant="{{ $test->status === 'published' ? 'success' : 'neutral' }}">
                            {{ ucfirst($test->status) }}
                        </x-badge>
                    </x-td>
                    
                    <x-td class="text-right">
                        <x-button variant="secondary" type="button" onclick="window.location.href='{{ route('admin.tests.show', $test->id) }}'">Manage</x-button>
                    </x-td>
                </x-tr>
            @empty
                <x-tr>
                    <x-td colspan="3" class="text-center py-[32px] text-[var(--color-text)] opacity-60">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-folder-open text-[36px] opacity-30 mb-[16px]"></i>
                            <p class="mb-[16px] text-[16px]">No tests available yet.</p>
                            <x-button variant="primary" type="button" onclick="window.location.href='{{ route('admin.tests.create') }}'">Create your first test</x-button>
                        </div>
                    </x-td>
                </x-tr>
            @endforelse
        </x-table>
    </x-card>
@endsection
