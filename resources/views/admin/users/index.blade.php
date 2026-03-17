@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<!-- Top Navbar -->
<header class="sticky top-0 h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 z-40">
    <form method="GET" action="{{ route('admin.users.index') }}" class="relative w-full max-w-md">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
        <input name="search" value="{{ request('search') }}" class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-10 pr-10 py-2 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400" placeholder="Search users by name or email..." type="text"/>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500">
                <span class="material-symbols-outlined text-xl">close</span>
            </a>
        @endif
    </form>
    <div class="flex items-center gap-4 hidden sm:flex">
        <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition-colors">
            <span class="material-symbols-outlined">notifications</span>
        </button>
        <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition-colors">
            <span class="material-symbols-outlined">help</span>
        </button>
    </div>
</header>

<!-- Page Body -->
<div class="p-4 sm:p-8 max-w-7xl mx-auto space-y-6 sm:space-y-8">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Users Management</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm sm:text-base">Manage and audit your organization's user access and permissions.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="primary-gradient text-white px-6 py-2.5 rounded-xl font-semibold shadow-lg shadow-primary/20 flex items-center justify-center gap-2 hover:opacity-90 transition-opacity whitespace-nowrap">
            <span class="material-symbols-outlined">person_add</span>
            Add New User
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Name &amp; Email</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Joined Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        @php
                            $isAdmin = $user->roles->contains('name', 'Admin');
                            $initials = strtoupper(substr($user->name, 0, 2));
                            if(str_contains($user->name, ' ')) {
                                $parts = explode(' ', $user->name);
                                $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
                            }
                            // Randomize avatar colors based on ID for visual variety
                            $colors = ['bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400', 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400', 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400', 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'];
                            $colorClass = $colors[$user->id % count($colors)];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden font-bold {{ $colorClass }}">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate" title="{{ $user->name }}">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500 truncate" title="{{ $user->email }}">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($isAdmin)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-primary/10 text-primary border border-primary/20">Admin</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    <span class="text-sm font-medium text-emerald-600">Active</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-slate-400 transition-colors inline-block">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-slate-400 hover:text-red-500 transition-colors cursor-pointer">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-500">
                                    <span class="material-symbols-outlined text-4xl mb-3 text-slate-300">search_off</span>
                                    <h3 class="font-bold text-slate-900 dark:text-white mb-1">No users found</h3>
                                    <p class="text-sm max-w-sm mx-auto">We couldn't find any users matching your criteria.</p>
                                    <a href="{{ route('admin.users.index') }}" class="mt-4 text-primary font-medium text-sm hover:underline">Clear search filter</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-slate-200 dark:border-slate-800">
                <p class="text-sm text-slate-500 hidden sm:block">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</p>
                <div class="w-full sm:w-auto">
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">groups</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Users</p>
                <p class="text-2xl font-bold">{{ number_format($users->total()) }}</p>
            </div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined">online_prediction</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Active Sessions</p>
                <p class="text-2xl font-bold">1</p>
            </div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined">admin_panel_settings</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">System Roles</p>
                <p class="text-2xl font-bold">2</p>
            </div>
        </div>
    </div>
</div>
@endsection
