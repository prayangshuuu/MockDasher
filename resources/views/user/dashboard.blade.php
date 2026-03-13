@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header & Profile Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 md:flex md:items-center md:justify-between bg-gradient-to-r from-blue-600 to-indigo-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 relative">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="Profile Photo" class="h-20 w-20 rounded-full object-cover border-4 border-white shadow-md">
                        @else
                            <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center text-blue-600 font-bold text-3xl border-4 border-white shadow-md shadow-inner">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-6 text-white">
                        <h2 class="text-2xl font-bold">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
                        <p class="mt-1 text-blue-100 font-medium opacity-90">Ready to crush your {{ auth()->user()->exam_type ?? 'IELTS' }} goals?</p>
                    </div>
                </div>
                <div class="mt-6 md:mt-0 flex gap-3">
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-md text-sm font-medium text-white transition-colors shadow-sm backdrop-blur-sm">
                        <i class="fas fa-user-edit mr-2"></i> Edit Profile
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100 bg-white">
                <div class="p-4 text-center">
                    <span class="block text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Target Score</span>
                    <span class="text-xl font-bold text-gray-900">{{ auth()->user()->target_band_score ?? 'Not Set' }}</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Tests Taken</span>
                    <span class="text-xl font-bold text-gray-900">{{ auth()->user()->testAttempts()->count() }}</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Exam Type</span>
                    <span class="text-xl font-bold text-gray-900">{{ auth()->user()->exam_type ?? 'Any' }}</span>
                </div>
                <div class="p-4 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition duration-150 cursor-pointer text-blue-600 rounded-br-lg md:rounded-tr-none">
                    <a href="{{ route('user.history.index') }}" class="font-semibold text-sm w-full h-full flex items-center justify-center">
                        View History <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content: Collections -->
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Available Mock Tests</h3>
                    
                    @forelse($collections as $collection)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">{{ $collection->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $collection->exam_type ?? 'Academic/General' }} &bull; {{ $collection->year ?? 'Latest' }}</p>
                                </div>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-blue-200">{{ $collection->tests->count() }} Tests</span>
                            </div>
                            <div class="p-6">
                                @if($collection->description)
                                    <p class="text-sm text-gray-600 mb-4">{{ $collection->description }}</p>
                                @endif
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @forelse($collection->tests as $test)
                                        <div class="border border-gray-200 rounded p-4 hover:border-blue-400 hover:shadow-sm transition group">
                                            <div class="flex justify-between items-start mb-2">
                                                <h5 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ $test->title }}</h5>
                                                @if($test->status === 'draft')
                                                    <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-1 rounded">Draft</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mb-4 text-left">4 Modules &bull; Reading, Writing, Listening, Speaking</p>
                                            
                                            <form action="{{ route('user.tests.start', $test->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full text-center bg-gray-50 hover:bg-blue-600 text-gray-700 hover:text-white border border-gray-200 hover:border-blue-600 font-medium py-2 px-4 rounded text-sm transition" {{ $test->status === 'draft' ? 'disabled' : '' }}>
                                                    {{ $test->status === 'draft' ? 'Unavailable' : 'Start Preparation' }}
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="col-span-full text-center py-4 text-sm text-gray-500 bg-gray-50 rounded">
                                            No tests available in this collection yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                            <i class="fas fa-books text-gray-300 text-5xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900">No Content Available</h4>
                            <p class="text-sm text-gray-500 mt-2">Check back later for new IELTS collections and mock exams.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
                    @if($recentAttempts->isEmpty())
                        <div class="text-center py-6">
                            <i class="fas fa-history text-gray-300 text-3xl mb-3"></i>
                            <p class="text-sm text-gray-500">You haven't taken any tests yet.</p>
                        </div>
                    @else
                        <ul class="space-y-4">
                            @foreach($recentAttempts as $attempt)
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($attempt->status == 'completed')
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        @else
                                            <i class="fas fa-clock text-yellow-500"></i>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $attempt->test->title ?? 'Unknown Test' }}</p>
                                        <p class="text-xs text-gray-500">{{ $attempt->created_at->diffForHumans() }} &bull; Band: {{ $attempt->overall_band ?? '-' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                            <a href="{{ route('user.history.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">View All History</a>
                        </div>
                    @endif
                </div>

                <!-- Quick Tips -->
                <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-100 p-6">
                    <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center">
                        <i class="far fa-lightbulb text-yellow-500 mr-2"></i> Prep Tip
                    </h3>
                    <p class="text-sm text-blue-800 mb-4">
                        Consistent practice is key to a higher band score. Try to complete one full mock exam under timed conditions every week. Focus on your weakest module!
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
