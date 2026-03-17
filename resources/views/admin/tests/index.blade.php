@extends('layouts.admin')

@section('title', 'Manage Tests')

@section('content')
<!-- Top Navbar -->
<header class="h-16 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md sticky top-0 z-40 px-4 sm:px-8 flex items-center justify-between">
    <form method="GET" action="{{ route('admin.tests.index') }}" class="flex items-center gap-4 flex-1 max-w-xl">
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl py-2 pl-10 pr-10 text-sm focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-slate-500" placeholder="Search tests by book or year..." type="text"/>
            @if(request('search'))
                <a href="{{ route('admin.tests.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500">
                    <span class="material-symbols-outlined text-xl">close</span>
                </a>
            @endif
        </div>
    </form>
    <div class="flex items-center gap-3">
        <button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors relative hidden sm:block">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
        </button>
        <button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors hidden sm:block">
            <span class="material-symbols-outlined">help_outline</span>
        </button>
        <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-800 mx-1 sm:mx-2 hidden sm:block"></div>
        <a href="{{ route('admin.tests.create') }}" class="primary-gradient text-white px-3 sm:px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-primary/25 flex items-center gap-1 sm:gap-2 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span>
            <span class="hidden sm:inline">Create Test</span>
        </a>
    </div>
</header>

