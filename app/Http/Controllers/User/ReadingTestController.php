<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ReadingAttempt;
use App\Models\ReadingAnswer;
use Illuminate\Http\Request;

class ReadingTestController extends Controller
{
    public function show(ReadingAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($attempt->status === 'completed') {
            return redirect()->route('dashboard')->with('error', 'This reading test has already been completed.');
        }

        // Start timer on first visit
        if (!$attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'in_progress']);
        }

        // Enforce 60-minute limit
        $elapsedSeconds  = now()->diffInSeconds($attempt->started_at);
        $totalSeconds    = 3600; // 60 minutes
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $test = $attempt->test;
        $passages = $test->readingPassages()
            ->with(['questionGroups' => fn($q) => $q->with(['questions.options'])])
            ->orderBy('passage_number')
            ->get();

        if ($passages->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'No reading passages found for this test.');
        }

        // Saved answers keyed by question_id
        $savedAnswers = $attempt->answers()->pluck('answer_text', 'question_id')->toArray();

        return view('user.reading-test.show', compact(
            'attempt', 'test', 'passages', 'savedAnswers', 'remainingSeconds'
        ));
    }

    public function autosave(Request $request, ReadingAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized or completed'], 403);
        }

        foreach ($request->input('answers', []) as $questionId => $answerText) {
            ReadingAnswer::updateOrCreate(
                [
                    'user_id'        => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'question_id'    => $questionId,
                ],
                ['answer_text' => $answerText]
            );
        }

        return response()->json(['success' => true, 'saved_at' => now()->toTimeString()]);
    }

    public function submit(Request $request, ReadingAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            abort(403);
        }

        foreach ($request->input('answers', []) as $questionId => $answerText) {
            ReadingAnswer::updateOrCreate(
                [
                    'user_id'         => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'question_id'     => $questionId,
                ],
                ['answer_text' => $answerText]
            );
        }

        $attempt->update(['status' => 'completed', 'completed_at' => now()]);

        return redirect()->route('dashboard')->with('success', 'Reading test submitted successfully!');
    }

    protected function forceSubmit(ReadingAttempt $attempt)
    {
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        return redirect()->route('dashboard')->with('success', 'Time expired. Reading test submitted automatically.');
    }
}
