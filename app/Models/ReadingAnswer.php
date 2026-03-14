<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingAnswer extends Model
{
    protected $fillable = ['user_id', 'test_attempt_id', 'question_id', 'answer_text', 'is_flagged'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attempt()
    {
        return $this->belongsTo(ReadingAttempt::class, 'test_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
