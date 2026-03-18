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
    <x-admin.page-header title="Student Management" description="Manage student accounts, monitor progress, and audit system access.">
        <x-slot:actions>
            <form method="GET" action="{{ route('admin.users.index') }}" class="relative group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">search</span>
                <input name="search" value="{{ request('search') }}" class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm w-64 focus:ring-2 focus:ring-primary/20 shadow-sm" placeholder="Search by name or email...">
            </form>
            <x-admin.button :href="route('admin.users.create')" icon="person_add">
                Add Student
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

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
                                <x-admin.badge :type="$isAdmin ? 'primary' : 'info'" :label="$isAdmin ? 'Administrator' : 'Candidate'" />
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
                                <div class="flex justify-end gap-3 transition-opacity">
                                    <x-admin.button :href="route('admin.users.edit', $user->id)" variant="ghost" icon="edit" size="icon" class="!bg-white dark:!bg-slate-800 !text-slate-600 hover:!text-primary dark:!text-slate-300 border border-slate-100 dark:border-slate-700 shadow-sm transition-colors" />
                                    
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Expel this student from the system?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-admin.button variant="danger" icon="person_remove" size="icon" class="!bg-white dark:!bg-slate-800 !text-red-500 hover:bg-red-50 hover:!text-red-600 dark:hover:!bg-red-500/10 border border-slate-100 dark:border-slate-700 shadow-sm transition-colors" />
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-10 py-24 text-center">
                                <x-admin.empty-state 
                                    title="No candidates found" 
                                    description="We couldn't find any users matching your criteria."
                                    icon="person_search"
                                />
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
        <x-admin.stat-card 
            label="Total Enrolled" 
            :value="number_format($users->total())" 
            icon="groups" 
            iconColor="primary" 
        />
        <x-admin.stat-card 
            label="Weekly Growth" 
            value="+14.2%" 
            icon="bolt" 
            iconColor="emerald" 
        />
        <x-admin.stat-card 
            label="Secure Profiles" 
            value="100%" 
            icon="shield_person" 
            iconColor="blue" 
        />
    </div>
</div>
@endsection
