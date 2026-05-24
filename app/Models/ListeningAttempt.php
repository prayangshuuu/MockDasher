<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListeningAttempt extends Model
{
    protected $fillable = [
        'user_id', 'test_set_id', 'test_attempt_id', 'current_section', 'status',
        'total_correct', 'band_score',
        'started_at', 'transfer_started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'transfer_started_at' => 'datetime',
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

    public function answers()
    {
        return $this->hasMany(ListeningAnswer::class, 'test_attempt_id');
    }

    public function getScoreAttribute(): int
    {
        if ($this->total_correct !== null) {
            return $this->total_correct;
        }
        return $this->calculateRawScore();
    }

    public function getBandAttribute(): float
    {
        if ($this->band_score !== null) {
            return $this->band_score;
        }
        return self::rawToBand($this->calculateRawScore());
    }

    /**
     * Evaluate all answers, persist score + band.
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
     * Calculate raw score: case-insensitive, pipe-delimited support.
     */
    public function calculateRawScore(): int
    {
        $correctCount = 0;
        foreach ($this->answers()->with('question')->get() as $answer) {
            if (!$answer->question) continue;

            $userAnswer = strtolower(trim($answer->answer_text ?? ''));
            if ($userAnswer === '') continue;

            $validAnswers = array_map(
                fn($a) => strtolower(trim($a)),
                explode('|', $answer->question->correct_answer ?? '')
            );

            if (in_array($userAnswer, $validAnswers)) {
                $correctCount++;
            }
        }
        return $correctCount;
    }

    /**
     * Band Score Rules (same as Reading for Academic):
     */
    public static function rawToBand(int $rawScore): float
    {
        if ($rawScore >= 39) return 9.0;
        if ($rawScore >= 37) return 8.5;
        if ($rawScore >= 35) return 8.0;
        if ($rawScore >= 33) return 7.5;
        if ($rawScore >= 30) return 7.0;
        if ($rawScore >= 27) return 6.5;
        if ($rawScore >= 23) return 6.0;
        if ($rawScore >= 19) return 5.5;
        if ($rawScore >= 15) return 5.0;
        if ($rawScore >= 13) return 4.5;
        if ($rawScore >= 10) return 4.0;
        if ($rawScore >= 8)  return 3.5;
        if ($rawScore >= 6)  return 3.0;
        if ($rawScore >= 4)  return 2.5;

        return 0.0;
    }
}
