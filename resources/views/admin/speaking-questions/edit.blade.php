@extends('layouts.admin')

@section('title', 'Edit Speaking Question')
@section('header', 'Edit Speaking Question')
@section('subheader', 'For test: ' . $speaking_question->testSet->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $speaking_question->testSet->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Edit Speaking Question" 
        description="Modify the configuration for part in: {{ $speaking_question->testSet->test->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $speaking_question->test_set_id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.speaking-questions.update', $speaking_question->id) }}" method="POST" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Speaking Part</label>
                    <select name="part" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                        <option value="1" {{ $speaking_question->part == 1 ? 'selected' : '' }}>Part 1 (Introduction/Interview)</option>
                        <option value="2" {{ $speaking_question->part == 2 ? 'selected' : '' }}>Part 2 (Cue Card)</option>
                        <option value="3" {{ $speaking_question->part == 3 ? 'selected' : '' }}>Part 3 (Discussion)</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Time Limit (Seconds)</label>
                    <input type="number" name="time_limit" value="{{ $speaking_question->time_limit }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Question Text / Cue Card</label>
                <textarea name="question_text" rows="4" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ $speaking_question->question_text }}</textarea>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Preparation Instructions</label>
                <textarea name="preparation_instructions" rows="2" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ $speaking_question->preparation_instructions }}</textarea>
            </div>

            <div class="p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Update Examiner Audio</label>
                    <p class="text-[10px] font-bold text-slate-400 italic mb-4">Optional. Will replace existing audio.</p>
                    <input type="file" name="audio_file" accept=".mp3,.wav" class="text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-emerald-600 file:text-white hover:file:opacity-90 transition-all">
                </div>
                
                @if($speaking_question->audio_path)
                    <div class="flex flex-col items-center gap-3">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Current Audio</span>
                        <audio controls class="h-10 w-64 brightness-90">
                            <source src="{{ Storage::url($speaking_question->audio_path) }}" type="audio/mpeg">
                        </audio>
                    </div>
                @endif
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this speaking question?')) { document.getElementById('delete-question').submit(); }" class="text-red-500 hover:text-red-700 font-black text-xs uppercase tracking-widest transition-all">
                    Delete Question
                </button>
                <x-admin.button type="submit" size="lg" class="from-emerald-600 to-teal-600">
                    Update Question
                </x-admin.button>
            </div>
        </form>

        <form id="delete-question" action="{{ route('admin.speaking-questions.destroy', $speaking_question->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
