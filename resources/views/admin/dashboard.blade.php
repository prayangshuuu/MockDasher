@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     WELCOME HEADER
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-8">
    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[var(--color-text-primary)]">
        Admin Overview
    </h2>
    <p class="text-small mt-1 text-[var(--color-text-secondary)]">Platform statistics and recent activity.</p>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     STATS GRID — 4 stat cards
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

    {{-- Total Users --}}
    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-small text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Total Users</p>
                <p class="mt-2 text-3xl font-bold text-[var(--color-text-primary)]">
                    {{ $totalUsers ?? 0 }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-primary)]">group</span>
            </div>
        </div>
    </x-ui.card>

    {{-- Active Exams --}}
    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-small text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Active Exams</p>
                <p class="mt-2 text-3xl font-bold text-[var(--color-text-primary)]">
                    {{ $activeExams ?? 0 }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-success)]">bolt</span>
            </div>
        </div>
    </x-ui.card>

    {{-- Questions in Bank --}}
    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-small text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Questions in Bank</p>
                <p class="mt-2 text-3xl font-bold text-[var(--color-text-primary)]">
                    {{ $totalQuestions ?? 0 }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,#F59E0B_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[#B45309]">database</span>
            </div>
        </div>
    </x-ui.card>

    {{-- Pass Rate --}}
    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-small text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Pass Rate</p>
                <p class="mt-2 text-3xl font-bold text-[var(--color-text-primary)]">
                    {{ $passRate ?? 0 }}<span class="text-lg font-medium text-[var(--color-text-secondary)]">%</span>
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-primary)]">trending_up</span>
            </div>
        </div>
    </x-ui.card>

</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     RECENT ACTIVITY TABLE
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section>
    <x-ui.card :flush="true">
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-[var(--color-text-primary)]">Recent Test Attempts</h3>
                <a href="{{ route('admin.tests.index') }}" class="text-sm font-semibold text-[var(--color-primary)] transition-opacity hover:opacity-80">
                    View All
                </a>
            </div>
        </x-slot:header>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--color-divider)]">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Student</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Exam</th>
                        <th class="hidden px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:table-cell sm:px-6">Date</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttempts ?? [] as $attempt)
                        <tr class="border-b border-[var(--color-divider)] last:border-b-0 transition-colors hover:bg-[var(--color-bg-secondary)]">
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-primary)_12%,transparent)] text-xs font-bold text-[var(--color-primary)] overflow-hidden">
                                        @if($attempt->user->profile_photo_path ?? false)
                                            <img class="h-full w-full object-cover" src="{{ Storage::url($attempt->user->profile_photo_path) }}" alt="{{ $attempt->user->name }}">
                                        @else
                                            {{ strtoupper(substr($attempt->user->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold text-[var(--color-text-primary)]">
                                        {{ $attempt->user->name ?? 'Unknown Student' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-4 sm:px-6 text-sm text-[var(--color-text-primary)] font-medium">
                                IELTS {{ $attempt->testSet->test->book_number ?? '' }} {{ $attempt->testSet->test->exam_type ?? 'Test' }}
                            </td>

                            <td class="hidden px-5 py-4 text-sm text-[var(--color-text-secondary)] sm:table-cell sm:px-6">
                                {{ $attempt->created_at->format('M d, Y h:i A') }}
                            </td>

                            <td class="px-5 py-4 sm:px-6">
                                @if(($attempt->status ?? 'pending') === 'completed')
                                    <x-ui.badge variant="success">Completed</x-ui.badge>
                                @else
                                    <x-ui.badge variant="pending">In Progress</x-ui.badge>
                                @endif
                            </td>

                            <td class="px-5 py-4 sm:px-6 text-right">
                                <x-ui.button variant="outline" href="{{ route('admin.results.show', $attempt) }}" class="text-xs px-3 py-1.5">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    View Result
                                </x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center sm:px-6">
                                <x-ui.empty-state
                                    icon="monitoring"
                                    title="No recent activity"
                                    description="No student attempts have been recorded yet."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</section>

@endsection
