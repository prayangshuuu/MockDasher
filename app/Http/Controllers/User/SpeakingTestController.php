<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AiSpeakingEvaluation;
use App\Models\SpeakingAnswer;
use App\Models\SpeakingQuestion;
use App\Models\TestAttempt;
use App\Services\GeminiEvaluationService;
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

        $existingAnswers = $attempt->speakingAnswers()
            ->get()
            ->keyBy('speaking_question_id');

        return view('user.speaking-test.show', compact('attempt', 'parts', 'speakingQuestions', 'existingAnswers'));
    }

    public function uploadAudio(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:speaking_questions,id',
            'audio'       => 'required|file|max:10240',
            'transcript'  => 'nullable|string',
            'duration'    => 'nullable|integer',
        ]);

        // Don't overwrite a submitted answer
        $existing = SpeakingAnswer::where([
            'user_id'              => auth()->id(),
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $request->question_id,
        ])->whereNotNull('submitted_at')->first();

        if ($existing) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $path = $request->file('audio')->store('speaking_recordings/' . $attempt->id, 'public');

        SpeakingAnswer::updateOrCreate(
            ['user_id' => auth()->id(), 'test_attempt_id' => $attempt->id, 'speaking_question_id' => $request->question_id],
            ['audio_path' => $path, 'transcript_text' => $request->transcript ?? '', 'duration_seconds' => $request->duration ?? 0]
        );

        return response()->json(['success' => true, 'path' => $path]);
    }

    public function submitQuestion(Request $request, TestAttempt $attempt, SpeakingQuestion $question)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $existing = SpeakingAnswer::where([
            'user_id'              => auth()->id(),
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $question->id,
        ])->whereNotNull('submitted_at')->first();

        if ($existing) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $answer = SpeakingAnswer::where([
            'user_id'              => auth()->id(),
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $question->id,
        ])->first();

        $transcript = $answer ? trim($answer->transcript_text ?? '') : '';

        $service = app(GeminiEvaluationService::class);
        $result = $service->evaluateSpeakingQuestion(
            $question->part,
            $question->question_text,
            $transcript
        );

        SpeakingAnswer::updateOrCreate(
            ['user_id' => auth()->id(), 'test_attempt_id' => $attempt->id, 'speaking_question_id' => $question->id],
            [
                'evaluation_json' => $result['evaluation_text'],
                'band_score'      => $result['band_score'],
                'submitted_at'    => now(),
            ]
        );

        return response()->json([
            'success'    => true,
            'band_score' => $result['band_score'],
            'evaluation' => $result['evaluation_text'] ? json_decode($result['evaluation_text'], true) : null,
        ]);
    }

    public function submit(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            abort(403);
        }

        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        $this->saveOverallBand($attempt);

        return redirect()->route('dashboard')->with('success', 'Speaking test completed.');
    }

    protected function saveOverallBand(TestAttempt $attempt): void
    {
        $scores = $attempt->speakingAnswers()
            ->whereNotNull('band_score')
            ->pluck('band_score')
            ->toArray();

        if (empty($scores)) {
            return;
        }

        $overall = round((array_sum($scores) / count($scores)) * 2) / 2;

        AiSpeakingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['band_score' => $overall]
        );
    }
}
