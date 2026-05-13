@extends('layouts.admin')

@section('title', 'Speaking Module Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    {{-- Page Header --}}
    <x-admin.page-header
        title="Speaking Module Manager"
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $partLabels = [1 => 'Introduction & Interview', 2 => 'Long Turn', 3 => 'Discussion'];
            $partIcons = [1 => 'chat', 2 => 'record_voice_over', 3 => 'forum'];
            $partColors = [1 => 'indigo', 2 => 'violet', 3 => 'emerald'];
        @endphp
        @foreach([1,2,3] as $p)
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Part {{ $p }}</p>
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ ($parts[$p] ?? collect())->count() }}</p>
            <p class="text-xs text-slate-400 font-bold mt-1">{{ $partLabels[$p] }}</p>
        </div>
        @endforeach
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total</p>
            <p class="text-3xl font-black text-primary">{{ $questions->count() }}</p>
            <p class="text-xs text-slate-400 font-bold mt-1">Questions</p>
        </div>
    </div>

    {{-- Existing Questions by Part --}}
    @foreach([1,2,3] as $partNum)
    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="size-12 rounded-2xl flex items-center justify-center shrink-0 bg-{{ $partColors[$partNum] }}-50 dark:bg-{{ $partColors[$partNum] }}-900/20 text-{{ $partColors[$partNum] }}-500">
                    <span class="material-symbols-outlined text-2xl">{{ $partIcons[$partNum] }}</span>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Part {{ $partNum }}: {{ $partLabels[$partNum] }}</h3>
                    <p class="text-sm text-slate-500 font-bold">
                        @if($partNum == 1) 0:45 per question · Walking in Daily Life
                        @elseif($partNum == 2) 2:15 total · Cue Card Topic
                        @else 1:30 per question · Theatre Discussion
                        @endif
                    </p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-black text-slate-500">{{ ($parts[$partNum] ?? collect())->count() }} Q</span>
        </div>

        @if(($parts[$partNum] ?? collect())->isNotEmpty())
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($parts[$partNum] as $qi => $question)
            <div class="p-6 sm:p-8 flex items-start gap-6 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                <div class="size-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                    <span class="text-sm font-black text-slate-500">{{ $qi + 1 }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white leading-relaxed whitespace-pre-line">{{ $question->question_text }}</p>
                    <div class="flex items-center gap-4 mt-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">timer</span>
                            {{ $question->time_limit }}s
                        </span>
                        @if($question->preparation_instructions)
                        <span class="text-[10px] font-black text-primary uppercase tracking-widest flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">note</span>
                            Has prep instructions
                        </span>
                        @endif
                        @if($question->audio_path)
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">headphones</span>
                            Audio uploaded
                        </span>
                        @else
                        <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">text_to_speech</span>
                            Will use TTS
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <x-admin.button :href="route('admin.speaking-questions.edit', $question->id)" variant="secondary" size="sm">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </x-admin.button>
                    <form action="{{ route('admin.speaking-questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 rounded-xl border border-rose-200 text-rose-500 text-xs font-black hover:bg-rose-50 transition-all">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 text-center text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2 block opacity-30">mic_off</span>
            <p class="text-sm font-bold">No questions added for Part {{ $partNum }}</p>
        </div>
        @endif
    </div>
    @endforeach

    {{-- Add New Question Form --}}
    <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-500">add_circle</span>
                Add Speaking Question
            </h3>
        </div>
        <form action="{{ route('admin.speaking-questions.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 text-sm font-bold">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Part</label>
                    <select name="part" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                        <option value="1">Part 1 — Introduction</option>
                        <option value="2">Part 2 — Long Turn</option>
                        <option value="3">Part 3 — Discussion</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Time Limit (seconds)</label>
                    <input type="number" name="time_limit" value="{{ old('time_limit', 45) }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Audio File (optional)</label>
                    <input type="file" name="audio_file" accept="audio/*" class="w-full px-5 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary/10 file:text-primary file:font-black file:text-xs">
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Question Text</label>
                <textarea name="question_text" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>{{ old('question_text') }}</textarea>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Preparation Instructions (Part 2 only)</label>
                <textarea name="preparation_instructions" rows="2" class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">{{ old('preparation_instructions') }}</textarea>
            </div>
            <div class="flex justify-end">
                <x-admin.button type="submit" size="lg">
                    <span class="material-symbols-outlined text-sm mr-2">add</span> Add Question
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection
