@extends('layouts.admin')

@section('title', 'Add New Student')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('admin.users.index') }}" class="hover:text-primary transition-colors">Students</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <span class="text-slate-900 dark:text-slate-100 font-medium">Add Student</span>
</nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-10 text-center sm:text-left">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Enroll New Student</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-1 text-base">Create a new candidate account and define their system access levels.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-primary/5 dark:bg-primary/10 p-8 rounded-[2.5rem] border border-primary/20">
                <h4 class="text-primary font-black text-xs uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">shield</span>
                    Security Note
                </h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Ensure the email address is correct as it will be used for login. Passwords must be at least <b>8 characters</b> long for security.
                </p>
            </div>

            <div class="glass-card p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-soft">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">manage_accounts</span>
                    Role Permissions
                </h4>
                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Administrator</p>
                        <p class="text-xs font-medium text-slate-500">Full control over tests, users, and global settings.</p>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Candidate</p>
                        <p class="text-xs font-medium text-slate-500">Access to take mock exams and view personal performance analytics.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
                <form action="{{ route('admin.users.store') }}" method="POST" class="p-8 sm:p-10 space-y-8">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Field: Name -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                   class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" 
                                   placeholder="e.g. Alex Johnson" required>
                            @error('name') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Field: Email -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                   class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" 
                                   placeholder="e.g. alex@mockdasher.io" required>
                            @error('email') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Field: Password -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Secure Password</label>
                            <input type="password" name="password" 
                                   class="w-full px-5 py-3.5 rounded-2xl border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm" 
                                   placeholder="Minimum 8 characters" required>
                            @error('password') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Field: Role -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Account Role</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex cursor-pointer group">
                                    <input type="radio" name="role" value="user" class="sr-only peer" {{ old('role') === 'user' || !old('role') ? 'checked' : '' }}>
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
                                    <input type="radio" name="role" value="admin" class="sr-only peer" {{ old('role') === 'admin' ? 'checked' : '' }}>
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
                            @error('role') <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-end gap-5">
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-8 py-3 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                            Discard
                        </a>
                        <button type="submit" class="px-10 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl hover:scale-[1.05] active:scale-100 transition-all">
                            Save Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
