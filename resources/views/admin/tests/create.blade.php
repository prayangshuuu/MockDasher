@extends('layouts.admin')

@section('title', 'Create New Test')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.tests.index') }}" class="hover:text-primary transition-colors">Tests</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <span class="text-slate-900 dark:text-slate-100 font-medium">Create Test</span>
</nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header title="Create New Test" description="Enter the details to initiate a new IELTS book volume and test sets." />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-primary/5 dark:bg-primary/10 p-8 rounded-[2.5rem] border border-primary/20">
                <h4 class="text-primary font-black text-xs uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">auto_awesome</span>
                    Auto-Generation
                </h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Once created, the system will automatically generate <b>4 complete mock sets</b>. You can customize the modules and tasks for each set individually.
                </p>
            </div>

            <div class="glass-card p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Quick Stats</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Modules/Set</span>
                        <span class="font-bold text-slate-900 dark:text-white">4</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Total Tasks</span>
                        <span class="font-bold text-slate-900 dark:text-white">16+</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                <form action="{{ route('admin.tests.store') }}" method="POST" class="p-8 sm:p-10 space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <!-- Field: Book Number -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Book Number</label>
                            <input type="number" name="book_number" value="{{ old('book_number') }}" 
                                   class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('book_number') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" 
                                   placeholder="e.g. 18" required>
                            @error('book_number') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Field: Year -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Year</label>
                            <input type="number" name="year" value="{{ old('year', date('Y')) }}" 
                                   class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('year') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" 
                                   required>
                            @error('year') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Field: Exam Type -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Exam Type</label>
                            <select name="exam_type" class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('exam_type') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer" required>
                                <option value="" disabled {{ !old('exam_type') ? 'selected' : '' }}>Select Type</option>
                                <option value="Academic" {{ old('exam_type') === 'Academic' ? 'selected' : '' }}>Academic</option>
                                <option value="General" {{ old('exam_type') === 'General' ? 'selected' : '' }}>General Training</option>
                            </select>
                            @error('exam_type') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Field: Status -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Initial Status</label>
                            <select name="status" class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('status') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm cursor-pointer">
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft Mode</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Live / Published</option>
                            </select>
                            @error('status') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-100 dark:border-slate-800 flex items-start gap-4">
                        <span class="material-symbols-outlined text-primary shrink-0">info</span>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                            Upon clicking "Save & Initialize", the platform will prepare the test infrastructure. You can then begin populating question data for each module.
                        </p>
                    </div>

                    <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-end gap-5">
                        <a href="{{ route('admin.tests.index') }}" 
                           class="px-8 py-3 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                            Cancel
                        </a>
                        <x-admin.button type="submit" size="lg">
                            Save & Initialize
                        </x-admin.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer Docs -->
    <div class="mt-12 text-center pb-12">
        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">
            {{ config('app.name', 'MockDasher') }} Exam Builder — v{{ config('app.version', '2.0') }}
        </p>
    </div>
</div>
@endsection
