<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestSet;
use App\Models\WritingTask;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class WritingTaskController extends Controller
{
    public function create($testSetId)
    {
        $testSet = TestSet::findOrFail($testSetId);

        return view('admin.writing-tasks.create', compact('testSet'));
    }

    public function store(Request $request, $testSetId)
    {
        $testSet = TestSet::findOrFail($testSetId);

        $validated = $request->validate([
            'task_number' => 'required|in:1,2',
            'task_title' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'task_prompt' => 'nullable|string',
            'instruction_text' => 'nullable|string',
            'minimum_word_count' => 'required|integer|min:1',
            'task_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $task = $testSet->writingTasks()->create(Arr::except($validated, ['task_image']));

        if ($request->hasFile('task_image')) {
            $path = $request->file('task_image')->store('writing_images', 'public');
            $task->images()->create(['image_path' => $path]);
        }

        return redirect()->route('admin.test_sets.show', $testSet->id)->with('success', 'Writing task added successfully.');
    }

    public function edit(WritingTask $writing_task)
    {
        return view('admin.writing-tasks.edit', compact('writing_task'));
    }

    public function update(Request $request, WritingTask $writing_task)
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

        $writing_task->update(Arr::except($validated, ['task_image']));

        if ($request->hasFile('task_image')) {
            // Delete old images if any (assuming one image per task for now)
            foreach ($writing_task->images as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }

            $path = $request->file('task_image')->store('writing_images', 'public');
            $writing_task->images()->create(['image_path' => $path]);
        }

        return redirect()->route('admin.test_sets.show', $writing_task->test_set_id)->with('success', 'Writing task updated successfully.');
    }

    public function destroy(WritingTask $writing_task)
    {
        $testSetId = $writing_task->test_set_id;
        foreach ($writing_task->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $writing_task->delete();

        return redirect()->route('admin.test_sets.show', $testSetId)->with('success', 'Writing task deleted successfully.');
    }
}
