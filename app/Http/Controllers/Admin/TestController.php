<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function create(\App\Models\IeltsCollection $collection)
    {
        return view('admin.tests.create', compact('collection'));
    }

    public function store(Request $request, \App\Models\IeltsCollection $collection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'number' => 'required|integer',
            'status' => 'required|in:draft,published',
        ]);

        $collection->tests()->create($validated);

        return redirect()->route('admin.collections.show', $collection->id)->with('success', 'Test created successfully.');
    }

    public function show(\App\Models\Test $test)
    {
        $test->load(['collection', 'writingTasks', 'speakingQuestions', 'listeningSections', 'readingPassages']);
        return view('admin.tests.show', compact('test'));
    }

    public function edit(\App\Models\Test $test)
    {
        return view('admin.tests.edit', compact('test'));
    }

    public function update(Request $request, \App\Models\Test $test)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'number' => 'required|integer',
            'status' => 'required|in:draft,published',
        ]);

        $test->update($validated);

        return redirect()->route('admin.collections.show', $test->ielts_collection_id)->with('success', 'Test updated successfully.');
    }

    public function destroy(\App\Models\Test $test)
    {
        $collectionId = $test->ielts_collection_id;
        $test->delete();
        return redirect()->route('admin.collections.show', $collectionId)->with('success', 'Test deleted successfully.');
    }
}
