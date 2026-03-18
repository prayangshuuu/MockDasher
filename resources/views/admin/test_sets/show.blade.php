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
    <!-- Page Header -->
    <x-admin.page-header 
        title="Test Set: 0{{ $test_set->set_number }}" 
        description="Configure the specific module content and tasks for this set."
    >
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <x-admin.badge variant="neutral" :label="'IELTS ' . $test_set->test->book_number" />
                <x-admin.badge variant="neutral" :label="$test_set->test->exam_type" />
            </div>
        </x-slot:actions>
    </x-admin.page-header>

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
                        <x-admin.badge variant="success" label="Configured" />
                    </div>
                </div>
                <x-admin.button :href="route('admin.writing-tasks.create', $test_set->id)" block>
                    Manage Tasks
                </x-admin.button>
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
                        <x-admin.badge variant="success" label="Configured" />
                    </div>
                </div>
                <x-admin.button :href="route('admin.speaking-questions.create', $test_set->id)" block class="from-blue-600 to-cyan-600">
                    Manage Parts
                </x-admin.button>
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
                        <x-admin.badge variant="success" label="Active" />
                    </div>
                </div>
                <x-admin.button :href="route('admin.listening-sections.create', $test_set->id)" block class="from-purple-600 to-pink-600">
                    Manage Audio
                </x-admin.button>
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
                        <x-admin.badge variant="success" label="Optimized" />
                    </div>
                </div>
                <x-admin.button :href="route('admin.reading-passages.create', $test_set->id)" block class="from-orange-500 to-red-500">
                    Manage Reading
                </x-admin.button>
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
            <x-admin.button variant="ghost" size="md">
                View Documentation
            </x-admin.button>
            <x-admin.button size="lg">
                Invite Students
            </x-admin.button>
        </div>
    </div>
</div>
@endsection
