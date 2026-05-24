<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ReadingAnswer;
use App\Models\ReadingAttempt;
use Illuminate\Http\Request;

class ReadingTestController extends Controller
{
    private const READING_LIMIT_SECONDS = 3600; // 60 minutes

    private const GRACE_SECONDS = 60;

    public function show(ReadingAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return redirect()->route('user.tests.start', $attempt->testSet->test_id)
                ->with('error', 'This reading test has already been completed.');
        }

        if (! $attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'in_progress']);
            $attempt->refresh();
        }

        $elapsed = (int) now()->diffInSeconds($attempt->started_at);
        $remainingSeconds = (int) max(0, self::READING_LIMIT_SECONDS - $elapsed);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $testSet = $attempt->testSet;
        $test = $testSet->test;
        $passages = $testSet->readingPassages()
            ->with(['questionGroups' => fn ($q) => $q->with(['questions.options'])])
            ->orderBy('passage_number')
            ->get();

        if ($passages->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'No reading passages found for this test.');
        }

        $answers = $attempt->answers;
        $savedAnswers = $answers->pluck('answer_text', 'question_id')->toArray();
        $flaggedAnswers = $answers->filter(fn ($a) => $a->is_flagged)->pluck('is_flagged', 'question_id')->toArray();

        return view('user.reading-test.show', compact(
            'attempt', 'test', 'passages', 'savedAnswers', 'flaggedAnswers', 'remainingSeconds'
        ));
    }

    public function autosave(Request $request, ReadingAttempt $attempt)
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

    public function submit(Request $request, ReadingAttempt $attempt)
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

        return redirect()->route('user.reading.result', $attempt->id);
    }

    public function result(ReadingAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status !== 'completed') {
            return redirect()->route('user.reading.show', $attempt->id);
        }

        $testSet = $attempt->testSet;
        $test = $testSet->test;
        $passages = $testSet->readingPassages()
            ->with(['questionGroups' => fn ($q) => $q->with(['questions.options'])])
            ->orderBy('passage_number')
            ->get();

        $answers = $attempt->answers()->with('question')->get()->keyBy('question_id');
        $totalQuestions = $passages
            ->flatMap(fn ($p) => $p->questionGroups->flatMap(fn ($g) => $g->questions))
            ->count();

        return view('user.reading-test.result', compact(
            'attempt', 'test', 'passages', 'answers', 'totalQuestions'
        ));
    }

    protected function forceSubmit(ReadingAttempt $attempt)
    {
        if ($attempt->status !== 'completed') {
            $attempt->update(['status' => 'completed', 'completed_at' => now()]);
            $attempt->evaluate();
        }

        return redirect()->route('user.reading.result', $attempt->id);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function authorizeAttempt(ReadingAttempt $attempt): void
    {
        abort_unless((int) $attempt->user_id === (int) auth()->id(), 403, 'Unauthorized access.');
    }

    private function isTimeExpired(ReadingAttempt $attempt): bool
    {
        return $attempt->started_at
            && now()->diffInSeconds($attempt->started_at) > (self::READING_LIMIT_SECONDS + self::GRACE_SECONDS);
    }

    /** @return array<int> */
    private function validQuestionIds(ReadingAttempt $attempt): array
    {
        return $attempt->testSet
            ->readingPassages()
            ->with(['questionGroups.questions:id,questionable_id,questionable_type'])
            ->get()
            ->flatMap(fn ($p) => $p->questionGroups->flatMap(fn ($g) => $g->questions->pluck('id')))
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    private function upsertAnswers(ReadingAttempt $attempt, array $validIds, array $answers, array $flagged): void
    {
        foreach ($answers as $questionId => $answerText) {
            if (! in_array((int) $questionId, $validIds, true)) {
                continue;
            }
            ReadingAnswer::updateOrCreate(
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
            ReadingAnswer::updateOrCreate(
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
