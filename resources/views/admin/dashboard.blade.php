@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Overview')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stats Card 1 -->
        <x-card>
            <div class="flex flex-col justify-center">
                <p class="text-sm font-medium text-[var(--color-dwimik-text)] opacity-70 mb-2">Total Tests</p>
                <p class="text-[var(--text-dwimik-h3)] font-bold text-[var(--color-dwimik-text)]">{{ $stats['total_tests'] }}</p>
            </div>
        </x-card>

        <!-- Stats Card 2 -->
        <x-card>
            <div class="flex flex-col justify-center">
                <p class="text-sm font-medium text-[var(--color-dwimik-text)] opacity-70 mb-2">Total Test Sets</p>
                <p class="text-[var(--text-dwimik-h3)] font-bold text-[var(--color-dwimik-text)]">{{ $stats['total_test_sets'] }}</p>
            </div>
        </x-card>

        <!-- Stats Card 3 -->
        <x-card>
            <div class="flex flex-col justify-center">
                <p class="text-sm font-medium text-[var(--color-dwimik-text)] opacity-70 mb-2">Total Users</p>
                <p class="text-[var(--text-dwimik-h3)] font-bold text-[var(--color-dwimik-text)]">{{ $stats['users'] ?? 0 }}</p>
            </div>
        </x-card>

        <!-- Stats Card 4 -->
        <x-card>
            <div class="flex flex-col justify-center">
                <p class="text-sm font-medium text-[var(--color-dwimik-text)] opacity-70 mb-2">Total Attempts</p>
                <p class="text-[var(--text-dwimik-h3)] font-bold text-[var(--color-dwimik-text)]">{{ $stats['attempts'] ?? 0 }}</p>
            </div>
        </x-card>
    </div>

    <!-- Recent Tests Table -->
    <x-card class="p-0 overflow-hidden" style="padding: 0;">
        <x-slot name="header">
            <div class="flex justify-between items-center w-full">
                <h3 class="text-lg font-medium text-[var(--color-dwimik-text)]">Recent Tests Overview</h3>
                <a href="{{ route('admin.tests.index') }}" class="text-sm text-[var(--color-dwimik-primary)] hover:underline font-medium">View All &rarr;</a>
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
                        <div class="font-medium">IELTS {{ $test->book_number }} {{ $test->exam_type }} {{ $test->year }}</div>
                        <div class="text-gray-500 mt-1">Book #{{ $test->book_number }}</div>
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
                    <x-td colspan="3" class="text-center py-8 text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                            <p class="mb-4">No tests available yet.</p>
                            <x-button variant="primary" type="button" onclick="window.location.href='{{ route('admin.tests.create') }}'">Create your first test</x-button>
                        </div>
                    </x-td>
                </x-tr>
            @endforelse
        </x-table>
    </x-card>
@endsection
