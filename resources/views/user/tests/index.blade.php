@extends('layouts.student')

@section('title', 'Mock Tests')

@section('content')
<section class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[var(--color-text-primary)]">
            Mock Tests
        </h2>
        <p class="text-small mt-1">Browse and start a new mock test to prepare for your exam.</p>
    </div>
</section>

<section class="mb-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($tests as $test)
        <x-ui.card class="flex flex-col hover-lift">
            <div class="mb-3 flex items-center gap-2">
                <x-ui.badge variant="primary">{{ $test->exam_type }}</x-ui.badge>
            </div>

            <h4 class="text-base font-bold text-[var(--color-text-primary)]">IELTS {{ $test->book_number ?? '' }}</h4>
            <p class="text-small mt-1 line-clamp-2 text-xs">
                {{ $test->exam_type }} — Volume {{ $test->book_number }} ({{ $test->year }})
            </p>

            <div class="mt-4 flex items-center gap-4 text-[var(--color-text-secondary)]">
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">timer</span>
                    <span class="text-xs font-medium">{{ $test->duration ?? '2h 45m' }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">menu_book</span>
                    <span class="text-xs font-medium">{{ $test->test_sets_count ?? 4 }} Modules</span>
                </div>
            </div>

            <div class="mt-auto pt-5">
                <form action="{{ route('user.tests.start', $test->id) }}" method="POST">
                    @csrf
                    <x-ui.button type="submit" variant="primary" class="w-full">
                        Start Preparation
                    </x-ui.button>
                </form>
            </div>
        </x-ui.card>
    @empty
        <div class="col-span-full">
            <x-ui.card>
                <x-ui.empty-state
                    icon="auto_stories"
                    title="No tests available"
                    description="No mock tests currently available. Check back later."
                />
            </x-ui.card>
        </div>
    @endforelse
</section>
@endsection
