@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

<section class="mb-8">
    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
        Admin Overview
    </h2>
    <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Platform statistics and recent activity.</p>
</section>

<section class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Users</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ $totalUsers ?? 0 }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30">
                <img src="/storage/asset/icons/group.svg" class="w-5 h-5" alt="Users" />
            </div>
        </div>
    </div>

    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Exams</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ $activeExams ?? 0 }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                <img src="/storage/asset/icons/bolt.svg" class="w-5 h-5" alt="Exams" />
            </div>
        </div>
    </div>

    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Questions in Bank</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ $totalQuestions ?? 0 }}
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/30">
                <img src="/storage/asset/icons/database.svg" class="w-5 h-5" alt="Questions" />
            </div>
        </div>
    </div>

    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pass Rate</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ $passRate ?? 0 }}<span class="text-lg font-medium text-slate-400">%</span>
                </p>
            </div>
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 dark:bg-sky-900/30">
                <img src="/storage/asset/icons/trending-up.svg" class="w-5 h-5" alt="Pass Rate" />
            </div>
        </div>
    </div>

</section>

<section>
    <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Test Attempts</h3>
                <a href="{{ route('admin.tests.index') }}" class="text-sm font-bold text-primary hover:text-primary-hover transition-colors">
                    View All
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Student</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Exam</th>
                        <th class="hidden px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">Date</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($recentAttempts ?? [] as $attempt)
                        <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-xs font-bold text-primary overflow-hidden border border-indigo-100 dark:border-indigo-800">
                                        <img class="h-full w-full object-cover" src="{{ $attempt->user->getAvatarUrl() }}" alt="{{ $attempt->user->name }}">
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $attempt->user->name ?? 'Unknown Student' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 font-medium">
                                IELTS {{ $attempt->testSet->test->book_number ?? '' }} {{ $attempt->testSet->test->exam_type ?? 'Test' }}
                            </td>

                            <td class="hidden px-6 py-4 text-sm text-slate-500 dark:text-slate-400 sm:table-cell">
                                {{ $attempt->created_at->format('M d, Y h:i A') }}
                            </td>

                            <td class="px-6 py-4">
                                @if(($attempt->status ?? 'pending') === 'completed')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-medium text-emerald-800 dark:text-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-medium text-amber-800 dark:text-amber-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        In Progress
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.results.show', $attempt) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 hover:border-primary/50 dark:hover:border-primary/50 bg-white dark:bg-surface-dark hover:bg-primary/5 dark:hover:bg-primary/10">
                                    <img src="/storage/asset/icons/eye.svg" class="w-4 h-4" alt="View" />
                                    View Result
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="max-w-xs mx-auto">
                                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-700">monitoring</span>
                                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mt-4">No recent activity</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">No student attempts have been recorded yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
