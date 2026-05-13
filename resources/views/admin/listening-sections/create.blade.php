@extends('layouts.admin')

@section('title', 'Listening Module Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <x-admin.page-header
        title="Listening Module Manager"
        :description="'IELTS ' . $testSet->test->book_number . ' · ' . $testSet->test->exam_type . ' ' . $testSet->test->year . ' · Set ' . $testSet->set_number"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $testSet->id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach([1,2,3,4] as $s)
        @php $sec = $sections->firstWhere('section_number', $s); @endphp
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Section {{ $s }}</p>
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $sec ? $sec->questions->count() : 0 }}</p>
            <p class="text-xs text-slate-400 font-bold mt-1">Questions</p>
        </div>
        @endforeach
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total</p>
            <p class="text-3xl font-black text-primary">{{ $sections->sum(fn($s) => $s->questions->count()) }}</p>
            <p class="text-xs text-slate-400 font-bold mt-1">/ 40 Questions</p>
        </div>
    </div>

    {{-- Sections --}}
    @foreach($sections as $section)
    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="size-12 rounded-2xl flex items-center justify-center shrink-0 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-500">
                    <span class="material-symbols-outlined text-2xl">headphones</span>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Section {{ $section->section_number }}</h3>
                    <p class="text-sm text-slate-500 font-bold">{{ $section->questions->count() }} questions · {{ Str::limit($section->instruction_text, 80) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($section->audio_path)
                <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest">Audio ✓</span>
                @else
                <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest">No Audio</span>
                @endif
                <x-admin.button :href="route('admin.listening-sections.edit', $section->id)" variant="secondary" size="sm">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </x-admin.button>
            </div>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($section->questions as $qi => $question)
            @php $globalNum = $sections->take($section->section_number - 1)->sum(fn($s) => $s->questions->count()) + $qi + 1; @endphp
            <div class="p-6 sm:px-8 flex items-start gap-5 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                <div class="size-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                    <span class="text-xs font-black text-slate-500">{{ $globalNum }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white leading-relaxed">{{ $question->question_text }}</p>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $question->question_type === 'multiple_choice' ? 'text-violet-500' : 'text-teal-500' }}">
                            {{ $question->question_type === 'multiple_choice' ? 'MCQ' : 'Fill-in' }}
                        </span>
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                            Ans: {{ $question->correct_answer }}
                        </span>
                    </div>
                    @if($question->options->isNotEmpty())
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($question->options as $opt)
                        <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $opt->is_correct ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-50 text-slate-500 border border-slate-200' }}">
                            {{ $opt->option_text }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- Add Section Form --}}
    @if($sections->count() < 4)
    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-500">add_circle</span>
                Add Listening Section
            </h3>
        </div>
        <form action="{{ route('admin.listening-sections.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Section Number</label>
                    <select name="section_number" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold">
                        @for($i = 1; $i <= 4; $i++)
                            @if(!$sections->contains('section_number', $i))
                                <option value="{{ $i }}">Section {{ $i }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Audio File</label>
                    <input type="file" name="audio_file" accept="audio/*" class="w-full px-5 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary/10 file:text-primary file:font-black file:text-xs">
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Instruction Text</label>
                <textarea name="instruction_text" rows="2" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm"></textarea>
            </div>
            <div class="flex justify-end">
                <x-admin.button type="submit" size="lg">
                    <span class="material-symbols-outlined text-sm mr-2">add</span> Add Section
                </x-admin.button>
            </div>
        </form>
    </div>
    @else
    <div class="p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800 flex items-center gap-4">
        <span class="material-symbols-outlined text-emerald-500 text-2xl">check_circle</span>
        <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">All 4 listening sections configured with {{ $sections->sum(fn($s) => $s->questions->count()) }} questions total.</p>
    </div>
    @endif
</div>
@endsection
