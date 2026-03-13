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
        } elseif ($type === 'reading_group') {
            return \App\Models\ReadingQuestionGroup::findOrFail($id);
        }
        abort(404);
    }

    private function getRedirectRoute($type, $id)
    {
        if ($type === 'listening') return ['admin.listening-sections.edit', $id];
        if ($type === 'reading')   return ['admin.reading-passages.edit',   $id];
        if ($type === 'reading_group') {
            $group = \App\Models\ReadingQuestionGroup::find($id);
            return ['admin.reading-question-groups.edit', $id];
        }
        return ['admin.reading-passages.edit', $id];
    }

    private function allTypes(): string
    {
        return 'multiple_choice,short_answer,true_false_not_given,yes_no_not_given,matching_headings,matching_information,matching_sentence_endings,sentence_completion,summary_completion,table_completion,flow_chart_completion,matching,form_completion';
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
            'question_type' => 'required|in:'.$this->allTypes(),
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

        [$route, $param] = $this->getRedirectRoute($type, $id);
        return redirect()->route($route, $param)->with('success', 'Question added successfully.');
    }

    public function edit(\App\Models\Question $question)
    {
        $morphType = $question->questionable_type;
        if ($morphType === \App\Models\ListeningSection::class) {
            $type = 'listening';
        } elseif ($morphType === \App\Models\ReadingQuestionGroup::class) {
            $type = 'reading_group';
        } else {
            $type = 'reading';
        }
        return view('admin.questions.create', compact('question', 'type'));
    }

    public function update(Request $request, \App\Models\Question $question)
    {
        $validated = $request->validate([
            'question_type' => 'required|in:'.$this->allTypes(),
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

        $morphType = $question->questionable_type;
        if ($morphType === \App\Models\ListeningSection::class) {
            $type = 'listening';
        } elseif ($morphType === \App\Models\ReadingQuestionGroup::class) {
            $type = 'reading_group';
        } else {
            $type = 'reading';
        }
        [$route, $param] = $this->getRedirectRoute($type, $question->questionable_id);
        return redirect()->route($route, $param)->with('success', 'Question updated successfully.');
    }

    public function destroy(\App\Models\Question $question)
    {
        $morphType = $question->questionable_type;
        $parentId  = $question->questionable_id;
        if ($morphType === \App\Models\ListeningSection::class) {
            $type = 'listening';
        } elseif ($morphType === \App\Models\ReadingQuestionGroup::class) {
            $type = 'reading_group';
        } else {
            $type = 'reading';
        }

        $question->options()->delete();
        $question->delete();

        [$route, $param] = $this->getRedirectRoute($type, $parentId);
        return redirect()->route($route, $param)->with('success', 'Question deleted successfully.');
    }
}
