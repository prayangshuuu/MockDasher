<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WritingTaskController extends Controller
{
    public function create($testId)
    {
        $test = \App\Models\Test::findOrFail($testId);
        return view('admin.writing-tasks.create', compact('test'));
    }

    public function store(Request $request, $testId)
    {
        $test = \App\Models\Test::findOrFail($testId);

        $validated = $request->validate([
            'task_number' => 'required|in:1,2',
            'task_title' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'task_prompt' => 'nullable|string',
            'instruction_text' => 'nullable|string',
            'minimum_word_count' => 'required|integer|min:1',
            'task_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $task = $test->writingTasks()->create(\Illuminate\Support\Arr::except($validated, ['task_image']));

        if ($request->hasFile('task_image')) {
            $path = $request->file('task_image')->store('writing_images', 'public');
            $task->images()->create(['image_path' => $path]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Writing task added successfully.');
    }
}
