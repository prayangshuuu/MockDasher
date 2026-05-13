@extends('layouts.student')

@section('title', 'Start Test — IELTS ' . $test->book_number)

@section('content')

<div class="mb-10">
    {{-- Header --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-primary)]">
                IELTS {{ $test->book_number ?? '' }} ({{ $test->exam_type ?? 'Mock Test' }})
            </h1>
            <p class="text-sm mt-1 text-[var(--color-text-secondary)]">Select a module to begin your practice session</p>
        </div>
        <x-ui.button variant="secondary" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Dashboard
        </x-ui.button>
    </div>

    {{-- Module Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- LISTENING --}}
        <x-ui.card class="flex flex-col text-center hover-lift">
            <div class="mx-auto flex size-14 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] mb-4">
                <span class="material-symbols-outlined text-2xl text-[var(--color-primary)]">headphones</span>
            </div>
            <h2 class="text-lg font-bold text-[var(--color-text-primary)] mb-1">Listening</h2>
            <p class="text-xs text-[var(--color-text-secondary)] mb-6">4 parts &bull; 40 questions &bull; ~30 min</p>
            
            <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto w-full">
                @csrf
                <input type="hidden" name="module" value="listening">
                <x-ui.button type="submit" variant="primary" class="w-full">Start Module</x-ui.button>
            </form>
        </x-ui.card>

        {{-- READING --}}
        <x-ui.card class="flex flex-col text-center hover-lift">
            <div class="mx-auto flex size-14 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,#F59E0B_10%,transparent)] mb-4">
                <span class="material-symbols-outlined text-2xl text-[#B45309]">menu_book</span>
            </div>
            <h2 class="text-lg font-bold text-[var(--color-text-primary)] mb-1">Reading</h2>
            <p class="text-xs text-[var(--color-text-secondary)] mb-6">3 passages &bull; 40 questions &bull; 60 min</p>
            
            <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto w-full">
                @csrf
                <input type="hidden" name="module" value="reading">
                <x-ui.button type="submit" variant="primary" class="w-full">Start Module</x-ui.button>
            </form>
        </x-ui.card>

        {{-- WRITING --}}
        <x-ui.card class="flex flex-col text-center hover-lift">
            <div class="mx-auto flex size-14 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] mb-4">
                <span class="material-symbols-outlined text-2xl text-[var(--color-success)]">edit_note</span>
            </div>
            <h2 class="text-lg font-bold text-[var(--color-text-primary)] mb-1">Writing</h2>
            <p class="text-xs text-[var(--color-text-secondary)] mb-6">Task 1 + Task 2 &bull; 60 min</p>
            
            <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto w-full">
                @csrf
                <input type="hidden" name="module" value="writing">
                <x-ui.button type="submit" variant="primary" class="w-full">Start Module</x-ui.button>
            </form>
        </x-ui.card>

        {{-- SPEAKING --}}
        <x-ui.card class="flex flex-col text-center hover-lift">
            <div class="mx-auto flex size-14 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,#8B5CF6_10%,transparent)] mb-4">
                <span class="material-symbols-outlined text-2xl text-[#6D28D9]">record_voice_over</span>
            </div>
            <h2 class="text-lg font-bold text-[var(--color-text-primary)] mb-1">Speaking</h2>
            <p class="text-xs text-[var(--color-text-secondary)] mb-6">Part 1, 2 & 3 &bull; ~15 min</p>
            
            <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto w-full">
                @csrf
                <input type="hidden" name="module" value="speaking">
                <x-ui.button type="submit" variant="primary" class="w-full">Start Module</x-ui.button>
            </form>
        </x-ui.card>

    </div>

    {{-- Info note --}}
    <x-ui.card class="mt-8 border-l-4 border-l-[var(--color-primary)]">
        <div class="flex gap-3">
            <span class="material-symbols-outlined text-[var(--color-primary)]">info</span>
            <p class="text-sm text-[var(--color-text-secondary)]">
                <span class="font-bold text-[var(--color-text-primary)]">Good to know:</span> Each module is independent. You can start and resume modules in any order. Your answers are auto-saved during the exam.
            </p>
        </div>
    </x-ui.card>
</div>
@endsection
