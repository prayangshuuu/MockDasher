@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'Account Settings')

@section('content')

<div class="max-w-5xl mx-auto space-y-10">

    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 1: PROFILE INFORMATION
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        {{-- Left: Title --}}
        <div>
            <h3 class="text-lg font-bold text-[var(--color-text-primary)]">Profile Information</h3>
            <p class="mt-1 text-sm text-[var(--color-text-secondary)]">Update your account's profile information and email address.</p>
        </div>

        {{-- Right: Form Card --}}
        <div class="lg:col-span-2">
            <x-ui.card>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
                    @csrf
                    @method('PUT')

                    {{-- Avatar --}}
                    <div class="flex items-center gap-5 mb-8">
                        <div class="relative group size-16 shrink-0 rounded-full overflow-hidden border border-[var(--color-divider)]">
                            @if($user->profile_photo_path)
                                <img class="w-full h-full object-cover" src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[color-mix(in_srgb,var(--color-primary)_12%,transparent)] text-[var(--color-primary)] font-bold text-lg">
                                    {{ strtoupper(substr($user->first_name ?: $user->name, 0, 1)) }}
                                </div>
                            @endif
                            <label for="photo-upload" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <span class="material-symbols-outlined text-white text-lg">photo_camera</span>
                            </label>
                            <input type="file" id="photo-upload" name="photo" class="hidden" accept="image/*" onchange="document.getElementById('profile-form').submit()">
                        </div>
                        <div>
                            <label for="photo-upload" class="text-sm font-medium text-[var(--color-primary)] cursor-pointer hover:opacity-80 transition-opacity">Change Photo</label>
                            <p class="mt-0.5 text-xs text-[var(--color-text-secondary)]">JPG, GIF or PNG. Max 2MB.</p>
                        </div>
                    </div>

                    {{-- Form Grid --}}
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-ui.input name="first_name" label="First Name" type="text" :value="old('first_name', $user->first_name)" />
                        <x-ui.input name="last_name" label="Last Name" type="text" :value="old('last_name', $user->last_name)" />
                        <div class="md:col-span-2">
                            <x-ui.input name="email" label="Email Address" type="email" icon="mail" :value="old('email', $user->email)" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 2: SECURITY
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div>
            <h3 class="text-lg font-bold text-[var(--color-text-primary)]">Security</h3>
            <p class="mt-1 text-sm text-[var(--color-text-secondary)]">Update your password to keep your account secure.</p>
        </div>

        <div class="lg:col-span-2">
            <x-ui.card>
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5 max-w-md">
                        <x-ui.input name="current_password" label="Current Password" type="password" icon="lock" placeholder="••••••••" />
                        <x-ui.input name="password" label="New Password" type="password" icon="lock" placeholder="••••••••" />
                        <x-ui.input name="password_confirmation" label="Confirm New Password" type="password" icon="lock" placeholder="••••••••" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-ui.button type="submit" variant="primary">Update Password</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         DANGER ZONE
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div>
            <h3 class="text-lg font-bold text-[var(--color-error)]">Danger Zone</h3>
            <p class="mt-1 text-sm text-[var(--color-text-secondary)]">Irreversible and destructive actions.</p>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-[var(--radius-xl)] border border-[color-mix(in_srgb,var(--color-error)_25%,transparent)] bg-[color-mix(in_srgb,var(--color-error)_4%,transparent)] p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-[var(--color-error)]">Delete Account</h4>
                        <p class="mt-0.5 text-xs text-[var(--color-text-secondary)]">Once deleted, all your data will be permanently removed.</p>
                    </div>
                    <x-ui.button
                        variant="danger"
                        x-data
                        @click="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="text-xs shrink-0"
                    >
                        Delete Account
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Delete Account Modal --}}
<div id="delete-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('delete-modal').classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md rounded-[var(--radius-xl)] border border-[var(--color-divider)] bg-[var(--color-bg-primary)] p-6">
        <h3 class="text-lg font-bold text-[var(--color-text-primary)] mb-2">Are you absolutely sure?</h3>
        <p class="text-sm text-[var(--color-text-secondary)] mb-6">Please enter your password to confirm. This action cannot be undone.</p>

        <form action="{{ route('profile.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="space-y-4">
                <x-ui.input name="password" type="password" placeholder="Confirm Password" />
                <div class="flex gap-3">
                    <x-ui.button variant="secondary" type="button" class="flex-1" onclick="document.getElementById('delete-modal').classList.add('hidden')">Cancel</x-ui.button>
                    <x-ui.button variant="danger" type="submit" class="flex-1">Delete Forever</x-ui.button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
