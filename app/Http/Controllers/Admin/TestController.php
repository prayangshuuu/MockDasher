<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $tests = \App\Models\Test::withCount('testSets')
                    ->latest()
                    ->paginate(15);
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.tests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_number' => 'required|integer',
            'year' => 'required|integer',
            'exam_type' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        /** @var \App\Models\Test $test */
        $test = \App\Models\Test::query()->create($validated);

        // Auto-generate 4 Test Sets
        for ($i = 1; $i <= 4; $i++) {
            $test->testSets()->create(['set_number' => $i]);
        }

        return redirect()->route('admin.tests.show', $test->id)->with('success', 'Test created successfully with 4 test sets.');
    }

    public function show(\App\Models\Test $test)
    {
        $test->load(['testSets.writingTasks', 'testSets.speakingQuestions', 'testSets.listeningSections', 'testSets.readingPassages']);
        return view('admin.tests.show', compact('test'));
    }

    public function edit(\App\Models\Test $test)
    {
        return view('admin.tests.edit', compact('test'));
    }

    public function update(Request $request, \App\Models\Test $test)
    {
        $validated = $request->validate([
            'book_number' => 'required|integer',
            'year' => 'required|integer',
            'exam_type' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $test->update($validated);

        return redirect()->route('admin.tests.show', $test->id)->with('success', 'Test updated successfully.');
    }

    public function destroy(\App\Models\Test $test)
    {
        $test->delete();

        return redirect()->route('admin.tests.index')->with('success', 'Test deleted successfully.');
    }
}
