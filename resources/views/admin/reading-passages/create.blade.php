@extends('layouts.admin')

@section('title', 'Add Reading Passage')
@section('header', 'Add Reading Passage')
@section('subheader', 'For test: ' . $testSet->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $testSet->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-10">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Add Reading Passage" 
        description="Configure a new reading module for: {{ $testSet->test->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $testSet->id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.reading-passages.store', $testSet->id) }}" method="POST" class="p-8 sm:p-10 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Passage Number</label>
                    <select name="passage_number" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                        <option value="1">Passage 1</option>
                        <option value="2">Passage 2</option>
                        <option value="3">Passage 3</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Passage Title / Heading</label>
                    <input type="text" name="title" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required placeholder="e.g. The Future of Ocean Exploration">
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Passage Content</label>
                <p class="text-[10px] font-bold text-slate-400 italic mb-2">Use HTML tags: &lt;p&gt;, &lt;h3&gt;, &lt;strong&gt;, &lt;ul&gt; for formatting.</p>
                <textarea name="content" rows="15" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-mono text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required placeholder="<p>Passage text goes here...</p>"></textarea>
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <x-admin.button type="submit" size="lg" class="from-orange-500 to-red-600">
                    Create Passage
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection
