@extends('layouts.admin')

@section('title', 'Exam Management')

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)]">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-[var(--color-primary)] transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="font-semibold text-[var(--color-text-primary)]">Exams & Tests</span>
    </nav>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     HEADER & ACTIONS
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[var(--color-text-primary)]">Exam Management</h2>
        <p class="text-small mt-1 text-[var(--color-text-secondary)]">Create and manage tests for students.</p>
    </div>
    <x-ui.button variant="primary" href="{{ route('admin.tests.create') }}">
        <span class="material-symbols-outlined text-sm">add</span>
        Create New Exam
    </x-ui.button>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     DATA TABLE
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section>
    <x-ui.card :flush="true">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--color-divider)] bg-[var(--color-bg-primary)]">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Exam Name</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Year</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Test Sets</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests ?? [] as $test)
                        <tr class="border-b border-[var(--color-divider)] last:border-b-0 transition-colors hover:bg-[var(--color-bg-secondary)]">
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-[var(--radius-xs)] bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)]">
                                        <span class="material-symbols-outlined text-base text-[var(--color-primary)]">library_books</span>
                                    </div>
                                    <span class="text-sm font-semibold text-[var(--color-text-primary)]">
                                        IELTS {{ $test->book_number ?? '' }} ({{ $test->exam_type ?? 'Test' }})
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-4 sm:px-6 text-sm text-[var(--color-text-secondary)]">
                                {{ $test->year ?? 'N/A' }}
                            </td>

                            <td class="px-5 py-4 sm:px-6 text-sm text-[var(--color-text-secondary)]">
                                {{ $test->testSets ? $test->testSets->count() : 0 }} Sets
                            </td>

                            <td class="px-5 py-4 sm:px-6">
                                @if(($test->status ?? 'published') === 'published')
                                    <x-ui.badge variant="success">Published</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">Draft</x-ui.badge>
                                @endif
                            </td>

                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center justify-end gap-2">
                                    <x-ui.button variant="outline" href="{{ route('admin.tests.show', $test) }}" class="text-xs px-3 py-1.5 font-medium">
                                        Manage
                                    </x-ui.button>
                                    
                                    <form action="{{ route('admin.tests.destroy', $test) }}" method="POST" onsubmit="return confirm('Delete Exam: Are you sure? This will remove all associated test sets and module data.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-[var(--radius-xs)] text-[var(--color-text-secondary)] hover:text-[var(--color-error)] hover:bg-[color-mix(in_srgb,var(--color-error)_10%,transparent)] transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center sm:px-6">
                                <x-ui.empty-state
                                    icon="library_books"
                                    title="No exams found"
                                    description="You haven't created any exams yet."
                                >
                                    <x-slot:action>
                                        <x-ui.button variant="primary" href="{{ route('admin.tests.create') }}">Create Your First Exam</x-ui.button>
                                    </x-slot:action>
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (if applicable) --}}
        @if(isset($tests) && $tests instanceof \Illuminate\Pagination\LengthAwarePaginator && $tests->hasPages())
            <x-slot:footer>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-[var(--color-text-secondary)]">
                        Showing <span class="font-semibold text-[var(--color-text-primary)]">{{ $tests->firstItem() }}-{{ $tests->lastItem() }}</span> of <span class="font-semibold text-[var(--color-text-primary)]">{{ $tests->total() }}</span>
                    </p>
                    {{ $tests->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-ui.card>
</section>

@endsection
