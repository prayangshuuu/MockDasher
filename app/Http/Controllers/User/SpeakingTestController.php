<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\EvaluateSpeakingSubmission;
use App\Models\AiSpeakingEvaluation;
use App\Models\SpeakingAnswer;
use App\Models\SpeakingQuestion;
use App\Models\TestAttempt;
use Illuminate\Http\Request;

class SpeakingTestController extends Controller
{
    private const SPEAKING_LIMIT_SECONDS = 1200; // 20 minutes

    private const GRACE_SECONDS = 60;

    public function show(TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->completed_at) {
            return redirect()->route('user.history.show', $attempt->id)
                ->with('error', 'This exam has already been finished.');
        }

        if ($attempt->aiSpeakingEvaluation()->whereNotNull('band_score')->exists()) {
            return redirect()->route('user.tests.start', $attempt->testSet->test_id)
                ->with('error', 'You have already completed the Speaking module.');
        }

        if ($attempt->aiSpeakingEvaluation()->whereIn('evaluation_status', ['pending', 'evaluating'])->exists()) {
            return redirect()->route('user.tests.start', $attempt->testSet->test_id)
                ->with('error', 'Your Speaking evaluation is still in progress.');
        }

        if (! $attempt->speaking_started_at) {
            $attempt->update(['speaking_started_at' => now(), 'status' => 'speaking']);
            $attempt->refresh();
        } elseif ($attempt->status !== 'speaking') {
            $attempt->update(['status' => 'speaking']);
        }

        $elapsed = (int) now()->diffInSeconds($attempt->speaking_started_at);
        $remainingSeconds = (int) max(0, self::SPEAKING_LIMIT_SECONDS - $elapsed);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $speakingQuestions = $attempt->testSet
            ->speakingQuestions()
            ->orderBy('part')
            ->orderBy('id')
            ->get();

        $parts = $speakingQuestions->groupBy('part');
        $existingAnswers = $attempt->speakingAnswers()->get()->keyBy('speaking_question_id');

        return view('user.speaking-test.show', compact(
            'attempt', 'parts', 'speakingQuestions', 'existingAnswers', 'remainingSeconds'
        ));
    }

    /**
     * Upload audio recording for a question, storing the Speech-to-Text transcript.
     * POST /tests/attempts/{attempt}/speaking/upload
     */
    public function uploadAudio(Request $request, TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Attempt already completed'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $request->validate([
            'question_id' => 'required|integer',
            'audio' => 'required|file|mimes:webm,ogg,mp4,mpeg,mp3,wav|max:10240',
            'transcript' => 'nullable|string|max:10000',
            'duration' => 'nullable|integer|min:0|max:600',
        ]);

        // Scope: question must belong to this attempt's test set
        $validQuestionIds = $this->validQuestionIds($attempt);
        if (! in_array((int) $request->question_id, $validQuestionIds, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Do not overwrite a submitted answer
        $alreadySubmitted = SpeakingAnswer::where([
            'user_id' => auth()->id(),
            'test_attempt_id' => $attempt->id,
            'speaking_question_id' => (int) $request->question_id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $path = $request->file('audio')->store('speaking_recordings/'.$attempt->id, 'public');

        SpeakingAnswer::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'test_attempt_id' => $attempt->id,
                'speaking_question_id' => (int) $request->question_id,
            ],
            [
                'audio_path' => $path,
                'transcript_text' => $request->transcript ?? '',
                'duration_seconds' => (int) ($request->duration ?? 0),
            ]
        );

        return response()->json(['success' => true, 'path' => $path]);
    }

    /**
     * Lock a single speaking question answer (no AI evaluation yet).
     * POST /tests/attempts/{attempt}/speaking/questions/{question}/submit
     */
    public function submitQuestion(Request $request, TestAttempt $attempt, SpeakingQuestion $question)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Attempt already completed'], 403);
        }

        // Scope: question must belong to this attempt's test set
        $validQuestionIds = $this->validQuestionIds($attempt);
        if (! in_array($question->id, $validQuestionIds, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $alreadySubmitted = SpeakingAnswer::where([
            'user_id' => auth()->id(),
            'test_attempt_id' => $attempt->id,
            'speaking_question_id' => $question->id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        SpeakingAnswer::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'test_attempt_id' => $attempt->id,
                'speaking_question_id' => $question->id,
            ],
            ['submitted_at' => now()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Question answer locked. Evaluation will be compiled on final submission.',
        ]);
    }

    /**
     * Final submission — trigger AI evaluation and redirect.
     */
    public function submit(Request $request, TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        abort_if($attempt->completed_at !== null, 403, 'This exam has already been finished.');

        $evaluation = AiSpeakingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['evaluation_status' => 'pending', 'failure_reason' => null]
        );

        EvaluateSpeakingSubmission::dispatch($attempt->id);

        $evaluation->refresh();
        if ($evaluation->evaluation_status === 'failed') {
            return redirect()->route('user.speaking.show', $attempt->id)
                ->with('error', $evaluation->failure_reason ?: 'Speaking evaluation could not be completed.');
        }

        if ($evaluation->evaluation_status === 'completed') {
            $attempt->update(['status' => 'in_progress']);

            return redirect()->route('user.tests.start', $attempt->testSet->test_id)
                ->with('success', 'Speaking test completed. Your AI evaluation report has been compiled.');
        }

        return redirect()->route('user.tests.start', $attempt->testSet->test_id)
            ->with('success', 'Speaking test submitted. Your AI evaluation is in progress.');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    protected function forceSubmit(TestAttempt $attempt)
    {
        if (! $attempt->aiSpeakingEvaluation()->whereNotNull('band_score')->exists()) {
            $evaluation = AiSpeakingEvaluation::updateOrCreate(
                ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
                ['evaluation_status' => 'pending', 'failure_reason' => null]
            );

            EvaluateSpeakingSubmission::dispatch($attempt->id);
            $evaluation->refresh();

            if ($evaluation->evaluation_status === 'failed') {
                return redirect()->route('user.speaking.show', $attempt->id)
                    ->with('error', $evaluation->failure_reason ?: 'Speaking evaluation could not be completed.');
            }

            if ($evaluation->evaluation_status === 'completed') {
                $attempt->update(['status' => 'in_progress']);
            }
        }

        return redirect()->route('user.tests.start', $attempt->testSet->test_id)
            ->with('success', 'Time expired. Speaking test submitted automatically.');
    }

    private function authorizeAttempt(TestAttempt $attempt): void
    {
        abort_unless((int) $attempt->user_id === (int) auth()->id(), 403, 'Unauthorized access.');
    }

    private function isTimeExpired(TestAttempt $attempt): bool
    {
        return $attempt->speaking_started_at
            && now()->diffInSeconds($attempt->speaking_started_at) > (self::SPEAKING_LIMIT_SECONDS + self::GRACE_SECONDS);
    }

    /** @return array<int> */
    private function validQuestionIds(TestAttempt $attempt): array
    {
        return $attempt->testSet
            ->speakingQuestions()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }
}
