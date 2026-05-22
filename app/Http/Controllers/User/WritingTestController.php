<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AiWritingEvaluation;
use App\Models\TestAttempt;
use App\Models\WritingAnswer;
use App\Models\WritingTask;
use App\Services\GeminiEvaluationService;
use Illuminate\Http\Request;

class WritingTestController extends Controller
{
    public function show(TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if (! $attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'writing']);
        }

        if ($attempt->status === 'completed' || $attempt->completed_at) {
            return redirect()->route('dashboard')->with('error', 'Test already completed.');
        }

        $elapsedSeconds = now()->diffInSeconds($attempt->started_at);
        $totalSeconds = 60 * 60;
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $tasks = $attempt->testSet->writingTasks()->with('images')->orderBy('task_number')->get();
        $answers = $attempt->writingAnswers->keyBy('writing_task_id');

        return view('user.writing-test.show', compact('attempt', 'tasks', 'answers', 'remainingSeconds'));
    }

    public function autosave(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized or completed'], 403);
        }

        foreach ($request->answers as $taskId => $text) {
            $existing = WritingAnswer::where([
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => $taskId,
            ])->first();

            // Don't overwrite if already submitted
            if ($existing && $existing->submitted_at) {
                continue;
            }

            WritingAnswer::updateOrCreate(
                ['user_id' => auth()->id(), 'test_attempt_id' => $attempt->id, 'writing_task_id' => $taskId],
                ['answer_text' => $text, 'word_count' => str_word_count(strip_tags((string) $text))]
            );
        }

        return response()->json(['success' => true]);
    }

    public function submitTask(Request $request, TestAttempt $attempt, WritingTask $task)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check not already submitted
        $existing = WritingAnswer::where([
            'user_id' => auth()->id(),
            'test_attempt_id' => $attempt->id,
            'writing_task_id' => $task->id,
        ])->whereNotNull('submitted_at')->first();

        if ($existing) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $request->validate(['answer' => 'nullable|string']);

        $answer = trim($request->input('answer', ''));
        $wordCount = $answer ? str_word_count(strip_tags($answer)) : 0;

        $writingAnswer = WritingAnswer::updateOrCreate(
            ['user_id' => auth()->id(), 'test_attempt_id' => $attempt->id, 'writing_task_id' => $task->id],
            ['answer_text' => $answer, 'word_count' => $wordCount, 'submitted_at' => now()]
        );

        // Evaluate synchronously
        $service = app(GeminiEvaluationService::class);
        $result = $service->evaluateWritingTask(
            $task->task_number,
            $task->task_prompt ?? $task->task_description ?? '',
            $task->precontext,
            strip_tags($answer)
        );

        $writingAnswer->update([
            'evaluation_json' => $result['evaluation_text'],
            'band_score'      => $result['band_score'],
        ]);

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

        // Persist overall band from per-task scores
        $this->saveOverallBand($attempt);

        return redirect()->route('dashboard')->with('success', 'Writing test completed.');
    }

    protected function forceSubmit(TestAttempt $attempt)
    {
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        $this->saveOverallBand($attempt);

        return redirect()->route('dashboard')->with('success', 'Time expired. Test submitted.');
    }

    protected function saveOverallBand(TestAttempt $attempt): void
    {
        $scores = $attempt->writingAnswers()
            ->whereNotNull('band_score')
            ->pluck('band_score')
            ->toArray();

        if (empty($scores)) {
            return;
        }

        $overall = round((array_sum($scores) / count($scores)) * 2) / 2;

        AiWritingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['band_score' => $overall]
        );
    }
}
