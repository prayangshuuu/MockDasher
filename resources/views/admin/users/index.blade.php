@extends('layouts.admin')

@section('title', 'Users')

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
        <span class="font-semibold text-slate-900 dark:text-white">Users</span>
    </nav>
@endsection

@section('content')

<section class="mb-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Users</h2>
            <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Manage student accounts and permissions.</p>
        </div>
    </div>

    <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-96">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <img src="/storage/asset/icons/search.svg" class="w-5 h-5 opacity-50" alt="Search" />
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-2 bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-sm dark:text-white placeholder-slate-400">
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm">Filter</button>
    </form>
</section>

<section>
    <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-max">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 whitespace-nowrap">Name</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 whitespace-nowrap">Email</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 whitespace-nowrap">Join Date</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 whitespace-nowrap">Role</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 whitespace-nowrap">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($users ?? [] as $user)
                        <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-xs font-bold text-primary overflow-hidden border border-indigo-100 dark:border-indigo-800">
                                        <img class="h-full w-full object-cover" src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}">
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $user->name }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-medium whitespace-nowrap">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $primaryRole = $user->roles->first(); @endphp
                                @if($primaryRole && strtolower($primaryRole->name) === 'admin')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-100 dark:bg-violet-900/30 px-2.5 py-1 text-xs font-bold text-violet-700 dark:text-violet-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-xs font-bold text-slate-600 dark:text-slate-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        {{ $primaryRole?->name ?? 'User' }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(($user->status ?? 'active') === 'active')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-medium text-emerald-800 dark:text-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-1 text-xs font-medium text-red-800 dark:text-red-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Suspended
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="flex size-8 items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-primary transition-colors border border-transparent hover:border-indigo-100 dark:hover:border-indigo-800" title="Edit">
                                        <img src="/storage/asset/icons/edit.svg" class="w-4 h-4" alt="Edit" />
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to block this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex size-8 items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-error transition-colors border border-transparent hover:border-red-100 dark:hover:border-red-800" title="Block">
                                            <img src="/storage/asset/icons/block.svg" class="w-4 h-4" alt="Block" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="max-w-xs mx-auto">
                                    <img src="/storage/asset/icons/group.svg" class="w-12 h-12 mx-auto opacity-20" alt="No Users" />
                                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mt-4">No users found</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">No users matched your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($users) && $users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Showing <span class="font-bold text-slate-900 dark:text-white">{{ $users->firstItem() }}-{{ $users->lastItem() }}</span> of <span class="font-bold text-slate-900 dark:text-white">{{ $users->total() }}</span>
                </p>
                <div>
                    {{ $users->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif
    </div>
</section>

@endsection
