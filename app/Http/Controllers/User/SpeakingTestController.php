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
    private const SPEAKING_LIMIT_SECONDS = 1200; // 20 minutes
    private const GRACE_SECONDS          = 60;

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

        if (! $attempt->speaking_started_at) {
            $attempt->update(['speaking_started_at' => now(), 'status' => 'speaking']);
            $attempt->refresh();
        } elseif ($attempt->status !== 'speaking') {
            $attempt->update(['status' => 'speaking']);
        }

        $elapsed          = (int) now()->diffInSeconds($attempt->speaking_started_at);
        $remainingSeconds = (int) max(0, self::SPEAKING_LIMIT_SECONDS - $elapsed);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $speakingQuestions = $attempt->testSet
            ->speakingQuestions()
            ->orderBy('part')
            ->orderBy('id')
            ->get();

        $parts           = $speakingQuestions->groupBy('part');
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
            'audio'       => 'required|file|mimes:webm,ogg,mp4,mpeg,mp3,wav|max:10240',
            'transcript'  => 'nullable|string|max:10000',
            'duration'    => 'nullable|integer|min:0|max:600',
        ]);

        // Scope: question must belong to this attempt's test set
        $validQuestionIds = $this->validQuestionIds($attempt);
        if (! in_array((int) $request->question_id, $validQuestionIds, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Do not overwrite a submitted answer
        $alreadySubmitted = SpeakingAnswer::where([
            'user_id'              => auth()->id(),
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => (int) $request->question_id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $path = $request->file('audio')->store('speaking_recordings/' . $attempt->id, 'public');

        SpeakingAnswer::updateOrCreate(
            [
                'user_id'              => auth()->id(),
                'test_attempt_id'      => $attempt->id,
                'speaking_question_id' => (int) $request->question_id,
            ],
            [
                'audio_path'       => $path,
                'transcript_text'  => $request->transcript ?? '',
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
            'user_id'              => auth()->id(),
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $question->id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        SpeakingAnswer::updateOrCreate(
            [
                'user_id'              => auth()->id(),
                'test_attempt_id'      => $attempt->id,
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

        if (! $this->evaluateAllAnswers($attempt)) {
            return redirect()->route('user.speaking.show', $attempt->id)
                ->with('error', 'Speaking evaluation could not be completed. Please check your Gemini API key and try again.');
        }

        $this->saveOverallBand($attempt);
        $attempt->update(['status' => 'in_progress']);

        return redirect()->route('user.tests.start', $attempt->testSet->test_id)
            ->with('success', 'Speaking test completed. Your AI evaluation report has been compiled.');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    protected function forceSubmit(TestAttempt $attempt)
    {
        if (! $attempt->aiSpeakingEvaluation()->whereNotNull('band_score')->exists()) {
            if (! $this->evaluateAllAnswers($attempt)) {
                return redirect()->route('user.speaking.show', $attempt->id)
                    ->with('error', 'Time expired, but Speaking evaluation could not be completed. Please check your Gemini API key and submit again.');
            }

            $this->saveOverallBand($attempt);
            $attempt->update(['status' => 'in_progress']);
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

    /**
     * Evaluate all un-graded speaking answers via Gemini and persist results.
     */
    private function evaluateAllAnswers(TestAttempt $attempt): bool
    {
        $user   = $attempt->user;
        $apiKey = $user->getRawOriginal('gemini_api_key') ?: config('services.gemini.key');

        if (empty($apiKey)) {
            Log::warning('[SpeakingTestController] No Gemini API key — skipping evaluation.', ['attempt' => $attempt->id]);
            return false;
        }

        try {
            $service          = GeminiEvaluationService::forUser($user);
            $speakingQuestions = $attempt->testSet
                ->speakingQuestions()
                ->orderBy('part')
                ->orderBy('id')
                ->get();

            if ($speakingQuestions->isEmpty()) {
                Log::warning('[SpeakingTestController] No speaking questions configured.', ['attempt' => $attempt->id]);
                return false;
            }

            foreach ($speakingQuestions as $sq) {
                $speakingAnswer = SpeakingAnswer::firstOrCreate(
                    [
                        'user_id'              => $attempt->user_id,
                        'test_attempt_id'      => $attempt->id,
                        'speaking_question_id' => $sq->id,
                    ],
                    [
                        'transcript_text'  => '',
                        'duration_seconds' => 0,
                        'submitted_at'     => now(),
                    ]
                );

                if ($speakingAnswer->band_score !== null) {
                    continue;
                }

                $result = $service->evaluateSpeakingQuestion(
                    $sq->part,
                    $sq->question_text,
                    trim($speakingAnswer->transcript_text)
                );

                if (! $result['success'] || $result['band_score'] === null) {
                    Log::warning('[SpeakingTestController] Gemini returned no speaking score.', [
                        'attempt'  => $attempt->id,
                        'question' => $sq->id,
                    ]);
                    return false;
                }

                $speakingAnswer->update([
                    'evaluation_json' => $result['evaluation_text'],
                    'band_score'      => $result['band_score'],
                    'submitted_at'    => $speakingAnswer->submitted_at ?? now(),
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('[SpeakingTestController] Gemini evaluation failed', [
                'attempt' => $attempt->id,
                'error'   => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Compute overall speaking band score, build combined transcript, and persist to AiSpeakingEvaluation.
     */
    private function saveOverallBand(TestAttempt $attempt): void
    {
        $answers = $attempt->speakingAnswers()->with('question')->get();

        $scores = $answers->whereNotNull('band_score')->pluck('band_score')->toArray();
        $questionCount = $attempt->testSet->speakingQuestions()->count();

        if ($questionCount === 0 || count($scores) !== $questionCount) {
            return;
        }

        $overall = round((array_sum($scores) / count($scores)) * 2) / 2;

        $transcript = $answers->map(function ($a) {
            $q = $a->question;
            return $q
                ? "Part {$q->part} — Q: {$q->question_text}\nA: " . ($a->transcript_text ?: '[No answer]')
                : null;
        })->filter()->implode("\n\n");

        $allEvaluations = $answers->whereNotNull('evaluation_json')->map(fn ($a) => [
            'question_id' => $a->speaking_question_id,
            'part'        => $a->question?->part,
            'question'    => $a->question?->question_text,
            'band_score'  => $a->band_score,
            'evaluation'  => json_decode($a->evaluation_json, true),
        ])->values()->toArray();

        AiSpeakingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            [
                'band_score'      => $overall,
                'full_transcript' => $transcript,
                'evaluation_json' => json_encode($allEvaluations, JSON_UNESCAPED_UNICODE),
            ]
        );
    }
}
