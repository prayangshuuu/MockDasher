<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestSet;

class TestSetController extends Controller
{
    public function show(TestSet $test_set)
    {
        $test_set->load(['test', 'writingTasks', 'speakingQuestions', 'listeningSections', 'readingPassages']);

        return view('admin.test_sets.show', compact('test_set'));
    }

    public function destroy(TestSet $test_set)
    {
        $test_set->delete();

        return back()->with('success', 'Test set deleted successfully.');
    }

    public function store(Test $test)
    {
        $nextNumber = ($test->testSets()->max('set_number') ?? 0) + 1;
        $test->testSets()->create(['set_number' => $nextNumber]);

        return back()->with('success', 'New test set added.');
    }
}
