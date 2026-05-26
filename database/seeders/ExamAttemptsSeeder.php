<?php

namespace Database\Seeders;

use App\Models\AiSpeakingEvaluation;
use App\Models\AiWritingEvaluation;
use App\Models\ListeningAnswer;
use App\Models\ListeningAttempt;
use App\Models\Question;
use App\Models\ReadingAnswer;
use App\Models\ReadingAttempt;
use App\Models\SpeakingAnswer;
use App\Models\SpeakingQuestion;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use App\Models\WritingAnswer;
use App\Models\WritingTask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamAttemptsSeeder extends Seeder
{
    /**
     * Seed 4 completed exam attempts for Daniel Rozario.
     *
     * Each attempt has module band scores between 5.0 and 7.0, giving an
     * overall IELTS band of 5.5–6.5 so the dashboard shows meaningful stats.
     *
     * Reading / Listening bands are derived from correct-answer counts.
     * Band  5.0 → 15–18 correct  (we use 17)
     * Band  5.5 → 19–22 correct  (we use 20)
     * Band  6.0 → 23–26 correct  (we use 25)
     * Band  6.5 → 27–29 correct  (we use 28)
     * Band  7.0 → 30–32 correct  (we use 31)
     *
     * Writing / Speaking bands are stored directly on the AI-evaluation rows.
     */
    public function run(): void
    {
        /** @var User $daniel */
        $daniel = User::where('email', 'user@prayangshu.com')->firstOrFail();

        // We only have one TestSet seeded; reuse it for all 4 attempts.
        $testSet = TestSet::firstOrFail();

        // Fetch all reading questions (via their groups → passage → test-set)
        $readingQuestionIds = Question::whereIn(
            'questionable_id',
            DB::table('reading_question_groups')
                ->whereIn('reading_passage_id', DB::table('reading_passages')->where('test_set_id', $testSet->id)->pluck('id'))
                ->pluck('id')
        )->where('questionable_type', 'App\\Models\\ReadingQuestionGroup')->pluck('id')->values()->all();

        // Fetch all listening questions
        $listeningQuestionIds = Question::where('questionable_type', 'App\\Models\\ListeningSection')
            ->whereIn('questionable_id', DB::table('listening_sections')->where('test_set_id', $testSet->id)->pluck('id'))
            ->pluck('id')->values()->all();

        // Fetch writing tasks for this test-set
        $writingTasks = WritingTask::where('test_set_id', $testSet->id)->orderBy('task_number')->get();

        // Fetch speaking questions for this test-set
        $speakingQuestions = SpeakingQuestion::where('test_set_id', $testSet->id)->get();

        /*
         * Define the 4 attempts.
         * Each entry: [reading_correct, listening_correct, writing_band, speaking_band, days_ago]
         * These produce overall bands of: 6.0, 5.5, 6.5, 6.0
         */
        $attemptConfigs = [
            // Attempt 1 – Avg ≈ 6.0  (5+7+6+6)
            [
                'reading_correct'   => 17,   // Band 5.0
                'listening_correct' => 31,   // Band 7.0
                'writing_band'      => 6.0,
                'speaking_band'     => 6.0,
                'days_ago'          => 60,
            ],
            // Attempt 2 – Avg ≈ 5.5  (5+5.5+6+5.5)
            [
                'reading_correct'   => 17,   // Band 5.0
                'listening_correct' => 20,   // Band 5.5
                'writing_band'      => 6.0,
                'speaking_band'     => 5.5,
                'days_ago'          => 45,
            ],
            // Attempt 3 – Avg ≈ 6.5  (6.5+7+6.5+6.5)
            [
                'reading_correct'   => 28,   // Band 6.5
                'listening_correct' => 31,   // Band 7.0
                'writing_band'      => 6.5,
                'speaking_band'     => 6.0,
                'days_ago'          => 30,
            ],
            // Attempt 4 – Avg ≈ 6.0  (6+5.5+6.5+6)
            [
                'reading_correct'   => 25,   // Band 6.0
                'listening_correct' => 20,   // Band 5.5
                'writing_band'      => 6.5,
                'speaking_band'     => 6.0,
                'days_ago'          => 7,
            ],
        ];

        foreach ($attemptConfigs as $i => $cfg) {
            $attemptNumber = $i + 1;
            $startedAt     = now()->subDays($cfg['days_ago'])->setTime(9, 0);
            $completedAt   = $startedAt->copy()->addHours(3);

            // ── TestAttempt ──────────────────────────────────────────────
            /** @var TestAttempt $attempt */
            $attempt = TestAttempt::create([
                'user_id'          => $daniel->id,
                'test_set_id'      => $testSet->id,
                'status'           => 'completed',
                'started_at'       => $startedAt,
                'writing_started_at'  => $startedAt->copy()->addHours(1, 30),
                'speaking_started_at' => $startedAt->copy()->addHours(2, 30),
                'completed_at'     => $completedAt,
            ]);

            $this->command->info("Seeding attempt #{$attemptNumber} (ID: {$attempt->id}) …");

            // ── Reading Attempt ──────────────────────────────────────────
            $this->seedReadingAttempt(
                $attempt,
                $daniel,
                $testSet,
                $readingQuestionIds,
                $cfg['reading_correct']
            );

            // ── Listening Attempt ────────────────────────────────────────
            $this->seedListeningAttempt(
                $attempt,
                $daniel,
                $testSet,
                $listeningQuestionIds,
                $cfg['listening_correct']
            );

            // ── Writing Answers + AI Evaluation ─────────────────────────
            $this->seedWritingAttempt($attempt, $daniel, $writingTasks, $cfg['writing_band']);

            // ── Speaking Answers + AI Evaluation ────────────────────────
            $this->seedSpeakingAttempt($attempt, $daniel, $speakingQuestions, $cfg['speaking_band']);
        }

        $this->command->info('✅ 4 exam attempts seeded for Daniel Rozario.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function seedReadingAttempt(
        TestAttempt $attempt,
        User $user,
        TestSet $testSet,
        array $questionIds,
        int $correctCount
    ): void {
        $band = ReadingAttempt::rawToBand($correctCount);

        /** @var ReadingAttempt $readingAttempt */
        $readingAttempt = ReadingAttempt::create([
            'user_id'         => $user->id,
            'test_set_id'     => $testSet->id,
            'test_attempt_id' => $attempt->id,
            'status'          => 'completed',
            'total_correct'   => $correctCount,
            'band_score'      => $band,
            'started_at'      => $attempt->started_at,
            'completed_at'    => $attempt->started_at->copy()->addMinutes(60),
        ]);

        $total = count($questionIds);

        // Shuffle so the "correct" ones are randomly distributed
        $shuffled = $questionIds;
        shuffle($shuffled);

        foreach ($shuffled as $idx => $questionId) {
            $question   = Question::find($questionId);
            $isCorrect  = $idx < $correctCount;

            // Pick the right or a wrong answer
            $answerText = $isCorrect
                ? $this->correctAnswerFor($question)
                : $this->wrongAnswerFor($question);

            ReadingAnswer::create([
                'user_id'         => $user->id,
                'test_attempt_id' => $readingAttempt->id,
                'question_id'     => $questionId,
                'answer_text'     => $answerText,
                'is_flagged'      => false,
            ]);
        }
    }

    private function seedListeningAttempt(
        TestAttempt $attempt,
        User $user,
        TestSet $testSet,
        array $questionIds,
        int $correctCount
    ): void {
        $band = ListeningAttempt::rawToBand($correctCount);

        /** @var ListeningAttempt $listeningAttempt */
        $listeningAttempt = ListeningAttempt::create([
            'user_id'            => $user->id,
            'test_set_id'        => $testSet->id,
            'test_attempt_id'    => $attempt->id,
            'current_section'    => 4,
            'status'             => 'completed',
            'total_correct'      => $correctCount,
            'band_score'         => $band,
            'started_at'         => $attempt->started_at->copy()->addMinutes(60),
            'transfer_started_at'=> $attempt->started_at->copy()->addMinutes(90),
            'completed_at'       => $attempt->started_at->copy()->addMinutes(110),
        ]);

        $shuffled = $questionIds;
        shuffle($shuffled);

        foreach ($shuffled as $idx => $questionId) {
            $question  = Question::find($questionId);
            $isCorrect = $idx < $correctCount;

            $answerText = $isCorrect
                ? $this->correctAnswerFor($question)
                : $this->wrongAnswerFor($question);

            ListeningAnswer::create([
                'user_id'         => $user->id,
                'test_attempt_id' => $listeningAttempt->id,
                'question_id'     => $questionId,
                'answer_text'     => $answerText,
                'is_flagged'      => false,
            ]);
        }
    }

    private function seedWritingAttempt(
        TestAttempt $attempt,
        User $user,
        $writingTasks,
        float $overallBand
    ): void {
        // Task 1 band is slightly lower, Task 2 slightly higher (realistic)
        $task1Band = max(5.0, $overallBand - 0.5);
        $task2Band = $overallBand;

        $sampleTask1 = 'The tables illustrate how the total population of New York City changed '
            . 'between 1800 and 2000, along with how the five boroughs — Manhattan, Brooklyn, '
            . 'Bronx, Queens and Staten Island — evolved over the same period. '
            . 'Overall, the city saw dramatic growth, rising from under one million to over '
            . 'eight million residents over two centuries. Manhattan dominated in the early '
            . 'period but was later surpassed by Brooklyn. By 2000, Queens had become the '
            . 'second-largest borough. Staten Island remained consistently the smallest '
            . 'throughout the entire period under review.';

        $sampleTask2 = 'I strongly agree that access to clean water is a fundamental human right. '
            . 'Clean water is essential for health, sanitation, and basic dignity. '
            . 'Governments should therefore ensure that every household receives a free basic '
            . 'allocation. Without water, survival itself is impossible, so treating it as a '
            . 'commodity available only to those who can pay inevitably harms the most '
            . 'vulnerable members of society. Countries such as South Africa have enshrined '
            . 'the right to water in their constitutions, providing a minimum free supply to '
            . 'all citizens. In conclusion, while broader economic arguments exist, basic '
            . 'water provision free of charge is both morally justifiable and practically '
            . 'necessary for equitable development.';

        foreach ($writingTasks as $task) {
            $isTask1    = $task->task_number === 1;
            $answerText = $isTask1 ? $sampleTask1 : $sampleTask2;
            $wordCount  = str_word_count($answerText);
            $band       = $isTask1 ? $task1Band : $task2Band;

            WritingAnswer::create([
                'user_id'         => $user->id,
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => $task->id,
                'answer_text'     => $answerText,
                'word_count'      => $wordCount,
                'submitted_at'    => $attempt->started_at->copy()->addHours(2),
                'band_score'      => $band,
                'evaluation_json' => json_encode($this->writingEvaluationJson($band, $isTask1)),
            ]);
        }

        // AI Writing Evaluation record (needed for overall_band calculation)
        AiWritingEvaluation::create([
            'user_id'                => $user->id,
            'test_attempt_id'        => $attempt->id,
            'task_1_answer'          => $sampleTask1,
            'task_2_answer'          => $sampleTask2,
            'task_1_band_score'      => $task1Band,
            'task_2_band_score'      => $task2Band,
            'band_score'             => $overallBand,
            'evaluation_status'      => 'completed',
            'task_1_evaluation_json' => json_encode($this->writingEvaluationJson($task1Band, true)),
            'task_2_evaluation_json' => json_encode($this->writingEvaluationJson($task2Band, false)),
        ]);
    }

    private function seedSpeakingAttempt(
        TestAttempt $attempt,
        User $user,
        $speakingQuestions,
        float $overallBand
    ): void {
        $sampleTranscripts = [
            'I walk quite a lot in my daily life. I usually walk to the local shops and sometimes take evening strolls in the park near my house.',
            'When I was at school I definitely walked more because the school was not far from my home and I enjoyed the walk with friends.',
            'Near where I live there is a lovely riverside path and a small park with benches. Both are very popular with local residents.',
            'Yes, I would love to go on a walking holiday. I think it would be a great way to see the countryside and stay fit at the same time.',
            'I would love to see the film Inception again with my friends. It is a complex thriller that rewards repeated viewing, and discussing it afterwards is always fascinating.',
            'In my country the most popular theatre shows are musicals and comedies. There is also a strong tradition of classical drama performed in outdoor amphitheatres.',
            'Getting tickets depends on the city. In major cities tickets can be expensive and sell out quickly, but regional theatres are more accessible.',
            'I think theatres should offer student discounts and create more interactive or immersive productions to attract a younger audience.',
            'Many people are attracted to acting because of the creative freedom it offers and the opportunity to experience different lives through characters.',
            'A good actor needs empathy, discipline, and strong memorisation skills. Adaptability is also crucial because every performance is slightly different.',
            'Acting can be unpredictable work — long periods without roles, irregular income, and the constant pressure to audition can be very stressful.',
        ];

        $fullTranscript = '';

        // Build per-question evaluation array — this is what both history/show.blade.php
        // and speaking-test/result.blade.php iterate over.
        $perQuestionEvals = [];

        foreach ($speakingQuestions as $idx => $question) {
            $transcriptText = $sampleTranscripts[$idx] ?? 'I think this is a very interesting question and I would like to share my thoughts on it.';
            $fullTranscript .= "Q: {$question->question_text}\nA: {$transcriptText}\n\n";
            $questionBand   = max(5.0, $overallBand + ($idx % 2 === 0 ? 0 : -0.5));

            $evaluation = $this->speakingEvaluationJson($questionBand);

            SpeakingAnswer::create([
                'user_id'              => $user->id,
                'test_attempt_id'      => $attempt->id,
                'speaking_question_id' => $question->id,
                'transcript_text'      => $transcriptText,
                'duration_seconds'     => 40 + ($idx * 5),
                'submitted_at'         => $attempt->started_at->copy()->addHours(2)->addMinutes(30 + $idx),
                'band_score'           => $questionBand,
                'evaluation_json'      => json_encode($evaluation),
            ]);

            // Each entry in the AI evaluation JSON array must have:
            // question_id, part, question, band_score, evaluation (criteria object)
            $perQuestionEvals[] = [
                'question_id' => $question->id,
                'part'        => $question->part,
                'question'    => $question->question_text,
                'band_score'  => $questionBand,
                'evaluation'  => $evaluation,
            ];
        }

        // AI Speaking Evaluation record (needed for overall_band calculation)
        AiSpeakingEvaluation::create([
            'user_id'           => $user->id,
            'test_attempt_id'   => $attempt->id,
            'full_transcript'   => trim($fullTranscript),
            'band_score'        => $overallBand,
            'evaluation_status' => 'completed',
            // The view iterates this as an array of per-question objects
            'evaluation_json'   => json_encode($perQuestionEvals),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Answer helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Return the first valid correct answer for a question.
     */
    private function correctAnswerFor(?Question $question): string
    {
        if (! $question || ! $question->correct_answer) {
            return 'TRUE';
        }

        // Pipe-separated alternatives → take first
        return explode('|', $question->correct_answer)[0];
    }

    /**
     * Return a plausible wrong answer for a question.
     */
    private function wrongAnswerFor(?Question $question): string
    {
        if (! $question) {
            return 'NOT GIVEN';
        }

        $correct = strtoupper(trim(explode('|', $question->correct_answer)[0]));

        return match ($question->question_type) {
            'true_false_not_given' => match ($correct) {
                'TRUE'      => 'FALSE',
                'FALSE'     => 'NOT GIVEN',
                default     => 'FALSE',
            },
            'yes_no_not_given' => match ($correct) {
                'YES'       => 'NO',
                'NO'        => 'NOT GIVEN',
                default     => 'NO',
            },
            'multiple_choice' => $correct === 'A' ? 'B' : 'A',
            default            => 'incorrect answer',
        };
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Evaluation JSON builders
    // ──────────────────────────────────────────────────────────────────────────

    private function writingEvaluationJson(float $band, bool $isTask1): array
    {
        $task = $isTask1 ? 'Task 1' : 'Task 2';

        return [
            'task'                      => $task,
            'overall_band'              => $band,
            'task_achievement'          => $band,
            'coherence_and_cohesion'    => $band,
            'lexical_resource'          => $band - 0.5,
            'grammatical_range_accuracy'=> $band + 0.5,
            'feedback'                  => "Good {$task} response with clear structure. "
                . 'Consider expanding your range of vocabulary for a higher band.',
        ];
    }

    private function speakingEvaluationJson(float $band): array
    {
        // Use the new v2 schema that _evaluation.blade.php expects
        return [
            'criteria_scores' => [
                'fluency_and_coherence'          => $band,
                'lexical_resource'               => max(4.0, $band - 0.5),
                'grammatical_range_and_accuracy' => $band,
                'pronunciation'                  => min(9.0, $band + 0.5),
            ],
            'detailed_feedback'           => 'Good communicative ability with clear ideas. '
                . 'Some hesitation noted but overall fluency is at the expected level for this band.',
            'vocabulary_corrections'      => [],
            'grammar_corrections'         => [],
            'suggestions_for_improvement' => 'Focus on expanding topic-specific vocabulary '
                . 'and using a wider range of complex sentence structures to achieve a higher band.',
        ];
    }
}
