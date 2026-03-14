<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WritingTestController extends Controller
{
    public function show(\App\Models\TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Initialize timer if first time
        if (!$attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'writing']);
        }

        // Check if already completed
        if ($attempt->status === 'completed' || $attempt->completed_at) {
            return redirect()->route('dashboard')->with('error', 'Test already completed.');
        }

        // Calculate remaining time
        $elapsedSeconds = now()->diffInSeconds($attempt->started_at);
        $totalSeconds = 60 * 60; // 60 minutes
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $tasks = $attempt->test->writingTasks()->with('images')->orderBy('task_number')->get();
        $answers = $attempt->writingAnswers->keyBy('writing_task_id');

        return view('user.writing-test.show', compact('attempt', 'tasks', 'answers', 'remainingSeconds'));
    }

    public function autosave(Request $request, \App\Models\TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized or completed'], 403);
        }

        foreach ($request->answers as $taskId => $text) {
            $wordCount = str_word_count(strip_tags((string)$text));

            \App\Models\WritingAnswer::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'writing_task_id' => $taskId,
                ],
                [
                    'answer_text' => $text,
                    'word_count' => $wordCount,
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function submit(Request $request, \App\Models\TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            abort(403);
        }

        // Final save
        foreach ($request->answers as $taskId => $text) {
            $wordCount = str_word_count(strip_tags((string)$text));

            \App\Models\WritingAnswer::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'writing_task_id' => $taskId,
                ],
                [
                    'answer_text' => $text,
                    'word_count' => $wordCount,
                    'submitted_at' => now(),
                ]
            );
        }

        $attempt->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('dashboard')->with('success', 'Writing test submitted successfully.');
    }

    protected function forceSubmit(\App\Models\TestAttempt $attempt)
    {
        $attempt->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
        return redirect()->route('dashboard')->with('success', 'Time expired. Test submitted successfully.');
    }
}
