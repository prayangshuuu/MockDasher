@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="mb-10">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">{{ __('messages.welcome', ['name' => explode(' ', $user->name)[0]]) }}</h2>
    <p class="text-slate-500 mt-2 font-medium">
        @if($daysToExam !== null)
            @if($daysToExam > 0)
                You are {{ $daysToExam }} days away from your IELTS exam. Focus on your writing module today.
            @elseif($daysToExam === 0)
                Today is your IELTS exam day! Good luck!
            @else
                Your IELTS exam was {{ abs($daysToExam) }} days ago.
            @endif
        @else
            Welcome to MockDasher. Set your exam date in settings to track your progress.
        @endif
    </p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm font-medium">{{ __('messages.target_score') }}</p>
            <p class="text-2xl font-bold mt-1 text-slate-900">{{ number_format($targetScore, 1) }} <span class="text-sm font-medium text-slate-400">/ 9.0</span></p>
        </div>
        <div class="relative size-14">
            <svg class="size-full -rotate-90" viewbox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                <circle class="stroke-slate-100" cx="18" cy="18" fill="none" r="16" stroke-width="3"></circle>
                <circle class="stroke-primary" cx="18" cy="18" fill="none" r="16" stroke-dasharray="100" stroke-dashoffset="{{ 100 - (min(9, $targetScore) / 9 * 100) }}" stroke-linecap="round" stroke-width="3"></circle>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center text-[10px] font-bold text-primary">{{ round((min(9, $targetScore) / 9) * 100) }}%</div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-100">
        <p class="text-slate-500 text-sm font-medium">{{ __('messages.tests_taken') }}</p>
        <div class="flex items-end justify-between mt-1">
            <p class="text-2xl font-bold text-slate-900">{{ $testsTakenCount }}</p>
            <span class="bg-emerald-50 text-emerald-600 text-[10px] px-2 py-0.5 rounded-full font-bold mb-1">+{{ $user->testAttempts()->where('created_at', '>=', now()->startOfWeek())->count() }} this week</span>
        </div>
        <div class="w-full bg-slate-100 h-1.5 rounded-full mt-4">
            <div class="bg-emerald-500 h-1.5 rounded-full" x-data="{ width: '{{ min(100, ($testsTakenCount / 20) * 100) }}%' }" :style="`width: ${width}`"></div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-100">
        <p class="text-slate-500 text-sm font-medium">{{ __('messages.avg_band_score') }}</p>
        <div class="flex items-end justify-between mt-1">
            <p class="text-2xl font-bold text-slate-900">{{ $avgBandScore !== null ? number_format($avgBandScore, 1) : 'N/A' }}</p>
            @if($avgBandScore !== null)
                <span class="bg-indigo-50 text-indigo-600 text-[10px] px-2 py-0.5 rounded-full font-bold mb-1">Band {{ number_format($avgBandScore, 1) }}</span>
            @endif
        </div>
        <div class="flex gap-1 mt-4">
            @for($i = 0; $i < 5; $i++)
                <div class="flex-1 h-3 {{ $avgBandScore !== null && $i < floor($avgBandScore - 2) ? 'bg-indigo-500' : 'bg-indigo-100' }} rounded-sm"></div>
            @endfor
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-100 border-l-4 border-l-orange-500">
        <p class="text-slate-500 text-sm font-medium">{{ __('messages.days_to_exam') }}</p>
        <p class="text-2xl font-bold mt-1 text-slate-900">
            {{ $daysToExam ?? 'N/A' }} 
            @if($user->exam_date)
                <span class="text-xs font-normal text-slate-400">{{ $user->exam_date->format('M d, Y') }}</span>
            @endif
        </p>
        <div class="flex items-center gap-1 mt-4 text-orange-600">
            <span class="material-symbols-outlined text-sm">schedule</span>
            <span class="text-[10px] font-bold uppercase tracking-widest">Time to intensity up</span>
        </div>
    </div>
</div>

