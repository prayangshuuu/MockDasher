@extends('layouts.admin')

@section('title', 'Add Listening Section')
@section('header', 'Add Listening Section')
@section('subheader', 'For test: ' . $testSet->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $testSet->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Add Listening Section" 
        description="Configure a new listening part for: {{ $testSet->test->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $testSet->id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.listening-sections.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8">
            @csrf
            
            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Section Number</label>
                <select name="section_number" class="w-full md:w-1/2 px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                    <option value="1">Part 1 (Conversation, everyday social context)</option>
                    <option value="2">Part 2 (Monologue, everyday social context)</option>
                    <option value="3">Part 3 (Conversation, educational/training context)</option>
                    <option value="4">Part 4 (Monologue, academic subject)</option>
                </select>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Section Instructions</label>
                <p class="text-[10px] font-bold text-slate-400 italic mb-2">Displayed to test-takers above the questions</p>
                <textarea name="instruction_text" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="e.g. Questions 1–10. Complete the form below..."></textarea>
            </div>

            <div class="p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 text-center">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Audio Recording <span class="text-red-500 font-black">*</span></label>
                <input type="file" name="audio_file" accept=".mp3,.wav" class="text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-purple-600 file:text-white hover:file:opacity-90 transition-all" required>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-4">MP3 or WAV — MAX 10MB</p>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Transcript / Passage Text <span class="text-slate-300 font-normal">(Optional)</span></label>
                <p class="text-[10px] font-bold text-slate-400 italic mb-2">For admin reference. Not shown to test-takers during the exam.</p>
                <textarea name="passage_text" rows="8" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm"></textarea>
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <x-admin.button type="submit" size="lg" class="from-purple-600 to-indigo-600">
                    Save Section
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection
