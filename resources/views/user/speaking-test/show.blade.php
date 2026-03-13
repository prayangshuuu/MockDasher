@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Speaking Test: {{ $attempt->test->title }}</h1>
            <p class="text-gray-500 mt-1">Please ensure your microphone is working properly.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200 shadow-sm">
                <i class="fas fa-circle text-xs mr-2 animate-pulse"></i> In Progress
            </span>
        </div>
    </div>

    @if($speakingQuestions->isEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 shadow-sm rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                </div>
                <div class="ml-3 text-sm text-yellow-700">
                    <p>No speaking questions are currently assigned to this test module.</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white shadow border border-gray-200 rounded-lg overflow-hidden">
            <!-- Tabs for Parts -->
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex space-x-6">
                @foreach([1, 2, 3] as $partNum)
                    @if(isset($parts[$partNum]))
                        <h2 class="text-lg font-bold {{ $loop->first ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500' }} pb-2">
                            Part {{ $partNum }}
                        </h2>
                    @endif
                @endforeach
            </div>

            <div class="p-8">
                <!-- Simple sequential rendering for demonstration. A real app would likely use JS to paginate. -->
                @foreach($parts as $partNum => $questions)
                    <div class="mb-10 last:mb-0">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Speaking Part {{ $partNum }}</h3>
                        
                        <div class="space-y-8">
                            @foreach($questions as $index => $question)
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-100">
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="font-semibold text-lg text-gray-900">Question {{ $index + 1 }}</h4>
                                        @if($question->time_limit)
                                            <span class="text-sm font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded"><i class="fas fa-clock mr-1"></i> {{ $question->time_limit }}s target</span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-gray-700 text-lg mb-6 leading-relaxed">{{ $question->question_text }}</p>

                                    @if($question->audio_path)
                                        <div class="mb-6 bg-white p-3 rounded border border-gray-200 inline-block">
                                            <p class="text-xs font-bold text-gray-500 uppercase mb-2">Examiner Audio</p>
                                            <audio controls class="h-10 outline-none">
                                                <source src="{{ Storage::url($question->audio_path) }}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                    @endif

                                    <!-- Mock Recording Interface -->
                                    <div class="border-t border-gray-200 pt-4 mt-2">
                                        <p class="text-sm font-medium text-gray-500 mb-3">Your Response (Simulated)</p>
                                        <div class="flex items-center space-x-4">
                                            <button type="button" class="flex items-center justify-center w-12 h-12 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm">
                                                <i class="fas fa-microphone"></i>
                                            </button>
                                            <div class="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-red-500 w-0 transition-all duration-300"></div>
                                            </div>
                                            <span class="text-sm font-mono text-gray-500">00:00</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end">
                <form action="{{ route('user.speaking.submit', $attempt->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-sm transition">
                        Finish Speaking Module
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
