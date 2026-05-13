@extends('layouts.student')

@section('title', 'Start Test — IELTS ' . $test->book_number)

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Top Header Section --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <x-ui.badge variant="primary" class="px-2 py-0.5 text-[10px]">Academic</x-ui.badge>
                <x-ui.badge variant="neutral" class="px-2 py-0.5 text-[10px]">Series {{ $test->year }}</x-ui.badge>
            </div>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-[var(--color-text-primary)]">
                IELTS {{ $test->book_number ?? '' }}
            </h1>
            <p class="text-base text-[var(--color-text-secondary)] mt-2 max-w-xl">
                Ready to boost your band score? Select a module below to start your timed practice session. Each module is designed to replicate the real IELTS experience.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" href="{{ route('dashboard') }}" class="rounded-full shadow-sm">
                <span class="material-symbols-outlined text-lg">dashboard</span>
                Dashboard
            </x-ui.button>
        </div>
    </div>

    {{-- Module Selection Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">

        {{-- LISTENING --}}
        <div class="group relative flex flex-col rounded-[2.5rem] border border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-8 transition-all hover:border-[var(--color-primary)] hover:shadow-[var(--shadow-premium)]">
            <div class="mb-6 flex size-14 items-center justify-center rounded-2xl bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] text-[var(--color-primary)] transition-transform group-hover:scale-110">
                <span class="material-symbols-outlined text-3xl" style="font-variation-settings:'FILL' 1">headphones</span>
            </div>
            <h3 class="text-xl font-bold text-[var(--color-text-primary)] mb-2">Listening</h3>
            <div class="space-y-2 mb-8">
                <p class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)]">
                    <span class="material-symbols-outlined text-base">format_list_numbered</span> 40 Questions
                </p>
                <p class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)]">
                    <span class="material-symbols-outlined text-base">schedule</span> 30 Minutes
                </p>
            </div>
            <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto">
                @csrf
                <input type="hidden" name="module" value="listening">
                <x-ui.button type="submit" variant="primary" class="w-full rounded-2xl">Start Module</x-ui.button>
            </form>
        </div>

        {{-- READING --}}
        <div class="group relative flex flex-col rounded-[2.5rem] border border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-8 transition-all hover:border-[#F59E0B] hover:shadow-[var(--shadow-premium)]">
            <div class="mb-6 flex size-14 items-center justify-center rounded-2xl bg-[color-mix(in_srgb,#F59E0B_10%,transparent)] text-[#F59E0B] transition-transform group-hover:scale-110">
                <span class="material-symbols-outlined text-3xl" style="font-variation-settings:'FILL' 1">menu_book</span>
            </div>
            <h3 class="text-xl font-bold text-[var(--color-text-primary)] mb-2">Reading</h3>
            <div class="space-y-2 mb-8">
                <p class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)]">
                    <span class="material-symbols-outlined text-base">chrome_reader_mode</span> 3 Passages
                </p>
                <p class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)]">
                    <span class="material-symbols-outlined text-base">schedule</span> 60 Minutes
                </p>
            </div>
            <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto">
                @csrf
                <input type="hidden" name="module" value="reading">
                <x-ui.button type="submit" variant="primary" class="w-full rounded-2xl !bg-[#F59E0B] !border-[#F59E0B]">Start Module</x-ui.button>
            </form>
        </div>

        {{-- WRITING --}}
        <div class="group relative flex flex-col rounded-[2.5rem] border border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-8 transition-all hover:border-[var(--color-success)] hover:shadow-[var(--shadow-premium)]">
            <div class="mb-6 flex size-14 items-center justify-center rounded-2xl bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)] text-[var(--color-success)] transition-transform group-hover:scale-110">
                <span class="material-symbols-outlined text-3xl" style="font-variation-settings:'FILL' 1">edit_note</span>
            </div>
            <h3 class="text-xl font-bold text-[var(--color-text-primary)] mb-2">Writing</h3>
            <div class="space-y-2 mb-8">
                <p class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)]">
                    <span class="material-symbols-outlined text-base">task</span> 2 Tasks
                </p>
                <p class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)]">
                    <span class="material-symbols-outlined text-base">schedule</span> 60 Minutes
                </p>
            </div>
            <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto">
                @csrf
                <input type="hidden" name="module" value="writing">
                <x-ui.button type="submit" variant="primary" class="w-full rounded-2xl !bg-[var(--color-success)] !border-[var(--color-success)]">Start Module</x-ui.button>
            </form>
        </div>

        {{-- SPEAKING --}}
        <div class="group relative flex flex-col rounded-[2.5rem] border border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-8 transition-all hover:border-[#8B5CF6] hover:shadow-[var(--shadow-premium)]">
            <div class="mb-6 flex size-14 items-center justify-center rounded-2xl bg-[color-mix(in_srgb,#8B5CF6_10%,transparent)] text-[#8B5CF6] transition-transform group-hover:scale-110">
                <span class="material-symbols-outlined text-3xl" style="font-variation-settings:'FILL' 1">record_voice_over</span>
            </div>
            <h3 class="text-xl font-bold text-[var(--color-text-primary)] mb-2">Speaking</h3>
            <div class="space-y-2 mb-8">
                <p class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)]">
                    <span class="material-symbols-outlined text-base">record_voice_over</span> 3 Parts
                </p>
                <p class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)]">
                    <span class="material-symbols-outlined text-base">schedule</span> ~15 Minutes
                </p>
            </div>
            <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="mt-auto">
                @csrf
                <input type="hidden" name="module" value="speaking">
                <x-ui.button type="submit" variant="primary" class="w-full rounded-2xl !bg-[#8B5CF6] !border-[#8B5CF6]">Start Module</x-ui.button>
            </form>
        </div>

    </div>

    {{-- Tips Section --}}
    <div class="rounded-[3rem] bg-[var(--color-bg-secondary)] border border-[var(--color-divider)] p-8 md:p-12">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="size-20 shrink-0 bg-[var(--color-bg-primary)] rounded-[2rem] flex items-center justify-center text-[var(--color-primary)] shadow-sm">
                <span class="material-symbols-outlined text-4xl">lightbulb</span>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h4 class="text-xl font-black text-[var(--color-text-primary)] mb-2">IELTS Practice Tips</h4>
                <p class="text-[var(--color-text-secondary)] leading-relaxed">
                    Make sure you are in a quiet environment. Use headphones for the Listening module to ensure clarity. Your progress is saved automatically, so if you lose connection, you can resume from where you left off in the Dashboard or History sections.
                </p>
            </div>
            <div class="shrink-0">
                <x-ui.button variant="outline" class="rounded-full px-8">Read Full Guide</x-ui.button>
            </div>
        </div>
    </div>
</div>
@endsection

