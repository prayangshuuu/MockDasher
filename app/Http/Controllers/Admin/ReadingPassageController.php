<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReadingPassageController extends Controller
{
    public function create($testId)
    {
        $test = \App\Models\Test::findOrFail($testId);
        return view('admin.reading-passages.create', compact('test'));
    }

    public function store(Request $request, $testId)
    {
        $test = \App\Models\Test::findOrFail($testId);

        $validated = $request->validate([
            'passage_number' => 'required|in:1,2,3',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $test->readingPassages()->create($validated);

        return redirect()->route('admin.tests.show', $testId)->with('success', 'Reading passage added successfully.');
    }

    public function edit(\App\Models\ReadingPassage $reading_passage)
    {
        return view('admin.reading-passages.edit', compact('reading_passage'));
    }

    public function update(Request $request, \App\Models\ReadingPassage $reading_passage)
    {
        $validated = $request->validate([
            'passage_number' => 'required|in:1,2,3',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $reading_passage->update($validated);

        return redirect()->route('admin.tests.show', $reading_passage->test_id)->with('success', 'Reading passage updated successfully.');
    }

    public function destroy(\App\Models\ReadingPassage $reading_passage)
    {
        $testId = $reading_passage->test_id;
        $reading_passage->delete();

        return redirect()->route('admin.tests.show', $testId)->with('success', 'Reading passage deleted successfully.');
    }
}
