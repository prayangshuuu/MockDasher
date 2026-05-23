@extends('layouts.admin')

@section('title', 'Manage Set ' . $test_set->set_number)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
    <a href="{{ route('admin.tests.index') }}" class="hover:text-primary transition-colors">Exams & Tests</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
    <a href="{{ route('admin.tests.show', $test_set->test_id) }}" class="hover:text-primary transition-colors">IELTS {{ $test_set->test->book_number }}</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
    <span class="font-semibold text-slate-900 dark:text-white">Set 0{{ $test_set->set_number }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Test Set: 0{{ $test_set->set_number }}</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Configure the specific module content and tasks for this set.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-bold text-slate-600 dark:text-slate-300">
                IELTS {{ $test_set->test->book_number }}
            </span>
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-bold text-slate-600 dark:text-slate-300">
                {{ $test_set->test->exam_type }}
            </span>
        </div>
    </div>

    <div class="space-y-6">
        @php
            $modules = [
                ['name' => 'Listening Module', 'icon' => '/storage/asset/icons/headphone.svg', 'count' => $test_set->listeningSections->count(), 'label' => 'Sections', 'route' => route('admin.listening-sections.create', $test_set->id)],
                ['name' => 'Reading Module', 'icon' => '/storage/asset/icons/menu.svg', 'count' => $test_set->readingPassages->count(), 'label' => 'Passages', 'route' => route('admin.reading-passages.create', $test_set->id)],
                ['name' => 'Writing Module', 'icon' => '/storage/asset/icons/edit.svg', 'count' => $test_set->writingTasks->count(), 'label' => 'Tasks', 'route' => route('admin.writing-tasks.create', $test_set->id)],
                ['name' => 'Speaking Module', 'icon' => '/storage/asset/icons/microphone.svg', 'count' => $test_set->speakingQuestions->count(), 'label' => 'Parts', 'route' => route('admin.speaking-questions.create', $test_set->id)],
            ];
        @endphp

        @foreach($modules as $mod)
            <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col sm:flex-row sm:items-center justify-between gap-6 hover:shadow-premium hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center gap-5">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 group-hover:scale-110 transition-transform">
                        <img src="{{ $mod['icon'] }}" class="w-6 h-6 opacity-60" alt="{{ $mod['name'] }}" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $mod['name'] }}</h3>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">
                            {{ $mod['count'] }} {{ $mod['label'] }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:ml-auto">
                    <a href="{{ $mod['route'] }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm">
                        Manage {{ str_replace(' Module', '', $mod['name']) }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
