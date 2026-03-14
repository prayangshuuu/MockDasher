<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $tests = \App\Models\Test::with('collection')
                    ->withCount(['writingTasks', 'speakingQuestions', 'listeningSections', 'readingPassages'])
                    ->latest()
                    ->paginate(15);
        return view('admin.tests.index', compact('tests'));
    }

    public function create(Request $request)
    {
        $collections = \App\Models\IeltsCollection::orderBy('title')->get();
        $selectedCollectionId = $request->query('collection');
        return view('admin.tests.create', compact('collections', 'selectedCollectionId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ielts_collection_id' => 'nullable|exists:ielts_collections,id',
            'title' => 'required|string|max:255',
            'number' => 'required|integer',
            'status' => 'required|in:draft,published',
        ]);

        /** @var \App\Models\Test $test */
        $test = \App\Models\Test::query()->create($validated);

        if ($request->has('from_collection') && $test->ielts_collection_id) {
            return redirect()->route('admin.collections.show', $test->ielts_collection_id)->with('success', 'Test created successfully.');
        }

        return redirect()->route('admin.tests.show', $test->id)->with('success', 'Test created successfully.');
    }

    public function show(\App\Models\Test $test)
    {
        $test->load(['collection', 'writingTasks', 'speakingQuestions', 'listeningSections', 'readingPassages']);
        return view('admin.tests.show', compact('test'));
    }

    public function edit(\App\Models\Test $test)
    {
        $collections = \App\Models\IeltsCollection::orderBy('title')->get();
        return view('admin.tests.edit', compact('test', 'collections'));
    }

    public function update(Request $request, \App\Models\Test $test)
    {
        $validated = $request->validate([
            'ielts_collection_id' => 'nullable|exists:ielts_collections,id',
            'title' => 'required|string|max:255',
            'number' => 'required|integer',
            'status' => 'required|in:draft,published',
        ]);

        $test->update($validated);

        return redirect()->route('admin.tests.show', $test->id)->with('success', 'Test updated successfully.');
    }

    public function destroy(\App\Models\Test $test)
    {
        $collectionId = $test->ielts_collection_id;
        $test->delete();

        if (request()->has('redirect_to_collection') && $collectionId) {
            return redirect()->route('admin.collections.show', $collectionId)->with('success', 'Test deleted successfully.');
        }

        return redirect()->route('admin.tests.index')->with('success', 'Test deleted successfully.');
    }
}
