<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadingPassage;
use App\Models\TestSet;
use Illuminate\Http\Request;

class ReadingPassageController extends Controller
{
    public function create($testSetId)
    {
        $testSet = TestSet::findOrFail($testSetId);

        return view('admin.reading-passages.create', compact('testSet'));
    }

    public function store(Request $request, $testSetId)
    {
        $testSet = TestSet::findOrFail($testSetId);

        $validated = $request->validate([
            'passage_number' => 'required|in:1,2,3',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $testSet->readingPassages()->create($validated);

        return redirect()->route('admin.test_sets.show', $testSetId)->with('success', 'Reading passage added successfully.');
    }

    public function edit(ReadingPassage $reading_passage)
    {
        return view('admin.reading-passages.edit', compact('reading_passage'));
    }

    public function update(Request $request, ReadingPassage $reading_passage)
    {
        $validated = $request->validate([
            'passage_number' => 'required|in:1,2,3',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $reading_passage->update($validated);

        return redirect()->route('admin.test_sets.show', $reading_passage->test_set_id)->with('success', 'Reading passage updated successfully.');
    }

    public function destroy(ReadingPassage $reading_passage)
    {
        $testSetId = $reading_passage->test_set_id;
        $reading_passage->delete();

        return redirect()->route('admin.test_sets.show', $testSetId)->with('success', 'Reading passage deleted successfully.');
    }
}
