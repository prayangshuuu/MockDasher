<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingAttempt extends Model
{
    protected $fillable = ['user_id', 'test_set_id', 'test_attempt_id', 'status', 'total_correct', 'band_score', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'band_score' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testSet()
    {
        return $this->belongsTo(TestSet::class);
    }

    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function answers()
    {
        return $this->hasMany(ReadingAnswer::class, 'test_attempt_id');
    }

    public function getScoreAttribute(): int
    {
        // Use stored value if available (after evaluation)
        if ($this->total_correct !== null) {
            return $this->total_correct;
        }

        return $this->calculateRawScore();
    }

    public function getBandAttribute(): float
    {
        // Use stored value if available (after evaluation)
        if ($this->band_score !== null) {
            return $this->band_score;
        }
        $rawScore = $this->calculateRawScore();

        return self::rawToBand($rawScore);
    }

    /**
     * Evaluate all answers against correct_answer, persist results.
     */
    public function evaluate(): array
    {
        $correctCount = $this->calculateRawScore();
        $band = self::rawToBand($correctCount);

        $this->update([
            'total_correct' => $correctCount,
            'band_score' => $band,
        ]);

        return [
            'total_correct' => $correctCount,
            'band_score' => $band,
            'total_questions' => $this->answers->count(),
        ];
    }

    /**
     * Calculate raw score by comparing user answers to correct answers.
     * Uses case-insensitive, trimmed comparison.
     */
    public function calculateRawScore(): int
    {
        $correctCount = 0;
        foreach ($this->answers()->with('question')->get() as $answer) {
            if (! $answer->question) {
                continue;
            }

            $userAnswer = self::normalizeAnswer($answer->answer_text ?? '');
            $correctAnswer = trim($answer->question->correct_answer ?? '');

            if ($userAnswer === '' || $correctAnswer === '') {
                continue;
            }

            // Support multiple valid answers separated by pipe (|)
            $validAnswers = array_map(fn ($a) => self::normalizeAnswer($a), explode('|', $answer->question->correct_answer));

            if (in_array($userAnswer, $validAnswers, true)) {
                $correctCount++;
            }
        }

        return $correctCount;
    }

    private static function normalizeAnswer(?string $answer): string
    {
        $answer = strtolower(strip_tags((string) $answer));
        $answer = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $answer) ?? $answer;
        $answer = preg_replace('/\s+/u', ' ', $answer) ?? $answer;

        return trim($answer);
    }

    /**
     * Convert raw score to IELTS Band using the exact rules provided.
     *
     * Band Score Rules:
     *  4–5  → 2.5    6–7  → 3.0    8–9  → 3.5    10–12 → 4.0
     * 13–14 → 4.5   15–18 → 5.0   19–22 → 5.5    23–26 → 6.0
     * 27–29 → 6.5   30–32 → 7.0   33–34 → 7.5    35–36 → 8.0
     * 37–38 → 8.5   39–40 → 9.0
     */
    public static function rawToBand(int $rawScore): float
    {
        if ($rawScore >= 39) {
            return 9.0;
        }
        if ($rawScore >= 37) {
            return 8.5;
        }
        if ($rawScore >= 35) {
            return 8.0;
        }
        if ($rawScore >= 33) {
            return 7.5;
        }
        if ($rawScore >= 30) {
            return 7.0;
        }
        if ($rawScore >= 27) {
            return 6.5;
        }
        if ($rawScore >= 23) {
            return 6.0;
        }
        if ($rawScore >= 19) {
            return 5.5;
        }
        if ($rawScore >= 15) {
            return 5.0;
        }
        if ($rawScore >= 13) {
            return 4.5;
        }
        if ($rawScore >= 10) {
            return 4.0;
        }
        if ($rawScore >= 8) {
            return 3.5;
        }
        if ($rawScore >= 6) {
            return 3.0;
        }
        if ($rawScore >= 4) {
            return 2.5;
        }

        return 0.0;
    }
}
