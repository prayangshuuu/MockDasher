@extends('layouts.admin')

@section('title', 'Edit Listening Section')
@section('header', 'Edit Listening Section')
@section('subheader', 'For test: ' . $listening_section->testSet->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $listening_section->testSet->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-12">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Edit Listening Section" 
        description="Modify the configuration for part in: {{ $listening_section->testSet->test->title }}"
    >
        <x-slot:actions>
            <x-admin.button :href="route('admin.test_sets.show', $listening_section->test_set_id)" variant="ghost" size="sm">
                <span class="material-symbols-outlined text-lg mr-2">arrow_back</span>
                Back to Set
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <form action="{{ route('admin.listening-sections.update', $listening_section->id) }}" method="POST" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Section Number</label>
                <select name="section_number" class="w-full md:w-1/2 px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                    <option value="1" {{ $listening_section->section_number == 1 ? 'selected' : '' }}>Part 1 (Conversation, everyday social context)</option>
                    <option value="2" {{ $listening_section->section_number == 2 ? 'selected' : '' }}>Part 2 (Monologue, everyday social context)</option>
                    <option value="3" {{ $listening_section->section_number == 3 ? 'selected' : '' }}>Part 3 (Conversation, educational/training context)</option>
                    <option value="4" {{ $listening_section->section_number == 4 ? 'selected' : '' }}>Part 4 (Monologue, academic subject)</option>
                </select>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Section Instructions</label>
                <p class="text-[10px] font-bold text-slate-400 italic mb-2">Displayed to test-takers above the questions</p>
                <textarea name="instruction_text" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" placeholder="e.g. Questions 1–10. Complete the form below...">{{ $listening_section->instruction_text }}</textarea>
            </div>

            <div class="p-8 rounded-[2.5rem] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center gap-8">
                <div class="w-full text-center">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Update Audio Recording</label>
                    <input type="file" name="audio_file" accept=".mp3,.wav" class="text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-purple-600 file:text-white hover:file:opacity-90 transition-all">
                </div>
                
                @if($listening_section->audio_path)
                    <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-slate-700 w-full max-w-lg">
                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 text-center">Current Activity Recording</span>
                        <audio controls class="h-10 w-full brightness-90">
                            <source src="{{ Storage::url($listening_section->audio_path) }}" type="audio/mpeg">
                        </audio>
                    </div>
                @endif
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Transcript / Passage Text <span class="text-slate-300 font-normal">(Optional)</span></label>
                <textarea name="passage_text" rows="8" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ $listening_section->passage_text }}</textarea>
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <button type="button" onclick="if(confirm('Are you sure? All associated questions will also be deleted.')) { document.getElementById('delete-section').submit(); }" class="text-red-500 hover:text-red-700 font-black text-xs uppercase tracking-widest transition-all">
                    Delete Section
                </button>
                <x-admin.button type="submit" size="lg" class="from-purple-600 to-indigo-600">
                    Update Section
                </x-admin.button>
            </div>
        </form>

        <form id="delete-section" action="{{ route('admin.listening-sections.destroy', $listening_section->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- ── Questions Manager ── --}}
        <div class="p-8 sm:p-10 border-t border-slate-100 dark:border-slate-800 space-y-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Questions for Part {{ $listening_section->section_number }}</h3>
                    <p class="text-sm font-bold text-slate-400 italic mt-1">{{ $listening_section->questions->count() }} question(s) configured.</p>
                </div>
                <x-admin.button :href="route('admin.questions.create', ['type' => 'listening', 'id' => $listening_section->id])" size="sm">
                    <span class="material-symbols-outlined text-lg mr-2">add</span>
                    Add Question
                </x-admin.button>
            </div>

            @if($listening_section->questions->isEmpty())
                <div class="p-12 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 text-center">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl text-slate-400">quiz</span>
                    </div>
                    <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No questions added yet</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach($listening_section->questions as $index => $q)
                        <div class="group flex items-center justify-between p-6 rounded-3xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary hover:shadow-soft transition-all duration-300">
                            <div class="flex items-center gap-6 overflow-hidden">
                                <div class="w-12 h-12 shrink-0 rounded-2xl bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center text-primary font-black text-lg">
                                    {{ $index + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-900 dark:text-white truncate pr-4 leading-tight">{{ $q->question_text }}</h4>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-900/30 text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest border border-purple-100 dark:border-purple-800/50">
                                            {{ str_replace('_', ' ', $q->question_type) }}
                                        </span>
                                        @if($q->correct_answer)
                                            <span class="text-[10px] font-bold text-slate-400 italic">
                                                Ans: <span class="text-slate-600 dark:text-slate-300">{{ $q->correct_answer }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <x-admin.button :href="route('admin.questions.edit', $q->id)" variant="secondary" size="sm">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </x-admin.button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
