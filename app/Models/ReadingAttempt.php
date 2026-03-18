<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingAttempt extends Model
{
    protected $fillable = ['user_id', 'test_set_id', 'status', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
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
        return $this->hasMany(ReadingAnswer::class, 'test_attempt_id');
    }

    public function getBandScoreAttribute(): float
    {
        $rawScore = $this->calculateRawScore();
        return $this->rawToBand($rawScore, 'reading');
    }

    public function calculateRawScore(): int
    {
        $correctCount = 0;
        foreach ($this->answers as $answer) {
            if ($answer->question && trim(strtolower($answer->answer_text)) === trim(strtolower($answer->question->correct_answer))) {
                $correctCount++;
            }
        }
        return $correctCount;
    }

    protected function rawToBand(int $rawScore, string $type): float
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
        return 3.5;
    }
}
