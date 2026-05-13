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

    <!-- ─── Modules List (Non-Card Layout) ─── -->
    <div class="space-y-4">
        @php
            $modules = [
                [
                    'name' => 'Listening Module',
                    'icon' => 'headphones',
                    'color' => 'var(--color-primary)',
                    'bg' => 'color-mix(in_srgb, var(--color-primary)_10%, transparent)',
                    'count' => $test_set->listeningSections->count(),
                    'label' => 'Sections',
                    'route' => route('admin.listening-sections.create', $test_set->id),
                    'status' => 'Active'
                ],
                [
                    'name' => 'Reading Module',
                    'icon' => 'menu_book',
                    'color' => '#F59E0B',
                    'bg' => 'color-mix(in_srgb, #F59E0B_10%, transparent)',
                    'count' => $test_set->readingPassages->count(),
                    'label' => 'Passages',
                    'route' => route('admin.reading-passages.create', $test_set->id),
                    'status' => 'Optimized'
                ],
                [
                    'name' => 'Writing Module',
                    'icon' => 'edit_note',
                    'color' => 'var(--color-success)',
                    'bg' => 'color-mix(in_srgb, var(--color-success)_10%, transparent)',
                    'count' => $test_set->writingTasks->count(),
                    'label' => 'Tasks',
                    'route' => route('admin.writing-tasks.create', $test_set->id),
                    'status' => 'Configured'
                ],
                [
                    'name' => 'Speaking Module',
                    'icon' => 'record_voice_over',
                    'color' => '#8B5CF6',
                    'bg' => 'color-mix(in_srgb, #8B5CF6_10%, transparent)',
                    'count' => $test_set->speakingQuestions->count(),
                    'label' => 'Parts',
                    'route' => route('admin.speaking-questions.create', $test_set->id),
                    'status' => 'Ready'
                ],
            ];
        @endphp

        @foreach($modules as $mod)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-[var(--radius-lg)] border border-[var(--color-divider)] bg-[var(--color-bg-primary)] hover:border-[color-mix(in_srgb,{{ $mod['color'] }}_30%,transparent)] transition-all group">
                <div class="flex items-center gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl transition-transform group-hover:scale-110" style="background: {{ $mod['bg'] }}; color: {{ $mod['color'] }}">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">{{ $mod['icon'] }}</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[var(--color-text-primary)]">{{ $mod['name'] }}</h3>
                        <p class="text-xs text-[var(--color-text-secondary)] font-medium">
                            {{ $mod['count'] }} {{ $mod['label'] }} &bull; <span class="text-[var(--color-success)]">{{ $mod['status'] }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-ui.button variant="outline" :href="$mod['route']" class="text-xs px-4 py-2 rounded-xl">
                        Manage {{ str_replace(' Module', '', $mod['name']) }}
                    </x-ui.button>
                </div>
            </div>
        @endforeach
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
