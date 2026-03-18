@extends('layouts.student')

@section('title', 'Test Result — Attempt #' . $attempt->id)

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    
    <!-- Header / Breadcrumbs -->
    <nav class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-slate-400">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
        <a href="{{ route('user.history.index') }}" class="hover:text-primary transition-colors">History</a>
        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
        <span class="text-slate-900 dark:text-white">Attempt #{{ $attempt->id }}</span>
    </nav>

    <!-- Main Score Hero -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-[48px] shadow-2xl border border-slate-200/50 dark:border-slate-800 p-12 md:p-16 flex flex-col md:flex-row items-center gap-12">
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 size-64 bg-primary/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 flex-col items-center flex">
            <div class="size-48 rounded-[60px] bg-gradient-to-br from-primary to-violet-600 p-1 shadow-2xl shadow-primary/20 hover:scale-105 transition-transform">
                <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[58px] flex flex-col items-center justify-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Band Score</span>
                    <div class="text-7xl font-black text-slate-900 dark:text-white leading-none tracking-tighter tabular-nums">
                        {{ $attempt->overall_band !== null ? number_format($attempt->overall_band, 1) : 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="mt-6 inline-flex items-center gap-2 px-4 py-1.5 {{ $attempt->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' : 'bg-amber-50 dark:bg-amber-900/20 text-amber-600' }} rounded-full text-[10px] font-black uppercase tracking-widest">
                <span class="material-symbols-outlined text-sm">{{ $attempt->status === 'completed' ? 'verified' : 'pending' }}</span>
                {{ ucfirst($attempt->status) }}
            </div>
        </div>

        <div class="flex-1 space-y-6 text-center md:text-left">
            <div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                    {{ $attempt->testSet->test->title ?? 'Full Mock Exam' }}
                </h1>
                <p class="text-lg text-slate-500 font-medium mt-2">
                    IELTS {{ $attempt->testSet->test->exam_type ?? 'Academic' }} Simulation • Book {{ $attempt->testSet->test->book_number ?? '?' }}
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Completion Date</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $attempt->created_at->format('M j, Y') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Time Spent</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $attempt->time_spent ?? 'N/A' }}</p>
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Candidate ID</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">MD-{{ str_pad($attempt->user_id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Reading Module -->
        <div class="group bg-white dark:bg-slate-900 rounded-[40px] p-10 border border-slate-200/60 dark:border-slate-800 shadow-soft hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl font-light">auto_stories</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">Reading</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Academic Passage Analysis</p>
                    </div>
                </div>
                <div class="text-4xl font-black text-primary tabular-nums">
                    {{ $attempt->reading_band !== null ? number_format($attempt->reading_band, 1) : 'N/A' }}
                </div>
            </div>
            <div class="space-y-4">
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all duration-1000" style="width: {{ ($attempt->reading_band ?? 0) * 11 }}%"></div>
                </div>
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Correct: {{ $attempt->reading_score !== null ? $attempt->reading_score . '/40' : 'N/A' }}</span>
                    <span>Band Level: {{ $attempt->reading_band !== null ? floor($attempt->reading_band) : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Listening Module -->
        <div class="group bg-white dark:bg-slate-900 rounded-[40px] p-10 border border-slate-200/60 dark:border-slate-800 shadow-soft hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl font-light">headphones</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">Listening</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Audio Comprehension</p>
                    </div>
                </div>
                <div class="text-4xl font-black text-emerald-600 tabular-nums">
                    {{ $attempt->listening_band !== null ? number_format($attempt->listening_band, 1) : 'N/A' }}
                </div>
            </div>
            <div class="space-y-4">
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" style="width: {{ ($attempt->listening_band ?? 0) * 11 }}%"></div>
                </div>
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Correct: {{ $attempt->listening_score !== null ? $attempt->listening_score . '/40' : 'N/A' }}</span>
                    <span>Accuracy: {{ $attempt->listening_score !== null ? number_format(($attempt->listening_score / 40) * 100, 1) . '%' : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Writing Module -->
        <div class="group bg-white dark:bg-slate-900 rounded-[40px] p-10 border border-slate-200/60 dark:border-slate-800 shadow-soft hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl font-light">edit_square</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">Writing</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Task 1 & 2</p>
                    </div>
                </div>
                <div class="text-4xl font-black text-amber-600 tabular-nums">
                    <span class="text-slate-300 text-2xl">Pending</span>
                </div>
            </div>
            <div class="space-y-4">
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-slate-200 rounded-full w-0"></div>
                </div>
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Automated scoring not yet available</span>
                    <span>Manual review required</span>
                </div>
            </div>
        </div>

        <!-- Speaking Module -->
        <div class="group bg-white dark:bg-slate-900 rounded-[40px] p-10 border border-slate-200/60 dark:border-slate-800 shadow-soft hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl font-light">record_voice_over</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">Speaking</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Oral Interview Assessment</p>
                    </div>
                </div>
                <div class="text-4xl font-black text-rose-600 tabular-nums">
                    <span class="text-slate-300 text-2xl">Pending</span>
                </div>
            </div>
            <div class="space-y-4">
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-slate-200 rounded-full w-0"></div>
                </div>
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Automated scoring not yet available</span>
                    <span>Manual review required</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Call to Action -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-6 p-10 bg-slate-900 dark:bg-white rounded-[40px]">
        <div class="text-white dark:text-slate-900 text-center sm:text-left">
            <h3 class="text-xl font-black tracking-tight">Ready to improve?</h3>
            <p class="text-slate-400 dark:text-slate-500 text-sm font-medium">Analyze your weak points and try another specialized module.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('user.history.index') }}" class="px-8 py-4 bg-white dark:bg-slate-950 text-slate-900 dark:text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all">
                View All Results
            </a>
            <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-primary text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all shadow-xl shadow-primary/20">
                Back to Dashboard
            </a>
        </div>
    </div>

</div>
@endsection
