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
<body class="bg-dwimik-bg text-dwimik-text font-sans antialiased overflow-hidden">
    <!-- Navbar -->
    <nav class="bg-dwimik-primary text-white shadow-sm fixed w-full z-10 top-0 h-16">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold tracking-wider">MockDasher CMS</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-transparent border border-white hover:bg-white hover:text-dwimik-primary text-white px-3 py-1.5 rounded-dwimik text-sm font-medium transition duration-150">
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
        <aside class="w-64 bg-white flex-shrink-0 h-full overflow-y-auto border-r border-dwimik-divider">
            <div class="py-6 px-4">
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-dwimik {{ request()->routeIs('admin.dashboard') ? 'bg-dwimik-primary text-white' : 'text-dwimik-text hover:bg-dwimik-bg hover:text-dwimik-primary' }} transition">
                        <i class="fas fa-home w-6 text-center mr-2 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-dwimik-primary' }}"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-dwimik {{ request()->routeIs('admin.tests.*') ? 'bg-dwimik-primary text-white' : 'text-dwimik-text hover:bg-dwimik-bg hover:text-dwimik-primary' }} transition">
                        <i class="fas fa-file-alt w-6 text-center mr-2 {{ request()->routeIs('admin.tests.*') ? 'text-white' : 'text-gray-400 group-hover:text-dwimik-primary' }}"></i>
                        Tests
                    </a>
                    
                    <div class="pt-4 pb-2">
                        <span class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Modules Management</span>
                    </div>

                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-dwimik text-dwimik-text hover:bg-dwimik-bg hover:text-dwimik-primary transition">
                        <i class="fas fa-pen-nib w-6 text-center mr-2 text-gray-400 group-hover:text-dwimik-primary"></i>
                        Writing Tasks
                    </a>
                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-dwimik text-dwimik-text hover:bg-dwimik-bg hover:text-dwimik-primary transition">
                        <i class="fas fa-microphone w-6 text-center mr-2 text-gray-400 group-hover:text-dwimik-primary"></i>
                        Speaking Module
                    </a>
                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-dwimik text-dwimik-text hover:bg-dwimik-bg hover:text-dwimik-primary transition">
                        <i class="fas fa-headphones w-6 text-center mr-2 text-gray-400 group-hover:text-dwimik-primary"></i>
                        Listening Module
                    </a>
                    <a href="{{ route('admin.tests.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-dwimik text-dwimik-text hover:bg-dwimik-bg hover:text-dwimik-primary transition">
                        <i class="fas fa-book-open w-6 text-center mr-2 text-gray-400 group-hover:text-dwimik-primary"></i>
                        Reading Module
                    </a>

                    <div class="pt-4 pb-2">
                        <span class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</span>
                    </div>

                    <a href="{{ route('admin.users.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-dwimik {{ request()->routeIs('admin.users.*') ? 'bg-dwimik-primary text-white' : 'text-dwimik-text hover:bg-dwimik-bg hover:text-dwimik-primary' }} transition">
                        <i class="fas fa-users w-6 text-center mr-2 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-400 group-hover:text-dwimik-primary' }}"></i>
                        Users
                    </a>
                    <a href="{{ route('admin.results.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-dwimik {{ request()->routeIs('admin.results.*') ? 'bg-dwimik-primary text-white' : 'text-dwimik-text hover:bg-dwimik-bg hover:text-dwimik-primary' }} transition">
                        <i class="fas fa-chart-bar w-6 text-center mr-2 {{ request()->routeIs('admin.results.*') ? 'text-white' : 'text-gray-400 group-hover:text-dwimik-primary' }}"></i>
                        Results
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-dwimik-bg pb-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center border-b border-dwimik-divider pb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-dwimik-text">@yield('header', 'Dashboard')</h1>
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
