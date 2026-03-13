@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between px-4 sm:px-0">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:truncate">
                    My Test History
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Review all your past mock exams and track your progress.
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition">
                    Take New Test
                </a>
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Information</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Taken</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Band</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($attempts as $attempt)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $attempt->test->title ?? 'Unknown Test' }}</div>
                                    <div class="text-xs text-gray-500">{{ optional($attempt->test->collection)->title ?? 'N/A' }}</div>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    @if($attempt->status === 'completed')
                                        {{ $attempt->overall_band ?? 'Evaluating' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('user.history.show', $attempt->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold bg-blue-50 px-3 py-1 rounded">View Results</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="mx-auto h-12 w-12 text-gray-400 mb-3 text-4xl">
                                        <i class="far fa-folder-open"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">No tests taken yet.</p>
                                    <p class="text-sm text-gray-500 mt-1">Start your first IELTS mock exam to build your history.</p>
                                </td>
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
    </div>
</div>
@endsection
