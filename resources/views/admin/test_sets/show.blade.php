@extends('layouts.admin')

@section('title', 'Manage Set ' . $test_set->set_number)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" href="{{ route('admin.tests.index') }}">Tests</a>
    <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-sm">chevron_right</span>
    <a class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" href="{{ route('admin.tests.show', $test_set->test_id) }}">IELTS {{ $test_set->test->book_number }}</a>
    <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-sm">chevron_right</span>
    <span class="font-semibold text-slate-900 dark:text-white">Set 0{{ $test_set->set_number }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Title Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div class="space-y-1">
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Test Set: <span class="text-primary">0{{ $test_set->set_number }}</span></h2>
            <p class="text-slate-500 dark:text-slate-400 text-base">Configure the specific module content and tasks for this set.</p>
        </div>
        <div class="flex items-center gap-3">
             <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-bold text-slate-500 uppercase tracking-widest">IELTS {{ $test_set->test->book_number }}</span>
             <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $test_set->test->exam_type }}</span>
        </div>
    </div>

    <!-- 4-Column Module Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Writing Module -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden premium-shadow group hover:border-primary/30 transition-all flex flex-col">
            <div class="h-40 bg-indigo-50 dark:bg-slate-800 relative flex items-center justify-center p-6 overflow-hidden">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-gradient-to-br from-primary to-blue-600"></div>
                <div class="size-16 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center shadow-sm z-10">
                    <span class="material-symbols-outlined text-3xl text-primary">edit_note</span>
                </div>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-4">Writing Module</h3>
                <div class="space-y-3 mb-6 flex-1">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Tasks added</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $test_set->writingTasks->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Status</span>
                        <span class="font-bold text-emerald-600 text-xs uppercase tracking-widest">Configured</span>
                    </div>
                </div>
                <a href="{{ route('admin.writing-tasks.create', $test_set->id) }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-primary to-indigo-600 text-white text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-100 transition-all">
                    Manage Tasks
                </a>
            </div>
        </div>

        <!-- Speaking Module -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden premium-shadow group hover:border-primary/30 transition-all flex flex-col">
            <div class="h-40 bg-blue-50 dark:bg-slate-800 relative flex items-center justify-center p-6 overflow-hidden">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-gradient-to-br from-blue-500 to-cyan-500"></div>
                <div class="size-16 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center shadow-sm z-10">
                    <span class="material-symbols-outlined text-3xl text-blue-500">record_voice_over</span>
                </div>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-4">Speaking Module</h3>
                <div class="space-y-3 mb-6 flex-1">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Parts added</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $test_set->speakingQuestions->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Status</span>
                        <span class="font-bold text-emerald-600 text-xs uppercase tracking-widest">Configured</span>
                    </div>
                </div>
                <a href="{{ route('admin.speaking-questions.create', $test_set->id) }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-blue-500/20 hover:scale-[1.02] active:scale-100 transition-all">
                    Manage Parts
                </a>
            </div>
        </div>

        <!-- Listening Module -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden premium-shadow group hover:border-primary/30 transition-all flex flex-col">
            <div class="h-40 bg-purple-50 dark:bg-slate-800 relative flex items-center justify-center p-6 overflow-hidden">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-gradient-to-br from-purple-500 to-pink-500"></div>
                <div class="size-16 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center shadow-sm z-10">
                    <span class="material-symbols-outlined text-3xl text-purple-500">headphones</span>
                </div>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-4">Listening Module</h3>
                <div class="space-y-3 mb-6 flex-1">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Sections</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $test_set->listeningSections->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Audio Sync</span>
                        <span class="font-bold text-emerald-600 text-xs uppercase tracking-widest">Active</span>
                    </div>
                </div>
                <a href="{{ route('admin.listening-sections.create', $test_set->id) }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-purple-500/20 hover:scale-[1.02] active:scale-100 transition-all">
                    Manage Audio
                </a>
            </div>
        </div>

        <!-- Reading Module -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden premium-shadow group hover:border-primary/30 transition-all flex flex-col">
            <div class="h-40 bg-orange-50 dark:bg-slate-800 relative flex items-center justify-center p-6 overflow-hidden">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-gradient-to-br from-orange-500 to-red-500"></div>
                <div class="size-16 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center shadow-sm z-10">
                    <span class="material-symbols-outlined text-3xl text-orange-500">menu_book</span>
                </div>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-4">Reading Module</h3>
                <div class="space-y-3 mb-6 flex-1">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Passages</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $test_set->readingPassages->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Layout</span>
                        <span class="font-bold text-emerald-600 text-xs uppercase tracking-widest">Optimized</span>
                    </div>
                </div>
                <a href="{{ route('admin.reading-passages.create', $test_set->id) }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-orange-500/20 hover:scale-[1.02] active:scale-100 transition-all">
                    Manage Reading
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Summary Section -->
    <div class="mt-12 p-8 bg-white dark:bg-slate-900 rounded-[3rem] border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="flex items-center gap-6">
            <div class="size-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400">
                <span class="material-symbols-outlined text-4xl">inventory</span>
            </div>
            <div>
                <h4 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Set Configuration Status</h4>
                <p class="text-slate-500 font-medium">You have no active submissions for this test set yet. Deploy to students to gather data.</p>
            </div>
        </div>
        <div class="flex gap-4">
            <button class="px-6 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary transition-colors">
                View Documentation
            </button>
            <button class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-8 py-3.5 rounded-2xl text-xs font-black uppercase tracking-widest premium-shadow hover:scale-[1.05] transition-all">
                Invite Students
            </button>
        </div>
    </div>
</div>
@endsection
