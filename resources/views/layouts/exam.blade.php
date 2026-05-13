<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IELTS Simulation') - MockDasher</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    


    @stack('styles')
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 font-sans antialiased text-slate-900 dark:text-slate-100 overflow-hidden flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="h-16 flex-shrink-0 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between z-50 shadow-sm">
        <!-- Left: Brand & Info -->
        <div class="flex items-center gap-4 w-1/3">
            <div class="size-10 exam-gradient rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined font-bold text-2xl">menu_book</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-primary uppercase tracking-widest leading-none mb-1">@yield('test_type', 'IELTS Simulation')</p>
                <h1 class="text-sm font-bold text-slate-900 dark:text-white truncate max-w-[240px]">@yield('test_title', 'Mock Test')</h1>
            </div>
        </div>

        <!-- Center: Dynamic Timer -->
        <div class="flex items-center justify-center w-1/3 px-4">
            @yield('timer_area')
        </div>

        <!-- Right: Status & Actions -->
        <div class="flex items-center justify-end gap-6 w-1/3">
            @yield('top_right_actions')
            
            <div class="flex items-center gap-3 pl-6 border-l border-slate-200 dark:border-slate-800">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Candidate</p>
                    <p class="text-xs font-black text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                </div>
                <div class="size-10 rounded-full bg-slate-100 dark:bg-slate-800 border-2 border-white dark:border-slate-900 flex items-center justify-center text-primary font-black shadow-inner">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </div>
    </header>

    <!-- Main Dynamic Content -->
    <main class="flex-1 overflow-hidden relative flex flex-col">
        @yield('content')
    </main>

    <!-- Global Scripts -->
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
