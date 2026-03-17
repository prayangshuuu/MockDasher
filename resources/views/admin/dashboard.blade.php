@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
<!-- Dashboard Content -->
<div class="p-8 max-w-7xl mx-auto w-full space-y-8">
    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">assignment</span>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Tests</h3>
            <p class="text-2xl font-bold mt-1 tracking-tight">{{ number_format($stats['total_tests'] ?? 0) }}</p>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">folder_copy</span>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded-full">+5%</span>
            </div>
            <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Test Sets</h3>
            <p class="text-2xl font-bold mt-1 tracking-tight">{{ number_format($stats['total_test_sets'] ?? 0) }}</p>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600">
                    <span class="material-symbols-outlined">person_celebrate</span>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded-full">+18%</span>
            </div>
            <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Active Users</h3>
            <p class="text-2xl font-bold mt-1 tracking-tight">{{ number_format($stats['users'] ?? 0) }}</p>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center text-orange-600">
                    <span class="material-symbols-outlined">trending_up</span>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded-full">+22%</span>
            </div>
            <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Attempts</h3>
            <p class="text-2xl font-bold mt-1 tracking-tight">{{ number_format($stats['attempts'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Main Grid: Chart and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Usage Analytics Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-200 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-none">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-lg font-bold">Usage Analytics</h2>
                    <p class="text-sm text-slate-400">Weekly Engagement Levels</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold text-slate-900 dark:text-slate-100">94.2%</span>
                    <span class="text-sm font-semibold text-green-500 flex items-center">+4.3%</span>
                </div>
            </div>
            <!-- Chart SVG -->
            <div class="relative h-64 w-full">
                <svg class="w-full h-full" viewbox="0 0 800 240" preserveAspectRatio="none">
                    <defs>
                        <lineargradient id="chartGradient" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="5%" stop-color="#5048e5" stop-opacity="0.3"></stop>
                            <stop offset="95%" stop-color="#5048e5" stop-opacity="0"></stop>
                        </lineargradient>
                    </defs>
                    <path d="M0,200 Q100,180 200,120 T400,100 T600,60 T800,40 V240 H0 Z" fill="url(#chartGradient)"></path>
                    <path d="M0,200 Q100,180 200,120 T400,100 T600,60 T800,40" fill="none" stroke="#5048e5" stroke-linecap="round" stroke-width="4"></path>
                    <circle cx="200" cy="120" fill="#5048e5" r="6" stroke="white" stroke-width="2"></circle>
                    <circle cx="400" cy="100" fill="#5048e5" r="6" stroke="white" stroke-width="2"></circle>
                    <circle cx="600" cy="60" fill="#5048e5" r="6" stroke="white" stroke-width="2"></circle>
                </svg>
                <div class="flex justify-between mt-4 px-2">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-tighter">Mon</span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-tighter">Tue</span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-tighter">Wed</span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-tighter">Thu</span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-tighter">Fri</span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-tighter">Sat</span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-tighter">Sun</span>
                </div>
            </div>
        </div>

        <!-- Side Card: Quick Actions / Stats -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-primary to-indigo-700 p-8 rounded-xl text-white shadow-xl shadow-primary/30 relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-lg font-bold opacity-90 mb-2">Create New Test</h3>
                    <p class="text-sm opacity-80 mb-6 leading-relaxed">Publish new Mock tests with automated grading algorithms.</p>
                    <a href="{{ route('admin.tests.create') }}" class="inline-block bg-white text-primary px-6 py-2.5 rounded-lg text-sm font-bold shadow-sm hover:bg-slate-50 transition-colors">
                        Get Started
                    </a>
                </div>
                <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-9xl opacity-10">auto_awesome</span>
            </div>
            
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800">
                <h4 class="font-bold text-sm mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">electric_bolt</span>
                    System Health
                </h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-500">API Latency</span>
                        <span class="text-xs font-bold text-green-500">24ms</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-green-500 h-full w-[94%]"></div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs font-medium text-slate-500">Database Load</span>
                        <span class="text-xs font-bold text-slate-900 dark:text-slate-100">12%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-primary h-full w-[12%]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tests Table -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold">Recent Tests</h2>
                <p class="text-sm text-slate-400">Latest activity from your dashboard</p>
            </div>
            <a href="{{ route('admin.tests.index') }}" class="text-primary text-sm font-bold hover:underline">View All</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-[10px] font-bold tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Test Name</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($tests as $test)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                                        @if($test->exam_type == 'reading')
                                            <span class="material-symbols-outlined text-lg">menu_book</span>
                                        @elseif($test->exam_type == 'listening')
                                            <span class="material-symbols-outlined text-lg">hearing</span>
                                        @elseif($test->exam_type == 'writing')
                                            <span class="material-symbols-outlined text-lg">edit_note</span>
                                        @else
                                            <span class="material-symbols-outlined text-lg">school</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-semibold text-sm">IELTS {{ ucfirst($test->exam_type) }} {{ $test->year }}</span>
                                        <p class="text-xs text-slate-500 mt-0.5">Book #{{ $test->book_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($test->status === 'published')
                                    <span class="px-2 py-1 rounded-md bg-green-50 dark:bg-green-900/30 text-green-600 text-[10px] font-bold uppercase tracking-wider">Published</span>
                                @else
                                    <span class="px-2 py-1 rounded-md bg-orange-50 dark:bg-orange-900/30 text-orange-600 text-[10px] font-bold uppercase tracking-wider">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tests.show', $test->id) }}" class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-500">
                                    <span class="material-symbols-outlined text-4xl mb-3 text-slate-300">folder_open</span>
                                    <p class="text-sm font-medium">No tests available yet.</p>
                                    <a href="{{ route('admin.tests.create') }}" class="text-primary text-xs font-bold mt-2 hover:underline">Create your first test</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
