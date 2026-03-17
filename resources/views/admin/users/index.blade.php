@extends('layouts.admin')

@section('title', 'Students Management')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <span class="font-semibold text-slate-900 dark:text-white">Students</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Student Management</h2>
            <p class="text-slate-500 dark:text-slate-400 text-base">Manage student accounts, monitor progress, and audit system access.</p>
        </div>
        <div class="flex items-center gap-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="relative group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">search</span>
                <input name="search" value="{{ request('search') }}" class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm w-64 focus:ring-2 focus:ring-primary/20 shadow-sm" placeholder="Search by name or email...">
            </form>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Add Student
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass-card rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Student Information</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Role</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Enrollment Date</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    @forelse($users as $user)
                        @php
                            $isAdmin = $user->roles->contains('name', 'Admin');
                            $initials = strtoupper(substr($user->name, 0, 1));
                            $colors = ['bg-indigo-50 text-indigo-600', 'bg-blue-50 text-blue-600', 'bg-purple-50 text-purple-600', 'bg-emerald-50 text-emerald-600', 'bg-orange-50 text-orange-600'];
                            $colorClass = $colors[$user->id % count($colors)];
                        @endphp
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="size-12 rounded-2xl flex items-center justify-center font-black text-xl {{ $colorClass }} group-hover:scale-110 transition-transform">
                                        {{ $initials }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</span>
                                        <span class="text-xs font-medium text-slate-400">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                @if($isAdmin)
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20">Administrator</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-400 border border-slate-200 dark:border-slate-700">Candidate</span>
                                @endif
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/20">
                                    <div class="size-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Active</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-xs font-bold text-slate-500 tracking-tight">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="size-10 flex items-center justify-center bg-white dark:bg-slate-800 text-slate-400 hover:text-primary rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:-translate-y-1">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Expel this student from the system?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="size-10 flex items-center justify-center bg-white dark:bg-slate-800 text-slate-400 hover:text-red-500 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:-translate-y-1">
                                                <span class="material-symbols-outlined text-lg">person_remove</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-10 py-24 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto opacity-40">
                                    <span class="material-symbols-outlined text-6xl mb-4">person_search</span>
                                    <h4 class="font-black text-xs uppercase tracking-widest">No candidates found</h4>
                                    <p class="text-sm font-medium mt-1">We couldn't find any users matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-10 py-6 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</p>
                <div class="scale-90">
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Enrollment Insights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6 group hover:border-primary/50 transition-all">
            <div class="size-16 rounded-[1.5rem] bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">groups</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Enrolled</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($users->total()) }}</p>
            </div>
        </div>
        
        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6 group hover:border-emerald-500/30 transition-all">
            <div class="size-16 rounded-[1.5rem] bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">bolt</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Weekly Growth</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">+14.2%</p>
            </div>
        </div>
        
        <div class="glass-card p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft flex items-center gap-6 group hover:border-blue-500/30 transition-all">
            <div class="size-16 rounded-[1.5rem] bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">shield_person</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Secure Profiles</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">100%</p>
            </div>
        </div>
    </div>
</div>
@endsection
