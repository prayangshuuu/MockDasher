@extends('layouts.admin')

@section('title', 'Exam Management')

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
        <span class="font-semibold text-slate-900 dark:text-white">Exams & Tests</span>
    </nav>
@endsection

@section('content')

<section class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Exam Management</h2>
        <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Create and manage tests for students.</p>
    </div>
    <a href="{{ route('admin.tests.create') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
        <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Create" />
        Create New Exam
    </a>
</section>

<section>
    <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Exam Name</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Year</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Test Sets</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($tests ?? [] as $test)
                        <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800">
                                        <img src="/storage/asset/icons/library.svg" class="w-5 h-5 opacity-60" alt="Exam" />
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">
                                        IELTS {{ $test->book_number ?? '' }} ({{ $test->exam_type ?? 'Test' }})
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                                {{ $test->year ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                                {{ $test->testSets ? $test->testSets->count() : 0 }} Sets
                            </td>

                            <td class="px-6 py-4">
                                @if(($test->status ?? 'published') === 'published')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-medium text-emerald-800 dark:text-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-xs font-medium text-slate-700 dark:text-slate-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.tests.show', $test) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 hover:border-primary/50 dark:hover:border-primary/50 bg-white dark:bg-surface-dark hover:bg-primary/5 dark:hover:bg-primary/10">
                                        <img src="/storage/asset/icons/manage.svg" class="w-4 h-4 opacity-70" alt="Manage" />
                                        Manage
                                    </a>

                                    <form action="{{ route('admin.tests.destroy', $test) }}" method="POST" onsubmit="return confirm('Delete Exam: Are you sure? This will remove all associated test sets and module data.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex size-8 items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-error transition-colors border border-transparent hover:border-red-100 dark:hover:border-red-800" title="Delete">
                                            <img src="/storage/asset/icons/delete.svg" class="w-4 h-4 opacity-70" alt="Delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="max-w-xs mx-auto">
                                    <img src="/storage/asset/icons/library.svg" class="w-12 h-12 mx-auto opacity-20" alt="No Exams" />
                                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mt-4">No exams found</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">You haven't created any exams yet.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('admin.tests.create') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                                            <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Create" />
                                            Create Your First Exam
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($tests) && $tests instanceof \Illuminate\Pagination\LengthAwarePaginator && $tests->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Showing <span class="font-bold text-slate-900 dark:text-white">{{ $tests->firstItem() }}-{{ $tests->lastItem() }}</span> of <span class="font-bold text-slate-900 dark:text-white">{{ $tests->total() }}</span>
                </p>
                <div>
                    {{ $tests->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif
    </div>
</section>

@endsection
