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
        'evaluation_text',
        'band_score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }
}
