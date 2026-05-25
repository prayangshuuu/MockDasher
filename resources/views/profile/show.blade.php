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
                            <img class="w-full h-full object-cover" src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" id="avatar-preview"/>
                            <label for="photo-upload" class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <img src="/storage/asset/icons/edit.svg" class="w-6 h-6 invert brightness-0" alt="Edit Photo" />
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
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/name.svg" class="w-5 h-5 opacity-55" alt="First Name" />
                                </div>
                                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('first_name') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            </div>
                            @error('first_name') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Last Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/name.svg" class="w-5 h-5 opacity-55" alt="Last Name" />
                                </div>
                                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('last_name') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            </div>
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
                            <img src="/storage/asset/icons/check-circle.svg" class="w-4 h-4 invert brightness-0" alt="Save" />
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
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/lock-.svg" class="w-5 h-5 opacity-55" alt="Current Password" />
                                </div>
                                <input type="password" name="current_password" placeholder="••••••••"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('current_password') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            </div>
                            @error('current_password') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">New Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/lock-.svg" class="w-5 h-5 opacity-55" alt="New Password" />
                                </div>
                                <input type="password" name="password" placeholder="••••••••"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            </div>
                            @error('password') <p class="text-red-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Confirm New Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <img src="/storage/asset/icons/lock-.svg" class="w-5 h-5 opacity-55" alt="Confirm New Password" />
                                </div>
                                <input type="password" name="password_confirmation" placeholder="••••••••"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-300">
                            <img src="/storage/asset/icons/check-circle.svg" class="w-4 h-4 invert brightness-0" alt="Save" />
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200 dark:border-slate-800 my-10"></div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 3: GEMINI AI API KEY
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="flex items-center gap-2.5 mb-2">
                <div class="flex size-7 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-950/40 text-violet-500 border border-violet-100 dark:border-violet-900/50">
                    <img src="/storage/asset/icons/ai.svg" class="w-4 h-4 animate-pulse" alt="AI Key" />
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">AI Evaluation Key</h3>
            </div>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Your personal Gemini API key is used to evaluate your Speaking &amp; Writing answers. Each submission uses your own quota.
            </p>
            <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 mt-4 text-xs font-bold text-violet-600 dark:text-violet-400 hover:underline">
                <img src="/storage/asset/icons/explore.svg" class="w-3.5 h-3.5 opacity-70" alt="Explore" />
                Get a free key from Google AI Studio
            </a>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">

                {{-- Status banner --}}
                @if($user->getRawOriginal('gemini_api_key'))
                    <div class="flex items-center gap-3 px-6 py-3 bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-200 dark:border-emerald-800">
                        <img src="/storage/asset/icons/verified.svg" class="w-5 h-5 opacity-90 dark:invert shrink-0" alt="Active" />
                        <div>
                            <p class="text-xs font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">API Key Active</p>
                            <p class="text-[10px] font-medium text-emerald-600 dark:text-emerald-500 mt-0.5">
                                Stored: <span class="font-mono">{{ substr($user->getRawOriginal('gemini_api_key'), 0, 8) }}••••••••••••••••••••••••••••••••</span>
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 px-6 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800">
                        <img src="/storage/asset/icons/info.svg" class="w-5 h-5 opacity-80 shrink-0" alt="Info" />
                        <div>
                            <p class="text-xs font-black text-amber-700 dark:text-amber-400 uppercase tracking-widest">No API Key Set</p>
                            <p class="text-[10px] font-medium text-amber-600 dark:text-amber-500 mt-0.5">
                                AI evaluation of Speaking &amp; Writing answers requires your Gemini API key.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Flash messages for this section --}}
                @if(session('gemini_success'))
                    <div class="mx-6 mt-4 flex items-center gap-2 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm font-semibold">
                        <svg class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-450" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('gemini_success') }}
                    </div>
                @endif
                @error('gemini_api_key')
                    <div class="mx-6 mt-4 flex items-center gap-2 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-semibold">
                        <svg class="w-5 h-5 shrink-0 text-red-600 dark:text-red-450" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror

                <form action="{{ route('profile.gemini.update') }}" method="POST" class="p-6 space-y-4" id="gemini-key-form">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Gemini API Key
                        </label>
                        <p class="text-[11px] text-slate-400 font-medium">
                            Paste your key from
                            <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-violet-500 hover:underline font-bold">Google AI Studio</a>.
                            Keys look like <code class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded font-mono">AIzaSy...</code>
                        </p>
                        <div class="relative">
                            <input type="password"
                                   name="gemini_api_key"
                                   id="gemini-key-input"
                                   autocomplete="off"
                                   spellcheck="false"
                                   value="{{ old('gemini_api_key', $user->getRawOriginal('gemini_api_key') ?? '') }}"
                                   placeholder="AIzaSy..."
                                   class="w-full pr-12 pl-4 py-3 rounded-xl border {{ $errors->has('gemini_api_key') ? 'border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-violet-500 focus:border-violet-500' }} bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white font-mono text-sm focus:ring-2 focus:bg-white dark:focus:bg-surface-dark transition-all shadow-sm">
                            <button type="button"
                                    onclick="toggleGeminiKeyVisibility()"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors focus:outline-none"
                                    title="Show/hide key">
                                <img src="/storage/asset/icons/eye.svg" class="w-5 h-5 opacity-60 hover:opacity-100 transition-opacity" id="gemini-eye-icon" alt="Visibility" />
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                        @if($user->getRawOriginal('gemini_api_key'))
                            <button type="button"
                                    onclick="clearGeminiKey()"
                                    class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors flex items-center gap-1.5 focus:outline-none">
                                <img src="/storage/asset/icons/delete.svg" class="w-4 h-4" alt="Delete" />
                                Remove Key
                            </button>
                        @else
                            <div></div>
                        @endif

                        <button type="submit"
                                id="gemini-save-btn"
                                class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-soft transition-all duration-200 active:scale-95">
                            <img src="/storage/asset/icons/verified.svg" class="w-4 h-4 invert brightness-0" alt="Save" />
                            Save &amp; Verify Key
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-3 flex items-start gap-2.5 text-[11px] text-slate-450 dark:text-slate-500 font-medium px-1 leading-relaxed">
                <img src="/storage/asset/icons/info.svg" class="w-3.5 h-3.5 shrink-0 mt-0.5 opacity-60" alt="Info" />
                <span>Your API key is stored securely and never shared. It is only used to call Gemini on your behalf when you submit Speaking or Writing answers.</span>
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
                        class="shrink-0 inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-soft transition-all duration-200 active:scale-95 focus:outline-none"
                    >
                        <img src="/storage/asset/icons/delete.svg" class="w-4 h-4 invert brightness-0" alt="Delete" />
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

@push('scripts')
<script>
    // ─── Gemini Key: Show / Hide Toggle ───────────────────────────────────────
    function toggleGeminiKeyVisibility() {
        const input = document.getElementById('gemini-key-input');
        const icon  = document.getElementById('gemini-eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('opacity-60');
            icon.classList.add('opacity-100', 'brightness-125', 'scale-110');
        } else {
            input.type = 'password';
            icon.classList.remove('opacity-100', 'brightness-125', 'scale-110');
            icon.classList.add('opacity-60');
        }
    }

    // ─── Gemini Key: Clear / Remove ───────────────────────────────────────────
    function clearGeminiKey() {
        if (! confirm('Remove your Gemini API key? AI evaluation will stop working until you add a new one.')) return;
        const input = document.getElementById('gemini-key-input');
        if (input) input.value = '';
        const form = document.getElementById('gemini-key-form');
        if (form) form.submit();
    }

    // ─── Gemini Key: Loading state on save ────────────────────────────────────
    document.getElementById('gemini-key-form')?.addEventListener('submit', function () {
        const btn = document.getElementById('gemini-save-btn');
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block align-middle" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span class="align-middle">Verifying...</span>';
        }
    });
</script>
@endpush