<!-- Performance Insights Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
    <!-- Score Chart -->
    <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-layered border border-slate-100 relative overflow-hidden">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-lg font-bold text-slate-900">{{ __('messages.score_improvement') }}</h3>
                <p class="text-sm text-slate-400">Your progress over the last mock tests</p>
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200 hover:bg-slate-50">Monthly</button>
                <button class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-900 text-white shadow-lg">Weekly</button>
            </div>
        </div>
        
        <!-- Score Chart Visualization -->
        @if(count($chartData) > 0)
            <div class="h-64 flex items-end justify-between gap-4 px-2">
                @foreach($chartData as $data)
                    <div class="flex-1 bg-primary/{{ 10 * ($loop->index + 1) }} rounded-t-lg relative group transition-all duration-500" x-data="{ height: '{{ $data['height'] }}' }" :style="`height: ${height}`">
                        @if($loop->last)
                            <div class="absolute inset-0 indigo-gradient rounded-t-lg shadow-xl shadow-primary/20"></div>
                        @endif
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">{{ $data['score'] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                @foreach($chartData as $data)
                    <span>{{ $data['label'] }}</span>
                @endforeach
            </div>
        @else
            <div class="h-64 flex items-center justify-center">
                <div class="text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">bar_chart</span>
                    <p class="text-sm text-slate-400 font-medium">Complete a test to see your score progression</p>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Radar/Module Breakdown -->
    <div class="bg-white p-8 rounded-3xl shadow-layered border border-slate-100">
        <h3 class="text-lg font-bold text-slate-900 mb-6">{{ __('messages.module_breakdown') }}</h3>
        <div class="space-y-6">
            @foreach($moduleBreakdown as $module)
                <div>
                    <div class="flex justify-between text-xs font-bold mb-2">
                        <span class="text-slate-600">{{ $module['name'] }}</span>
                        @if($module['score'] !== null)
                            <span class="{{ $module['type'] === 'primary' ? 'text-primary' : 'text-slate-300' }}">{{ number_format($module['score'], 1) }}</span>
                        @else
                            <span class="text-slate-400">Pending</span>
                        @endif
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        @if($module['score'] !== null)
                            <div class="{{ $module['type'] === 'primary' ? 'bg-primary' : 'bg-slate-300' }} h-full rounded-full" x-data="{ width: '{{ $module['percentage'] }}%' }" :style="`width: ${width}`"></div>
                        @else
                            <div class="bg-slate-200 h-full rounded-full w-0"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <a href="{{ route('user.history.index') }}" class="block w-full mt-8 py-3 rounded-xl border-2 border-slate-100 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-colors text-center">
            View Detailed Analysis
        </a>
    </div>
</div>

<!-- Available Mock Tests Grid -->
<div class="mb-10">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-slate-900">{{ __('messages.recommended_mock_tests') }}</h3>
        <a class="text-primary font-bold text-sm hover:underline" href="{{ route('user.history.index') }}">View All</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($recommendedTests as $test)
            <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-100 hover-lift flex flex-col group">
                <div class="flex items-center justify-between mb-4">
                    <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">{{ $test->exam_type }}</span>
                    <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase">{{ __('messages.new') ?? 'New' }}</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">{{ $test->title }}</h4>
                <p class="text-sm text-slate-500 mb-6 line-clamp-2">{{ $test->exam_type }} — Book {{ $test->book_number }} ({{ $test->year }}). All four IELTS modules available.</p>
                <div class="mt-auto space-y-4">
                    <div class="flex items-center gap-3 text-slate-400">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">timer</span>
                            <span class="text-[11px] font-bold uppercase">{{ $test->duration ?? '2h 45m' }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">menu_book</span>
                            <span class="text-[11px] font-bold uppercase">{{ $test->modules_count ?? 4 }} Modules</span>
                        </div>
                    </div>
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 indigo-gradient text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/20 group-hover:scale-[1.02] transition-transform">
                            {{ __('messages.start_preparation') ?? 'Start Preparation' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-3 bg-white p-8 rounded-2xl shadow-soft border border-slate-100 flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-4xl text-slate-300 mb-3">auto_stories</span>
                <p class="text-slate-500 font-medium">No tests currently available. Check back later.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Recent Activity/History -->
<div class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-lg font-bold text-slate-900">{{ __('messages.recent_test_history') }}</h3>
        <button class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">more_horiz</span></button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Test Title</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date Taken</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Modules</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentAttempts as $attempt)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-base">task</span>
                                </div>
                                <span class="text-sm font-bold text-slate-700">{{ $attempt->testSet->test->title ?? 'Practice Test' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $attempt->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-1">
                                <span class="size-5 rounded bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600" title="Listening">L</span>
                                <span class="size-5 rounded bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600" title="Reading">R</span>
                                <span class="size-5 rounded bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600" title="Writing">W</span>
                                <span class="size-5 rounded bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600" title="Speaking">S</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($attempt->status == 'completed')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold">Completed</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-bold">In Progress</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('user.history.show', $attempt->id) }}" class="text-sm font-bold text-primary group-hover:underline">Review Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium">
                            No test history found. Start your first mock test today!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
