<!-- Fixed Side Navigation -->
<aside class="fixed left-0 top-0 h-screen w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col z-50">
    <div class="p-6 flex items-center gap-3">
        <div class="size-9 bg-primary rounded-lg flex items-center justify-center text-white">
            <span class="material-symbols-outlined text-2xl">auto_awesome</span>
        </div>
        <div>
            <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white leading-none">MockDasher</h1>
            <p class="text-xs font-medium text-primary uppercase tracking-wider mt-1">Admin Panel</p>
        </div>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1">
        <x-nav-link-admin href="{{ route('admin.dashboard') }}" icon="dashboard" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link-admin>
        <x-nav-link-admin href="{{ route('admin.tests.index') }}" icon="description" :active="request()->routeIs('admin.tests.*')">Tests</x-nav-link-admin>
        <x-nav-link-admin href="{{ route('admin.users.index') }}" icon="group" :active="request()->routeIs('admin.users.*')">Students</x-nav-link-admin>
        <x-nav-link-admin href="{{ route('admin.results.index') }}" icon="bar_chart" :active="request()->routeIs('admin.results.*')">Reports</x-nav-link-admin>
        
        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
            <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Account</p>
            <x-nav-link-admin href="{{ route('profile.show') }}" icon="settings">Settings</x-nav-link-admin>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-red-500 rounded-lg transition-colors group">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </nav>

    <div class="p-4">
        <div class="bg-primary/5 rounded-xl p-4 border border-primary/10">
            <p class="text-xs font-semibold text-primary mb-1">System Health</p>
            <p class="text-[11px] text-slate-500 mb-3">All services are running normally.</p>
            <div class="flex items-center gap-2">
                <div class="size-2 bg-emerald-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">v2.4.0 Online</span>
            </div>
        </div>
    </div>
</aside>
