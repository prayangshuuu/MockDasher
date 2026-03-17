@extends('layouts.admin')

@section('title', 'Create New Test')

@section('content')
<!-- Top Navbar -->
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 flex items-center justify-between sticky top-0 z-40">
    <div class="flex items-center gap-2 sm:gap-4 overflow-x-auto whitespace-nowrap hide-scrollbar">
        <nav class="flex items-center text-xs sm:text-sm font-medium">
            <a class="text-slate-500 hover:text-primary transition-colors" href="{{ route('admin.tests.index') }}">Tests</a>
            <span class="material-symbols-outlined text-slate-400 mx-1 sm:mx-2 text-base">chevron_right</span>
            <span class="text-slate-900 dark:text-white">Create New Test</span>
        </nav>
    </div>
    <div class="flex flex-shrink-0 items-center gap-3 sm:gap-6 ml-4">
        <div class="relative hidden lg:block">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
            <input class="bg-slate-50 dark:bg-slate-800 border-none rounded-full pl-10 pr-4 py-2 text-sm w-48 xl:w-64 focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-slate-500" placeholder="Search tests..." type="text"/>
        </div>
        <button class="relative text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-0 right-0 h-2 w-2 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900"></span>
        </button>
    </div>
</header>

<!-- Content Area -->
<div class="flex-1 overflow-y-auto p-4 sm:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Title Section -->
        <div class="mb-6 sm:mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Create New Test</h2>
            <p class="text-slate-500 mt-1 text-sm sm:text-base">Enter the details to generate a new IELTS simulation test set.</p>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] border border-slate-200 dark:border-slate-800 overflow-hidden">
            <form action="{{ route('admin.tests.store') }}" method="POST">
                @csrf
                <div class="p-6 sm:p-8 space-y-6 sm:space-y-8">
                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Book Number</label>
                            <input name="book_number" value="{{ old('book_number') }}" required min="1" type="number"
                                class="w-full bg-slate-50 dark:bg-slate-800 border {{ $errors->has('book_number') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200 dark:border-slate-700' }} rounded-lg px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" 
                                placeholder="e.g., 20"/>
                            @error('book_number') <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Year</label>
                            <input name="year" value="{{ old('year', date('Y')) }}" required min="1990" type="number"
                                class="w-full bg-slate-50 dark:bg-slate-800 border {{ $errors->has('year') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200 dark:border-slate-700' }} rounded-lg px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" 
                                placeholder="e.g., {{ date('Y') }}"/>
                            @error('year') <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Exam Type</label>
                            <div class="relative">
                                <select name="exam_type" required
                                    class="w-full appearance-none bg-slate-50 dark:bg-slate-800 border {{ $errors->has('exam_type') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200 dark:border-slate-700' }} rounded-lg px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-10 outline-none">
                                    <option value="" disabled {{ !old('exam_type') ? 'selected' : '' }}>Select Exam Type...</option>
                                    <option value="Academic" {{ old('exam_type') === 'Academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="General" {{ old('exam_type') === 'General' ? 'selected' : '' }}>General Training</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                            </div>
                            @error('exam_type') <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                            <div class="relative">
                                <select name="status"
                                    class="w-full appearance-none bg-slate-50 dark:bg-slate-800 border {{ $errors->has('status') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200 dark:border-slate-700' }} rounded-lg px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-10 outline-none">
                                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Hidden from users)</option>
                                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (Visible to users)</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                            </div>
                            @error('status') <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <!-- Info Box -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/50 rounded-lg p-4 flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary shrink-0">info</span>
                        <p class="text-sm text-indigo-700 dark:text-indigo-300 font-medium">
                            4 Test Sets will be auto-generated upon saving. Each set will include Listening, Reading, Writing, and Speaking modules.
                        </p>
                    </div>
                </div>
                
                <!-- Action Footer -->
                <div class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 px-6 sm:px-8 py-5 flex flex-col sm:flex-row items-center justify-end gap-3 sm:gap-4">
                    <a href="{{ route('admin.tests.index') }}" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all border border-transparent">
                        Cancel
                    </a>
                    <button type="submit" class="w-full sm:w-auto primary-gradient px-8 py-2.5 rounded-lg text-sm font-bold text-white shadow-lg shadow-primary/20 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary/30 transition-all flex items-center justify-center gap-2 active:scale-95 border border-transparent">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Save Test
                    </button>
                </div>
            </form>
        </div>

        <!-- Help / Secondary Info -->
        <div class="mt-8 text-center pb-8">
            <p class="text-slate-400 text-sm">Need help? Check our <a href="#" class="text-primary hover:underline font-medium focus:outline-none focus:ring-2 focus:ring-primary/50 rounded">documentation</a> on creating test simulations.</p>
        </div>
    </div>
</div>
@endsection
