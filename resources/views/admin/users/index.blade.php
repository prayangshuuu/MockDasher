@extends('layouts.admin')

@section('title', 'Users')

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)]">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-[var(--color-primary)] transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="font-semibold text-[var(--color-text-primary)]">Users</span>
    </nav>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     HEADER & FILTERS
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section class="mb-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[var(--color-text-primary)]">Users</h2>
            <p class="text-small mt-1 text-[var(--color-text-secondary)]">Manage student accounts and permissions.</p>
        </div>
    </div>

    {{-- Search / Filter Bar --}}
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-96">
        <div class="flex-1">
            <x-ui.input 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search by name or email..." 
                icon="search" 
            />
        </div>
        <x-ui.button type="submit" variant="secondary" class="px-4">Filter</x-ui.button>
    </form>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     DATA TABLE
     ═══════════════════════════════════════════════════════════════════════════ --}}
<section>
    <x-ui.card :flush="true">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--color-divider)] bg-[var(--color-bg-primary)]">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Name</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Email</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Join Date</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Role</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-secondary)] sm:px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                        <tr class="border-b border-[var(--color-divider)] last:border-b-0 transition-colors hover:bg-[var(--color-bg-secondary)]">
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-primary)_12%,transparent)] text-xs font-bold text-[var(--color-primary)] overflow-hidden">
                                        @if($user->profile_photo_path)
                                            <img class="h-full w-full object-cover" src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}">
                                        @else
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold text-[var(--color-text-primary)]">
                                        {{ $user->name }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-4 sm:px-6 text-sm text-[var(--color-text-secondary)]">
                                {{ $user->email }}
                            </td>

                            <td class="px-5 py-4 sm:px-6 text-sm text-[var(--color-text-secondary)]">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-5 py-4 sm:px-6 text-sm text-[var(--color-text-secondary)] capitalize">
                                {{ $user->role ?? 'student' }}
                            </td>

                            <td class="px-5 py-4 sm:px-6">
                                @if(($user->status ?? 'active') === 'active')
                                    <x-ui.badge variant="success">Active</x-ui.badge>
                                @else
                                    <x-ui.badge variant="error">Suspended</x-ui.badge>
                                @endif
                            </td>

                            <td class="px-5 py-4 sm:px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="#" class="flex size-8 items-center justify-center rounded-[var(--radius-xs)] text-[var(--color-text-secondary)] transition-colors hover:bg-[color-mix(in_srgb,var(--color-primary)_10%,transparent)] hover:text-[var(--color-primary)]" title="Edit">
                                        <span class="material-symbols-outlined text-small">edit</span>
                                    </a>
                                    <form action="#" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to block this user?');">
                                        @csrf
                                        <button type="submit" class="flex size-8 items-center justify-center rounded-[var(--radius-xs)] text-[var(--color-text-secondary)] transition-colors hover:bg-[color-mix(in_srgb,var(--color-error)_10%,transparent)] hover:text-[var(--color-error)]" title="Block">
                                            <span class="material-symbols-outlined text-small">block</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center sm:px-6">
                                <x-ui.empty-state
                                    icon="group_off"
                                    title="No users found"
                                    description="No users matched your search criteria."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (if applicable) --}}
        @if(isset($users) && $users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
            <x-slot:footer>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-[var(--color-text-secondary)]">
                        Showing <span class="font-semibold text-[var(--color-text-primary)]">{{ $users->firstItem() }}-{{ $users->lastItem() }}</span> of <span class="font-semibold text-[var(--color-text-primary)]">{{ $users->total() }}</span>
                    </p>
                    {{ $users->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-ui.card>
</section>

@endsection
