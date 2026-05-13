@extends('layouts.admin')

@section('title', 'Test Details - ' . $test->book_number)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" href="{{ route('admin.tests.index') }}">Tests</a>
    <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-sm">chevron_right</span>
    <span class="font-semibold text-slate-900 dark:text-white">IELTS {{ $test->book_number }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Title Section -->
    <x-admin.page-header :title="'Test Details: ' . $test->book_number" :description="'Manage modules and monitor performance for this book volume (' . $test->year . ').'">
        <x-slot:actions>
            <x-admin.button :href="route('admin.tests.edit', $test->id)" variant="outline" icon="edit">
                Edit Test
            </x-admin.button>
            <x-admin.button icon="add" size="md">
                Create New Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <!-- 4-Column Aggregate Module Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Writing Module Summary -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-premium group hover:border-primary/30 transition-all">
            <div class="h-32 bg-indigo-50 dark:bg-slate-800 relative flex items-center justify-center p-6 overflow-hidden">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-gradient-to-br from-primary to-blue-600"></div>
                <div class="size-14 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center shadow-sm z-10">
                    <span class="material-symbols-outlined text-3xl text-primary">edit_note</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Writing Tasks</h3>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Total Tasks</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $test->testSets->flatMap->writingTasks->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Avg. Score</span>
                        <span class="font-bold text-slate-900 dark:text-white">N/A</span>
                    </div>
                </div>
                <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-center">Informatics Overview</div>
            </div>
        </div>

        <!-- Speaking Module Summary -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-premium group hover:border-primary/30 transition-all">
            <div class="h-32 bg-emerald-50 dark:bg-slate-800 relative flex items-center justify-center p-6 overflow-hidden">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-gradient-to-br from-emerald-500 to-teal-500"></div>
                <div class="size-14 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center shadow-sm z-10">
                    <span class="material-symbols-outlined text-3xl text-emerald-500">record_voice_over</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Speaking Parts</h3>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Total Parts</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $test->testSets->flatMap->speakingQuestions->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Avg. Band</span>
                        <span class="font-bold text-slate-900 dark:text-white">N/A</span>
                    </div>
                </div>
                <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-center">Oral Assessment Data</div>
            </div>
        </div>

        <!-- Listening Module Summary -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-premium group hover:border-primary/30 transition-all">
            <div class="h-32 bg-purple-50 dark:bg-slate-800 relative flex items-center justify-center p-6 overflow-hidden">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-gradient-to-br from-purple-500 to-pink-500"></div>
                <div class="size-14 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center shadow-sm z-10">
                    <span class="material-symbols-outlined text-3xl text-purple-500">headphones</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Listening Sections</h3>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Total Sections</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $test->testSets->flatMap->listeningSections->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Avg. Accuracy</span>
                        <span class="font-bold text-slate-900 dark:text-white">N/A</span>
                    </div>
                </div>
                <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-center">Audio Analytics</div>
            </div>
        </div>

        <!-- Reading Module Summary -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-premium group hover:border-primary/30 transition-all">
            <div class="h-32 bg-orange-50 dark:bg-slate-800 relative flex items-center justify-center p-6 overflow-hidden">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-gradient-to-br from-orange-500 to-red-500"></div>
                <div class="size-14 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center shadow-sm z-10">
                    <span class="material-symbols-outlined text-3xl text-orange-500">menu_book</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Reading Passages</h3>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Total Passages</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $test->testSets->flatMap->readingPassages->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Avg. Completion</span>
                        <span class="font-bold text-slate-900 dark:text-white">N/A</span>
                    </div>
                </div>
                <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-center">Comprehension Data</div>
            </div>
        </div>
    </div>

    <!-- Active Test Sets Grid -->
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
        <span class="material-symbols-outlined text-primary">layers</span>
        Available Test Sets
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @foreach($test->testSets->sortBy('set_number') as $testSet)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:border-primary shadow-premium transition-all">
                <div class="p-8">
                    <div class="flex items-start justify-between mb-8">
                        <div class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                            <span class="text-2xl font-black">0{{ $testSet->set_number }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Status</span>
                            <x-admin.badge type="success" label="Active" />
                        </div>
                    </div>

                    <h4 class="text-2xl font-black text-slate-900 dark:text-white mb-6">Test Set {{ $testSet->set_number }}</h4>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Writing</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white">{{ collect($testSet->writingTasks ?? [])->count() }} <span class="text-xs font-medium text-slate-500">Tasks</span></p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Speaking</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white">{{ collect($testSet->speakingQuestions ?? [])->count() }} <span class="text-xs font-medium text-slate-500">Parts</span></p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Listening</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white">{{ collect($testSet->listeningSections ?? [])->count() }} <span class="text-xs font-medium text-slate-500">Sec.</span></p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Reading</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white">{{ collect($testSet->readingPassages ?? [])->count() }} <span class="text-xs font-medium text-slate-500">Pass.</span></p>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <button class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-red-500 transition-colors">Delete Set</button>
                    <x-admin.button :href="route('admin.test_sets.show', $testSet->id)" variant="secondary" size="md">
                        Manage Set
                    </x-admin.button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bottom Summary Section -->
    <div class="mt-12 p-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-8 shadow-premium">
        <div class="flex items-center gap-6">
            <div class="size-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400">
                <span class="material-symbols-outlined text-4xl">analytics</span>
            </div>
            <div>
                <h4 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Test Volume Overview</h4>
                <p class="text-slate-500 font-medium">This book volume contains {{ $test->testSets->count() }} complete mock test sets with full module coverage.</p>
            </div>
        </div>
        <div class="flex gap-4">
            <button class="px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-primary transition-colors">Documentation</button>
            <x-admin.button size="lg">
                Publish Volume
            </x-admin.button>
        </div>
    </div>
</div>
@endsection

