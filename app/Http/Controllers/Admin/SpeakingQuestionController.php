<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpeakingQuestionController extends Controller
{
    public function create($testSetId)
    {
        $testSet = \App\Models\TestSet::findOrFail($testSetId);
        return view('admin.speaking-questions.create', compact('testSet'));
    }

    public function store(Request $request, $testSetId)
    {
        $testSet = \App\Models\TestSet::findOrFail($testSetId);

        $validated = $request->validate([
            'part' => 'required|in:1,2,3',
            'question_text' => 'nullable|string',
            'time_limit' => 'nullable|integer',
            'preparation_instructions' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:5120',
        ]);

        $data = \Illuminate\Support\Arr::except($validated, ['audio_file']);

        if ($request->hasFile('audio_file')) {
            $data['audio_path'] = $request->file('audio_file')->store('speaking_audio', 'public');
        }

        $testSet->speakingQuestions()->create($data); 

        return redirect()->route('admin.test_sets.show', $testSetId)->with('success', 'Speaking question added successfully.');
    }

    public function edit(\App\Models\SpeakingQuestion $speaking_question)
    {
        return view('admin.speaking-questions.edit', compact('speaking_question'));
    }

    public function update(Request $request, \App\Models\SpeakingQuestion $speaking_question)
    {
        $validated = $request->validate([
            'part' => 'required|in:1,2,3',
            'question_text' => 'nullable|string',
            'time_limit' => 'nullable|integer',
            'preparation_instructions' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:5120',
        ]);

        $data = \Illuminate\Support\Arr::except($validated, ['audio_file']);

        if ($request->hasFile('audio_file')) {
            if ($speaking_question->audio_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($speaking_question->audio_path);
            }
            $data['audio_path'] = $request->file('audio_file')->store('speaking_audio', 'public');
        }

        $speaking_question->update($data);

        return redirect()->route('admin.test_sets.show', $speaking_question->test_set_id)->with('success', 'Speaking question updated successfully.');
    }

    public function destroy(\App\Models\SpeakingQuestion $speaking_question)
    {
        $testSetId = $speaking_question->test_set_id;
        if ($speaking_question->audio_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($speaking_question->audio_path);
        }
        $speaking_question->delete();

        return redirect()->route('admin.test_sets.show', $testSetId)->with('success', 'Speaking question deleted successfully.');
    }
}
