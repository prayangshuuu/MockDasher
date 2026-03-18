<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestSet;

class TestSetController extends Controller
{
    public function show(TestSet $test_set)
    {
        $test_set->load(['test', 'writingTasks', 'speakingQuestions', 'listeningSections', 'readingPassages']);

        return view('admin.test_sets.show', compact('test_set'));
    }
}
