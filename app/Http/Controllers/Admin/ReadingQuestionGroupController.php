<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadingPassage;
use App\Models\ReadingQuestionGroup;
use Illuminate\Http\Request;

class ReadingQuestionGroupController extends Controller
{
    public function create($passageId)
    {
        $passage = ReadingPassage::with('test')->findOrFail($passageId);

        return view('admin.reading-question-groups.create', compact('passage'));
    }

    public function store(Request $request, $passageId)
    {
        $passage = ReadingPassage::findOrFail($passageId);

        $validated = $request->validate([
            'group_instruction' => 'nullable|string',
            'question_type' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $passage->questionGroups()->create($validated);

        return redirect()
            ->route('admin.reading-passages.edit', $passageId)
            ->with('success', 'Question group created.');
    }

    public function edit(ReadingQuestionGroup $group)
    {
        $group->load('passage.test', 'questions.options');

        return view('admin.reading-question-groups.edit', compact('group'));
    }

    public function update(Request $request, ReadingQuestionGroup $group)
    {
        $validated = $request->validate([
            'group_instruction' => 'nullable|string',
            'question_type' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $group->update($validated);

        return redirect()
            ->route('admin.reading-question-groups.edit', $group->id)
            ->with('success', 'Group updated.');
    }

    public function destroy(ReadingQuestionGroup $group)
    {
        $passageId = $group->reading_passage_id;
        // Questions will cascade-delete via morph (handled by question model)
        $group->questions()->each(function ($q) {
            $q->options()->delete();
            $q->delete();
        });
        $group->delete();

        return redirect()
            ->route('admin.reading-passages.edit', $passageId)
            ->with('success', 'Question group deleted.');
    }
}
