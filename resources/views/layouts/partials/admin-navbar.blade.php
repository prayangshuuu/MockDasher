<!-- Header / Top Bar -->
<header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 md:px-8 py-3 flex items-center justify-between">
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Mobile Menu Toggle -->
        <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-base transition-colors">
            <span class="material-symbols-outlined">menu</span>
        </button>
        @yield('breadcrumbs')
    </div>

    <div class="flex items-center gap-4">
        <div class="relative hidden sm:block">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input type="text" placeholder="Search resources..." class="pl-11 pr-4 py-1.5 bg-slate-100 dark:bg-slate-800 border-none rounded-base text-sm w-64 focus:ring-2 focus:ring-primary/20">
        </div>
        
        <button class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
        </button>

        <div class="h-8 w-px bg-slate-200 dark:bg-slate-800 mx-1"></div>

        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 pl-2 hover:opacity-80 transition-opacity">
            <div class="text-right hidden md:block">
                <p class="text-xs font-bold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-black">ID: {{ auth()->user()->id }}-{{ auth()->user()->isAdmin() ? 'ADMIN' : 'STUD' }}</p>
            </div>
            <div class="size-9 bg-primary/10 rounded-full border-2 border-white dark:border-slate-700 shadow-sm overflow-hidden">
                <img class="w-full h-full object-cover" src="{{ auth()->user()->getAvatarUrl() }}" alt="{{ auth()->user()->name }}"/>
            </div>
        </a>
    </div>
</header>