<div class="flex flex-col flex-1">
    <!-- Page Header & Actions -->
    <div class="px-4 sm:px-8 pt-8 pb-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Manage Tests</h2>
                <p class="text-slate-500 mt-1 font-medium text-sm sm:text-base">Create, organize, and monitor your mock examinations.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-1 shadow-sm">
                    <button class="px-3 py-1.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-bold">List</button>
                    <button class="px-3 py-1.5 rounded-md text-slate-500 dark:text-slate-400 text-xs font-bold hover:text-slate-900">Grid</button>
                </div>
                <form action="{{ route('admin.tests.index') }}" method="GET" class="flex gap-2 relative group">
                    <select name="type" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-sm font-bold shadow-sm hover:bg-slate-50 transition-colors focus:ring-0">
                        <option value="">All Types</option>
                        <option value="Academic" {{ request('type') == 'Academic' ? 'selected' : '' }}>Academic</option>
                        <option value="General" {{ request('type') == 'General' ? 'selected' : '' }}>General Training</option>
                    </select>
                </form>
            </div>
        </div>
        
        <!-- Filters Bar -->
        <div class="flex gap-2 mt-6 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ route('admin.tests.index') }}" class="px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ !request('status') ? 'bg-primary text-white' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-primary/50' }}">All Tests</a>
            <a href="{{ route('admin.tests.index', ['status' => 'published']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request('status') === 'published' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-primary/50' }}">Published</a>
            <a href="{{ route('admin.tests.index', ['status' => 'pending']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request('status') === 'pending' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-primary/50' }}">Pending</a>
            <a href="{{ route('admin.tests.index', ['status' => 'draft']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request('status') === 'draft' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-primary/50' }}">Drafts</a>
        </div>
    </div>

    <!-- Content Area: Data -->
    <div class="flex-1 px-4 sm:px-8 pb-8 flex flex-col">
        @if($tests->isEmpty())
            <div class="w-full flex-1 flex flex-col items-center justify-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-12 text-center my-auto min-h-[500px]">
                <!-- Premium Empty Illustration -->
                <div class="relative mb-8">
                    <div class="absolute -inset-4 bg-primary/5 blur-3xl rounded-full"></div>
                    <div class="relative flex items-center justify-center w-32 h-32 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-inner overflow-hidden">
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, var(--tw-gradient-stops) 1px, transparent 0); background-size: 16px 16px;"></div>
                        <span class="material-symbols-outlined text-6xl text-primary/40 leading-none">post_add</span>
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-100 dark:border-slate-800 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-xl">search</span>
                    </div>
                </div>
                
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">No tests created yet</h3>
                <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">Get started by creating your first mock examination. You can customize timing, question types, and scoring rules.</p>
                
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="{{ route('admin.tests.create') }}" class="primary-gradient text-white px-8 py-3 rounded-xl font-bold shadow-xl shadow-primary/30 flex items-center gap-3 hover:scale-[1.02] transition-transform">
                        <span class="material-symbols-outlined">add_circle</span>
                        Create Your First Test
                    </a>
                </div>
                
                <!-- Secondary Assistance -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-4xl">
                    <div class="p-6 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 text-left group hover:border-primary/20 transition-colors">
                        <span class="material-symbols-outlined text-primary mb-3 bg-white dark:bg-slate-900 p-2 rounded-lg shadow-sm">school</span>
                        <h4 class="font-bold text-slate-900 dark:text-white mb-1">Academy Guide</h4>
                        <p class="text-xs text-slate-500 font-medium">Learn how to build high-converting assessment tests.</p>
                    </div>
                    <div class="p-6 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 text-left group hover:border-primary/20 transition-colors">
                        <span class="material-symbols-outlined text-primary mb-3 bg-white dark:bg-slate-900 p-2 rounded-lg shadow-sm">auto_awesome</span>
                        <h4 class="font-bold text-slate-900 dark:text-white mb-1">AI Generator</h4>
                        <p class="text-xs text-slate-500 font-medium">Auto-generate questions from your existing PDFs or URLs.</p>
                    </div>
                    <div class="p-6 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 text-left group hover:border-primary/20 transition-colors">
                        <span class="material-symbols-outlined text-primary mb-3 bg-white dark:bg-slate-900 p-2 rounded-lg shadow-sm">cloud_sync</span>
                        <h4 class="font-bold text-slate-900 dark:text-white mb-1">Bulk Upload</h4>
                        <p class="text-xs text-slate-500 font-medium">CSV/Excel support for importing massive question banks.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($tests as $test)
                    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 group hover:border-primary/30 transition-colors">
                        <div class="flex items-start sm:items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $test->exam_type === 'Academic' ? 'bg-indigo-50 dark:bg-indigo-900/30 text-primary' : 'bg-sky-50 dark:bg-sky-900/30 text-sky-600' }}">
                                <span class="material-symbols-outlined text-2xl">{{ $test->exam_type === 'Academic' ? 'school' : 'public' }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-base">IELTS {{ ucfirst($test->exam_type) }} Edition</h3>
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-1 text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                    <span>Book {{ $test->book_number }}</span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                    <span>Year {{ $test->year }}</span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                    <span>{{ $test->testSets->count() }} Set{{ $test->testSets->count() !== 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between sm:justify-end gap-4 w-full md:w-auto mt-2 md:mt-0 pt-3 md:pt-0 border-t border-slate-100 dark:border-slate-800 md:border-0">
                            <div>
                                @if($test->status === 'published')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 text-[10px] font-bold uppercase tracking-widest border border-emerald-200/50">Published</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-600 text-[10px] font-bold uppercase tracking-widest border border-amber-200/50">Pending</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <a href="{{ route('admin.tests.show', $test->id) }}" class="flex-1 sm:flex-none text-center px-4 py-2 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-xs font-bold transition-colors">
                                    Manage
                                </a>
                                <a href="{{ route('admin.tests.edit', $test->id) }}" class="p-2 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-primary rounded-lg transition-colors border border-transparent shadow-sm">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </a>
                                <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" class="inline-block" onsubmit="return confirm('WARNING: Are you sure you want to delete this test book and all of its question sets?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors border border-transparent shadow-sm">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($tests->hasPages())
                <div class="mt-6">
                    {{ $tests->links() }}
                </div>
            @endif
        @endif
        
        <!-- Footer Stats -->
        <footer class="mt-auto px-4 sm:px-8 py-6 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 font-semibold uppercase tracking-widest gap-4">
            <div class="flex flex-wrap justify-center sm:justify-start gap-4 sm:gap-6">
                <span>Total Tests: {{ $tests->total() ?? 0 }}</span>
                <span>Active Candidates: 421</span>
                <span class="flex items-center gap-1.5"><div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div> Server Status: Online</span>
            </div>
            <div class="flex items-center gap-2 text-slate-400">
                <span>v2.4.0-pro</span>
            </div>
        </footer>
    </div>
</div>
@endsection
