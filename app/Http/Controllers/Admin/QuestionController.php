<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    private function getParentModel($type, $id)
    {
        if ($type === 'listening') {
            return \App\Models\ListeningSection::findOrFail($id);
        } elseif ($type === 'reading') {
            return \App\Models\ReadingPassage::findOrFail($id);
        }
        abort(404);
    }

    public function create($type, $id)
    {
        $parent = $this->getParentModel($type, $id);
        return view('admin.questions.create', compact('parent', 'type'));
    }

    public function store(Request $request, $type, $id)
    {
        $parent = $this->getParentModel($type, $id);

        $validated = $request->validate([
            'question_type' => 'required|in:multiple_choice,short_answer,true_false_not_given,matching',
            'question_text' => 'required|string',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_option' => 'nullable|integer',
        ]);

        $question = $parent->questions()->create([
            'question_type' => $validated['question_type'],
            'question_text' => $validated['question_text'],
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'],
        ]);

        if (isset($validated['options']) && is_array($validated['options'])) {
            foreach ($validated['options'] as $index => $optionText) {
                if (!empty($optionText)) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => ($index === (int)$request->input('correct_option')),
                    ]);
                }
            }
        }

        $redirectRoute = $type === 'listening' ? 'admin.listening-sections.edit' : 'admin.reading-passages.edit';
        return redirect()->route($redirectRoute, $id)->with('success', 'Question added successfully.');
    }

    public function edit(\App\Models\Question $question)
    {
        $type = $question->questionable_type === \App\Models\ListeningSection::class ? 'listening' : 'reading';
        return view('admin.questions.edit', compact('question', 'type'));
    }

    public function update(Request $request, \App\Models\Question $question)
    {
        $validated = $request->validate([
            'question_type' => 'required|in:multiple_choice,short_answer,true_false_not_given,matching',
            'question_text' => 'required|string',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_option' => 'nullable|integer',
        ]);

        $question->update([
            'question_type' => $validated['question_type'],
            'question_text' => $validated['question_text'],
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'],
        ]);

        // Manage options: easiest way is to delete old and create new
        $question->options()->delete();
        if (isset($validated['options']) && is_array($validated['options'])) {
            foreach ($validated['options'] as $index => $optionText) {
                if (!empty($optionText)) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => ($index === (int)$request->input('correct_option')),
                    ]);
                }
            }
        }

        $type = $question->questionable_type === \App\Models\ListeningSection::class ? 'listening' : 'reading';
        $redirectRoute = $type === 'listening' ? 'admin.listening-sections.edit' : 'admin.reading-passages.edit';
        
        return redirect()->route($redirectRoute, $question->questionable_id)->with('success', 'Question updated successfully.');
    }

    public function destroy(\App\Models\Question $question)
    {
        $type = $question->questionable_type === \App\Models\ListeningSection::class ? 'listening' : 'reading';
        $parentId = $question->questionable_id;
        $redirectRoute = $type === 'listening' ? 'admin.listening-sections.edit' : 'admin.reading-passages.edit';

        $question->options()->delete();
        $question->delete();

        return redirect()->route($redirectRoute, $parentId)->with('success', 'Question deleted successfully.');
    }
}
