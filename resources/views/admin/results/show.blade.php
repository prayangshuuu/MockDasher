@extends('layouts.admin')

@section('title', 'Attempt Details')
@section('header', 'Test Attempt Details')
@section('subheader', 'User: ' . optional($result->user)->name . ' | Test: ' . optional($result->test)->title)

@section('header_actions')
    <a href="{{ route('admin.results.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Results
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <i class="fas fa-user fa-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Candidate</p>
                <p class="text-lg font-bold text-gray-900">{{ optional($result->user)->name ?? 'Unknown' }}</p>
                <p class="text-xs text-gray-500">{{ optional($result->user)->email }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <i class="fas fa-file-alt fa-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Test Info</p>
                <p class="text-lg font-bold text-gray-900">{{ optional($result->test)->title ?? 'Unknown' }}</p>
                <p class="text-xs text-gray-500">Attempted on: {{ $result->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <i class="fas fa-check-circle fa-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Status</p>
                <p class="text-lg font-bold text-gray-900">{{ ucfirst($result->status) }}</p>
                <p class="text-xs text-gray-500">Overall Score: <span class="font-semibold">{{ $result->overall_band ?? 'N/A' }}</span></p>
            </div>
        </div>
    </div>

    <!-- Detailed Module Results (Placeholder/Structure) -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Module Breakdown</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-500 text-sm mb-4">Detailed answers and scoring breakdown will be displayed here.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Reading Score -->
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-gray-800">Reading Module</h4>
                        <p class="text-sm text-gray-500">Band Score</p>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $result->reading_band ?? 'N/A' }}</div>
                </div>

                <!-- Listening Score -->
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-gray-800">Listening Module</h4>
                        <p class="text-sm text-gray-500">Band Score</p>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $result->listening_band ?? 'N/A' }}</div>
                </div>

                <!-- Writing Score -->
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-gray-800">Writing Module</h4>
                        <p class="text-sm text-gray-500">Band Score</p>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $result->writing_band ?? 'Pending Evaluation' }}</div>
                </div>

                <!-- Speaking Score -->
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-gray-800">Speaking Module</h4>
                        <p class="text-sm text-gray-500">Band Score</p>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $result->speaking_band ?? 'Pending Evaluation' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
