@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'My Test History')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     HEADER
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[var(--color-text-primary)]">My Test History</h2>
        <p class="text-small mt-1">Review all your past mock exams and track your progress.</p>
    </div>
    <x-ui.button variant="primary" href="{{ route('dashboard') }}">
        <span class="material-symbols-outlined text-sm">add</span>
        Take New Test
    </x-ui.button>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     QUICK STATS
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-ui.card>
        <div class="flex items-center gap-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-primary)]">analytics</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Average Band Score</p>
                <p class="text-2xl font-bold text-[var(--color-text-primary)]">{{ $stats['averageBandScore'] !== null ? number_format($stats['averageBandScore'], 1) : 'N/A' }}</p>
            </div>
        </div>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center gap-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-success)]">task_alt</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Tests Completed</p>
                <p class="text-2xl font-bold text-[var(--color-text-primary)]">{{ $stats['testsCompleted'] }}</p>
            </div>
        </div>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center gap-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-base)] bg-[color-mix(in_srgb,var(--color-success)_10%,transparent)]">
                <span class="material-symbols-outlined text-xl text-[var(--color-success)]">trending_up</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)]">Strongest Module</p>
                <p class="text-lg font-bold text-[var(--color-text-primary)]">
                    @if($stats['strongestModule']['name'])
                        {{ $stats['strongestModule']['name'] }}
                        <span class="text-xs font-medium text-[var(--color-success)]">{{ number_format($stats['strongestModule']['score'], 1) }}</span>
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </x-ui.card>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     HISTORY TABLE
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section>
    <x-ui.card :flush="true">
        <x-slot:header>
            <h3 class="text-base font-bold text-[var(--color-text-primary)]">Recent Attempts</h3>
        </x-slot:header>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-[var(--color-divider)]">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Test</th>
                        <th class="hidden px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:table-cell sm:px-6">Status</th>
                        <th class="hidden px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] md:table-cell sm:px-6">Date</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Score</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                    <tr class="border-b border-[var(--color-divider)] last:border-b-0 transition-colors hover:bg-[var(--color-bg-secondary)]">
                        <td class="px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                                    <span class="material-symbols-outlined text-base text-[var(--color-primary)]">description</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-[var(--color-text-primary)] truncate">{{ $attempt->test->title ?? 'Mock Test' }}</p>
                                    <p class="text-xs text-[var(--color-text-secondary)] truncate">{{ $attempt->testSet->title ?? 'Full Simulation' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden px-5 py-4 sm:table-cell sm:px-6">
                            @if($attempt->status === 'completed')
                                <x-ui.badge variant="success">Completed</x-ui.badge>
                            @elseif($attempt->status === 'in_progress')
                                <x-ui.badge variant="pending">In Progress</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">{{ ucfirst($attempt->status) }}</x-ui.badge>
                            @endif
                        </td>
                        <td class="hidden px-5 py-4 text-sm text-[var(--color-text-secondary)] md:table-cell sm:px-6">
                            {{ $attempt->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            @if($attempt->status === 'completed' && $attempt->overall_band !== null)
                                <span class="text-sm font-bold text-[var(--color-primary)]">{{ number_format($attempt->overall_band, 1) }}</span>
                            @else
                                <span class="text-sm text-[var(--color-text-secondary)]">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 sm:px-6 text-right">
                            @if($attempt->status === 'completed')
                                <x-ui.button variant="outline" href="{{ route('user.history.show', $attempt->id) }}" class="text-xs px-3 py-1.5">Review</x-ui.button>
                            @else
                                <x-ui.button variant="primary" href="{{ route('user.history.show', $attempt->id) }}" class="text-xs px-3 py-1.5">Resume</x-ui.button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 sm:px-6">
                            <x-ui.empty-state icon="history" title="No tests taken yet" description="Start your first mock exam to track your progress.">
                                <x-slot:action>
                                    <x-ui.button variant="primary" href="{{ route('dashboard') }}">Start Your First Test</x-ui.button>
                                </x-slot:action>
                            </x-ui.empty-state>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($attempts->hasPages())
            <x-slot:footer>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-[var(--color-text-secondary)]">
                        Showing <span class="font-semibold text-[var(--color-text-primary)]">{{ $attempts->firstItem() }}-{{ $attempts->lastItem() }}</span> of <span class="font-semibold text-[var(--color-text-primary)]">{{ $attempts->total() }}</span>
                    </p>
                    {{ $attempts->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-ui.card>
</section>

@endsection
