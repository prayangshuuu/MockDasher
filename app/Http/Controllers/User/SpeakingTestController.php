<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AiSpeakingEvaluation;
use App\Models\SpeakingAnswer;
use App\Models\SpeakingQuestion;
use App\Models\TestAttempt;
use App\Services\GeminiEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        if (! $attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'speaking']);
        } elseif ($attempt->status !== 'speaking') {
            $attempt->update(['status' => 'speaking']);
        }

        $elapsedSeconds   = now()->diffInSeconds($attempt->started_at);
        $totalSeconds     = 20 * 60; // 20 minutes IELTS Speaking session limit
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $speakingQuestions = $attempt->testSet
            ->speakingQuestions()
            ->orderBy('part')
            ->orderBy('id')
            ->get();

        $parts = $speakingQuestions->groupBy('part');

        $existingAnswers = $attempt->speakingAnswers()
            ->get()
            ->keyBy('speaking_question_id');

        return view('user.speaking-test.show', compact('attempt', 'parts', 'speakingQuestions', 'existingAnswers', 'remainingSeconds'));
    }

    /**
     * Upload audio recording for a question.
     * Also stores the browser Speech-to-Text transcript.
     */
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
            [
                'user_id'              => auth()->id(),
                'test_attempt_id'      => $attempt->id,
                'speaking_question_id' => $request->question_id,
            ],
            [
                'audio_path'       => $path,
                'transcript_text'  => $request->transcript ?? '',
                'duration_seconds' => $request->duration ?? 0,
            ]
        );

        return response()->json(['success' => true, 'path' => $path]);
    }

    /**
     * Submit a single speaking question response. Locks the answer instantly, postponing Gemini AI evaluation.
     *
     * POST /tests/attempts/{attempt}/speaking/questions/{question}/submit
     */
    public function submitQuestion(Request $request, TestAttempt $attempt, SpeakingQuestion $question)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check already submitted
        $alreadySubmitted = SpeakingAnswer::where([
            'user_id'              => auth()->id(),
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $question->id,
        ])->whereNotNull('submitted_at')->first();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        // Mark the answer as submitted (locks microphone in taker workspace)
        SpeakingAnswer::updateOrCreate(
            [
                'user_id'              => auth()->id(),
                'test_attempt_id'      => $attempt->id,
                'speaking_question_id' => $question->id,
            ],
            [
                'submitted_at' => now(),
            ]
        );

        return response()->json([
            'success'    => true,
            'band_score' => null,
            'evaluation' => null,
            'message'    => 'Question answer locked successfully. Evaluation will be compiled on final submission.'
        ]);
    }

    /**
     * Final submission — mark attempt as completed, compile batch AI reports, and save overall band.
     */
    public function submit(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            abort(403);
        }

        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        $this->evaluateAllAnswers($attempt);
        $this->saveOverallBand($attempt);

        return redirect()->route('dashboard')->with('success', 'Speaking test completed successfully. Your AI evaluation report has been compiled.');
    }

    protected function forceSubmit(TestAttempt $attempt)
    {
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        $this->evaluateAllAnswers($attempt);
        $this->saveOverallBand($attempt);

        return redirect()->route('dashboard')->with('success', 'Time expired. Speaking test submitted. Your AI evaluation report has been compiled.');
    }

    /**
     * Batch compile and save Gemini evaluations for all un-graded speaking answers in the sitting.
     */
    protected function evaluateAllAnswers(TestAttempt $attempt): void
    {
        $user = $attempt->user;
        $apiKey = $user->getRawOriginal('gemini_api_key');
        if (empty($apiKey)) {
            return;
        }

        try {
            $service = GeminiEvaluationService::forUser($user);
            $speakingQuestions = $attempt->testSet->speakingQuestions()->orderBy('part')->orderBy('id')->get();
            foreach ($speakingQuestions as $sq) {
                $speakingAnswer = SpeakingAnswer::where([
                    'test_attempt_id' => $attempt->id,
                    'speaking_question_id' => $sq->id,
                ])->first();

                // Skip if no answer was recorded, or it has already been evaluated
                if (!$speakingAnswer || empty($speakingAnswer->transcript_text) || $speakingAnswer->band_score !== null) {
                    continue;
                }

                $transcript = trim($speakingAnswer->transcript_text ?? '');
                
                $result = $service->evaluateSpeakingQuestion(
                    $sq->part,
                    $sq->question_text,
                    $transcript
                );

                $speakingAnswer->update([
                    'evaluation_json' => $result['evaluation_text'],
                    'band_score'      => $result['band_score'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[SpeakingTestController] Batch Gemini evaluation failed', [
                'attempt' => $attempt->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Compute overall speaking band score from all per-question scores,
     * build a combined transcript, and persist to AiSpeakingEvaluation.
     */
    protected function saveOverallBand(TestAttempt $attempt): void
    {
        $answers = $attempt->speakingAnswers()
            ->with('question')
            ->get();

        $scores = $answers
            ->whereNotNull('band_score')
            ->pluck('band_score')
            ->toArray();

        if (empty($scores)) {
            return;
        }

        $overall = round((array_sum($scores) / count($scores)) * 2) / 2;

        // Build a combined transcript for admin review
        $transcript = $answers->map(function ($a) {
            $q = $a->question;
            return $q
                ? "Part {$q->part} — Q: {$q->question_text}\nA: " . ($a->transcript_text ?: '[No answer]')
                : null;
        })->filter()->implode("\n\n");

        // Aggregate evaluation JSONs for all questions as an array
        $allEvaluations = $answers
            ->whereNotNull('evaluation_json')
            ->map(fn($a) => [
                'question_id' => $a->speaking_question_id,
                'part'        => $a->question?->part,
                'question'    => $a->question?->question_text,
                'band_score'  => $a->band_score,
                'evaluation'  => json_decode($a->evaluation_json, true),
            ])
            ->values()
            ->toArray();

        AiSpeakingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            [
                'band_score'       => $overall,
                'full_transcript'  => $transcript,
                'evaluation_json'  => json_encode($allEvaluations, JSON_UNESCAPED_UNICODE),
            ]
        );
    }
}
