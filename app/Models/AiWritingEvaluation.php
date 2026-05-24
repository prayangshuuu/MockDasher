<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiWritingEvaluation extends Model
{
    protected $fillable = [
        'user_id',
        'test_attempt_id',
        'task_1_answer',
        'task_2_answer',
        'task_1_evaluation_json',    // Full Gemini JSON for Task 1
        'task_2_evaluation_json',    // Full Gemini JSON for Task 2
        'task_1_band_score',         // Band score for Task 1
        'task_2_band_score',         // Band score for Task 2
        'evaluation_text',           // Legacy combined evaluation text
        'band_score',                // Overall averaged band score
        'evaluation_status',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'task_1_band_score' => 'float',
            'task_2_band_score' => 'float',
            'band_score' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }
}
