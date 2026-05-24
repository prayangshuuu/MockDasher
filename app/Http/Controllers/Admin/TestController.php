<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use App\Models\Test;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::with('testSets')
            ->latest()
            ->paginate(15);

        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.tests.create');
    }

    public function store(StoreTestRequest $request)
    {
        $validated = $request->validated();

        /** @var Test $test */
        $test = Test::query()->create($validated);

        // Auto-generate 4 Test Sets
        for ($i = 1; $i <= 4; $i++) {
            $test->testSets()->create(['set_number' => $i]);
        }

        return redirect()->route('admin.tests.show', $test->id)->with('success', 'Test created successfully with 4 test sets.');
    }

    public function show(Test $test)
    {
        $test->load(['testSets.writingTasks', 'testSets.speakingQuestions', 'testSets.listeningSections', 'testSets.readingPassages']);

        return view('admin.tests.show', compact('test'));
    }

    public function edit(Test $test)
    {
        return view('admin.tests.edit', compact('test'));
    }

    public function update(UpdateTestRequest $request, Test $test)
    {
        $validated = $request->validated();

        $test->update($validated);

        return redirect()->route('admin.tests.show', $test->id)->with('success', 'Test updated successfully.');
    }

    public function destroy(Test $test)
    {
        $test->delete();

        return redirect()->route('admin.tests.index')->with('success', 'Test deleted successfully.');
    }
}
