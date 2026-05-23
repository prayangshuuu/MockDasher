@extends('layouts.admin')

@section('title', 'Create New Exam')

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
        <a href="{{ route('admin.tests.index') }}" class="hover:text-primary transition-colors">Exams & Tests</a>
        <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
        <span class="font-semibold text-slate-900 dark:text-white">Create Exam</span>
    </nav>
@endsection

@section('content')

<div class="max-w-5xl mx-auto mb-10 space-y-8">
    <div class="flex items-center">
        <a href="{{ route('admin.tests.index') }}" class="flex items-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-primary transition-colors">
            <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform rotate-90 opacity-70" alt="<" />
            Back to Exams
        </a>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 items-start">

        <div class="lg:col-span-1">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Exam Details</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Provide the basic information for this test. Students will see these details before starting.</p>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
                <form action="{{ route('admin.tests.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">IELTS Book Number</label>
                            <input type="number" name="book_number" placeholder="e.g., 20" min="1"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Publication Year</label>
                            <input type="number" name="year" placeholder="e.g., 2025" min="1990"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Exam Type</label>
                            <div class="relative">
                                <select name="exam_type" class="block w-full appearance-none bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm cursor-pointer">
                                    <option value="Academic">Academic</option>
                                    <option value="General Training">General Training</option>
                                </select>
                                <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Status</label>
                            <div class="relative">
                                <select name="status" class="block w-full appearance-none bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm cursor-pointer">
                                    <option value="draft">Draft (Hidden)</option>
                                    <option value="published">Published (Visible)</option>
                                </select>
                                <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                            </div>
                        </div>

                    </div>

                    <div class="flex items-start gap-4 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 p-5">
                        <img src="/storage/asset/icons/info.svg" class="w-6 h-6 mt-0.5" alt="Info" />
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Automatic Test Sets</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Creating this exam will automatically generate 4 empty Test Sets (Set 1–4) for you to configure with module content.</p>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.tests.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors shadow-sm">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                            Save Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
