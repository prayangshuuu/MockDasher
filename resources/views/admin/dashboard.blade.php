@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Overview')
@section('subheader', 'Welcome to the MockDasher Content Management System.')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stats Card 1 -->
        <div class="bg-dwimik-bg rounded-dwimik border border-dwimik-divider p-6 flex flex-col justify-center">
            <p class="text-sm font-medium text-dwimik-text opacity-70 mb-1">Total Tests</p>
            <p class="text-dwimik-h3 font-bold text-dwimik-text">{{ $stats['total_tests'] }}</p>
        </div>

        <!-- Stats Card 2 -->
        <div class="bg-dwimik-bg rounded-dwimik border border-dwimik-divider p-6 flex flex-col justify-center">
            <p class="text-sm font-medium text-dwimik-text opacity-70 mb-1">Total Test Sets</p>
            <p class="text-dwimik-h3 font-bold text-dwimik-text">{{ $stats['total_test_sets'] }}</p>
        </div>

        <!-- Stats Card 3 -->
        <div class="bg-dwimik-bg rounded-dwimik border border-dwimik-divider p-6 flex flex-col justify-center">
            <p class="text-sm font-medium text-dwimik-text opacity-70 mb-1">Total Users</p>
            <p class="text-dwimik-h3 font-bold text-dwimik-text">{{ $stats['users'] }}</p>
        </div>

        <!-- Stats Card 4 -->
        <div class="bg-dwimik-bg rounded-dwimik border border-dwimik-divider p-6 flex flex-col justify-center">
            <p class="text-sm font-medium text-dwimik-text opacity-70 mb-1">Total Attempts</p>
            <p class="text-dwimik-h3 font-bold text-dwimik-text">{{ $stats['attempts'] }}</p>
        </div>
    </div>

    <!-- Recent Tests Table -->
    <div class="bg-white border border-dwimik-divider rounded-dwimik overflow-hidden">
        <div class="px-6 py-4 border-b border-dwimik-divider bg-dwimik-bg flex justify-between items-center">
            <h3 class="text-lg font-medium text-dwimik-text">Recent Tests Overview</h3>
            <a href="{{ route('admin.tests.index') }}" class="text-sm text-dwimik-primary hover:opacity-80 font-medium">View All →</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-dwimik-divider">
                <thead class="bg-dwimik-bg">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Info</th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-dwimik-divider">
                    @forelse($tests as $test)
                        <tr class="hover:bg-dwimik-bg transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-dwimik-text">IELTS {{ $test->book_number }} {{ $test->exam_type }} {{ $test->year }}</div>
                                <div class="text-sm text-gray-500">Book #{{ $test->book_number }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $test->status === 'published' ? 'bg-dwimik-success bg-opacity-10 text-dwimik-success' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($test->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.tests.show', $test->id) }}" class="text-dwimik-primary hover:opacity-80">Manage Content</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                    <p>No tests available yet.</p>
                                    <a href="{{ route('admin.tests.create') }}" class="mt-2 text-dwimik-primary font-medium hover:underline">Create your first test</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
