@extends('layouts.admin')

@section('title', 'Writing Tasks Manager')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Writing Module Manager</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">IELTS {{ $testSet->test->book_number }} · {{ $testSet->test->exam_type }} {{ $testSet->test->year }} · Set {{ $testSet->set_number }}</p>
        </div>
        <a href="{{ route('admin.test_sets.show', $testSet->id) }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors shadow-sm">
            <img src="/storage/asset/icons/arrowback.svg" class="w-4 h-4 opacity-70" alt="Back" />
            Back to Set
        </a>
    </div>

    {{-- Stats Strip --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Tasks</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $tasks->count() }}<span class="text-lg text-slate-400 font-medium">/2</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Min Words (Total)</p>
            <p class="text-3xl font-bold text-primary">{{ $tasks->sum('minimum_word_count') }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-soft">
            <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Status</p>
            @if($tasks->count() >= 2)
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

    {{-- Existing Tasks --}}
    @if($tasks->isNotEmpty())
    <div class="space-y-6">
        @foreach($tasks as $task)
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-xl flex items-center justify-center shrink-0 border {{ $task->task_number === 1 ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-100 dark:border-indigo-800' : 'bg-violet-50 dark:bg-violet-900/30 border-violet-100 dark:border-violet-800' }}">
                        <img src="{{ $task->task_number === 1 ? '/storage/asset/icons/bar-chart.svg' : '/storage/asset/icons/edit.svg' }}" class="w-6 h-6 opacity-60" alt="Task Icon" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $task->task_title }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">{{ $task->task_description }} · Min {{ $task->minimum_word_count }} words</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.writing-tasks.edit', $task->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm">
                        <img src="/storage/asset/icons/edit.svg" class="w-4 h-4 opacity-70" alt="Edit" />
                        Edit
                    </a>
                    <form action="{{ route('admin.writing-tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex size-10 items-center justify-center rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 transition-colors border border-red-100 dark:border-red-800 shadow-sm" title="Delete">
                            <img src="/storage/asset/icons/delete.svg" class="w-5 h-5 opacity-70" alt="Delete" />
                        </button>
                    </form>
                </div>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest block mb-2">Prompt</span>
                    <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium whitespace-pre-line bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-inner">{{ $task->task_prompt }}</div>
                </div>
                @if($task->images->isNotEmpty())
                <div>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest block mb-2">Attached Image</span>
                    <div class="flex gap-4 flex-wrap">
                        @foreach($task->images as $img)
                            <img src="{{ Storage::url($img->image_path) }}" alt="Task {{ $task->task_number }} Image" class="h-40 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm object-cover">
                        @endforeach
                    </div>
                </div>
                @endif
                @if($task->instruction_text)
                <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 shadow-sm">
                    <img src="/storage/asset/icons/info.svg" class="w-5 h-5 text-amber-500 opacity-70" alt="Info" />
                    <span class="text-sm font-bold text-amber-800 dark:text-amber-300">{{ $task->instruction_text }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- AI Content Generator Panel --}}
    @if($tasks->count() < 2)
    <div class="mt-8 rounded-2xl border border-violet-200 dark:border-violet-800/50 overflow-hidden shadow-soft"
         style="background: linear-gradient(135deg, rgba(109,40,217,0.04) 0%, rgba(139,92,246,0.04) 100%);">
        {{-- Header --}}
        <button type="button"
                onclick="toggleAiPanel('writing-ai-panel')"
                class="w-full flex items-center justify-between p-5 text-left group hover:opacity-90 transition-opacity">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center border border-violet-200 dark:border-violet-800 shadow-sm">
                    <span class="material-symbols-outlined text-violet-600 dark:text-violet-400 text-xl">auto_awesome</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        AI Content Generator
                        <span class="px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400 text-[10px] font-black uppercase tracking-widest border border-violet-200 dark:border-violet-800">Gemini</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Auto-generate exam-standard content — then review and save</p>
                </div>
            </div>
            <span class="material-symbols-outlined text-slate-400 transition-transform duration-200" id="writing-ai-panel-chevron">expand_more</span>
        </button>

        {{-- Collapsible Body --}}
        <div id="writing-ai-panel" class="hidden border-t border-violet-200 dark:border-violet-800/50 p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Module Type --}}
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Module Type</label>
                    <div class="relative">
                        <select id="ai-writing-module"
                                class="w-full px-4 py-3 rounded-xl border border-violet-200 dark:border-violet-700 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all shadow-sm appearance-none cursor-pointer">
                            <option value="Writing Task 1">Writing Task 1 — Graph / Chart / Diagram</option>
                            <option value="Writing Task 2">Writing Task 2 — Academic Essay</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">expand_more</span>
                    </div>
                </div>
                {{-- Topic --}}
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Topic / Theme</label>
                    <input id="ai-writing-topic"
                           type="text"
                           placeholder="e.g. Climate Change, Technology, Education..."
                           class="w-full px-4 py-3 rounded-xl border border-violet-200 dark:border-violet-700 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all shadow-sm placeholder:text-slate-400">
                </div>
            </div>

            {{-- Generate Button --}}
            <div class="flex items-center gap-4">
                <button type="button"
                        id="ai-writing-generate-btn"
                        onclick="aiGenerateWriting()"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 active:scale-95 text-white text-sm font-bold shadow-sm transition-all duration-200">
                    <span class="material-symbols-outlined text-base" id="ai-writing-btn-icon">auto_awesome</span>
                    <span id="ai-writing-btn-label">Generate Content</span>
                </button>
                <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Fields below will be auto-filled. Review before saving.</p>
            </div>

            {{-- Status / Error --}}
            <div id="ai-writing-status" class="hidden"></div>

            {{-- Preview Card --}}
            <div id="ai-writing-preview" class="hidden p-4 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 space-y-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-violet-500 text-base">check_circle</span>
                    <p class="text-xs font-black text-violet-700 dark:text-violet-400 uppercase tracking-widest">Content Generated — Scroll down to review & save</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Instructions</p>
                    <p id="ai-writing-preview-instructions" class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Prompt / Questions</p>
                    <p id="ai-writing-preview-prompt" class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line"></p>
                </div>
                <div id="ai-writing-preview-precontext-wrap">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Data Context (for AI grading)</p>
                    <p id="ai-writing-preview-precontext" class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line"></p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Add New Task Form --}}
    @if($tasks->count() < 2)
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden mt-8">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <img src="/storage/asset/icons/create.svg" class="w-6 h-6 text-primary" alt="Add" style="filter: invert(30%) sepia(85%) saturate(2716%) hue-rotate(231deg) brightness(97%) contrast(97%);" />
                Add New Writing Task
            </h3>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700">{{ 2 - $tasks->count() }} task slot(s) remaining</p>
        </div>
        <form action="{{ route('admin.writing-tasks.store', $testSet->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Task Number</label>
                    <div class="relative">
                        <select name="task_number" id="form-task-number" onchange="togglePrecontextField(this.value)" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm appearance-none cursor-pointer">
                            @for($i = 1; $i <= 2; $i++)
                                @if(!$tasks->contains('task_number', $i))
                                    <option value="{{ $i }}" {{ old('task_number') == $i ? 'selected' : '' }}>Task {{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                        <img src="/storage/asset/icons/expand-more.svg" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-50 pointer-events-none" alt="v" />
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Title</label>
                    <input type="text" name="task_title" id="form-task-title" value="{{ old('task_title') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required placeholder="e.g. Writing Task 1">
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Min Word Count</label>
                    <input type="number" name="minimum_word_count" id="form-minimum-word-count" value="{{ old('minimum_word_count', 150) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                </div>
            </div>
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Description</label>
                <input type="text" name="task_description" id="form-task-description" value="{{ old('task_description') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" placeholder="e.g. You should spend about 20 minutes on this task.">
            </div>
            <div class="space-y-3 bg-slate-50 dark:bg-slate-900/30 p-5 rounded-xl border border-slate-200 dark:border-slate-700" id="precontext-field">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">
                    Task 1 Data Context <span class="text-primary">(AI Evaluation Context)</span>
                </label>
                <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 mb-2">
                    For Task 1 only. The image you upload is shown to students as usual. This text is what gets sent to Gemini for AI scoring — describe the data the image shows in detail. Example: "A bar chart showing the percentage of households with internet access in five countries between 2000 and 2020."
                </p>
                <textarea name="precontext" id="form-precontext" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-surface-dark text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-sm" placeholder="Describe the visual data here...">{{ old('precontext') }}</textarea>
            </div>
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Prompt</label>
                <textarea name="task_prompt" id="form-task-prompt" rows="5" class="w-full px-4 py-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>{{ old('task_prompt') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Instruction Text</label>
                    <input type="text" name="instruction_text" id="form-instruction-text" value="{{ old('instruction_text') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" placeholder="e.g. Write at least 150 words.">
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Task Image (optional)</label>
                    <input type="file" name="task_image" accept="image/*" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-bold file:text-xs file:cursor-pointer hover:file:bg-primary/20">
                </div>
            </div>

            {{-- Image Alt Text for Gemini AI Evaluation (Task 1 only) --}}
            <div class="space-y-2 p-5 rounded-xl border-2 border-violet-200 dark:border-violet-700 bg-violet-50/50 dark:bg-violet-900/10" id="image-alt-text-field">
                <label class="block text-xs font-black text-violet-600 dark:text-violet-400 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">smart_toy</span>
                    Image Description for AI Evaluation (Alt Text)
                    <span class="ml-2 px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/40 text-violet-600 text-[9px] font-black uppercase tracking-widest border border-violet-200 dark:border-violet-700">GEMINI INPUT</span>
                </label>
                <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                    <strong class="text-violet-600">Task 1 only.</strong>
                    The image above is displayed to students as a visual. This text is what gets sent to the Gemini AI for evaluating the student's response — describe the graph, chart, or table data in complete detail.<br>
                    <em>Example: "A line graph comparing internet usage rates in five countries (UK, USA, China, India, Brazil) from 2000 to 2020. The UK started at 26% in 2000 and rose to 96% in 2020, the highest of all countries..."</em>
                </p>
                <textarea name="image_alt_text" id="form-image-alt-text" rows="5"
                          class="w-full px-4 py-4 rounded-xl border border-violet-200 dark:border-violet-700 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm"
                          placeholder="Describe the chart/graph/table data here so Gemini can evaluate the student's response...">{{ old('image_alt_text') }}</textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                    <img src="/storage/asset/icons/create.svg" class="w-4 h-4 invert brightness-0" alt="Create" />
                    Create Task
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="mt-8 p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 flex items-center gap-4 shadow-sm">
        <img src="/storage/asset/icons/check-circle.svg" class="w-8 h-8 text-emerald-500" alt="Complete" style="filter: invert(56%) sepia(54%) saturate(464%) hue-rotate(107deg) brightness(97%) contrast(92%);" />
        <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Both writing tasks have been configured. Edit existing tasks using the buttons above.</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function toggleAiPanel(panelId) {
        const panel = document.getElementById(panelId);
        const chevron = document.getElementById(panelId + '-chevron');
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            panel.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }

    function togglePrecontextField(taskNumber) {
        const precontextField = document.getElementById('precontext-field');
        if (precontextField) {
            if (taskNumber == '1') {
                precontextField.classList.remove('hidden');
            } else {
                precontextField.classList.add('hidden');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const taskNumSelect = document.getElementById('form-task-number');
        if (taskNumSelect) {
            togglePrecontextField(taskNumSelect.value);
        }
    });

    function aiGenerateWriting() {
        const moduleSelect = document.getElementById('ai-writing-module');
        const topicInput = document.getElementById('ai-writing-topic');
        const statusDiv = document.getElementById('ai-writing-status');
        const previewCard = document.getElementById('ai-writing-preview');
        const generateBtn = document.getElementById('ai-writing-generate-btn');
        const btnIcon = document.getElementById('ai-writing-btn-icon');
        const btnLabel = document.getElementById('ai-writing-btn-label');

        const moduleType = moduleSelect.value;
        const topic = topicInput.value.trim();

        if (!topic) {
            statusDiv.className = "p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-semibold flex items-center gap-2";
            statusDiv.innerHTML = '<span class="material-symbols-outlined text-lg">error</span> Please enter a topic or theme first.';
            statusDiv.classList.remove('hidden');
            return;
        }

        // Clear previous status & preview
        statusDiv.classList.add('hidden');
        statusDiv.innerHTML = '';
        previewCard.classList.add('hidden');

        // Loading State
        generateBtn.disabled = true;
        generateBtn.classList.add('opacity-75', 'cursor-not-allowed');
        btnLabel.textContent = 'Generating content...';
        if (btnIcon) {
            btnIcon.textContent = 'sync';
            btnIcon.classList.add('animate-spin');
        }

        fetch('{{ route("admin.ai.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                module_type: moduleType,
                topic: topic
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(payload => {
            if (payload.success && payload.data) {
                const data = payload.data;

                // Populate Form Fields
                const formTaskNum = document.getElementById('form-task-number');
                const formTaskTitle = document.getElementById('form-task-title');
                const formMinWords = document.getElementById('form-minimum-word-count');
                const formDesc = document.getElementById('form-task-description');
                const formPrecontext = document.getElementById('form-precontext');
                const formPrompt = document.getElementById('form-task-prompt');
                const formInstruction = document.getElementById('form-instruction-text');

                // Standard IELTS logic mapping
                const isTask1 = moduleType === 'Writing Task 1';
                
                // Set Task Number matching select option if possible
                if (formTaskNum) {
                    const targetVal = isTask1 ? '1' : '2';
                    const option = Array.from(formTaskNum.options).find(opt => opt.value === targetVal);
                    if (option) {
                        formTaskNum.value = targetVal;
                    }
                    togglePrecontextField(formTaskNum.value);
                }

                if (formTaskTitle) {
                    formTaskTitle.value = isTask1 ? 'Writing Task 1' : 'Writing Task 2';
                }
                if (formMinWords) {
                    formMinWords.value = isTask1 ? 150 : 250;
                }
                if (formInstruction) {
                    formInstruction.value = isTask1 ? 'Write at least 150 words.' : 'Write at least 250 words.';
                }
                if (formDesc) {
                    formDesc.value = data.db_precontext_instructions || '';
                }
                if (formPrompt) {
                    formPrompt.value = data.db_generated_questions_or_prompt || '';
                }
                if (formPrecontext) {
                    formPrecontext.value = isTask1 ? (data.db_image_description_data || '') : '';
                }

                // Populate Preview Card
                const previewInst = document.getElementById('ai-writing-preview-instructions');
                const previewPrompt = document.getElementById('ai-writing-preview-prompt');
                const previewPrecontextWrap = document.getElementById('ai-writing-preview-precontext-wrap');
                const previewPrecontext = document.getElementById('ai-writing-preview-precontext');

                if (previewInst) previewInst.textContent = data.db_precontext_instructions || '';
                if (previewPrompt) previewPrompt.textContent = data.db_generated_questions_or_prompt || '';
                
                if (isTask1 && data.db_image_description_data) {
                    if (previewPrecontext) previewPrecontext.textContent = data.db_image_description_data;
                    if (previewPrecontextWrap) previewPrecontextWrap.classList.remove('hidden');
                } else {
                    if (previewPrecontextWrap) previewPrecontextWrap.classList.add('hidden');
                }

                previewCard.classList.remove('hidden');
                
                // Success Status
                statusDiv.className = "p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 text-sm font-semibold flex items-center gap-2";
                statusDiv.innerHTML = '<span class="material-symbols-outlined text-lg">check_circle</span> IELTS content generated successfully! Form fields have been filled below.';
                statusDiv.classList.remove('hidden');
            } else {
                throw { error: 'Unknown error occurred.' };
            }
        })
        .catch(err => {
            const errorMsg = err.error || err.message || 'An error occurred during generation. Please try again.';
            statusDiv.className = "p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-semibold flex items-center gap-2";
            statusDiv.innerHTML = `<span class="material-symbols-outlined text-lg">error</span> ${errorMsg}`;
            statusDiv.classList.remove('hidden');
        })
        .finally(() => {
            // Reset Loading State
            generateBtn.disabled = false;
            generateBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            btnLabel.textContent = 'Generate Content';
            if (btnIcon) {
                btnIcon.textContent = 'auto_awesome';
                btnIcon.classList.remove('animate-spin');
            }
        });
    }
</script>
@endpush
