<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - MockDasher</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden">
    <!-- Navbar -->
    <nav class="bg-blue-800 text-white shadow-md fixed w-full z-10 top-0 h-16">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold tracking-wider">MockDasher CMS</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition duration-150">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar & Content -->
    <div class="flex h-screen pt-16">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex-shrink-0 h-full overflow-y-auto border-r border-gray-200">
            <div class="py-6 px-4">
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }} transition">
                        <i class="fas fa-home w-6 text-center mr-2 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.tests.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }} transition">
                        <i class="fas fa-file-alt w-6 text-center mr-2 {{ request()->routeIs('admin.tests.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        Tests
                    </a>
                    
                    <div class="pt-4 pb-2">
                        <span class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Modules Management</span>
                    </div>

                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.writing-tasks.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }} transition">
                        <i class="fas fa-pen-nib w-6 text-center mr-2 {{ request()->routeIs('admin.writing-tasks.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        Writing Tasks
                    </a>
                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.speaking-questions.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }} transition">
                        <i class="fas fa-microphone w-6 text-center mr-2 {{ request()->routeIs('admin.speaking-questions.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        Speaking Module
                    </a>
                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.listening-sections.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }} transition">
                        <i class="fas fa-headphones w-6 text-center mr-2 {{ request()->routeIs('admin.listening-sections.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        Listening Module
                    </a>
                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.reading-passages.*', 'admin.reading-question-groups.*', 'admin.questions.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }} transition">
                        <i class="fas fa-book-open w-6 text-center mr-2 {{ request()->routeIs('admin.reading-passages.*', 'admin.reading-question-groups.*', 'admin.questions.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        Reading Module
                    </a>

                    <div class="pt-4 pb-2">
                        <span class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</span>
                    </div>

                    <a href="{{ route('admin.users.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }} transition">
                        <i class="fas fa-users w-6 text-center mr-2 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        Users
                    </a>
                    <a href="{{ route('admin.results.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.results.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }} transition">
                        <i class="fas fa-chart-bar w-6 text-center mr-2 {{ request()->routeIs('admin.results.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        Results
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 pb-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center border-b border-gray-200 pb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">@yield('header', 'Dashboard')</h1>
                        @if(View::hasSection('subheader'))
                            <p class="mt-1 text-sm text-gray-500">@yield('subheader')</p>
                        @endif
                    </div>
                    <div>
                        @yield('header_actions')
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div id="admin-flash-success" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded shadow-sm flex items-center justify-between transition-all duration-500 ease-in-out">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
                            <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.style.opacity='0';setTimeout(()=>this.parentElement.remove(),500)" class="text-green-400 hover:text-green-600 ml-4">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div id="admin-flash-error" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded shadow-sm flex items-center justify-between transition-all duration-500 ease-in-out">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
                            <p class="text-red-700 text-sm font-medium">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.style.opacity='0';setTimeout(()=>this.parentElement.remove(),500)" class="text-red-400 hover:text-red-600 ml-4">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Page Content -->
                <div class="mt-4">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @stack('scripts')
    <script>
        setTimeout(function() {
            document.querySelectorAll('#admin-flash-success, #admin-flash-error').forEach(function(el) {
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 500);
            });
        }, 4000);
    </script>
</body>
</html>
