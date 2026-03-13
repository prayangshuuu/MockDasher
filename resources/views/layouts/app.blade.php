<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MockDasher') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Build assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full flex flex-col text-gray-900 selection:bg-blue-500 selection:text-white">

    <!-- Top Navigation -->
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded flex items-center justify-center font-bold text-lg shadow-sm">
                                M
                            </div>
                            <span class="font-bold text-xl tracking-tight text-gray-900 hidden sm:block">MockDasher</span>
                        </a>
                    </div>
                    
                    <!-- Primary Nav Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm leading-5 transition">
                            Dashboard
                        </a>
                        <a href="{{ route('user.history.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('user.history.*') ? 'border-blue-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm leading-5 transition">
                            My History
                        </a>
                    </div>
                </div>

                <!-- Settings Dropdown / Profile -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="Profile" class="h-8 w-8 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="hidden sm:block">{{ explode(' ', auth()->user()->name)[0] }}</span>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 transition">Log Out</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Mobile Nav Menu (Simplified) -->
        <div class="sm:hidden flex justify-around border-t border-gray-100 bg-gray-50 py-2">
            <a href="{{ route('dashboard') }}" class="text-gray-600 font-medium text-sm {{ request()->routeIs('dashboard') ? 'text-blue-600' : '' }}">Home</a>
            <a href="{{ route('user.history.index') }}" class="text-gray-600 font-medium text-sm {{ request()->routeIs('user.history.*') ? 'text-blue-600' : '' }}">History</a>
            <a href="{{ route('profile.show') }}" class="text-gray-600 font-medium text-sm {{ request()->routeIs('profile.show') ? 'text-blue-600' : '' }}">Profile</a>
        </div>
    </nav>

    <!-- Global Flash Messages -->
    @if(session('success') || session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded shadow-sm flex items-center">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded shadow-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif
    </div>
    @endif

    <!-- Page Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Simple Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
            <div class="mb-4 md:mb-0">
                <span class="font-bold text-gray-700 flex items-center gap-1">
                    <span class="text-blue-600">Mock</span>Dasher
                </span>
                <span class="ml-2">&copy; {{ date('Y') }} All rights reserved.</span>
            </div>
            <div class="flex space-x-4">
                <a href="#" class="hover:text-blue-600 transition">Terms</a>
                <a href="#" class="hover:text-blue-600 transition">Privacy</a>
                <a href="mailto:support@mockdasher.test" class="hover:text-blue-600 transition">Support</a>
            </div>
        </div>
    </footer>

</body>
</html>
