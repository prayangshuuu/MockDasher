@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'Account Settings')

@section('breadcrumbs')
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90" alt=">" />
        <span class="font-semibold text-slate-900 dark:text-white">Settings</span>
    </nav>
@endsection

@section('content')

<div class="max-w-5xl mx-auto space-y-10">
    <section class="mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Account Settings</h2>
        <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Manage your profile, security, and preferences.</p>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 1: PROFILE INFORMATION
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Profile Information</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Update your account's profile information and email address.</p>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
                    @csrf
                    @method('PUT')

                    {{-- Avatar --}}
                    <div class="flex items-center gap-6 mb-8">
                        <div class="relative group size-20 shrink-0 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm">
                            @if($user->profile_photo_path)
                                <img class="w-full h-full object-cover" src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/30 text-primary font-bold text-2xl">
                                    {{ strtoupper(substr($user->first_name ?: $user->name, 0, 1)) }}
                                </div>
                            @endif
                            <label for="photo-upload" class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <span class="material-symbols-outlined text-white text-xl">photo_camera</span>
                            </label>
                            <input type="file" id="photo-upload" name="photo" class="hidden" accept="image/*" onchange="document.getElementById('profile-form').submit()">
                        </div>
                        <div>
                            <label for="photo-upload" class="inline-flex text-sm font-bold text-primary cursor-pointer hover:text-primary-hover transition-colors mb-1">Change Photo</label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">JPG, GIF or PNG. Max 2MB.</p>
                        </div>
                    </div>

                    {{-- Form Grid --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('first_name') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            @error('first_name') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('last_name') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            @error('last_name') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/email.svg" class="w-5 h-5 opacity-50" alt="Email" />
                                </div>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                            </div>
                            @error('email') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200 dark:border-slate-800 my-10"></div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 2: SECURITY
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Security</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Update your password to keep your account secure.</p>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-surface-light dark:bg-surface-dark p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6 max-w-md">
                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Current Password</label>
                            <input type="password" name="current_password" placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('current_password') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            @error('current_password') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">New Password</label>
                            <input type="password" name="password" placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            @error('password') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200 dark:border-slate-800 my-10"></div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         DANGER ZONE
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <h3 class="text-lg font-bold text-red-600 dark:text-red-400">Danger Zone</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Irreversible and destructive actions.</p>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10 p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Delete Account</h4>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400/80 font-medium">Once deleted, all your data will be permanently removed.</p>
                    </div>
                    <button
                        x-data
                        @click="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="shrink-0 bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-soft transition-colors"
                    >
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Delete Account Modal --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('delete-modal').classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md rounded-2xl border border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark p-8 shadow-premium">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Are you absolutely sure?</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 font-medium">Please enter your password to confirm. This action cannot be undone.</p>

        <form action="{{ route('profile.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="space-y-6">
                <div class="space-y-3">
                    <input type="password" name="password" placeholder="Confirm Password" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm" required>
                </div>
                <div class="flex gap-4">
                    <button type="button" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm" onclick="document.getElementById('delete-modal').classList.add('hidden')">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl transition-colors shadow-soft">Delete Forever</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
