@extends('layouts.admin')

@section('title', 'Test Details - IELTS ' . $test->book_number)

@section('content')
<!-- Top Navbar -->
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-8 flex items-center justify-between sticky top-0 z-40">
    <div class="flex items-center gap-4">
        <nav class="flex items-center text-sm font-medium">
            <a class="text-slate-500 hover:text-primary transition-colors" href="{{ route('admin.tests.index') }}">Tests</a>
            <span class="material-symbols-outlined text-slate-400 mx-2 text-base">chevron_right</span>
            <span class="text-slate-900 dark:text-white">IELTS {{ $test->book_number }}</span>
        </nav>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.tests.edit', $test->id) }}" class="flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-all">
            <span class="material-symbols-outlined text-lg">settings</span>
            Test Settings
        </a>
        <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 mx-2"></div>
        <button class="size-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">notifications</span>
        </button>
    </div>
</header>

<!-- Content Area -->
<div class="flex-1 overflow-y-auto p-10">
    <div class="max-w-[1280px] mx-auto w-full">
        <!-- Header Section -->
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $test->status === 'published' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                    {{ $test->status }}
                </span>
                <span class="text-slate-400 font-bold text-xs uppercase tracking-tighter">{{ $test->exam_type }}</span>
            </div>
            <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">IELTS {{ $test->book_number }} <span class="text-slate-300 dark:text-slate-700 font-normal ml-2">({{ $test->year }})</span></h2>
            <p class="text-slate-500 mt-2 text-lg font-medium">Manage the four distinct test sets included in this book volume.</p>
        </div>

        <!-- Sets Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">
            @foreach($test->testSets->sortBy('set_number') as $testSet)
                <div class="group bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden hover:border-primary/50 transition-all flex flex-col h-full">
                    <div class="p-8 flex-1">
                        <div class="flex items-start justify-between mb-8">
                            <div class="size-14 gradient-primary rounded-2xl flex items-center justify-center text-white shadow-lg">
                                <span class="material-symbols-outlined text-3xl font-light">layers</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">SET NUMBER</span>
                                <span class="text-2xl font-black text-slate-900 dark:text-white">0{{ $testSet->set_number }}</span>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Test Set {{ $testSet->set_number }}</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between group/item">
                                <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400 font-semibold text-sm">
                                    <span class="material-symbols-outlined text-xl opacity-60">edit_note</span>
                                    Writing
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold">{{ collect($testSet->writingTasks ?? [])->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between group/item">
                                <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400 font-semibold text-sm">
                                    <span class="material-symbols-outlined text-xl opacity-60">record_voice_over</span>
                                    Speaking
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold">{{ collect($testSet->speakingQuestions ?? [])->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between group/item">
                                <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400 font-semibold text-sm">
                                    <span class="material-symbols-outlined text-xl opacity-60">headphones</span>
                                    Listening
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold">{{ collect($testSet->listeningSections ?? [])->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between group/item">
                                <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400 font-semibold text-sm">
                                    <span class="material-symbols-outlined text-xl opacity-60">menu_book</span>
                                    Reading
                                </div>
                                <span class="px-2.5 py-1 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold">{{ collect($testSet->readingPassages ?? [])->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
                        <a href="{{ route('admin.test_sets.show', $testSet->id) }}" class="flex items-center gap-2 text-primary dark:text-indigo-400 font-bold text-sm hover:gap-3 transition-all">
                            Manage Modules
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
