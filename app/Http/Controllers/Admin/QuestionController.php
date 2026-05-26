<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListeningSection;
use App\Models\Question;
use App\Models\ReadingPassage;
use App\Models\ReadingQuestionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{
    private function getParentModel($type, $id)
    {
        if ($type === 'listening') {
            return ListeningSection::findOrFail($id);
        } elseif ($type === 'reading') {
            return ReadingPassage::findOrFail($id);
        } elseif ($type === 'reading_group') {
            return ReadingQuestionGroup::findOrFail($id);
        }
        abort(404);
    }

    private function invalidateExamCache(string $type, $parent): void
    {
        if ($type === 'listening') {
            Cache::forget("testset:{$parent->test_set_id}:listening-sections");
        } elseif ($type === 'reading') {
            Cache::forget("testset:{$parent->test_set_id}:reading-passages");
        } elseif ($type === 'reading_group') {
            $passage = ReadingPassage::find($parent->reading_passage_id);
            if ($passage) {
                Cache::forget("testset:{$passage->test_set_id}:reading-passages");
            }
        }
    }

    private function getRedirectRoute($type, $id)
    {
        if ($type === 'listening') {
            return ['admin.listening-sections.edit', $id];
        }
        if ($type === 'reading') {
            return ['admin.reading-passages.edit',   $id];
        }
        if ($type === 'reading_group') {
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
            'correct_answer' => $validated['correct_answer'] ?? null,
            'explanation' => $validated['explanation'] ?? null,
        ]);

        if (isset($validated['options']) && is_array($validated['options'])) {
            foreach ($validated['options'] as $index => $optionText) {
                if (! empty($optionText)) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => ($index === (int) $request->input('correct_option')),
                    ]);
                }
            }
        }

        $this->invalidateExamCache($type, $parent);

        [$route, $param] = $this->getRedirectRoute($type, $id);

        return redirect()->route($route, $param)->with('success', 'Question added successfully.');
    }

    public function edit(Question $question)
    {
        $question->load('options');
        $morphType = $question->questionable_type;
        if ($morphType === ListeningSection::class) {
            $type = 'listening';
        } elseif ($morphType === ReadingQuestionGroup::class) {
            $type = 'reading_group';
        } else {
            $type = 'reading';
        }

        $parent = $question->questionable;

        return view('admin.questions.create', compact('question', 'type', 'parent'));
    }

    public function update(Request $request, Question $question)
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
            'correct_answer' => $validated['correct_answer'] ?? null,
            'explanation' => $validated['explanation'] ?? null,
        ]);

        // Manage options: easiest way is to delete old and create new
        $question->options()->delete();
        if (isset($validated['options']) && is_array($validated['options'])) {
            foreach ($validated['options'] as $index => $optionText) {
                if (! empty($optionText)) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => ($index === (int) $request->input('correct_option')),
                    ]);
                }
            }
        }

        $morphType = $question->questionable_type;
        if ($morphType === ListeningSection::class) {
            $type = 'listening';
        } elseif ($morphType === ReadingQuestionGroup::class) {
            $type = 'reading_group';
        } else {
            $type = 'reading';
        }
        $this->invalidateExamCache($type, $question->questionable);

        [$route, $param] = $this->getRedirectRoute($type, $question->questionable_id);

        return redirect()->route($route, $param)->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        $morphType = $question->questionable_type;
        $parentId  = $question->questionable_id;
        $parent    = $question->questionable;
        if ($morphType === ListeningSection::class) {
            $type = 'listening';
        } elseif ($morphType === ReadingQuestionGroup::class) {
            $type = 'reading_group';
        } else {
            $type = 'reading';
        }

        $question->options()->delete();
        $question->delete();

        $this->invalidateExamCache($type, $parent);

        [$route, $param] = $this->getRedirectRoute($type, $parentId);

        return redirect()->route($route, $param)->with('success', 'Question deleted successfully.');
    }
}
