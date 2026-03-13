@extends('layouts.admin')

@section('title', 'Edit Reading Passage')
@section('header', 'Edit Reading Passage')
@section('subheader', 'For test: ' . $reading_passage->test->title)

@section('header_actions')
    <a href="{{ route('admin.tests.show', $reading_passage->test_id) }}" class="text-gray-600 hover:text-gray-900 font-medium transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Test
    </a>
@endsection

@section('content')
    <div class="max-w-5xl">
        <form action="{{ route('admin.reading-passages.update', $reading_passage->id) }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Passage Number</label>
                    <select name="passage_number" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <option value="1" {{ $reading_passage->passage_number == 1 ? 'selected' : '' }}>Passage 1</option>
                        <option value="2" {{ $reading_passage->passage_number == 2 ? 'selected' : '' }}>Passage 2</option>
                        <option value="3" {{ $reading_passage->passage_number == 3 ? 'selected' : '' }}>Passage 3</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Passage Title / Heading</label>
                    <input type="text" name="title" value="{{ $reading_passage->title }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" required>
                </div>
            </div>

            <div class="mb-8 border border-gray-100 p-4 rounded bg-gray-50">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Passage Content</label>
                <p class="text-xs text-gray-500 mb-3">Include HTML tags (like &lt;p&gt;, &lt;h3&gt;, &lt;strong&gt;) for formatting.</p>
                <textarea name="content" rows="20" class="w-full font-mono text-sm border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" required>{{ $reading_passage->content }}</textarea>
            </div>

            <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this reading passage? All associated questions will be deleted.')) { document.getElementById('delete-passage').submit(); }" class="text-red-600 hover:text-red-800 font-medium text-sm transition">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Passage
                </button>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Passage
                </button>
            </div>
        </form>

        <form id="delete-passage" action="{{ route('admin.reading-passages.destroy', $reading_passage->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Questions</h3>
                    <p class="text-sm text-gray-600">Manage the questions associated with this reading passage.</p>
                </div>
                <a href="{{ route('admin.questions.create', ['type' => 'reading', 'id' => $reading_passage->id]) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded shadow-sm transition">
                    <i class="fas fa-plus mr-2"></i> Add Question
                </a>
            </div>

            @if($reading_passage->questions->isEmpty())
                <div class="bg-gray-50 border border-gray-200 rounded p-6 text-center text-gray-500">
                    No questions added yet.
                </div>
            @else
                <ul class="space-y-3">
                    @foreach($reading_passage->questions as $index => $q)
                        <li class="bg-white border border-gray-200 rounded p-4 flex justify-between items-start hover:shadow-sm transition">
                            <div>
                                <h4 class="font-medium text-gray-800">Q{{ $index + 1 }}. {{ \Illuminate\Support\Str::limit($q->question_text, 80) }}</h4>
                                <div class="flex items-center space-x-3 mt-2 text-xs text-gray-500">
                                    <span class="bg-gray-100 px-2 py-1 rounded">{{ ucwords(str_replace('_', ' ', $q->question_type)) }}</span>
                                    @if($q->question_type == 'multiple_choice')
                                        <span>{{ $q->options->count() }} options</span>
                                    @else
                                        <span>Answer: <strong class="text-gray-700">{{ $q->correct_answer }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.questions.edit', $q->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium"><i class="fas fa-edit"></i> Edit</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
