@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Overview')
@section('subheader', 'Welcome to the MockDasher Content Management System.')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stats Card 1 -->
        <div class="bg-[#F6F3EE] rounded-[8px] border border-[#D8D4CC] p-6 flex flex-col justify-center">
            <p class="text-sm font-medium text-[#1A1A1A] opacity-70 mb-1">Total Tests</p>
            <p class="text-[28px] font-bold text-[#1A1A1A]">{{ $stats['total_tests'] }}</p>
        </div>

        <!-- Stats Card 2 -->
        <div class="bg-[#F6F3EE] rounded-[8px] border border-[#D8D4CC] p-6 flex flex-col justify-center">
            <p class="text-sm font-medium text-[#1A1A1A] opacity-70 mb-1">Total Test Sets</p>
            <p class="text-[28px] font-bold text-[#1A1A1A]">{{ $stats['total_test_sets'] }}</p>
        </div>

        <!-- Stats Card 3 -->
        <div class="bg-[#F6F3EE] rounded-[8px] border border-[#D8D4CC] p-6 flex flex-col justify-center">
            <p class="text-sm font-medium text-[#1A1A1A] opacity-70 mb-1">Total Users</p>
            <p class="text-[28px] font-bold text-[#1A1A1A]">{{ $stats['users'] }}</p>
        </div>

        <!-- Stats Card 4 -->
        <div class="bg-[#F6F3EE] rounded-[8px] border border-[#D8D4CC] p-6 flex flex-col justify-center">
            <p class="text-sm font-medium text-[#1A1A1A] opacity-70 mb-1">Total Attempts</p>
            <p class="text-[28px] font-bold text-[#1A1A1A]">{{ $stats['attempts'] }}</p>
        </div>
    </div>

    <!-- Recent Tests Table -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Recent Tests Overview</h3>
            <a href="{{ route('admin.tests.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All →</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Info</th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tests as $test)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $test->title }}</div>
                                <div class="text-sm text-gray-500">Test #{{ $test->number }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $test->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($test->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.tests.show', $test->id) }}" class="text-blue-600 hover:text-blue-900">Manage Content</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                    <p>No tests available yet.</p>
                                    <a href="{{ route('admin.tests.create') }}" class="mt-2 text-blue-600 font-medium hover:underline">Create your first test</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
