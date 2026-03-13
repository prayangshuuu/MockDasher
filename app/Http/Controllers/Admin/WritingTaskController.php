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

        return redirect()->route('admin.tests.show', $test->id)->with('success', 'Writing task added successfully.');
    }

    public function edit(\App\Models\WritingTask $writing_task)
    {
        return view('admin.writing-tasks.edit', compact('writing_task'));
    }

    public function update(Request $request, \App\Models\WritingTask $writing_task)
    {
        $validated = $request->validate([
            'task_number' => 'required|in:1,2',
            'task_title' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'task_prompt' => 'nullable|string',
            'instruction_text' => 'nullable|string',
            'minimum_word_count' => 'required|integer|min:1',
            'task_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $writing_task->update(\Illuminate\Support\Arr::except($validated, ['task_image']));

        if ($request->hasFile('task_image')) {
            // Delete old images if any (assuming one image per task for now)
            foreach($writing_task->images as $img) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }

            $path = $request->file('task_image')->store('writing_images', 'public');
            $writing_task->images()->create(['image_path' => $path]);
        }

        return redirect()->route('admin.tests.show', $writing_task->test_id)->with('success', 'Writing task updated successfully.');
    }

    public function destroy(\App\Models\WritingTask $writing_task)
    {
        $testId = $writing_task->test_id;
        foreach($writing_task->images as $img) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
        }
        $writing_task->delete();

        return redirect()->route('admin.tests.show', $testId)->with('success', 'Writing task deleted successfully.');
    }
}
