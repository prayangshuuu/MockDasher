<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\EvaluateSpeakingSubmission;
use App\Models\AiSpeakingEvaluation;
use App\Models\SpeakingAnswer;
use App\Models\SpeakingQuestion;
use App\Models\TestAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpeakingApiController extends Controller
{
    private const SPEAKING_LIMIT_SECONDS = 1200;

    private const GRACE_SECONDS = 60;

    public function show(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        if ($attempt->aiSpeakingEvaluation()->whereNotNull('band_score')->exists()) {
            return response()->json(['error' => 'already_completed', 'message' => 'Speaking module already completed.'], 409);
        }

        if ($attempt->aiSpeakingEvaluation()->whereIn('evaluation_status', ['pending', 'evaluating'])->exists()) {
            return response()->json(['error' => 'evaluation_in_progress', 'message' => 'Your Speaking evaluation is still in progress.'], 409);
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
            if (! $attempt->aiSpeakingEvaluation()->whereNotNull('band_score')->exists()) {
                AiSpeakingEvaluation::updateOrCreate(
                    ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
                    ['evaluation_status' => 'pending', 'failure_reason' => null]
                );
                EvaluateSpeakingSubmission::dispatch($attempt->id);
            }

            return response()->json([
                'error'   => 'time_expired',
                'message' => 'Speaking time has expired.',
            ], 422);
        }

        $speakingQuestions = $attempt->testSet
            ->speakingQuestions()
            ->orderBy('part')
            ->orderBy('id')
            ->get();

        $existingAnswers = $attempt->speakingAnswers()->get()->keyBy('speaking_question_id');

        $parts = $speakingQuestions->groupBy('part')->map(function ($questions, $part) use ($existingAnswers) {
            return [
                'part'      => $part,
                'questions' => $questions->map(function (SpeakingQuestion $q) use ($existingAnswers) {
                    $answer = $existingAnswers->get($q->id);

                    return [
                        'id'           => $q->id,
                        'question_text' => $q->question_text,
                        'time_limit'   => $q->time_limit,
                        'answer'       => $answer ? [
                            'audio_path'       => $answer->audio_path,
                            'transcript_text'  => $answer->transcript_text,
                            'duration_seconds' => $answer->duration_seconds,
                            'submitted'        => $answer->submitted_at !== null,
                            'band_score'       => $answer->band_score,
                        ] : null,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'attempt_id'          => $attempt->id,
            'remaining_seconds'   => $remainingSeconds,
            'speaking_started_at' => $attempt->speaking_started_at?->toIso8601String(),
            'parts'               => $parts,
        ]);
    }

    public function uploadAudio(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

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

        $validQuestionIds = $this->validQuestionIds($attempt);
        if (! in_array((int) $request->question_id, $validQuestionIds, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $alreadySubmitted = SpeakingAnswer::where([
            'user_id'              => $request->user()->id,
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => (int) $request->question_id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $path = $request->file('audio')->store('speaking_recordings/'.$attempt->id, 'public');

        SpeakingAnswer::updateOrCreate(
            [
                'user_id'              => $request->user()->id,
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

    public function submitQuestion(Request $request, TestAttempt $attempt, SpeakingQuestion $question): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Attempt already completed'], 403);
        }

        $validQuestionIds = $this->validQuestionIds($attempt);
        if (! in_array($question->id, $validQuestionIds, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $alreadySubmitted = SpeakingAnswer::where([
            'user_id'              => $request->user()->id,
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $question->id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        SpeakingAnswer::updateOrCreate(
            [
                'user_id'              => $request->user()->id,
                'test_attempt_id'      => $attempt->id,
                'speaking_question_id' => $question->id,
            ],
            ['submitted_at' => now()]
        );

        return response()->json(['success' => true]);
    }

    public function submit(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        if ($attempt->completed_at !== null) {
            return response()->json(['error' => 'This exam has already been finished.'], 403);
        }

        AiSpeakingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['evaluation_status' => 'pending', 'failure_reason' => null]
        );

        EvaluateSpeakingSubmission::dispatch($attempt->id);

        return response()->json([
            'success'           => true,
            'evaluation_status' => 'pending',
            'message'           => 'Speaking submitted. AI evaluation queued.',
        ]);
    }

    public function result(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        $evaluation      = $attempt->aiSpeakingEvaluation;
        $questions       = $attempt->testSet->speakingQuestions()->orderBy('part')->orderBy('id')->get();
        $existingAnswers = $attempt->speakingAnswers()->get()->keyBy('speaking_question_id');

        $questionData = $questions->map(function (SpeakingQuestion $q) use ($existingAnswers) {
            $answer = $existingAnswers->get($q->id);

            return [
                'id'           => $q->id,
                'part'         => $q->part,
                'question_text' => $q->question_text,
                'transcript'   => $answer?->transcript_text,
                'band_score'   => $answer?->band_score,
                'evaluation'   => $answer?->evaluation_json ? json_decode($answer->evaluation_json, true) : null,
            ];
        });

        return response()->json([
            'attempt_id'        => $attempt->id,
            'speaking_band'     => $evaluation?->band_score,
            'evaluation_status' => $evaluation?->evaluation_status ?? 'not_started',
            'failure_reason'    => $evaluation?->failure_reason,
            'questions'         => $questionData,
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

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
