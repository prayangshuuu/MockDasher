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
    <div class="max-w-5xl space-y-8">

        {{-- ── Passage Form ── --}}
        <form action="{{ route('admin.reading-passages.update', $reading_passage->id) }}" method="POST"
              class="bg-white shadow-sm border border-gray-200 rounded-lg p-8">
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
                    <input type="text" name="title" value="{{ $reading_passage->title }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" required>
                </div>
            </div>

            <div class="mb-8 border border-gray-100 p-4 rounded bg-gray-50">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Passage Content</label>
                <p class="text-xs text-gray-500 mb-3">HTML tags supported: &lt;p&gt;, &lt;h3&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;li&gt;. Use &lt;p&gt; for paragraphs.</p>
                <textarea name="content" rows="20"
                    class="w-full font-mono text-sm border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                    required>{{ $reading_passage->content }}</textarea>
            </div>

            <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                <button type="button" class="text-red-600 hover:text-red-800 font-medium text-sm"
                    onclick="if(confirm('Delete this passage? All question groups and questions will be deleted.')) document.getElementById('delete-passage').submit();">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Passage
                </button>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-md shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Passage
                </button>
            </div>
        </form>

        <form id="delete-passage" action="{{ route('admin.reading-passages.destroy', $reading_passage->id) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>

        {{-- ── Question Groups ── --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Question Groups</h3>
                    <p class="text-sm text-gray-500">
                        {{ $reading_passage->questionGroups->count() }} group(s) · 
                        {{ $reading_passage->questionGroups->sum(fn($g) => $g->questions->count()) }} total questions
                    </p>
                </div>
                <a href="{{ route('admin.reading-question-groups.create', $reading_passage->id) }}"
                   class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded shadow-sm transition text-sm">
                    <i class="fas fa-plus mr-2"></i> Add Question Group
                </a>
            </div>

            @if($reading_passage->questionGroups->isEmpty())
                <div class="bg-gray-50 border border-gray-200 rounded p-6 text-center text-gray-500">
                    <i class="fas fa-layer-group text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm">No question groups yet. Click "Add Question Group" to get started.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($reading_passage->questionGroups as $group)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-2 py-0.5 rounded">
                                            {{ ucwords(str_replace('_', ' ', $group->question_type)) }}
                                        </span>
                                        <span class="text-xs text-gray-400">Sort: {{ $group->sort_order }}</span>
                                    </div>
                                    @if($group->group_instruction)
                                        <p class="text-sm text-gray-700 mb-2 italic">"{{ \Illuminate\Support\Str::limit($group->group_instruction, 100) }}"</p>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ $group->questions->count() }} question(s)</p>
                                </div>
                                <a href="{{ route('admin.reading-question-groups.edit', $group->id) }}"
                                   class="ml-4 flex-shrink-0 text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                                    <i class="fas fa-edit"></i> Manage
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endsection
