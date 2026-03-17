@extends('layouts.admin')

@section('title', 'Attempt Details - ' . optional($result->user)->name)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.results.index') }}" class="hover:text-primary transition-colors">Results</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <span class="text-slate-900 dark:text-white font-medium">Attempt Details</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Attempt Analysis</h2>
            <p class="text-slate-500 dark:text-slate-400 text-base">In-depth breakdown of the candidate's performance across all modules.</p>
        </div>
        <div class="flex items-center gap-3">
             <span class="px-4 py-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500">ID: #{{ $result->id }}</span>
             <button class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                Export PDF
             </button>
        </div>
    </div>

    <!-- Top Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6">
            <div class="size-16 rounded-[1.5rem] bg-indigo-50 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined text-3xl">person</span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Candidate</p>
                <p class="text-xl font-black text-slate-900 dark:text-white truncate">{{ optional($result->user)->name ?? 'Unknown' }}</p>
                <p class="text-xs font-medium text-slate-400 truncate">{{ optional($result->user)->email }}</p>
            </div>
        </div>

        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6">
            <div class="size-16 rounded-[1.5rem] bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                <span class="material-symbols-outlined text-3xl">description</span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Test Information</p>
                <p class="text-xl font-black text-slate-900 dark:text-white truncate">{{ optional($result->test)->title ?? 'IELTS Mock' }}</p>
                <p class="text-xs font-medium text-slate-400">{{ $result->created_at->format('M d, Y • h:i A') }}</p>
            </div>
        </div>

        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6">
            <div class="size-16 rounded-[1.5rem] bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
                <span class="material-symbols-outlined text-3xl">verified</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Overall Outcome</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $result->overall_band ?? 'N/A' }}</p>
                <p class="text-[10px] font-black uppercase text-purple-500 tracking-widest">Target Band: 7.5</p>
            </div>
        </div>
    </div>

    <!-- Module Breakdown Grid -->
    <div class="glass-card rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="p-10 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Module Performance Breakdown</h3>
        </div>
        
        <div class="p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
                @foreach([
                    ['label' => 'Reading', 'val' => $result->reading_band, 'icon' => 'menu_book', 'color' => 'orange'],
                    ['label' => 'Listening', 'val' => $result->listening_band, 'icon' => 'headphones', 'color' => 'purple'],
                    ['label' => 'Writing', 'val' => $result->writing_band, 'icon' => 'edit_note', 'color' => 'indigo'],
                    ['label' => 'Speaking', 'val' => $result->speaking_band, 'icon' => 'record_voice_over', 'color' => 'emerald']
                ] as $mod)
                    <div class="p-6 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex flex-col items-center text-center">
                        <div class="size-12 rounded-2xl bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-{{ $mod['color'] }}-500">{{ $mod['icon'] }}</span>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $mod['label'] }}</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $mod['val'] ?? 'N/A' }}</p>
                    </div>
                @endforeach
            </div>

            @if($result->writingAnswers && $result->writingAnswers->count() > 0)
                <div class="pt-10 border-t border-slate-100 dark:border-slate-800 space-y-8">
                    <h4 class="text-lg font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">history_edu</span>
                        Detailed Writing Responses
                    </h4>
                    
                    <div class="grid grid-cols-1 gap-8">
                        @foreach($result->writingAnswers as $index => $answer)
                            <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                                <div class="px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <div class="size-8 rounded-lg bg-primary text-white flex items-center justify-center font-black text-xs">
                                            0{{ $index + 1 }}
                                        </div>
                                        <span class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">
                                            {{ optional($answer->writingTask)->title ?? 'Writing Task' }}
                                        </span>
                                    </div>
                                    <span class="px-3 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-[10px] font-black uppercase text-slate-400 tracking-widest">
                                        {{ str_word_count($answer->answer_text ?? '') }} Words
                                    </span>
                                </div>
                                <div class="p-8 text-sm text-slate-600 dark:text-slate-400 leading-relaxed max-h-96 overflow-y-auto whitespace-pre-wrap font-medium">
                                    {{ $answer->answer_text ?? 'No response submitted.' }}
                                </div>
                                @if($index === 0)
                                    <div class="px-8 py-4 bg-primary/5 border-t border-primary/10 flex items-center justify-between">
                                        <span class="text-[10px] font-black text-primary uppercase tracking-widest">AI Feedback: Analysis Pending</span>
                                        <button class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">Auditor Notes</button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
