@extends('layouts.admin')

@section('title', 'Manage Tests')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <span class="font-semibold text-slate-900 dark:text-white">Tests</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Manage Tests</h2>
            <p class="text-slate-500 dark:text-slate-400 text-base">Create, organize, and monitor your mock examination library.</p>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <form action="{{ route('admin.tests.index') }}" method="GET" class="flex gap-3">
                <select name="type" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold shadow-sm focus:ring-primary/20 transition-all">
                    <option value="">All Types</option>
                    <option value="Academic" {{ request('type') == 'Academic' ? 'selected' : '' }}>Academic</option>
                    <option value="General" {{ request('type') == 'General' ? 'selected' : '' }}>General Training</option>
                </select>
                <select name="status" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold shadow-sm focus:ring-primary/20 transition-all">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </form>
            <a href="{{ route('admin.tests.create') }}" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">add</span>
                Create Test
            </a>
        </div>
    </div>

    <!-- Tests List Container -->
    <div class="space-y-4">
        @forelse($tests as $test)
            <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col md:flex-row md:items-center justify-between gap-6 group hover:border-primary/50 transition-all">
                <div class="flex items-center gap-5">
                    <div class="size-14 rounded-2xl flex items-center justify-center shrink-0 {{ $test->exam_type === 'Academic' ? 'bg-indigo-50 dark:bg-indigo-900/40 text-primary' : 'bg-sky-50 dark:bg-sky-900/40 text-sky-500' }}">
                        <span class="material-symbols-outlined text-3xl">{{ $test->exam_type === 'Academic' ? 'school' : 'public' }}</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="font-black text-slate-900 dark:text-white text-xl">IELTS {{ $test->book_number }}</h3>
                            <span class="px-2 py-0.5 rounded-lg {{ $test->status === 'published' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} text-[10px] font-black uppercase tracking-widest border border-current opacity-70">
                                {{ $test->status }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base">calendar_today</span> {{ $test->year }}</span>
                            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base">layers</span> {{ $test->testSets->count() }} Test Sets</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.tests.show', $test->id) }}" class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-xs font-black uppercase tracking-widest hover:-translate-y-1 transition-all shadow-lg">
                        Manage Library
                    </a>
                    <a href="{{ route('admin.tests.edit', $test->id) }}" class="size-12 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-primary rounded-xl transition-colors border border-transparent shadow-sm">
                        <span class="material-symbols-outlined text-xl">edit</span>
                    </a>
                    <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this entire test volume?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="size-12 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-red-500 rounded-xl transition-colors border border-transparent shadow-sm">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800 p-20 text-center">
                <div class="size-20 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl text-slate-300">library_add</span>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">No tests found</h3>
                <p class="text-slate-500 font-medium mb-8">Start your collection by creating a new IELTS book volume.</p>
                <a href="{{ route('admin.tests.create') }}" class="inline-flex items-center gap-2 bg-primary text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                    Create First Test
                </a>
            </div>
        @endforelse
    </div>

    @if($tests->hasPages())
        <div class="mt-8">
            {{ $tests->links() }}
        </div>
    @endif
</div>
@endsection
