@extends('layouts.admin')

@section('title', 'Add New User')

@section('content')
<div class="px-8 pt-8 pb-4">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.users.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-black tracking-tight">Add New User</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Create a new account and assign a system role.</p>
        </div>
    </div>

    <div class="max-w-2xl bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full bg-slate-50 dark:bg-slate-800 border {{ $errors->has('name') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200 dark:border-slate-700' }} rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                        placeholder="e.g. Jane Doe">
                    @error('name') <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        class="w-full bg-slate-50 dark:bg-slate-800 border {{ $errors->has('email') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200 dark:border-slate-700' }} rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                        placeholder="e.g. jane@mockdasher.io">
                    @error('email') <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password</label>
                    <input type="password" name="password" required 
                        class="w-full bg-slate-50 dark:bg-slate-800 border {{ $errors->has('password') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200 dark:border-slate-700' }} rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                        placeholder="Minimum 8 characters">
                    @error('password') <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">System Role</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex cursor-pointer rounded-xl border {{ old('role') === 'user' || !old('role') ? 'border-primary bg-primary/5 ring-1 ring-primary/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <input type="radio" name="role" value="user" class="sr-only" onchange="updateRoleUI(this)" {{ old('role') === 'user' || !old('role') ? 'checked' : '' }}>
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg text-primary">person</span>
                                    Standard User
                                </span>
                                <span class="text-xs text-slate-500 font-medium">Can take tests and view their own results.</span>
                            </div>
                        </label>
                        
                        <label class="relative flex cursor-pointer rounded-xl border {{ old('role') === 'admin' ? 'border-primary bg-primary/5 ring-1 ring-primary/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <input type="radio" name="role" value="admin" class="sr-only" onchange="updateRoleUI(this)" {{ old('role') === 'admin' ? 'checked' : '' }}>
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg text-indigo-500">security</span>
                                    Administrator
                                </span>
                                <span class="text-xs text-slate-500 font-medium">Full access to manage tests, users, and settings.</span>
                            </div>
                        </label>
                    </div>
                    @error('role') <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 bg-white hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-indigo-600 text-white text-sm font-bold shadow-lg shadow-primary/25 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateRoleUI(radio) {
    const labels = radio.closest('.grid').querySelectorAll('label');
    labels.forEach(label => {
        label.classList.remove('border-primary', 'bg-primary/5', 'ring-1', 'ring-primary/20');
        label.classList.add('border-slate-200', 'bg-white', 'dark:bg-slate-800', 'dark:border-slate-700');
    });
    const selectedLabel = radio.closest('label');
    selectedLabel.classList.remove('border-slate-200', 'bg-white', 'dark:bg-slate-800', 'dark:border-slate-700');
    selectedLabel.classList.add('border-primary', 'bg-primary/5', 'ring-1', 'ring-primary/20');
}
</script>
@endsection
