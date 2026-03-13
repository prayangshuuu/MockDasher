@extends('layouts.admin')

@section('title', 'Edit User')
@section('header', 'Edit User: ' . $user->name)
@section('subheader', 'Update role or basic details.')

@section('header_actions')
    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Users
    </a>
@endsection

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Name</label>
                <input type="text" name="name" value="{{ $user->name }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Email Address</label>
                <input type="email" name="email" value="{{ $user->email }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>

            <div class="mb-8 p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                <label class="block text-yellow-800 text-sm font-semibold mb-2"><i class="fas fa-shield-alt mr-2"></i> User Role</label>
                <select name="role" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                    <option value="user" {{ !$user->hasRole('Admin') ? 'selected' : '' }}>Standard User</option>
                    <option value="admin" {{ $user->hasRole('Admin') ? 'selected' : '' }}>Administrator</option>
                </select>
                @if(auth()->id() === $user->id)
                    <p class="text-xs text-yellow-600 mt-2">You cannot change your own role.</p>
                    <input type="hidden" name="role" value="admin">
                @endif
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update User
                </button>
            </div>
        </form>
    </div>
@endsection
