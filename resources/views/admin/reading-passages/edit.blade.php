@extends('layouts.admin')

@section('title', 'Edit Reading Passage')
@section('header', 'Edit Reading Passage')
@section('subheader', 'For test: ' . $reading_passage->testSet->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $reading_passage->testSet->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-12">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Edit Reading Passage" 
        description="Modify the configuration for module in: {{ $reading_passage->testSet->test->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $reading_passage->test_set_id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.reading-passages.update', $reading_passage->id) }}" method="POST" class="p-8 sm:p-10 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Passage Number</label>
                    <select name="passage_number" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                        <option value="1" {{ $reading_passage->passage_number == 1 ? 'selected' : '' }}>Passage 1</option>
                        <option value="2" {{ $reading_passage->passage_number == 2 ? 'selected' : '' }}>Passage 2</option>
                        <option value="3" {{ $reading_passage->passage_number == 3 ? 'selected' : '' }}>Passage 3</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Passage Title / Heading</label>
                    <input type="text" name="title" value="{{ $reading_passage->title }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Passage Content</label>
                <p class="text-[10px] font-bold text-slate-400 italic mb-2">Use HTML tags for formatting.</p>
                <textarea name="content" rows="15" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-mono text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>{{ $reading_passage->content }}</textarea>
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <button type="button" onclick="if(confirm('Delete this passage? All question groups and questions will be deleted.')) document.getElementById('delete-passage').submit();" class="text-red-500 hover:text-red-700 font-black text-xs uppercase tracking-widest transition-all">
                    Delete Passage
                </button>
                <x-admin.button type="submit" size="lg" class="from-orange-500 to-red-600">
                    Update Passage
                </x-admin.button>
            </div>
        </form>

        <form id="delete-passage" action="{{ route('admin.reading-passages.destroy', $reading_passage->id) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>

        {{-- ── Question Groups ── --}}
        <div class="p-8 sm:p-10 border-t border-slate-100 dark:border-slate-800 space-y-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Question Groups</h3>
                    <p class="text-sm font-bold text-slate-400 italic mt-1">
                        {{ $reading_passage->questionGroups->count() }} group(s) · 
                        {{ $reading_passage->questionGroups->sum(fn($g) => $g->questions->count()) }} total questions
                    </p>
                </div>
                <x-admin.button :href="route('admin.reading-question-groups.create', $reading_passage->id)" size="sm" class="from-orange-500 to-orange-600">
                    <span class="material-symbols-outlined text-lg mr-2">add_circle</span>
                    Add Group
                </x-admin.button>
            </div>

            @if($reading_passage->questionGroups->isEmpty())
                <div class="p-12 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 text-center">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl text-slate-400">category</span>
                    </div>
                    <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No groups added yet</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach($reading_passage->questionGroups as $group)
                        <div class="group flex items-center justify-between p-6 rounded-3xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-orange-500 hover:shadow-soft transition-all duration-300">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-900/30 text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest border border-orange-100 dark:border-orange-800/50">
                                        {{ str_replace('_', ' ', $group->question_type) }}
                                    </span>
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Order: {{ $group->sort_order }}</span>
                                </div>
                                @if($group->group_instruction)
                                    <p class="text-sm font-bold text-slate-600 dark:text-slate-400 italic truncate pr-8">"{{ $group->group_instruction }}"</p>
                                @endif
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">{{ $group->questions->count() }} question(s)</p>
                            </div>
                            <x-admin.button :href="route('admin.reading-question-groups.edit', $group->id)" variant="secondary" size="sm">
                                <span class="material-symbols-outlined text-lg">settings</span>
                            </x-admin.button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
