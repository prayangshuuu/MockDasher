<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ListeningAnswer;
use App\Models\ListeningAttempt;
use App\Models\Test;
use Illuminate\Http\Request;

class ListeningTestController extends Controller
{
    /**
     * Show the listening test interface.
     */
    public function show(ListeningAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($attempt->status === 'completed') {
            return redirect()->route('user.tests.start', $attempt->testSet->test_id)->with('error', 'This listening test is already completed.');
        }

        // Start timer if first visit
        if (! $attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'in_progress']);
        }

        $test = $attempt->testSet->test;
        $testSet = $attempt->testSet;
        $sections = $testSet->listeningSections()->with(['questions.options'])->orderBy('section_number')->get();

        if ($sections->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'No listening sections configured for this test.');
        }

        // Get saved answers keyed by question_id
        $answers = $attempt->answers;
        $savedAnswers = $answers->pluck('answer_text', 'question_id')->toArray();
        $flaggedAnswers = $answers->filter(fn ($a) => $a->is_flagged)->pluck('is_flagged', 'question_id')->toArray();

        // If in transfer mode, calculate transfer remaining time
        $transferRemainingSeconds = null;
        $listeningRemainingSeconds = null;
        if ($attempt->status === 'transfer' && $attempt->transfer_started_at) {
            $elapsed = now()->diffInSeconds($attempt->transfer_started_at);
            $transferRemainingSeconds = max(0, 600 - $elapsed); // 10 minutes
            if ($transferRemainingSeconds <= 0) {
                return $this->forceSubmit($attempt);
            }
        } elseif ($attempt->status === 'in_progress' && $attempt->started_at) {
            $elapsed = now()->diffInSeconds($attempt->started_at);
            $listeningRemainingSeconds = max(0, 1800 - $elapsed); // 30 minutes
            if ($listeningRemainingSeconds <= 0) {
                return $this->forceSubmit($attempt);
            }
        }

        return view('user.listening-test.show', compact(
            'attempt', 'test', 'sections', 'savedAnswers', 'flaggedAnswers', 'transferRemainingSeconds', 'listeningRemainingSeconds'
        ));
    }

    /**
     * Autosave answers (AJAX).
     */
    public function autosave(Request $request, ListeningAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized or completed'], 403);
        }

        $answers = $request->input('answers', []);
        $flagged = $request->input('flagged', []);

        foreach ($answers as $questionId => $answerText) {
            ListeningAnswer::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer_text' => $answerText,
                    'is_flagged' => ! empty($flagged[$questionId]),
                ]
            );
        }

        foreach ($flagged as $questionId => $isFlagged) {
            if (! array_key_exists($questionId, $answers)) {
                ListeningAnswer::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'is_flagged' => (bool) $isFlagged,
                    ]
                );
            }
        }

        return response()->json(['success' => true, 'saved_at' => now()->toTimeString()]);
    }

    /**
     * Mark audio as finished for a section, unlock next section.
     */
    public function completeSection(Request $request, ListeningAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $section = (int) $request->input('section', 1);

        // Only advance if this is the current section
        if ($section >= $attempt->current_section) {
            $nextSection = $section + 1;

            if ($nextSection > 4) {
                // All 4 parts done — begin answer transfer phase
                $attempt->update([
                    'status' => 'transfer',
                    'transfer_started_at' => now(),
                ]);

                return response()->json(['status' => 'transfer', 'transfer_seconds' => 600]);
            } else {
                $attempt->update(['current_section' => $nextSection]);

                return response()->json(['status' => 'next', 'next_section' => $nextSection]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Final submission of all listening answers.
     */
    public function submit(Request $request, ListeningAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            abort(403);
        }

        // Save any final answers passed in payload
        $answers = $request->input('answers', []);
        $flagged = $request->input('flagged', []);
        foreach ($answers as $questionId => $answerText) {
            ListeningAnswer::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer_text' => $answerText,
                    'is_flagged' => ! empty($flagged[$questionId]),
                ]
            );
        }

        foreach ($flagged as $questionId => $isFlagged) {
            if (! array_key_exists($questionId, $answers)) {
                ListeningAnswer::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'is_flagged' => (bool) $isFlagged,
                    ]
                );
            }
        }

        $attempt->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $attempt->evaluate();

        return redirect()->route('user.tests.start', $attempt->testSet->test_id)->with('success', 'Listening test submitted successfully!');
    }

    /**
     * Force-submit (called when timer expires).
     */
    protected function forceSubmit(ListeningAttempt $attempt)
    {
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        $attempt->evaluate();

        return redirect()->route('user.tests.start', $attempt->testSet->test_id)->with('success', 'Time expired. Listening test submitted automatically.');
    }
}
