@extends('layouts.admin')

@section('title', 'Manage Results')
@section('header', 'Test Results')
@section('subheader', 'View all user test attempts and scores.')

@section('content')
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <form action="{{ route('admin.results.index') }}" method="GET" class="w-full md:w-1/3 flex">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user or test..." class="w-full border-gray-300 rounded-l-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-r-md transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <span class="text-sm text-gray-500">Total Attempts: {{ $attempts->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attempts as $attempt)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $attempt->user->name ?? 'Unknown User' }}</div>
                                <div class="text-sm text-gray-500">{{ $attempt->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $attempt->test->title ?? 'Unknown Test' }}</div>
                                <div class="text-xs text-gray-500">Collection: {{ optional($attempt->test->collection)->title ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attempt->status === 'completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @elseif($attempt->status === 'in_progress')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($attempt->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attempt->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.results.show', $attempt->id) }}" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i> View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No test attempts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>
@endsection
