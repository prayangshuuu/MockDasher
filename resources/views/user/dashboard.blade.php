@extends('layouts.app')

@section('title', 'User Dashboard')
@section('header', 'Welcome back, ' . explode(' ', auth()->user()->name ?? 'User')[0] . '!')

@section('content')
<div class="space-y-8">
    
    <!-- Header Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-dwimik-text)] opacity-70 mb-1 tracking-wide uppercase">Target Score</p>
                    <p class="text-[var(--text-dwimik-h3)] font-bold text-[var(--color-dwimik-text)]">{{ auth()->user()->target_band_score ?? 'Not Set' }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-[var(--color-dwimik-primary)] rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-bullseye"></i>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-dwimik-text)] opacity-70 mb-1 tracking-wide uppercase">Tests Taken</p>
                    <p class="text-[var(--text-dwimik-h3)] font-bold text-[var(--color-dwimik-text)]">{{ $testsTakenCount ?? collect($recentAttempts ?? [])->count() ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 text-[var(--color-dwimik-success)] rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-dwimik-text)] opacity-70 mb-1 tracking-wide uppercase">Exam Type</p>
                    <p class="text-[var(--text-dwimik-h3)] font-bold text-[var(--color-dwimik-text)]">{{ auth()->user()->exam_type ?? 'IELTS Academic' }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content: Tests -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-xl font-bold text-[var(--color-dwimik-text)] tracking-tight">Available Mock Tests</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @forelse($tests as $test)
                    <x-card class="hover:border-[var(--color-dwimik-primary)] transition-colors h-full flex flex-col">
                        <div class="flex-grow">
                            <h5 class="font-bold text-lg text-[var(--color-dwimik-text)] mb-2 group-hover:text-[var(--color-dwimik-primary)] transition">{{ $test->title ?? 'IELTS Practice Test' }}</h5>
                            <p class="text-sm text-gray-500 mb-6 font-medium">Reading, Writing, Listening, Speaking</p>
                        </div>
                        <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto pt-4 border-t border-[var(--color-dwimik-divider)]">
                            @csrf
                            <x-button variant="primary" type="submit" class="w-full">Start Preparation</x-button>
                        </form>
                    </x-card>
                @empty
                    <div class="col-span-full">
                        <x-card class="text-center py-12">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="fas fa-file-alt text-4xl text-[var(--color-dwimik-divider)] mb-4"></i>
                                <h4 class="text-lg font-bold text-[var(--color-dwimik-text)]">No Content Available</h4>
                                <p class="text-sm mt-2">Check back later for new IELTS mock exams.</p>
                            </div>
                        </x-card>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Sidebar Content -->
        <div class="space-y-6">
            <!-- Recent Activity -->
            <x-card class="p-0 overflow-hidden" style="padding: 0;">
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-[var(--color-dwimik-text)]">Recent Activity</h3>
                </x-slot>
                
                <div class="p-6">
                    @if(empty($recentAttempts) || $recentAttempts->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-history text-[var(--color-dwimik-divider)] text-3xl mb-3"></i>
                            <p class="text-sm text-gray-500">You haven't taken any tests yet.</p>
                        </div>
                    @else
                        <ul class="space-y-6">
                            @foreach($recentAttempts as $attempt)
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($attempt->status == 'completed')
                                            <i class="fas fa-check-circle text-[var(--color-dwimik-success)] text-lg"></i>
                                        @else
                                            <i class="fas fa-clock text-yellow-500 text-lg"></i>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-[var(--color-dwimik-text)]">{{ $attempt->test->title ?? 'Practice Test' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $attempt->created_at->diffForHumans() }} &bull; Band: {{ $attempt->overall_band ?? '-' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                
                <x-slot name="footer">
                    <div class="text-center">
                        <a href="{{ route('user.history.index') }}" class="text-sm font-medium text-[var(--color-dwimik-primary)] hover:underline">View All History</a>
                    </div>
                </x-slot>
            </x-card>

            <!-- Quick Tips -->
            <div class="bg-blue-50 rounded-[var(--radius-dwimik)] border border-blue-100 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[var(--color-dwimik-primary)] mb-3 flex items-center uppercase tracking-wide">
                    <i class="far fa-lightbulb text-yellow-500 mr-2 text-lg"></i> Prep Tip
                </h3>
                <p class="text-sm text-[var(--color-dwimik-text)] leading-relaxed">
                    Consistent practice is key to a higher band score. Try to complete one full mock exam under timed conditions every week. Focus on your weakest module!
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
