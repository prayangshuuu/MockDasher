<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AiWritingEvaluation;
use App\Models\TestAttempt;
use App\Models\WritingAnswer;
use App\Models\WritingTask;
use App\Services\GeminiEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WritingTestController extends Controller
{
    private const WRITING_LIMIT_SECONDS = 3600; // 60 minutes
    private const GRACE_SECONDS         = 60;

    public function show(TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->completed_at) {
            return redirect()->route('user.history.show', $attempt->id)
                ->with('error', 'This exam has already been finished.');
        }

        if ($attempt->aiWritingEvaluation()->whereNotNull('band_score')->exists()) {
            return redirect()->route('user.tests.start', $attempt->testSet->test_id)
                ->with('error', 'You have already completed the Writing module.');
        }

        if (! $attempt->writing_started_at) {
            $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);
            $attempt->refresh();
        } elseif ($attempt->status !== 'writing') {
            $attempt->update(['status' => 'writing']);
        }

        $elapsed          = (int) now()->diffInSeconds($attempt->writing_started_at);
        $remainingSeconds = (int) max(0, self::WRITING_LIMIT_SECONDS - $elapsed);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $tasks   = $attempt->testSet->writingTasks()->with('images')->orderBy('task_number')->get();
        $answers = $attempt->writingAnswers->keyBy('writing_task_id');

        return view('user.writing-test.show', compact('attempt', 'tasks', 'answers', 'remainingSeconds'));
    }

    public function autosave(Request $request, TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Already completed'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $request->validate([
            'answers'   => 'array|max:10',
            'answers.*' => 'nullable|string|max:65535',
        ]);

        $validTaskIds = $attempt->testSet->writingTasks()->pluck('id')->toArray();

        foreach ($request->input('answers', []) as $taskId => $text) {
            if (! in_array((int) $taskId, $validTaskIds, true)) {
                continue;
            }

            // Don't overwrite a task that has already been submitted
            $alreadySubmitted = WritingAnswer::where([
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => (int) $taskId,
            ])->whereNotNull('submitted_at')->exists();

            if ($alreadySubmitted) {
                continue;
            }

            WritingAnswer::updateOrCreate(
                [
                    'user_id'         => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'writing_task_id' => (int) $taskId,
                ],
                [
                    'answer_text' => $text,
                    'word_count'  => str_word_count(strip_tags((string) $text)),
                ]
            );
        }

        return response()->json(['success' => true, 'saved_at' => now()->toTimeString()]);
    }

    /**
     * Lock a single writing task answer (no AI evaluation yet).
     * POST /tests/attempts/{attempt}/writing/tasks/{task}/submit
     */
    public function submitTask(Request $request, TestAttempt $attempt, WritingTask $task)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Attempt already completed'], 403);
        }

        // Scope: task must belong to this attempt's test set
        $validTaskIds = $attempt->testSet->writingTasks()->pluck('id')->toArray();
        if (! in_array($task->id, $validTaskIds, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $alreadySubmitted = WritingAnswer::where([
            'user_id'         => auth()->id(),
            'test_attempt_id' => $attempt->id,
            'writing_task_id' => $task->id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $request->validate(['answer' => 'nullable|string|max:65535']);

        $answer    = trim($request->input('answer', ''));
        $wordCount = $answer ? str_word_count(strip_tags($answer)) : 0;

        WritingAnswer::updateOrCreate(
            [
                'user_id'         => auth()->id(),
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => $task->id,
            ],
            [
                'answer_text'  => $answer,
                'word_count'   => $wordCount,
                'submitted_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Task answer locked. Evaluation will be compiled on final submission.',
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
            return redirect()->route('user.writing.show', $attempt->id)
                ->with('error', 'Writing evaluation could not be completed. Please check your Gemini API key and try again.');
        }

        $this->saveOverallBand($attempt);
        $attempt->update(['status' => 'in_progress']);

        return redirect()->route('user.tests.start', $attempt->testSet->test_id)
            ->with('success', 'Writing test completed. Your AI evaluation report has been compiled.');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    protected function forceSubmit(TestAttempt $attempt)
    {
        if (! $attempt->aiWritingEvaluation()->whereNotNull('band_score')->exists()) {
            if (! $this->evaluateAllAnswers($attempt)) {
                return redirect()->route('user.writing.show', $attempt->id)
                    ->with('error', 'Time expired, but Writing evaluation could not be completed. Please check your Gemini API key and submit again.');
            }

            $this->saveOverallBand($attempt);
            $attempt->update(['status' => 'in_progress']);
        }

        return redirect()->route('user.tests.start', $attempt->testSet->test_id)
            ->with('success', 'Time expired. Writing test submitted automatically.');
    }

    private function authorizeAttempt(TestAttempt $attempt): void
    {
        abort_unless((int) $attempt->user_id === (int) auth()->id(), 403, 'Unauthorized access.');
    }

    private function isTimeExpired(TestAttempt $attempt): bool
    {
        return $attempt->writing_started_at
            && now()->diffInSeconds($attempt->writing_started_at) > (self::WRITING_LIMIT_SECONDS + self::GRACE_SECONDS);
    }

    /**
     * Evaluate all un-graded writing tasks via Gemini and persist results.
     */
    private function evaluateAllAnswers(TestAttempt $attempt): bool
    {
        $user   = $attempt->user;
        $apiKey = $user->getRawOriginal('gemini_api_key') ?: config('services.gemini.key');

        if (empty($apiKey)) {
            Log::warning('[WritingTestController] No Gemini API key — skipping evaluation.', ['attempt' => $attempt->id]);
            return false;
        }

        try {
            $service = GeminiEvaluationService::forUser($user);
            $tasks   = $attempt->testSet->writingTasks()->with('images')->orderBy('task_number')->get();

            if ($tasks->isEmpty()) {
                Log::warning('[WritingTestController] No writing tasks configured.', ['attempt' => $attempt->id]);
                return false;
            }

            foreach ($tasks as $task) {
                $writingAnswer = WritingAnswer::firstOrCreate(
                    [
                        'user_id'         => $attempt->user_id,
                        'test_attempt_id' => $attempt->id,
                        'writing_task_id' => $task->id,
                    ],
                    [
                        'answer_text'  => '',
                        'word_count'   => 0,
                        'submitted_at' => now(),
                    ]
                );

                if ($writingAnswer->band_score !== null) {
                    continue;
                }

                $imageAltText = null;
                if ($task->task_number === 1) {
                    $firstImage   = $task->images->first();
                    $imageAltText = $firstImage?->alt_text ?? $task->precontext ?? null;
                }

                $question = trim($task->task_prompt ?? $task->task_description ?? "Writing Task {$task->task_number}");

                $result = $service->evaluateWritingTask(
                    $task->task_number,
                    $question,
                    $imageAltText,
                    strip_tags($writingAnswer->answer_text)
                );

                if (! $result['success'] || $result['band_score'] === null) {
                    Log::warning('[WritingTestController] Gemini returned no writing score.', [
                        'attempt' => $attempt->id,
                        'task'    => $task->id,
                    ]);
                    return false;
                }

                $writingAnswer->update([
                    'evaluation_json' => $result['evaluation_text'],
                    'band_score'      => $result['band_score'],
                    'submitted_at'    => $writingAnswer->submitted_at ?? now(),
                ]);

                $this->upsertAiWritingRecord($attempt, $task->task_number, $result, $writingAnswer->answer_text);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('[WritingTestController] Gemini evaluation failed', [
                'attempt' => $attempt->id,
                'error'   => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Upsert the per-task fields on the AiWritingEvaluation summary record.
     */
    private function upsertAiWritingRecord(TestAttempt $attempt, int $taskNumber, array $result, string $answerText): void
    {
        AiWritingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            [
                "task_{$taskNumber}_evaluation_json" => $result['evaluation_text'],
                "task_{$taskNumber}_band_score"      => $result['band_score'],
                "task_{$taskNumber}_answer"          => substr($answerText, 0, 65535),
            ]
        );
    }

    /**
     * Compute and persist the overall writing band score (average of all tasks, rounded to 0.5).
     */
    private function saveOverallBand(TestAttempt $attempt): void
    {
        $scores = $attempt->writingAnswers()
            ->whereNotNull('band_score')
            ->pluck('band_score')
            ->toArray();

        $taskCount = $attempt->testSet->writingTasks()->count();
        if ($taskCount === 0 || count($scores) !== $taskCount) {
            return;
        }

        $overall = round((array_sum($scores) / count($scores)) * 2) / 2;

        AiWritingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['band_score' => $overall]
        );
    }
}
