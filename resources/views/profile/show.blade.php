@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'Account Settings')

@section('content')
<div class="max-w-4xl mx-auto py-4">
    <!-- Title & Navigation Tabs -->
    <div class="mb-10">
        <h2 class="text-3xl font-bold tracking-tight mb-2">Account Settings</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-8">Manage your account preferences, security, and profile information.</p>
        <div class="flex border-b border-slate-200 dark:border-slate-800 gap-8">
            <button class="pb-4 text-sm font-semibold border-b-2 border-primary text-primary transition-all">Profile</button>
        </div>
    </div>

    <!-- Sections -->
    <div class="space-y-8">
        <!-- Profile Section Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-premium overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-semibold">Profile Information</h3>
                <p class="text-sm text-slate-500">This information will be displayed across the platform.</p>
            </div>
            <div class="p-8">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
                    @csrf
                    @method('PUT')
                    
                    <!-- Avatar Upload -->
                    <div class="flex items-center gap-8 mb-8">
                        <div class="size-24 rounded-full border-4 border-slate-50 dark:border-slate-800 overflow-hidden shadow-sm relative group">
                            @if($user->profile_photo_path)
                                <img class="w-full h-full object-cover" src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary text-white font-bold text-2xl">
                                    {{ substr($user->first_name ?: $user->name, 0, 1) }}
                                </div>
                            @endif
                            <label for="photo-upload" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <span class="material-symbols-outlined text-white">photo_camera</span>
                            </label>
                            <input type="file" id="photo-upload" name="photo" class="hidden" accept="image/*" onchange="document.getElementById('profile-form').submit()">
                        </div>
                        <div>
                            <div class="flex gap-3 mb-2">
                                <label for="photo-upload" class="px-4 py-2 text-sm font-semibold border border-slate-200 dark:border-slate-700 rounded-base hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Change Photo</label>
                                @if($user->profile_photo_path)
                                    <button type="button" class="px-4 py-2 text-sm font-semibold text-red-500 border border-transparent hover:bg-red-50 dark:hover:bg-red-900/20 rounded-base transition-colors">Remove</button>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed">JPG, GIF or PNG. Max size of 2MB.</p>
                        </div>
                    </div>

                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">First Name</label>
                            <input name="first_name" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-base text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none" type="text" value="{{ old('first_name', $user->first_name) }}"/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Last Name</label>
                            <input name="last_name" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-base text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none" type="text" value="{{ old('last_name', $user->last_name) }}"/>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Email Address</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
                                <input name="email" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-base text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none" type="email" value="{{ old('email', $user->email) }}"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-[#5048e5] to-[#7c3aed] text-white text-sm font-bold rounded-base shadow-lg shadow-primary/20 hover:shadow-xl hover:translate-y-[-1px] transition-all">
                            Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Section Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-premium overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-semibold">Security</h3>
                <p class="text-sm text-slate-500">Update your password and security settings.</p>
            </div>
            <div class="p-8">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 max-w-lg">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Current Password</label>
                            <input name="current_password" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-base text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none" placeholder="••••••••" type="password"/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">New Password</label>
                            <input name="password" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-base text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none" type="password" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Confirm New Password</label>
                            <input name="password_confirmation" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-base text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none" type="password" required/>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-slate-900 text-white text-sm font-bold rounded-base shadow-lg hover:shadow-xl hover:translate-y-[-1px] transition-all">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="mt-16 p-8 border border-red-200 dark:border-red-900/30 bg-red-50/50 dark:bg-red-950/10 rounded-xl">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <h4 class="text-red-700 dark:text-red-400 font-bold">Delete Account</h4>
                    <p class="text-sm text-red-600/80 dark:text-red-400/60 leading-relaxed">Once you delete your account, there is no going back. All your mock test history and analysis will be lost.</p>
                </div>
                <button type="button" onclick="openDeleteModal()" class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-base hover:bg-red-700 transition-colors">Delete Account</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="delete-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-6 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800">
        <h3 class="text-xl font-bold mb-4">Are you absolutely sure?</h3>
        <p class="text-sm text-slate-500 mb-6">Please enter your password to confirm account deletion. This action cannot be undone.</p>
        
        <form action="{{ route('profile.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="space-y-4">
                <input name="password" type="password" placeholder="Confirm Password" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:border-red-500 outline-none transition-all" required/>
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2.5 text-sm font-bold border border-slate-200 dark:border-slate-700 rounded-lg">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-red-600 rounded-lg shadow-lg shadow-red-500/20">Delete Forever</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openDeleteModal() {
        document.getElementById('delete-modal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection
