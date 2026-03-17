@extends('layouts.app')

@section('title', 'User Dashboard')
@section('header', 'Welcome back, ' . explode(' ', auth()->user()->name ?? 'User')[0] . '!')

@section('content')
<div class="space-y-[32px]">
    
    <!-- Header Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[14px] font-medium text-[var(--color-text-secondary)] tracking-wide uppercase mb-[4px]">Target Score</p>
                    <p class="text-[28px] font-bold text-[var(--color-text-primary)]">{{ auth()->user()->target_band_score ?? 'Not Set' }}</p>
                </div>
                <div class="w-[48px] h-[48px] bg-[var(--color-primary)] opacity-10 text-[var(--color-primary)] rounded-[var(--radius-base)] flex items-center justify-center text-[20px] relative">
                    <i class="fas fa-bullseye absolute opacity-100"></i>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[14px] font-medium text-[var(--color-text-secondary)] tracking-wide uppercase mb-[4px]">Tests Taken</p>
                    <p class="text-[28px] font-bold text-[var(--color-text-primary)]">{{ $testsTakenCount ?? collect($recentAttempts ?? [])->count() ?? 0 }}</p>
                </div>
                <div class="w-[48px] h-[48px] bg-[var(--color-success)] opacity-10 text-[var(--color-success)] rounded-[var(--radius-base)] flex items-center justify-center text-[20px] relative">
                    <i class="fas fa-check-circle absolute opacity-100"></i>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[14px] font-medium text-[var(--color-text-secondary)] tracking-wide uppercase mb-[4px]">Exam Type</p>
                    <p class="text-[28px] font-bold text-[var(--color-text-primary)]">{{ auth()->user()->exam_type ?? 'Academic' }}</p>
                </div>
                <div class="w-[48px] h-[48px] border border-[var(--color-divider)] text-[var(--color-text-secondary)] rounded-[var(--radius-base)] flex items-center justify-center text-[20px]">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-[32px]">
        <!-- Main Content: Tests -->
        <div class="lg:col-span-2 space-y-[24px]">
            <h3 class="text-[24px] font-bold text-[var(--color-text-primary)] tracking-tight">Available Mock Tests</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-[24px]">
                @forelse($tests as $test)
                    <x-card class="hover:border-[var(--color-primary)] transition-colors h-full flex flex-col">
                        <div class="flex-grow">
                            <h5 class="font-bold text-[18px] text-[var(--color-text-primary)] mb-[8px] group-hover:text-[var(--color-primary)] transition">{{ $test->title ?? 'IELTS Practice Test' }}</h5>
                            <p class="text-[14px] text-[var(--color-text-secondary)] mb-[24px] font-medium">Reading, Writing, Listening, Speaking</p>
                        </div>
                        <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto pt-[16px] border-t border-[var(--color-divider)]">
                            @csrf
                            <x-button variant="primary" type="submit" class="w-full">Start Preparation</x-button>
                        </form>
                    </x-card>
                @empty
                    <div class="col-span-full">
                        <x-card class="text-center py-[48px]">
                            <div class="flex flex-col items-center justify-center text-[var(--color-text-secondary)]">
                                <i class="fas fa-file-alt text-[48px] opacity-30 mb-[16px]"></i>
                                <h4 class="text-[18px] font-bold text-[var(--color-text-primary)] mt-[8px]">No Content Available</h4>
                                <p class="text-[14px] mt-[8px]">Check back later for new IELTS mock exams.</p>
                            </div>
                        </x-card>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Sidebar Content -->
        <div class="space-y-[24px]">
            <!-- Recent Activity -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-[18px] font-bold text-[var(--color-text-primary)]">Recent Activity</h3>
                </x-slot>
                
                <div>
                    @if(empty($recentAttempts) || $recentAttempts->isEmpty())
                        <div class="text-center py-[16px]">
                            <i class="fas fa-history text-[var(--color-text-secondary)] opacity-30 text-[24px] mb-[12px]"></i>
                            <p class="text-[14px] text-[var(--color-text-secondary)]">You haven't taken any tests yet.</p>
                        </div>
                    @else
                        <ul class="space-y-[24px]">
                            @foreach($recentAttempts as $attempt)
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 mt-[4px]">
                                        @if($attempt->status == 'completed')
                                            <i class="fas fa-check-circle text-[var(--color-success)] text-[18px]"></i>
                                        @else
                                            <i class="fas fa-clock text-[#EAB308] text-[18px]"></i>
                                        @endif
                                    </div>
                                    <div class="ml-[16px]">
                                        <p class="text-[14px] font-bold text-[var(--color-text-primary)]">{{ $attempt->test->title ?? 'Practice Test' }}</p>
                                        <p class="text-[12px] text-[var(--color-text-secondary)] mt-[4px]">{{ $attempt->created_at->diffForHumans() }} &bull; Band: {{ $attempt->overall_band ?? '-' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                
                <x-slot name="footer">
                    <div class="text-center">
                        <a href="{{ route('user.history.index') }}" class="text-[14px] font-bold text-[var(--color-primary)] hover:underline">View All History</a>
                    </div>
                </x-slot>
            </x-card>

            <!-- Quick Tips -->
            <div class="bg-[var(--color-bg-primary)] rounded-[var(--radius-base)] border border-[var(--color-divider)] shadow-sm p-[24px]">
                <h3 class="text-[14px] font-bold text-[var(--color-text-primary)] mb-[12px] flex items-center uppercase tracking-wide">
                    <i class="far fa-lightbulb text-[var(--color-primary)] mr-[8px] text-[18px]"></i> Prep Tip
                </h3>
                <p class="text-[14px] text-[var(--color-text-secondary)] leading-relaxed">
                    Consistent practice is key to a higher band score. Try to complete one full mock exam under timed conditions every week. Focus on your weakest module!
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
