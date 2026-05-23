@extends('layouts.admin')

@section('title', 'Listening Module Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Listening Module Manager</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">IELTS {{ $testSet->test->book_number }} · {{ $testSet->test->exam_type }} {{ $testSet->test->year }} · Set {{ $testSet->set_number }}</p>
        </div>
        <a href="{{ route('admin.test_sets.show', $testSet->id) }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors shadow-sm">
            <img src="/storage/asset/icons/arrowback.svg" class="w-4 h-4 opacity-70" alt="Back" />
            Back to Set
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach([1,2,3,4] as $s)
        @php $sec = $sections->firstWhere('section_number', $s); @endphp
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-[0.03] group-hover:opacity-[0.05] transition-opacity">
                <img src="/storage/asset/icons/section.svg" class="w-24 h-24" alt="bg" />
            </div>
            <div class="flex items-center gap-2 mb-2">
                <img src="/storage/asset/icons/section.svg" class="w-4 h-4 opacity-50" alt="Section" />
                <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Section {{ $s }}</p>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $sec ? $sec->questions->count() : 0 }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Questions</p>
        </div>
        @endforeach
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-primary/20 dark:border-primary/20 shadow-soft relative overflow-hidden group bg-primary/5">
            <div class="absolute -right-4 -top-4 opacity-[0.03] group-hover:opacity-[0.05] transition-opacity">
                <img src="/storage/asset/icons/section.svg" class="w-24 h-24" alt="bg" />
            </div>
            <div class="flex items-center gap-2 mb-2">
                <img src="/storage/asset/icons/section.svg" class="w-4 h-4 opacity-50" alt="Total" />
                <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total</p>
            </div>
            <p class="text-3xl font-bold text-primary">{{ $sections->sum(fn($s) => $s->questions->count()) }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">/ 40 Questions</p>
        </div>
    </div>

    {{-- Sections --}}
    <div class="space-y-6">
        @foreach($sections as $section)
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-xl flex items-center justify-center shrink-0 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800">
                        <img src="/storage/asset/icons/headphone.svg" class="w-6 h-6 opacity-60" alt="Headphones" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Section {{ $section->section_number }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">{{ $section->questions->count() }} questions · {{ Str::limit($section->instruction_text, 80) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($section->audio_path)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-widest">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Audio ✓
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-[10px] font-bold uppercase tracking-widest">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        No Audio
                    </span>
                    @endif
                    <a href="{{ route('admin.listening-sections.edit', $section->id) }}" class="flex size-9 items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700">
                        <img src="/storage/asset/icons/edit.svg" class="w-4 h-4 opacity-70" alt="Edit" />
                    </a>
                </div>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800/50">
                @foreach($section->questions as $qi => $question)
                @php $globalNum = $sections->take($section->section_number - 1)->sum(fn($s) => $s->questions->count()) + $qi + 1; @endphp
                <div class="p-6 flex items-start gap-5 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                    <div class="size-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-700">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $globalNum }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white leading-relaxed">{{ $question->question_text }}</p>
                        <div class="flex items-center gap-4 mt-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest {{ $question->question_type === 'multiple_choice' ? 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300' : 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300' }}">
                                {{ $question->question_type === 'multiple_choice' ? 'MCQ' : 'Fill-in' }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/30">
                                Ans: {{ $question->correct_answer }}
                            </span>
                        </div>
                        @if($question->options->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($question->options as $opt)
                            <span class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $opt->is_correct ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50' : 'bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
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
    </div>

    {{-- Add Section Form --}}
    @if($sections->count() < 4)
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden mt-8">
        <div class="p-6 sm:p-8 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <img src="/storage/asset/icons/create.svg" class="w-6 h-6" alt="Add" />
                Add Listening Section
            </h3>
        </div>
        <form action="{{ route('admin.listening-sections.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Section Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <img src="/storage/asset/icons/section.svg" class="w-5 h-5 opacity-50" alt="Section" />
                        </div>
                        <select name="section_number" class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm appearance-none cursor-pointer">
                            @for($i = 1; $i <= 4; $i++)
                                @if(!$sections->contains('section_number', $i))
                                    <option value="{{ $i }}">Section {{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                        <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Audio File</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <img src="/storage/asset/icons/microphone.svg" class="w-5 h-5 opacity-50" alt="Audio" />
                        </div>
                        <input type="file" name="audio_file" accept="audio/*" class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-bold file:text-xs file:cursor-pointer hover:file:bg-primary/20">
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Instruction Text</label>
                <div class="relative">
                    <div class="absolute top-3.5 left-4 flex items-start pointer-events-none">
                        <img src="/storage/asset/icons/instruction.svg" class="w-5 h-5 opacity-50" alt="Instruction" />
                    </div>
                    <textarea name="instruction_text" rows="2" class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm"></textarea>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                    <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Add" />
                    Add Section
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="mt-8 p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 flex items-center gap-4 shadow-sm">
        <img src="/storage/asset/icons/check-circle.svg" class="w-8 h-8 text-emerald-500" alt="Complete" style="filter: invert(56%) sepia(54%) saturate(464%) hue-rotate(107deg) brightness(97%) contrast(92%);" />
        <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">All 4 listening sections configured with <span class="bg-emerald-200 dark:bg-emerald-800/50 px-2 py-0.5 rounded text-emerald-900 dark:text-emerald-100 mx-1">{{ $sections->sum(fn($s) => $s->questions->count()) }}</span> questions total.</p>
    </div>
    @endif
</div>
@endsection
