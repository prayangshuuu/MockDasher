<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestSetController extends Controller
{
    public function show(\App\Models\TestSet $test_set)
    {
        $test_set->load(['test', 'writingTasks', 'speakingQuestions', 'listeningSections', 'readingPassages']);
        return view('admin.test_sets.show', compact('test_set'));
    }
}
