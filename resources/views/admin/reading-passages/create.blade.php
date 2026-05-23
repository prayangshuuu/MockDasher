@extends('layouts.admin')

@section('title', 'Reading Module Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Reading Module Manager</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">IELTS {{ $testSet->test->book_number }} · {{ $testSet->test->exam_type }} {{ $testSet->test->year }} · Set {{ $testSet->set_number }}</p>
        </div>
        <a href="{{ route('admin.test_sets.show', $testSet->id) }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors shadow-sm">
            <img src="/storage/asset/icons/arrowback.svg" class="w-4 h-4 opacity-70" alt="Back" />
            Back to Set
        </a>
    </div>

    {{-- Stats Strip --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Passages</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $passages->count() }}<span class="text-lg text-slate-400 font-medium">/3</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Question Groups</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $passages->sum(fn($p) => $p->questionGroups->count()) }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Total Questions</p>
            <p class="text-3xl font-bold text-primary">{{ $passages->sum(fn($p) => $p->questionGroups->sum(fn($g) => $g->questions->count())) }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Status</p>
            @if($passages->sum(fn($p) => $p->questionGroups->sum(fn($g) => $g->questions->count())) >= 40)
                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                    <img src="/storage/asset/icons/check-circle.svg" class="w-5 h-5 text-emerald-500" alt="Ready" style="filter: invert(56%) sepia(54%) saturate(464%) hue-rotate(107deg) brightness(97%) contrast(92%);" />
                    Ready
                </p>
            @else
                <p class="text-sm font-bold text-amber-600 dark:text-amber-400 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">pending</span> Incomplete
                </p>
            @endif
        </div>
    </div>

    {{-- Existing Passages --}}
    @if($passages->isNotEmpty())
    <div x-data="{ activePassage: {{ $passages->first()->passage_number }} }" class="space-y-6">
        {{-- Passage Tabs --}}
        <div class="flex items-center gap-3 overflow-x-auto pb-2">
            @foreach($passages as $passage)
                <button @click="activePassage = {{ $passage->passage_number }}"
                        class="flex items-center gap-3 px-6 py-3 rounded-2xl border-2 transition-all shrink-0"
                        :class="activePassage === {{ $passage->passage_number }}
                            ? 'border-primary bg-primary/5 text-primary shadow-sm'
                            : 'border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                    <span class="size-8 rounded-lg flex items-center justify-center text-xs font-bold"
                          :class="activePassage === {{ $passage->passage_number }} ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'">
                        {{ $passage->passage_number }}
                    </span>
                    <div class="text-left">
                        <p class="text-xs font-bold uppercase tracking-wider">Passage {{ $passage->passage_number }}</p>
                        <p class="text-[10px] font-medium opacity-70 truncate max-w-[180px]">{{ $passage->title }}</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-700"
                          :class="activePassage === {{ $passage->passage_number }} ? 'text-primary border-primary/20' : 'text-slate-500 dark:text-slate-400'">
                        {{ $passage->questionGroups->sum(fn($g) => $g->questions->count()) }}Q
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Passage Content Panels --}}
        @foreach($passages as $passage)
        <div x-show="activePassage === {{ $passage->passage_number }}" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-6">

            {{-- Passage Card --}}
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                {{-- Passage Header --}}
                <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $passage->title }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                            {{ $passage->questionGroups->count() }} group(s) ·
                            {{ $passage->questionGroups->sum(fn($g) => $g->questions->count()) }} question(s) ·
                            {{ str_word_count(strip_tags($passage->content)) }} words
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.reading-passages.edit', $passage->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm">
                            <img src="/storage/asset/icons/edit.svg" class="w-4 h-4 opacity-70" alt="Edit" />
                            Edit Passage
                        </a>
                        <a href="{{ route('admin.reading-question-groups.create', $passage->id) }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-2 rounded-xl text-sm font-bold shadow-soft transition-colors">
                            <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Add" />
                            Add Group
                        </a>
                    </div>
                </div>

                {{-- Question Groups --}}
                @forelse($passage->questionGroups as $group)
                <div class="border-b border-slate-200 dark:border-slate-800 last:border-b-0">
                    {{-- Group Header --}}
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border shrink-0
                                @if(str_contains($group->question_type, 'true_false')) bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800/50
                                @elseif(str_contains($group->question_type, 'yes_no')) bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800/50
                                @elseif($group->question_type === 'multiple_choice') bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-800/50
                                @else bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/50
                                @endif">
                                {{ str_replace('_', ' ', $group->question_type) }}
                            </span>
                            @if($group->group_instruction)
                                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 truncate">{{ Str::limit($group->group_instruction, 80) }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-white dark:bg-surface-dark px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700">{{ $group->questions->count() }}Q</span>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.reading-question-groups.edit', $group->id) }}" class="flex size-8 items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-colors" title="Settings">
                                    <img src="/storage/asset/icons/settings.svg" class="w-4 h-4 opacity-70" alt="Settings" />
                                </a>
                                <a href="{{ route('admin.questions.create', ['type' => 'reading_group', 'id' => $group->id]) }}" class="flex size-8 items-center justify-center rounded-lg text-primary bg-primary/10 hover:bg-primary/20 transition-colors" title="Add Question">
                                    <img src="/storage/asset/icons/add.svg" class="w-4 h-4 text-primary" alt="Add" style="filter: invert(30%) sepia(85%) saturate(2716%) hue-rotate(231deg) brightness(97%) contrast(97%);" />
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Questions List --}}
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach($group->questions as $qi => $q)
                        <div class="px-6 py-3.5 flex items-center gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors group/item">
                            <div class="size-8 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center shrink-0 shadow-sm">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $qi + 1 }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $q->question_text }}</p>
                            </div>
                            <div class="flex items-center gap-4 shrink-0">
                                <span class="px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-xs font-bold text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                    {{ $q->correct_answer }}
                                </span>
                                <a href="{{ route('admin.questions.edit', $q->id) }}" class="opacity-0 group-hover/item:opacity-100 flex size-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-700" title="Edit Question">
                                    <img src="/storage/asset/icons/edit.svg" class="w-4 h-4 opacity-70" alt="Edit" />
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="p-10 text-center">
                    <div class="max-w-xs mx-auto">
                        <img src="/storage/asset/icons/menu.svg" class="w-12 h-12 mx-auto opacity-20 mb-4" alt="No Groups" />
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No question groups yet</p>
                        <a href="{{ route('admin.reading-question-groups.create', $passage->id) }}" class="inline-flex items-center gap-2 mt-4 bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                            <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Create" />
                            Create First Group
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Add New Passage Form --}}
    @if($passages->count() < 3)
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden mt-8">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <img src="/storage/asset/icons/create.svg" class="w-6 h-6 text-primary" alt="Add" style="filter: invert(30%) sepia(85%) saturate(2716%) hue-rotate(231deg) brightness(97%) contrast(97%);" />
                Add New Passage
            </h3>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700">{{ 3 - $passages->count() }} passage slot(s) remaining</p>
        </div>
        <form action="{{ route('admin.reading-passages.store', $testSet->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Passage Number</label>
                    <div class="relative">
                        <select name="passage_number" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm appearance-none cursor-pointer">
                            @for($i = 1; $i <= 3; $i++)
                                @if(!$passages->contains('passage_number', $i))
                                    <option value="{{ $i }}" {{ old('passage_number') == $i ? 'selected' : '' }}>Passage {{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                        <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required placeholder="e.g. The Future of Ocean Exploration">
                </div>
            </div>
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Content (HTML)</label>
                <textarea name="content" rows="10" class="w-full px-4 py-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-mono text-sm focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required placeholder="<p>Passage text goes here...</p>">{{ old('content') }}</textarea>
            </div>
            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                    <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Create" />
                    Create Passage
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="mt-8 p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 flex items-center gap-4 shadow-sm">
        <img src="/storage/asset/icons/check-circle.svg" class="w-8 h-8 text-emerald-500" alt="Complete" style="filter: invert(56%) sepia(54%) saturate(464%) hue-rotate(107deg) brightness(97%) contrast(92%);" />
        <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">All 3 passages have been configured. Edit existing passages using the tabs above.</p>
    </div>
    @endif
</div>
@endsection
