<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\EvaluateSpeakingSubmission;
use App\Models\SpeakingAnswer;
use App\Models\TestAttempt;
use Illuminate\Http\Request;

class SpeakingTestController extends Controller
{
    public function show(TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($attempt->status === 'completed' || $attempt->completed_at) {
            return redirect()->route('dashboard')->with('error', 'Test already completed.');
        }

        if (!$attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'speaking']);
        } elseif ($attempt->status !== 'speaking') {
            $attempt->update(['status' => 'speaking']);
        }

        $speakingQuestions = $attempt->testSet->speakingQuestions()->orderBy('part')->orderBy('id')->get();
        $parts = $speakingQuestions->groupBy('part');

        // Load existing answers for resume
        $existingAnswers = $attempt->speakingAnswers()->pluck('transcript_text', 'speaking_question_id')->toArray();

        return view('user.speaking-test.show', compact('attempt', 'parts', 'speakingQuestions', 'existingAnswers'));
    }

    /**
     * Upload audio for a single question (AJAX).
     */
    public function uploadAudio(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:speaking_questions,id',
            'audio' => 'required|file|max:10240',
            'transcript' => 'nullable|string',
            'duration' => 'nullable|integer',
        ]);

        $path = $request->file('audio')->store('speaking_recordings/' . $attempt->id, 'public');

        SpeakingAnswer::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'test_attempt_id' => $attempt->id,
                'speaking_question_id' => $request->question_id,
            ],
            [
                'audio_path' => $path,
                'transcript_text' => $request->transcript ?? '',
                'duration_seconds' => $request->duration ?? 0,
            ]
        );

        return response()->json(['success' => true, 'path' => $path]);
    }

    public function submit(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            abort(403);
        }

        $attempt->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Build full transcript from all answers
        $answers = $attempt->speakingAnswers()->with('question')->orderBy('speaking_question_id')->get();
        $transcript = '';
        foreach ($answers as $ans) {
            $transcript .= "Part {$ans->question->part} — Q: {$ans->question->question_text}\nA: {$ans->transcript_text}\n\n";
        }

        EvaluateSpeakingSubmission::dispatch($attempt->id, $transcript);

        return redirect()->route('dashboard')->with('success', 'Speaking test submitted! AI evaluation will be available shortly.');
    }
}
