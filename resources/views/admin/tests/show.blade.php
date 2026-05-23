@extends('layouts.admin')

@section('title', 'Manage Test: IELTS ' . $test->book_number)

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
        <a href="{{ route('admin.tests.index') }}" class="hover:text-primary transition-colors">Exams & Tests</a>
        <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
        <span class="font-semibold text-slate-900 dark:text-white">IELTS {{ $test->book_number }}</span>
    </nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                IELTS {{ $test->book_number }} ({{ $test->year }}) - {{ $test->exam_type }}
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Manage test details and underlying test sets.</p>
        </div>
        <a href="{{ route('admin.tests.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors shadow-sm">
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        {{-- Left: Edit Test Details Form --}}
        <div class="lg:col-span-1">
            <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft h-full flex flex-col">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Update Test Details</h3>

                <form action="{{ route('admin.tests.update', $test) }}" method="POST" class="flex flex-col h-full">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6 flex-grow">
                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">IELTS Book Number</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/section.svg" class="w-5 h-5 opacity-50" alt="Book Number" />
                                </div>
                                <input type="number" name="book_number" value="{{ $test->book_number }}"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Publication Year</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/section.svg" class="w-5 h-5 opacity-50" alt="Year" />
                                </div>
                                <input type="number" name="year" value="{{ $test->year }}"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Exam Type</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/section.svg" class="w-5 h-5 opacity-50" alt="Exam Type" />
                                </div>
                                <select name="exam_type" class="w-full pl-11 pr-4 appearance-none bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl py-3 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm cursor-pointer">
                                    <option value="Academic" {{ $test->exam_type === 'Academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="General Training" {{ $test->exam_type === 'General Training' ? 'selected' : '' }}>General Training</option>
                                </select>
                                <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Status</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/check-circle.svg" class="w-5 h-5 opacity-50" alt="Status" />
                                </div>
                                <select name="status" class="w-full pl-11 pr-4 appearance-none bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl py-3 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm cursor-pointer">
                                    <option value="draft" {{ $test->status === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                                    <option value="published" {{ $test->status === 'published' ? 'selected' : '' }}>Published (Visible)</option>
                                </select>
                                <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 mt-8 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right: Test Sets --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Test Sets (Modules)</h3>
                <form action="{{ route('admin.test_sets.store', $test) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-primary hover:text-primary-hover border border-primary/20 hover:border-primary/50 bg-primary/5 hover:bg-primary/10 rounded-xl transition-all shadow-sm">
                        <img src="/storage/asset/icons/create.svg" class="w-4 h-4" alt="Add" />
                        Add New Set
                    </button>
                </form>
            </div>

            <div class="space-y-4">
                @forelse($test->testSets->sortBy('set_number') as $testSet)
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-6 hover:shadow-premium hover:-translate-y-1 transition-all duration-300 group">
                        <div class="flex items-center gap-5">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-primary font-bold text-lg border border-indigo-100 dark:border-indigo-800">
                                0{{ $testSet->set_number }}
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-slate-900 dark:text-white">Test Set {{ $testSet->set_number }}</h4>
                                <div class="flex items-center gap-3 mt-1.5">
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">4 Modules Configured</span>
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 sm:ml-auto">
                            <a href="{{ route('admin.test_sets.show', $testSet->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm">
                                <img src="/storage/asset/icons/manage.svg" class="w-4 h-4 opacity-70" alt="Manage" />
                                Manage
                            </a>
                            <form action="{{ route('admin.test_sets.destroy', $testSet) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this test set?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex size-10 items-center justify-center rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 transition-colors border border-red-100 dark:border-red-800 shadow-sm" title="Delete">
                                    <img src="/storage/asset/icons/delete.svg" class="w-5 h-5 opacity-70" alt="Delete" />
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl p-10 text-center shadow-soft">
                        <div class="max-w-xs mx-auto">
                            <img src="/storage/asset/icons/library.svg" class="w-12 h-12 mx-auto opacity-20" alt="No Sets" />
                            <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mt-4">No Test Sets</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">This exam doesn't have any test sets yet.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
