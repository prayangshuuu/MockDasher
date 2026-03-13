@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $test->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">Select a module to begin your practice session</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                <i class="fas fa-arrow-left text-xs"></i> Dashboard
            </a>
        </div>

        {{-- Module Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            {{-- LISTENING --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:border-teal-400 hover:shadow-md transition overflow-hidden group">
                <div class="h-1.5 bg-gradient-to-r from-teal-400 to-teal-600"></div>
                <div class="p-6 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-teal-50 rounded-full flex items-center justify-center mb-4 group-hover:bg-teal-100 transition">
                        <i class="fas fa-headphones text-teal-600 text-xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 mb-1">Listening</h2>
                    <p class="text-xs text-gray-500 mb-5">4 parts · 40 questions · ~30 min</p>
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="module" value="listening">
                        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow-sm">
                            Start Module
                        </button>
                    </form>
                </div>
            </div>

            {{-- READING --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden opacity-60">
                <div class="h-1.5 bg-gradient-to-r from-orange-300 to-orange-500"></div>
                <div class="p-6 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-book-open text-orange-500 text-xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-700 mb-1">Reading</h2>
                    <p class="text-xs text-gray-500 mb-5">3 passages · 40 questions · 60 min</p>
                    <span class="w-full text-center bg-gray-100 text-gray-500 font-semibold py-2 px-4 rounded-lg text-sm block">
                        Coming Soon
                    </span>
                </div>
            </div>

            {{-- WRITING --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:border-blue-400 hover:shadow-md transition overflow-hidden group">
                <div class="h-1.5 bg-gradient-to-r from-blue-400 to-blue-600"></div>
                <div class="p-6 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-100 transition">
                        <i class="fas fa-pen-alt text-blue-600 text-xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 mb-1">Writing</h2>
                    <p class="text-xs text-gray-500 mb-5">Task 1 + Task 2 · 60 min</p>
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="module" value="writing">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow-sm">
                            Start Module
                        </button>
                    </form>
                </div>
            </div>

            {{-- SPEAKING --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:border-green-400 hover:shadow-md transition overflow-hidden group">
                <div class="h-1.5 bg-gradient-to-r from-green-400 to-green-600"></div>
                <div class="p-6 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mb-4 group-hover:bg-green-100 transition">
                        <i class="fas fa-microphone-alt text-green-600 text-xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 mb-1">Speaking</h2>
                    <p class="text-xs text-gray-500 mb-5">Part 1, 2 & 3 · ~15 min</p>
                    <form action="{{ route('user.tests.start', $test->id) }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="module" value="speaking">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow-sm">
                            Start Module
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Info note --}}
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            Each module is independent. You can start and resume modules in any order. Answers are auto-saved during the exam.
        </div>
    </div>
</div>
@endsection
