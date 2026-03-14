@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Overview')
@section('subheader', 'Welcome to the MockDasher Content Management System.')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Stats Card 1 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <i class="fas fa-folder-open text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Collections</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['collections'] }}</p>
            </div>
        </div>

        <!-- Stats Card 2 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <i class="fas fa-file-alt text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Tests</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['published_tests'] }} <span class="text-sm font-normal text-gray-400">/ {{ $stats['total_tests'] }} total</span></p>
            </div>
        </div>

        <!-- Stats Card 3 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Active Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['users'] }}</p>
            </div>
        </div>

        <!-- Stats Card 4 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Attempts</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['attempts'] + $stats['listening_attempts'] + $stats['reading_attempts'] }}</p>
            </div>
        </div>
    </div>

    <!-- Module Content Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200 p-4 text-center hover:shadow-sm transition">
            <i class="fas fa-pen-nib text-blue-600 text-lg mb-2"></i>
            <p class="text-2xl font-bold text-blue-800">{{ $stats['writing_tasks'] }}</p>
            <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">Writing Tasks</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 p-4 text-center hover:shadow-sm transition">
            <i class="fas fa-microphone text-green-600 text-lg mb-2"></i>
            <p class="text-2xl font-bold text-green-800">{{ $stats['speaking_questions'] }}</p>
            <p class="text-xs font-medium text-green-600 uppercase tracking-wider">Speaking Questions</p>
        </div>
        <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg border border-teal-200 p-4 text-center hover:shadow-sm transition">
            <i class="fas fa-headphones text-teal-600 text-lg mb-2"></i>
            <p class="text-2xl font-bold text-teal-800">{{ $stats['listening_sections'] }}</p>
            <p class="text-xs font-medium text-teal-600 uppercase tracking-wider">Listening Sections</p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200 p-4 text-center hover:shadow-sm transition">
            <i class="fas fa-book-open text-orange-600 text-lg mb-2"></i>
            <p class="text-2xl font-bold text-orange-800">{{ $stats['reading_passages'] }}</p>
            <p class="text-xs font-medium text-orange-600 uppercase tracking-wider">Reading Passages</p>
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
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
                                <div class="text-sm text-gray-700">{{ optional($test->collection)->title ?? '—' }}</div>
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
