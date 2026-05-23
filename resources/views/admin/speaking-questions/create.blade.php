@extends('layouts.admin')

@section('title', 'Speaking Module Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Speaking Module Manager</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">IELTS {{ $testSet->test->book_number }} · {{ $testSet->test->exam_type }} {{ $testSet->test->year }} · Set {{ $testSet->set_number }}</p>
        </div>
        <a href="{{ route('admin.test_sets.show', $testSet->id) }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors shadow-sm">
            <img src="/storage/asset/icons/arrowback.svg" class="w-4 h-4 opacity-70" alt="Back" />
            Back to Set
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $partLabels = [1 => 'Introduction & Interview', 2 => 'Long Turn', 3 => 'Discussion'];
            $partIcons = [1 => '/storage/asset/icons/chat.svg', 2 => '/storage/asset/icons/microphone.svg', 3 => '/storage/asset/icons/chat.svg'];
            $partColors = [1 => 'indigo', 2 => 'violet', 3 => 'emerald'];
        @endphp
        @foreach([1,2,3] as $p)
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Part {{ $p }}</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ ($parts[$p] ?? collect())->count() }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ $partLabels[$p] }}</p>
        </div>
        @endforeach
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Total</p>
            <p class="text-3xl font-bold text-primary">{{ $questions->count() }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Questions</p>
        </div>
    </div>

    {{-- Existing Questions by Part --}}
    @foreach([1,2,3] as $partNum)
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="size-12 rounded-xl flex items-center justify-center shrink-0 bg-{{ $partColors[$partNum] }}-50 dark:bg-{{ $partColors[$partNum] }}-900/30 border border-{{ $partColors[$partNum] }}-100 dark:border-{{ $partColors[$partNum] }}-800">
                    <img src="{{ $partIcons[$partNum] }}" class="w-6 h-6 opacity-60" alt="Part {{ $partNum }}" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Part {{ $partNum }}: {{ $partLabels[$partNum] }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                        @if($partNum == 1) 0:45 per question · Walking in Daily Life
                        @elseif($partNum == 2) 2:15 total · Cue Card Topic
                        @else 1:30 per question · Theatre Discussion
                        @endif
                    </p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">{{ ($parts[$partNum] ?? collect())->count() }} Q</span>
        </div>

        @if(($parts[$partNum] ?? collect())->isNotEmpty())
        <div class="divide-y divide-slate-100 dark:divide-slate-800/50">
            @foreach($parts[$partNum] as $qi => $question)
            <div class="p-6 flex items-start gap-6 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors group/item">
                <div class="size-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ $qi + 1 }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $question->question_text }}</p>
                    <div class="flex flex-wrap items-center gap-4 mt-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <span class="material-symbols-outlined text-xs">timer</span>
                            {{ $question->time_limit }}s
                        </span>
                        @if($question->preparation_instructions)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold text-primary uppercase tracking-widest bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800/50">
                            <span class="material-symbols-outlined text-xs">note</span>
                            Has prep instructions
                        </span>
                        @endif
                        @if($question->audio_path)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50">
                            <span class="material-symbols-outlined text-xs">headphones</span>
                            Audio uploaded
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/50">
                            <span class="material-symbols-outlined text-xs">text_to_speech</span>
                            Will use TTS
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0 opacity-0 group-hover/item:opacity-100 transition-opacity">
                    <a href="{{ route('admin.speaking-questions.edit', $question->id) }}" class="flex size-9 items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700" title="Edit Question">
                        <img src="/storage/asset/icons/edit.svg" class="w-4 h-4 opacity-70" alt="Edit" />
                    </a>
                    <form action="{{ route('admin.speaking-questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex size-9 items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-error transition-colors border border-transparent hover:border-red-100 dark:hover:border-red-800" title="Delete">
                            <img src="/storage/asset/icons/delete.svg" class="w-4 h-4 opacity-70" alt="Delete" />
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-10 text-center">
            <div class="max-w-xs mx-auto">
                <img src="{{ $partIcons[$partNum] }}" class="w-12 h-12 mx-auto opacity-20 mb-4" alt="No Questions" />
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No questions added for Part {{ $partNum }}</p>
            </div>
        </div>
        @endif
    </div>
    @endforeach

    {{-- Add New Question Form --}}
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden mt-8">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
            <img src="/storage/asset/icons/create.svg" class="w-6 h-6 text-primary" alt="Add" style="filter: invert(30%) sepia(85%) saturate(2716%) hue-rotate(231deg) brightness(97%) contrast(97%);" />
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add Speaking Question</h3>
        </div>
        <form action="{{ route('admin.speaking-questions.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Part</label>
                    <div class="relative">
                        <select name="part" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm appearance-none cursor-pointer">
                            <option value="1">Part 1 — Introduction</option>
                            <option value="2">Part 2 — Long Turn</option>
                            <option value="3">Part 3 — Discussion</option>
                        </select>
                        <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Time Limit (seconds)</label>
                    <input type="number" name="time_limit" value="{{ old('time_limit', 45) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Audio File (optional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <img src="/storage/asset/icons/microphone.svg" class="w-5 h-5 opacity-50" alt="Audio" />
                        </div>
                        <input type="file" name="audio_file" accept="audio/*" class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-bold file:text-xs file:cursor-pointer hover:file:bg-primary/20">
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Question Text</label>
                <textarea name="question_text" rows="3" class="w-full px-4 py-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>{{ old('question_text') }}</textarea>
            </div>
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Preparation Instructions (Part 2 only)</label>
                <textarea name="preparation_instructions" rows="2" class="w-full px-4 py-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">{{ old('preparation_instructions') }}</textarea>
            </div>
            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                    <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Create" />
                    Add Question
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
