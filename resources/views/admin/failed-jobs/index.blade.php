@extends('layouts.admin')

@section('title', 'Failed Jobs — Queue Dashboard')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
    <span class="text-slate-900 dark:text-white font-medium">Failed Jobs</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Queue Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Monitor and recover failed AI evaluation jobs.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if($jobs->total() > 0)
            <form method="POST" action="{{ route('admin.failed-jobs.retry-all') }}" onsubmit="return confirm('Retry all {{ $jobs->total() }} failed jobs?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                    <span class="material-symbols-outlined text-base">replay</span>
                    Retry All
                </button>
            </form>
            <form method="POST" action="{{ route('admin.failed-jobs.destroy-all') }}" onsubmit="return confirm('Permanently delete ALL failed jobs?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                    <span class="material-symbols-outlined text-base">delete_sweep</span>
                    Flush All
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-sm font-medium text-emerald-800 dark:text-emerald-300">
        <span class="material-symbols-outlined text-base shrink-0">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Failed</p>
            <p class="text-3xl font-black {{ $jobs->total() > 0 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $jobs->total() }}</p>
        </div>
        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Queue</p>
            <p class="text-lg font-bold text-slate-900 dark:text-white">ai-evaluation</p>
        </div>
        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Driver</p>
            <p class="text-lg font-bold text-slate-900 dark:text-white">{{ ucfirst(config('queue.default')) }}</p>
        </div>
    </div>

    {{-- Jobs table --}}
    <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl shadow-soft overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Failed Job Log</h3>
            <span class="text-xs text-slate-400 dark:text-slate-500">Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }}</span>
        </div>

        @if($jobs->isEmpty())
        <div class="py-16 text-center">
            <span class="material-symbols-outlined text-5xl text-emerald-400 dark:text-emerald-500">check_circle</span>
            <p class="mt-3 text-base font-bold text-slate-600 dark:text-slate-400">No failed jobs — queue is healthy.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/30">
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Job Class</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Queue</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Failed At</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Exception</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($jobs as $job)
                    @php
                        $payload = json_decode($job->payload, true);
                        $jobClass = $payload['displayName'] ?? $payload['job'] ?? 'Unknown';
                        $shortClass = class_basename(str_replace('\\', '/', $jobClass));
                        $exceptionSnippet = mb_substr($job->exception ?? '', 0, 180);
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 text-xs font-bold border border-rose-100 dark:border-rose-900/40">
                                <span class="material-symbols-outlined text-xs">error</span>
                                {{ $shortClass }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs font-mono text-slate-500 dark:text-slate-400">{{ $job->queue }}</td>
                        <td class="px-5 py-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($job->failed_at)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-5 py-4 max-w-xs">
                            <p class="text-xs text-rose-600 dark:text-rose-400 font-mono truncate" title="{{ $job->exception }}">{{ $exceptionSnippet }}…</p>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.failed-jobs.retry', $job->uuid) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg hover:bg-emerald-100 transition-colors">
                                        <span class="material-symbols-outlined text-xs">replay</span>
                                        Retry
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.failed-jobs.destroy', $job->uuid) }}" onsubmit="return confirm('Delete this job?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                        <span class="material-symbols-outlined text-xs">delete</span>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages())
        <div class="p-5 border-t border-slate-200 dark:border-slate-800">
            {{ $jobs->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
