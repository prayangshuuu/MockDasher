@extends('layouts.student')

@section('title', 'Mock Tests')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
    <img src="/storage/asset/icons/expand-more.svg" class="w-4 h-4 transform -rotate-90 opacity-50" alt=">" />
    <span class="font-semibold text-slate-900 dark:text-white">Mock Tests</span>
</nav>
@endsection

@section('content')

<section class="mb-8">
    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Mock Tests</h2>
    <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Browse all available IELTS mock tests and start a new practice session.</p>
</section>

<section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($tests as $test)
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden flex flex-col hover:shadow-premium hover:border-primary/30 dark:hover:border-primary/30 transition-all duration-200">

            {{-- Accent stripe --}}
            <div class="h-1 bg-gradient-to-r from-primary to-violet-500"></div>

            <div class="p-6 flex flex-col flex-1">
                {{-- Badges --}}
                <div class="mb-4 flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-primary bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-full border border-indigo-100 dark:border-indigo-800">
                        <img src="/storage/asset/icons/verified.svg" class="w-3 h-3" alt="✓" />
                        {{ $test->exam_type }}
                    </span>
                    @if($test->year)
                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">{{ $test->year }}</span>
                    @endif
                </div>

                {{-- Title --}}
                <h4 class="text-lg font-bold text-slate-900 dark:text-white">
                    IELTS {{ $test->exam_type }} — Vol. {{ $test->book_number ?? 'N/A' }}
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Cambridge {{ $test->exam_type }} · Book {{ $test->book_number ?? 'N/A' }}
                </p>

                {{-- Meta --}}
                <div class="mt-4 flex items-center gap-5 text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <img src="/storage/asset/icons/history.svg" class="w-4 h-4 opacity-50" alt="Duration" />
                        <span class="text-xs font-semibold">2h 45m</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <img src="/storage/asset/icons/section.svg" class="w-4 h-4 opacity-50" alt="Modules" />
                        <span class="text-xs font-semibold">4 Modules</span>
                    </div>
                </div>

                {{-- Module pills --}}
                <div class="mt-4 flex gap-1.5">
                    @foreach([
                        ['L', 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400', 'Listening'],
                        ['R', 'bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400', 'Reading'],
                        ['W', 'bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400', 'Writing'],
                        ['S', 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400', 'Speaking'],
                    ] as [$mod, $cls, $title])
                        <span class="flex size-6 items-center justify-center rounded-lg {{ $cls }} text-[9px] font-black" title="{{ $title }}">{{ $mod }}</span>
                    @endforeach
                </div>

                {{-- CTA --}}
                <div class="mt-auto pt-5">
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-soft hover:shadow-premium transition-all duration-200">
                            <img src="/storage/asset/icons/start.svg" class="w-4 h-4 invert brightness-0" alt="Start" />
                            Start Test
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft px-6 py-16 flex flex-col items-center justify-center text-center">
                <div class="size-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <img src="/storage/asset/icons/library.svg" class="w-8 h-8 opacity-30" alt="No tests" />
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">No tests available</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">No mock tests are currently published. Check back later.</p>
            </div>
        </div>
    @endforelse
</section>

@endsection
