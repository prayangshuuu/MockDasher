@extends('layouts.admin')

@section('title', 'Test Set Modules - Set ' . $test_set->set_number)

@section('content')
<!-- Top Navbar -->
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-8 flex items-center justify-between sticky top-0 z-40">
    <div class="flex items-center gap-4">
        <nav class="flex items-center text-sm font-medium">
            <a class="text-slate-500 hover:text-primary transition-colors" href="{{ route('admin.tests.index') }}">Tests</a>
            <span class="material-symbols-outlined text-slate-400 mx-2 text-base">chevron_right</span>
            <a class="text-slate-500 hover:text-primary transition-colors" href="{{ route('admin.tests.show', $test_set->test_id) }}">IELTS {{ $test_set->test->book_number }}</a>
            <span class="material-symbols-outlined text-slate-400 mx-2 text-base">chevron_right</span>
            <span class="text-slate-900 dark:text-white font-bold">Set 0{{ $test_set->set_number }}</span>
        </nav>
    </div>
    <div class="flex items-center gap-4">
        <button class="size-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">help</span>
        </button>
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
        <div class="mb-10 flex items-end justify-between">
            <div>
                <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Manage Modules</h2>
                <p class="text-slate-500 mt-2 text-lg font-medium">Configure tasks, questions, and passages for Test Set {{ $test_set->set_number }}.</p>
            </div>
            <div class="flex items-center gap-3 pb-1">
                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-bold text-slate-500">IELTS {{ $test_set->test->book_number }}</span>
                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-bold text-slate-500">{{ $test_set->test->exam_type }}</span>
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Writing Module -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-indigo-50/30 dark:bg-indigo-900/10 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <span class="material-symbols-outlined text-2xl">edit_note</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Writing Tasks</h3>
                    </div>
                    <a href="{{ route('admin.writing-tasks.create', $test_set->id) }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-indigo-200 dark:shadow-none">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Add Task
                    </a>
                </div>
                <div class="p-8 flex-1">
                    @if($test_set->writingTasks->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 opacity-40">
                            <span class="material-symbols-outlined text-5xl mb-2">ink_pen</span>
                            <p class="text-sm font-bold">No tasks added yet</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($test_set->writingTasks as $task)
                                <div class="group flex items-center justify-between p-4 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-indigo-200 dark:hover:border-indigo-900/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">TASK {{ $task->task_number }}</span>
                                            <div class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                                            <span class="text-xs font-bold text-slate-400">{{ $task->minimum_word_count }} words</span>
                                        </div>
                                        <h4 class="font-bold text-slate-900 dark:text-white">{{ $task->task_title }}</h4>
                                    </div>
                                    <a href="{{ route('admin.writing-tasks.edit', $task->id) }}" class="size-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all">
                                        <span class="material-symbols-outlined">edit_square</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Speaking Module -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-emerald-50/30 dark:bg-emerald-900/10 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-2xl">record_voice_over</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Speaking Parts</h3>
                    </div>
                    <a href="{{ route('admin.speaking-questions.create', $test_set->id) }}" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-emerald-200 dark:shadow-none">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Add Part
                    </a>
                </div>
                <div class="p-8 flex-1">
                    @if($test_set->speakingQuestions->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 opacity-40">
                            <span class="material-symbols-outlined text-5xl mb-2">mic_none</span>
                            <p class="text-sm font-bold">No parts added yet</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($test_set->speakingQuestions->sortBy('part') as $q)
                                <div class="group flex items-start justify-between p-4 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-emerald-200 dark:hover:border-emerald-900/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all">
                                    <div class="max-w-[75%]">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">PART {{ $q->part }}</span>
                                            @if($q->audio_path)
                                                <div class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                                                <span class="flex items-center gap-1 text-[10px] font-black text-emerald-400 uppercase tracking-widest"><span class="material-symbols-outlined text-[10px]">volume_up</span> AUDIO</span>
                                            @endif
                                        </div>
                                        <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 truncate">{{ $q->question_text }}</h4>
                                    </div>
                                    <a href="{{ route('admin.speaking-questions.edit', $q->id) }}" class="size-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all">
                                        <span class="material-symbols-outlined">edit_square</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Listening Module -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-purple-50/30 dark:bg-purple-900/10 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <span class="material-symbols-outlined text-2xl">headphones</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Listening Sections</h3>
                    </div>
                    <a href="{{ route('admin.listening-sections.create', $test_set->id) }}" class="flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-purple-200 dark:shadow-none">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Add Section
                    </a>
                </div>
                <div class="p-8 flex-1">
                    @if($test_set->listeningSections->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 opacity-40">
                            <span class="material-symbols-outlined text-5xl mb-2">volume_off</span>
                            <p class="text-sm font-bold">No sections added yet</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($test_set->listeningSections->sortBy('section_number') as $sec)
                                <div class="group flex items-center justify-between p-4 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-purple-200 dark:hover:border-purple-900/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-black text-purple-500 uppercase tracking-widest">SECTION {{ $sec->section_number }}</span>
                                            @if($sec->audio_path)
                                                <div class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                                                <span class="flex items-center gap-1 text-[10px] font-black text-purple-400 uppercase tracking-widest"><span class="material-symbols-outlined text-[10px]">music_note</span> AUDIO LOADED</span>
                                            @endif
                                        </div>
                                        <h4 class="font-bold text-slate-900 dark:text-white">Listening Section {{ $sec->section_number }}</h4>
                                    </div>
                                    <a href="{{ route('admin.listening-sections.edit', $sec->id) }}" class="size-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all">
                                        <span class="material-symbols-outlined">edit_square</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reading Module -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-orange-50/30 dark:bg-orange-900/10 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <span class="material-symbols-outlined text-2xl">menu_book</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Reading Passages</h3>
                    </div>
                    <a href="{{ route('admin.reading-passages.create', $test_set->id) }}" class="flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-orange-200 dark:shadow-none">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Add Passage
                    </a>
                </div>
                <div class="p-8 flex-1">
                    @if($test_set->readingPassages->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 opacity-40">
                            <span class="material-symbols-outlined text-5xl mb-2">auto_stories</span>
                            <p class="text-sm font-bold">No passages added yet</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($test_set->readingPassages->sortBy('passage_number') as $passage)
                                <div class="group flex items-center justify-between p-4 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-orange-200 dark:hover:border-orange-900/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all">
                                    <div class="max-w-[80%]">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest">PASSAGE {{ $passage->passage_number }}</span>
                                        </div>
                                        <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 truncate">{{ $passage->title }}</h4>
                                    </div>
                                    <a href="{{ route('admin.reading-passages.edit', $passage->id) }}" class="size-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all">
                                        <span class="material-symbols-outlined">edit_square</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
