@extends('layouts.admin')

@section('title', 'Reading Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    {{-- Page Header --}}
    <x-admin.page-header
        title="Reading Module Manager"
        :description="'IELTS ' . $testSet->test->book_number . ' · ' . $testSet->test->exam_type . ' ' . $testSet->test->year . ' · Set ' . $testSet->set_number"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $testSet->id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Stats Strip --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Passages</p>
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $passages->count() }}<span class="text-lg text-slate-300">/3</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Question Groups</p>
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $passages->sum(fn($p) => $p->questionGroups->count()) }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Questions</p>
            <p class="text-3xl font-black text-primary">{{ $passages->sum(fn($p) => $p->questionGroups->sum(fn($g) => $g->questions->count())) }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
            @if($passages->sum(fn($p) => $p->questionGroups->sum(fn($g) => $g->questions->count())) >= 40)
                <p class="text-lg font-black text-emerald-500 flex items-center gap-2"><span class="material-symbols-outlined">check_circle</span> Ready</p>
            @else
                <p class="text-lg font-black text-amber-500 flex items-center gap-2"><span class="material-symbols-outlined">pending</span> Incomplete</p>
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
                            : 'border-slate-200 dark:border-slate-800 text-slate-500 hover:border-primary/30'">
                    <span class="size-8 rounded-lg flex items-center justify-center text-xs font-black"
                          :class="activePassage === {{ $passage->passage_number }} ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'">
                        {{ $passage->passage_number }}
                    </span>
                    <div class="text-left">
                        <p class="text-xs font-black uppercase tracking-widest">Passage {{ $passage->passage_number }}</p>
                        <p class="text-[10px] font-bold opacity-60 truncate max-w-[180px]">{{ $passage->title }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 dark:bg-slate-800"
                          :class="activePassage === {{ $passage->passage_number }} ? 'text-primary' : 'text-slate-400'">
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
            <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                {{-- Passage Header --}}
                <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ $passage->title }}</h3>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ $passage->questionGroups->count() }} group(s) ·
                            {{ $passage->questionGroups->sum(fn($g) => $g->questions->count()) }} question(s) ·
                            {{ str_word_count(strip_tags($passage->content)) }} words
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-admin.button :href="route('admin.reading-passages.edit', $passage->id)" variant="secondary" size="sm">
                            <span class="material-symbols-outlined text-sm mr-1">edit</span> Edit Passage
                        </x-admin.button>
                        <x-admin.button :href="route('admin.reading-question-groups.create', $passage->id)" size="sm" class="from-orange-500 to-red-500">
                            <span class="material-symbols-outlined text-sm mr-1">add</span> Add Group
                        </x-admin.button>
                    </div>
                </div>

                {{-- Question Groups --}}
                @forelse($passage->questionGroups as $group)
                <div class="border-b border-slate-100 dark:border-slate-800 last:border-b-0">
                    {{-- Group Header --}}
                    <div class="px-6 sm:px-8 py-4 bg-slate-50 dark:bg-slate-800/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border shrink-0
                                @if(str_contains($group->question_type, 'true_false')) bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800
                                @elseif(str_contains($group->question_type, 'yes_no')) bg-teal-50 text-teal-600 border-teal-200 dark:bg-teal-900/20 dark:text-teal-400 dark:border-teal-800
                                @elseif($group->question_type === 'multiple_choice') bg-violet-50 text-violet-600 border-violet-200 dark:bg-violet-900/20 dark:text-violet-400 dark:border-violet-800
                                @else bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800
                                @endif">
                                {{ str_replace('_', ' ', $group->question_type) }}
                            </span>
                            @if($group->group_instruction)
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 truncate">{{ Str::limit($group->group_instruction, 80) }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $group->questions->count() }}Q</span>
                            <x-admin.button :href="route('admin.reading-question-groups.edit', $group->id)" variant="ghost" size="sm">
                                <span class="material-symbols-outlined text-sm">settings</span>
                            </x-admin.button>
                            <x-admin.button :href="route('admin.questions.create', ['type' => 'reading_group', 'id' => $group->id])" variant="ghost" size="sm">
                                <span class="material-symbols-outlined text-sm">add</span>
                            </x-admin.button>
                        </div>
                    </div>

                    {{-- Questions List --}}
                    <div class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        @foreach($group->questions as $qi => $q)
                        <div class="px-6 sm:px-8 py-3 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors group/item">
                            <div class="size-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                <span class="text-[11px] font-black text-slate-500">{{ $qi + 1 }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 truncate">{{ $q->question_text }}</p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-[10px] font-black text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/50">
                                    {{ $q->correct_answer }}
                                </span>
                                <a href="{{ route('admin.questions.edit', $q->id) }}" class="opacity-0 group-hover/item:opacity-100 text-slate-400 hover:text-primary transition-all">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-3">quiz</span>
                    <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No question groups yet</p>
                    <x-admin.button :href="route('admin.reading-question-groups.create', $passage->id)" size="sm" class="mt-4 from-orange-500 to-red-500">
                        <span class="material-symbols-outlined text-sm mr-1">add</span> Create First Group
                    </x-admin.button>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Add New Passage Form --}}
    @if($passages->count() < 3)
    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="material-symbols-outlined text-orange-500">add_circle</span>
                Add New Passage
            </h3>
            <p class="text-sm text-slate-500 mt-1">{{ 3 - $passages->count() }} passage slot(s) remaining</p>
        </div>
        <form action="{{ route('admin.reading-passages.store', $testSet->id) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 text-sm font-bold">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Passage Number</label>
                    <select name="passage_number" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                        @for($i = 1; $i <= 3; $i++)
                            @if(!$passages->contains('passage_number', $i))
                                <option value="{{ $i }}" {{ old('passage_number') == $i ? 'selected' : '' }}>Passage {{ $i }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required placeholder="e.g. The Future of Ocean Exploration">
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Content (HTML)</label>
                <textarea name="content" rows="10" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-mono text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required placeholder="<p>Passage text goes here...</p>">{{ old('content') }}</textarea>
            </div>
            <div class="flex justify-end">
                <x-admin.button type="submit" size="lg" class="from-orange-500 to-red-600">
                    <span class="material-symbols-outlined text-sm mr-2">add</span> Create Passage
                </x-admin.button>
            </div>
        </form>
    </div>
    @else
    <div class="p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800 flex items-center gap-4">
        <span class="material-symbols-outlined text-emerald-500 text-2xl">check_circle</span>
        <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">All 3 passages have been configured. Edit existing passages using the tabs above.</p>
    </div>
    @endif
</div>
@endsection
