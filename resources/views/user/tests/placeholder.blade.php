<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modules - User</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-7xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Test {{ $test->title }} - Modules</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center justify-center min-h-[200px] border border-gray-200 opacity-50">
                <h2 class="text-xl font-bold text-gray-600 mb-4">Listening</h2>
                <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded text-sm font-semibold">Coming Soon</span>
            </div>
            
            <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center justify-center min-h-[200px] border border-gray-200 opacity-50">
                <h2 class="text-xl font-bold text-gray-600 mb-4">Reading</h2>
                <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded text-sm font-semibold">Coming Soon</span>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 flex flex-col items-center justify-center min-h-[200px] border-t-4 border-blue-500 hover:shadow-lg transition">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Writing</h2>
                <form action="{{ url('/tests/' . $test->id . '/start') }}" method="POST">
                    @csrf
                    <input type="hidden" name="module" value="writing">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">Start Module</button>
                </form>
            </div>

            <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center justify-center min-h-[200px] border border-gray-200 opacity-50">
                <h2 class="text-xl font-bold text-gray-600 mb-4">Speaking</h2>
                <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded text-sm font-semibold">Coming Soon</span>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
