@extends('layouts.student')

@section('title', 'My Exams')

@section('content')

<section class="mb-8">
    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[var(--color-text-primary)]">My Exams</h2>
    <p class="text-small mt-1">Browse available exams and review your past attempts.</p>
</section>

<div x-data="{ activeTab: 'available' }" class="space-y-8">
    <div class="flex gap-2">
        <button @click="activeTab = 'available'" :class="activeTab === 'available' ? 'bg-[var(--color-primary)] text-[var(--color-white)]' : 'bg-[var(--color-bg-primary)] text-[var(--color-text-secondary)] border border-[var(--color-divider)]'" class="rounded-full px-5 py-2 text-sm font-medium transition-all btn-active-state">Available</button>
        <button @click="activeTab = 'completed'" :class="activeTab === 'completed' ? 'bg-[var(--color-primary)] text-[var(--color-white)]' : 'bg-[var(--color-bg-primary)] text-[var(--color-text-secondary)] border border-[var(--color-divider)]'" class="rounded-full px-5 py-2 text-sm font-medium transition-all btn-active-state">Completed</button>
    </div>

    {{-- Available --}}
    <div x-show="activeTab === 'available'" x-transition.opacity>
        @if(isset($availableTests) && $availableTests->count() > 0)
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($availableTests as $test)
                    <x-ui.card class="flex flex-col">
                        <div class="flex-1">
                            <h4 class="text-body-large font-bold text-[var(--color-text-primary)]">{{ $test->title }}</h4>
                            <div class="mt-3 flex flex-wrap items-center gap-4 text-[var(--color-text-secondary)]">
                                <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">timer</span><span class="text-xs font-medium">{{ $test->duration ?? '45 Mins' }}</span></div>
                                <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">format_list_bulleted</span><span class="text-xs font-medium">{{ $test->questions_count ?? '50' }} Questions</span></div>
                                <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">workspace_premium</span><span class="text-xs font-medium">Pass: {{ $test->pass_percentage ?? '70' }}%</span></div>
                            </div>
                        </div>
                        <x-slot:footer>
                            <form action="{{ route('user.tests.start', $test->id) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" variant="primary" class="w-full"><span class="material-symbols-outlined text-sm">play_arrow</span> Start Exam</x-ui.button>
                            </form>
                        </x-slot:footer>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card><x-ui.empty-state icon="quiz" title="No exams available" description="Check back later for new tests." /></x-ui.card>
        @endif
    </div>

    {{-- Completed --}}
    <div x-show="activeTab === 'completed'" x-cloak x-transition.opacity>
        @if(isset($completedAttempts) && $completedAttempts->count() > 0)
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($completedAttempts as $attempt)
                    <x-ui.card class="flex flex-col">
                        <div class="flex-1">
                            <div class="mb-2"><x-ui.badge variant="success">Completed</x-ui.badge></div>
                            <h4 class="text-body-large font-bold text-[var(--color-text-primary)]">{{ $attempt->testSet->test->title ?? 'Mock Test' }}</h4>
                            <div class="mt-3 flex flex-wrap items-center gap-4 text-[var(--color-text-secondary)]">
                                <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">calendar_today</span><span class="text-xs font-medium">{{ $attempt->created_at->format('M d, Y') }}</span></div>
                                <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">analytics</span><span class="text-xs font-medium">Score: {{ $attempt->overall_band !== null ? number_format($attempt->overall_band, 1) : 'N/A' }}</span></div>
                            </div>
                        </div>
                        <x-slot:footer>
                            <x-ui.button variant="outline" href="{{ route('user.history.show', $attempt->id) }}" class="w-full"><span class="material-symbols-outlined text-sm">visibility</span> View Results</x-ui.button>
                        </x-slot:footer>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card><x-ui.empty-state icon="task_alt" title="No completed exams" description="Start an available exam to see your results here." /></x-ui.card>
        @endif
    </div>
</div>

@endsection
