@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between px-4 sm:px-0">
            <div class="flex-1 min-w-0">
                <a href="{{ route('user.history.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-3">
                    <i class="fas fa-arrow-left mr-2"></i> Back to History
                </a>
                <h2 class="text-3xl font-extrabold text-gray-900 sm:truncate">
                    Result: {{ $attempt->test->title ?? 'Unknown Test' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Taken on {{ $attempt->created_at->format('l, F j, Y \a\t h:i A') }}
                </p>
            </div>
        </div>

        <!-- Overall Score Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center sm:text-left sm:flex sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h3 class="text-xl font-bold text-gray-900">Overall Band Score</h3>
                <p class="text-sm text-gray-500 mt-1">Status: <span class="uppercase tracking-wide font-semibold {{ $attempt->status == 'completed' ? 'text-green-600' : 'text-yellow-600' }}">{{ $attempt->status }}</span></p>
            </div>
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-blue-50 border-4 border-blue-200">
                <span class="text-3xl font-extrabold text-blue-700">{{ $attempt->overall_band ?? '?' }}</span>
            </div>
        </div>

        <!-- Module Breakdown -->
        <h3 class="text-lg font-bold text-gray-900 px-4 sm:px-0 mt-8 mb-4">Module Breakdown</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-4 sm:px-0">
            
            <!-- Reading -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col items-center">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-full mb-4">
                    <i class="fas fa-book-open fa-lg"></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Reading</h4>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $attempt->reading_band ?? '-' }}</div>
            </div>

            <!-- Listening -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col items-center">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-full mb-4">
                    <i class="fas fa-headphones fa-lg"></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Listening</h4>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $attempt->listening_band ?? '-' }}</div>
            </div>

            <!-- Writing -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col items-center">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-full mb-4">
                    <i class="fas fa-pen-nib fa-lg"></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Writing</h4>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $attempt->writing_band ?? 'Pending' }}</div>
            </div>

            <!-- Speaking -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col items-center">
                <div class="p-3 bg-rose-50 text-rose-600 rounded-full mb-4">
                    <i class="fas fa-microphone-alt fa-lg"></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Speaking</h4>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $attempt->speaking_band ?? 'Pending' }}</div>
            </div>

        </div>

    </div>
</div>
@endsection
