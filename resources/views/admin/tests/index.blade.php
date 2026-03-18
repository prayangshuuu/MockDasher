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
    <x-admin.page-header title="Manage Tests" description="Create, organize, and monitor your mock examination library.">
        <x-slot:actions>
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
            <x-admin.button :href="route('admin.tests.create')" icon="add">
                Create Test
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

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
                            <x-admin.badge :type="$test->status === 'published' ? 'success' : 'warning'" :label="$test->status" />
                        </div>
                        <div class="flex items-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base">calendar_today</span> {{ $test->year }}</span>
                            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base">layers</span> {{ $test->testSets->count() }} Test Sets</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <x-admin.button :href="route('admin.tests.show', $test->id)" variant="secondary">
                        Manage Library
                    </x-admin.button>
                    <x-admin.button :href="route('admin.tests.edit', $test->id)" variant="ghost" icon="edit" size="icon" />
                    
                    <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this entire test volume?');">
                        @csrf
                        @method('DELETE')
                        <x-admin.button variant="danger" icon="delete" size="icon" />
                    </form>
                </div>
            </div>
        @empty
            <x-admin.empty-state 
                title="No tests found" 
                description="Start your collection by creating a new IELTS book volume."
                icon="library_add"
                :actionHref="route('admin.tests.create')"
                actionLabel="Create First Test"
            />
        @endforelse
    </div>

    @if($tests->hasPages())
        <div class="mt-8">
            {{ $tests->links() }}
        </div>
    @endif
</div>
@endsection
