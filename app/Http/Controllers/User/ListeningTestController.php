<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ListeningAnswer;
use App\Models\ListeningAttempt;
use Illuminate\Http\Request;

class ListeningTestController extends Controller
{
    /** Time limits in seconds (+ grace for network latency) */
    private const LISTENING_LIMIT_SECONDS = 1800; // 30 minutes

    private const TRANSFER_LIMIT_SECONDS = 600;  // 10 minutes

    private const GRACE_SECONDS = 60;

    public function show(ListeningAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return redirect()->route('user.tests.start', $attempt->testSet->test_id)
                ->with('error', 'This listening test is already completed.');
        }

        if (! $attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'in_progress']);
            $attempt->refresh();
        }

        $transferRemainingSeconds = null;
        $listeningRemainingSeconds = null;

        if ($attempt->status === 'transfer' && $attempt->transfer_started_at) {
            $elapsed = now()->diffInSeconds($attempt->transfer_started_at);
            $transferRemainingSeconds = max(0, self::TRANSFER_LIMIT_SECONDS - $elapsed);
            if ($transferRemainingSeconds <= 0) {
                return $this->forceSubmit($attempt);
            }
        } elseif ($attempt->status === 'in_progress' && $attempt->started_at) {
            $elapsed = now()->diffInSeconds($attempt->started_at);
            $listeningRemainingSeconds = max(0, self::LISTENING_LIMIT_SECONDS - $elapsed);
            if ($listeningRemainingSeconds <= 0) {
                return $this->forceSubmit($attempt);
            }
        }

        $test = $attempt->testSet->test;
        $testSet = $attempt->testSet;
        $sections = $testSet->listeningSections()
            ->with(['questions.options'])
            ->orderBy('section_number')
            ->get();

        if ($sections->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'No listening sections configured for this test.');
        }

        $answers = $attempt->answers;
        $savedAnswers = $answers->pluck('answer_text', 'question_id')->toArray();
        $flaggedAnswers = $answers->filter(fn ($a) => $a->is_flagged)->pluck('is_flagged', 'question_id')->toArray();

        return view('user.listening-test.show', compact(
            'attempt', 'test', 'sections', 'savedAnswers', 'flaggedAnswers',
            'transferRemainingSeconds', 'listeningRemainingSeconds'
        ));
    }

    public function autosave(Request $request, ListeningAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Already completed'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $request->validate([
            'answers' => 'array|max:50',
            'answers.*' => 'nullable|string|max:1000',
            'flagged' => 'array|max:50',
            'flagged.*' => 'nullable|boolean',
        ]);

        $this->upsertAnswers(
            $attempt,
            $this->validQuestionIds($attempt),
            $request->input('answers', []),
            $request->input('flagged', [])
        );

        return response()->json(['success' => true, 'saved_at' => now()->toTimeString()]);
    }

    public function completeSection(Request $request, ListeningAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        $request->validate(['section' => 'required|integer|min:1|max:4']);
        $section = (int) $request->input('section');

        if ($section >= $attempt->current_section) {
            $nextSection = $section + 1;

            if ($nextSection > 4) {
                $attempt->update(['status' => 'transfer', 'transfer_started_at' => now()]);

                return response()->json(['status' => 'transfer', 'transfer_seconds' => self::TRANSFER_LIMIT_SECONDS]);
            }

            $attempt->update(['current_section' => $nextSection]);

            return response()->json(['status' => 'next', 'next_section' => $nextSection]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function submit(Request $request, ListeningAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        abort_if($attempt->status === 'completed', 403);

        $request->validate([
            'answers' => 'array|max:50',
            'answers.*' => 'nullable|string|max:1000',
            'flagged' => 'array|max:50',
            'flagged.*' => 'nullable|boolean',
        ]);

        $this->upsertAnswers(
            $attempt,
            $this->validQuestionIds($attempt),
            $request->input('answers', []),
            $request->input('flagged', [])
        );

        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        $attempt->evaluate();
        app(\App\Http\Controllers\User\TestController::class)->autoFinishIfComplete($attempt->testAttempt);

        return redirect()->route('user.listening.result', $attempt->id)
            ->with('success', 'Listening test submitted successfully!');
    }

    public function result(ListeningAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status !== 'completed') {
            return redirect()->route('user.listening.show', $attempt->id);
        }

        $testSet = $attempt->testSet;
        $test = $testSet->test;
        $sections = $testSet->listeningSections()
            ->with(['questions.options'])
            ->orderBy('section_number')
            ->get();

        $answers = $attempt->answers()->with('question')->get()->keyBy('question_id');
        $totalQuestions = $sections->flatMap(fn ($s) => $s->questions)->count();

        return view('user.listening-test.result', compact(
            'attempt', 'test', 'sections', 'answers', 'totalQuestions'
        ));
    }

    protected function forceSubmit(ListeningAttempt $attempt)
    {
        if ($attempt->status !== 'completed') {
            $attempt->update(['status' => 'completed', 'completed_at' => now()]);
            $attempt->evaluate();
            app(\App\Http\Controllers\User\TestController::class)->autoFinishIfComplete($attempt->testAttempt);
        }

        return redirect()->route('user.listening.result', $attempt->id)
            ->with('success', 'Time expired. Listening test submitted automatically.');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function authorizeAttempt(ListeningAttempt $attempt): void
    {
        abort_unless((int) $attempt->user_id === (int) auth()->id(), 403, 'Unauthorized access.');
    }

    private function isTimeExpired(ListeningAttempt $attempt): bool
    {
        if ($attempt->status === 'transfer' && $attempt->transfer_started_at) {
            return now()->diffInSeconds($attempt->transfer_started_at)
                > (self::TRANSFER_LIMIT_SECONDS + self::GRACE_SECONDS);
        }

        if ($attempt->started_at) {
            return now()->diffInSeconds($attempt->started_at)
                > (self::LISTENING_LIMIT_SECONDS + self::GRACE_SECONDS);
        }

        return false;
    }

    /** @return array<int> */
    private function validQuestionIds(ListeningAttempt $attempt): array
    {
        return $attempt->testSet
            ->listeningSections()
            ->with('questions:id,questionable_id,questionable_type')
            ->get()
            ->flatMap(fn ($s) => $s->questions->pluck('id'))
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    private function upsertAnswers(ListeningAttempt $attempt, array $validIds, array $answers, array $flagged): void
    {
        foreach ($answers as $questionId => $answerText) {
            if (! in_array((int) $questionId, $validIds, true)) {
                continue;
            }
            ListeningAnswer::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'question_id' => (int) $questionId,
                ],
                [
                    'answer_text' => $answerText,
                    'is_flagged' => ! empty($flagged[$questionId]),
                ]
            );
        }

        foreach ($flagged as $questionId => $isFlagged) {
            if (! in_array((int) $questionId, $validIds, true) || array_key_exists($questionId, $answers)) {
                continue;
            }
            ListeningAnswer::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'question_id' => (int) $questionId,
                ],
                ['is_flagged' => (bool) $isFlagged]
            );
        }
    }
}
