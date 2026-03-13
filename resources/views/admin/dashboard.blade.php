<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MockDasher</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen">
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <span class="text-xl font-bold text-blue-600">MockDasher Admin</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">{{ auth()->user()->name }} (Admin)</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900 font-medium">Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Available Tests</h2>
                    <ul class="divide-y divide-gray-200">
                        @forelse($tests as $test)
                            <li class="py-4 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $test->title }} (Test {{ $test->number }})</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.writing-tasks.create', $test->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Add Writing Task
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="py-4 text-gray-500 text-sm">No tests available.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
