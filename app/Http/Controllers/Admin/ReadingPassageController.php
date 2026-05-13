<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadingPassage;
use App\Models\TestSet;
use Illuminate\Http\Request;

class ReadingPassageController extends Controller
{
    /**
     * Show the Reading Manager — lists all existing passages with question counts,
     * and provides a quick-add form at the bottom.
     */
    public function create($testSetId)
    {
        $testSet = TestSet::with(['test'])->findOrFail($testSetId);
        $passages = ReadingPassage::where('test_set_id', $testSet->id)
            ->with(['questionGroups' => fn ($q) => $q->with('questions.options')->orderBy('sort_order')])
            ->orderBy('passage_number')
            ->get();

        return view('admin.reading-passages.create', compact('testSet', 'passages'));
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

        return redirect()->route('admin.reading-passages.create', $testSetId)->with('success', 'Reading passage added successfully.');
    }

    public function edit(ReadingPassage $reading_passage)
    {
        $reading_passage->load(['testSet.test', 'questionGroups' => fn ($q) => $q->with('questions.options')->orderBy('sort_order')]);

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

        return redirect()->route('admin.reading-passages.edit', $reading_passage->id)->with('success', 'Reading passage updated successfully.');
    }

    public function destroy(ReadingPassage $reading_passage)
    {
        $testSetId = $reading_passage->test_set_id;
        $reading_passage->delete();

        return redirect()->route('admin.reading-passages.create', $testSetId)->with('success', 'Reading passage deleted successfully.');
    }
}
