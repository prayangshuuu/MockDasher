@extends('layouts.admin')

@section('title', 'Edit Student - ' . $user->name)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.users.index') }}" class="hover:text-primary transition-colors">Students</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
    <span class="text-slate-900 dark:text-slate-100 font-medium">Edit Student</span>
</nav>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Student Details</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Update the account information and access permissions for {{ $user->name }}.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
        <div class="lg:col-span-1">
            <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft h-full flex flex-col">
                <div class="flex flex-col items-center text-center pb-6 border-b border-slate-200 dark:border-slate-800 mb-6 mt-auto">
                    @php
                        $initials = strtoupper(substr($user->name, 0, 1));
                    @endphp
                    <div class="size-20 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 text-primary flex items-center justify-center font-bold text-3xl mb-4 shadow-sm">
                        {{ $initials }}
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-lg">{{ $user->name }}</h4>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Student ID #{{ $user->id }}</p>
                </div>

                <div class="space-y-4 mb-auto">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Joined</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Last Attempt</span>
                        <span class="font-bold text-slate-900 dark:text-white">N/A</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden h-full">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-8 sm:p-10 space-y-8 flex flex-col h-full">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6 flex-grow">
                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Full Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/name.svg" class="w-5 h-5 opacity-50" alt="Name" />
                                </div>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="w-full pl-11 pr-4 py-3.5 rounded-xl border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                            </div>
                            @error('name') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Email address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/email.svg" class="w-5 h-5 opacity-50" alt="Email" />
                                </div>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full pl-11 pr-4 py-3.5 rounded-xl border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                            </div>
                            @error('email') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">System Role</label>
                            @if(auth()->id() === $user->id)
                                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 italic">Role management disabled for your own profile.</p>
                                    <input type="hidden" name="role" value="admin">
                                </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="relative flex cursor-pointer group">
                                        <input type="radio" name="role" value="user" class="sr-only peer" {{ old('role', ($user->roles->first()?->name === 'User' ? 'user' : '')) === 'user' ? 'checked' : '' }}>
                                        <div class="w-full p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 rounded-lg bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-700 peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center transition-all shadow-sm">
                                                    <img src="/storage/asset/icons/name.svg" class="w-5 h-5 peer-checked:invert peer-checked:brightness-0 opacity-50 peer-checked:opacity-100" alt="Candidate" />
                                                </div>
                                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300 peer-checked:text-primary">Candidate</span>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="relative flex cursor-pointer group">
                                        <input type="radio" name="role" value="admin" class="sr-only peer" {{ old('role', ($user->roles->first()?->name === 'Admin' ? 'admin' : '')) === 'admin' ? 'checked' : '' }}>
                                        <div class="w-full p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 rounded-lg bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-700 peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center transition-all shadow-sm">
                                                    <img src="/storage/asset/icons/lock-.svg" class="w-5 h-5 peer-checked:invert peer-checked:brightness-0 opacity-50 peer-checked:opacity-100" alt="Admin" />
                                                </div>
                                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300 peer-checked:text-primary">Admin</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                            @error('role') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-8 mt-auto border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-end gap-4">
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">
                            <img src="/storage/asset/icons/delete.svg" class="w-4 h-4 opacity-70" alt="Discard" />
                            Discard
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                            <img src="/storage/asset/icons/check-circle.svg" class="w-4 h-4 invert brightness-0" alt="Update" />
                            Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
