@extends('layouts.admin')

@section('title', 'Edit Student - ' . $user->name)

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.users.index') }}" class="hover:text-primary transition-colors">Students</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <span class="text-slate-900 dark:text-slate-100 font-medium">Edit Student</span>
</nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header 
        title="Edit Student Details" 
        description="Update the account information and access permissions for {{ $user->name }}."
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft">
                <div class="flex flex-col items-center text-center pb-6 border-b border-slate-100 dark:border-slate-800 mb-6">
                    @php
                        $initials = strtoupper(substr($user->name, 0, 1));
                    @endphp
                    <div class="size-20 rounded-[2rem] bg-indigo-50 dark:bg-indigo-900/40 text-primary flex items-center justify-center font-black text-3xl mb-4">
                        {{ $initials }}
                    </div>
                    <h4 class="font-black text-slate-900 dark:text-white text-lg">{{ $user->name }}</h4>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Student ID #{{ $user->id }}</p>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Joined</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Last Attempt</span>
                        <span class="font-bold text-slate-900 dark:text-white">N/A</span>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 dark:bg-amber-900/10 p-8 rounded-[2.5rem] border border-amber-200/50">
                <h4 class="text-amber-600 font-black text-xs uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">warning</span>
                    Account Access
                </h4>
                <p class="text-sm text-amber-700 dark:text-amber-400 leading-relaxed font-medium">
                    Modifying roles affects global system permissions. If you change your own role, you may lose administrative access.
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-8 sm:p-10 space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Field: Name -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
                            @error('name') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Field: Email -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" required>
                            @error('email') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Field: Role -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">System Role</label>
                            @if(auth()->id() === $user->id)
                                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                    <p class="text-sm font-bold text-slate-400 italic">Role management disabled for your own profile.</p>
                                    <input type="hidden" name="role" value="admin">
                                </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="relative flex cursor-pointer group">
                                        <input type="radio" name="role" value="user" class="sr-only peer" {{ old('role', ($user->roles->first()?->name === 'User' ? 'user' : '')) === 'user' ? 'checked' : '' }}>
                                        <div class="w-full p-4 rounded-2xl border-2 border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded-lg bg-slate-100 dark:bg-slate-800 peer-checked:bg-primary peer-checked:text-white flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform">
                                                    <span class="material-symbols-outlined text-lg">person</span>
                                                </div>
                                                <span class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">Candidate</span>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative flex cursor-pointer group">
                                        <input type="radio" name="role" value="admin" class="sr-only peer" {{ old('role', ($user->roles->first()?->name === 'Admin' ? 'admin' : '')) === 'admin' ? 'checked' : '' }}>
                                        <div class="w-full p-4 rounded-2xl border-2 border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded-lg bg-slate-100 dark:bg-slate-800 peer-checked:bg-primary peer-checked:text-white flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform">
                                                    <span class="material-symbols-outlined text-lg">security</span>
                                                </div>
                                                <span class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">Admin</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                            @error('role') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-end gap-5">
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-8 py-3 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                            Discard
                        </a>
                        <x-admin.button type="submit" size="lg">
                            Update Student
                        </x-admin.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

</div>
@endsection
