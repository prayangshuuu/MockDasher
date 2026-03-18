@extends('layouts.admin')

@section('title', 'Edit Test - IELTS ' . $test->book_number)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.tests.index') }}" class="hover:text-primary transition-colors">Tests</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <span class="text-slate-900 dark:text-slate-100 font-medium">Edit Test</span>
</nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Edit Test Details" 
        description="Modify the configuration for the IELTS {{ $test->exam_type }} Mock Exam."
    >
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <x-admin.button 
                    variant="ghost" 
                    size="sm"
                    class="text-slate-500 hover:text-red-500"
                    onclick="if(confirm('WARNING: Are you sure you want to delete this test book and all of its question sets?')) { document.getElementById('delete-test-form').submit(); }"
                >
                    <span class="material-symbols-outlined text-lg mr-2">delete</span>
                    Delete Test
                </x-admin.button>
                <form id="delete-test-form" action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </x-slot:actions>
    </x-admin.page-header>

    <!-- Layout: Image + Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Preview Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-3 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                <div class="aspect-[4/3] rounded-[1.5rem] overflow-hidden relative group">
                    <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=2070&auto=format&fit=crop" 
                         alt="Test Cover" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer">
                        <div class="bg-white text-slate-900 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">photo_camera</span>
                            Change Cover
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-primary/5 dark:bg-primary/10 p-8 rounded-[2rem] border border-primary/20">
                <h4 class="text-primary font-black text-xs uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">lightbulb</span>
                    Editor's Tip
                </h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Published tests are visible to all students immediately. Use <b>Draft</b> status to keep working on questions privately.
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                <form action="{{ route('admin.tests.update', $test->id) }}" method="POST" class="p-8 sm:p-10 space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <!-- Field: Book Number -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Book Number</label>
                            <input type="number" name="book_number" value="{{ old('book_number', $test->book_number) }}" 
                                   class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
                        </div>
                        
                        <!-- Field: Year -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Year</label>
                            <input type="number" name="year" value="{{ old('year', $test->year) }}" 
                                   class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
                        </div>

                        <!-- Field: Exam Type -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Exam Type</label>
                            <select name="exam_type" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                                <option value="Academic" {{ $test->exam_type === 'Academic' ? 'selected' : '' }}>Academic</option>
                                <option value="General" {{ $test->exam_type === 'General' ? 'selected' : '' }}>General Training</option>
                            </select>
                        </div>

                        <!-- Field: Status -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Deployment Status</label>
                            <select name="status" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                                <option value="draft" {{ $test->status === 'draft' ? 'selected' : '' }}>Draft Mode</option>
                                <option value="published" {{ $test->status === 'published' ? 'selected' : '' }}>Live / Published</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-end gap-5">
                        <a href="{{ route('admin.tests.show', $test->id) }}" 
                           class="px-8 py-3 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                            Cancel Changes
                        </a>
                        <x-admin.button type="submit" size="lg">
                            Save Configurations
                        </x-admin.button>
                    </div>
                </form>
            </div>

            <!-- Additional Info Section -->
            <div class="mt-8 glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="size-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined text-3xl">help_center</span>
                    </div>
                    <div>
                        <h5 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Need help with exam modules?</h5>
                        <p class="text-xs font-medium text-slate-400 mt-0.5">Visit our documentation to learn more about test configuration.</p>
                    </div>
                </div>
                <a href="#" class="inline-flex items-center gap-2 text-primary font-black text-xs uppercase tracking-widest hover:gap-3 transition-all shrink-0">
                    Read Docs
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


            <!-- Additional Info Section -->
            <div class="mt-8 glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="size-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined text-3xl">help_center</span>
                    </div>
                    <div>
                        <h5 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Need help with exam modules?</h5>
                        <p class="text-xs font-medium text-slate-400 mt-0.5">Visit our documentation to learn more about test configuration.</p>
                    </div>
                </div>
                <a href="#" class="inline-flex items-center gap-2 text-primary font-black text-xs uppercase tracking-widest hover:gap-3 transition-all shrink-0">
                    Read Docs
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
