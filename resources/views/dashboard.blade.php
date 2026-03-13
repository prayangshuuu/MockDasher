<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MockDasher</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen">
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <span class="text-xl font-bold">MockDasher User</span>
                        </div>
                    </div>
                    <div class="flex items-center">
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
                <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
                
                @if (session('success'))
                    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Available IELTS Collections</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse(\App\Models\IeltsCollection::with('tests')->get() as $collection)
                            <div class="bg-white rounded-lg shadow-sm border p-6">
                                <h3 class="font-bold text-lg mb-2">{{ $collection->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($collection->description, 100) }}</p>
                                
                                <div class="space-y-2">
                                    <h4 class="font-medium text-sm text-gray-700">Tests:</h4>
                                    <ul class="text-sm">
                                        @foreach($collection->tests as $test)
                                            <li class="flex justify-between items-center bg-gray-50 py-2 px-3 rounded mt-1">
                                                <span>{{ $test->title }} (Test {{ $test->number }})</span>
                                                <form action="{{ url('/tests/' . $test->id . '/start') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 hover:underline text-xs font-semibold">Take Test</button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center text-gray-500 bg-white shadow-sm border rounded">
                                No test collections are currently available. Check back later!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
