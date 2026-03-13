@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
        
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between px-4 sm:px-0">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:truncate">
                    Account Settings
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage your profile information, security, and account preferences.
                </p>
            </div>
        </div>

        @if(session('status'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mx-4 sm:mx-0" role="alert">
                <p class="font-medium">Success</p>
                <p>Profile information updated successfully.</p>
            </div>
        @endif

        @if(session('status') == 'password-updated')
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mx-4 sm:mx-0" role="alert">
                <p class="font-medium">Success</p>
                <p>Password updated successfully.</p>
            </div>
        @endif
        
        @if(session('status') == 'two-factor-authentication-enabled')
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mx-4 sm:mx-0" role="alert">
                <p class="font-medium">Success</p>
                <p>Two factor authentication has been enabled.</p>
            </div>
        @endif

        @if(session('status') == 'two-factor-authentication-disabled')
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm mx-4 sm:mx-0" role="alert">
                <p class="font-medium">Notice</p>
                <p>Two factor authentication has been disabled.</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm mx-4 sm:mx-0">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Information -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-100">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Profile Information</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Update your account's profile information and details.</p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                        
                        <!-- Profile Photo -->
                        <div class="sm:col-span-6 flex items-center mb-4">
                            <div class="mr-4">
                                @if($user->profile_photo_path)
                                    <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Profile Photo" class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
                                @else
                                    <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl border-2 border-transparent">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                                <input type="file" name="photo" accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-3">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <!-- Country -->
                        <div class="sm:col-span-2">
                            <label for="country" class="block text-sm font-medium text-gray-700">Country (Optional)</label>
                            <input type="text" name="country" id="country" value="{{ old('country', $user->country) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="e.g. Canada">
                        </div>

                        <!-- Target Band Score -->
                        <div class="sm:col-span-2">
                            <label for="target_band_score" class="block text-sm font-medium text-gray-700">Target Band Score</label>
                            <input type="number" step="0.5" min="0" max="9" name="target_band_score" id="target_band_score" value="{{ old('target_band_score', $user->target_band_score) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="e.g. 7.5">
                        </div>

                        <!-- Exam Type -->
                        <div class="sm:col-span-2">
                            <label for="exam_type" class="block text-sm font-medium text-gray-700">Exam Type</label>
                            <select name="exam_type" id="exam_type" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option value="">Select...</option>
                                <option value="Academic" {{ old('exam_type', $user->exam_type) == 'Academic' ? 'selected' : '' }}>Academic</option>
                                <option value="General" {{ old('exam_type', $user->exam_type) == 'General' ? 'selected' : '' }}>General Training</option>
                            </select>
                        </div>

                    </div>
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-100">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Update Password</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Ensure your account is using a long, random password to stay secure.</p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('user-password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                        <div class="sm:col-span-4">
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" name="current_password" id="current_password" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <div class="sm:col-span-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <div class="sm:col-span-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Two Factor Authentication -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-100">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Two Factor Authentication</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Add additional security to your account using two factor authentication.</p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if(! auth()->user()->two_factor_secret)
                    <p class="text-sm text-gray-600 mb-4">When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.</p>
                    <form action="/user/two-factor-authentication" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900 transition">
                            Enable 2FA
                        </button>
                    </form>
                @else
                    <div class="bg-green-50 border border-green-200 p-4 rounded mb-6">
                        <p class="text-green-800 font-semibold mb-2"><i class="fas fa-check-circle mr-2"></i> Two factor authentication is enabled.</p>
                        <p class="text-sm text-green-700">Scan the following QR code using your phone's authenticator application.</p>
                        <div class="mt-4 bg-white p-2 inline-block rounded shadow-sm">
                            {!! auth()->user()->twoFactorQrCodeSvg() !!}
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm font-semibold text-gray-800 mb-2">Recovery Codes</p>
                        <p class="text-sm text-gray-600 mb-3">Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.</p>
                        <div class="bg-gray-100 rounded p-4 font-mono text-xs space-y-1">
                            @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                <div>{{ $code }}</div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <!-- Regenerate Tokens -->
                        <form action="/user/two-factor-recovery-codes" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                                Regenerate Recovery Codes
                            </button>
                        </form>

                        <!-- Disable 2FA -->
                        <form action="/user/two-factor-authentication" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition" onclick="return confirm('Are you sure you want to disable 2FA?');">
                                Disable 2FA
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Delete Account -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-red-100">
            <div class="px-4 py-5 sm:px-6 bg-red-50 border-b border-red-100">
                <h3 class="text-lg leading-6 font-medium text-red-800">Delete Account</h3>
                <p class="mt-1 max-w-2xl text-sm text-red-600">Permanently delete your account.</p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <p class="text-sm text-gray-500 mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
                
                <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-4 sm:w-1/2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password" id="password" class="focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Enter password to confirm" required>
                    </div>
                    
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                        Delete Account
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
