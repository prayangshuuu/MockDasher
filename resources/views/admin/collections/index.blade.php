<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">IELTS Collections</h1>
            <a href="{{ route('admin.collections.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Collection
            </a>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <ul class="divide-y divide-gray-200">
                @forelse($collections as $collection)
                    <li class="py-4 flex justify-between items-center">
                        <div>
                            <p class="text-lg font-medium text-gray-900">{{ $collection->title }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($collection->description, 100) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.tests.create', $collection->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                Add Test
                            </a>
                        </div>
                    </li>
                @empty
                    <li class="py-4 text-gray-500 text-sm">No collections available. Create one to get started!</li>
                @endforelse
            </ul>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-500 hover:underline">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
