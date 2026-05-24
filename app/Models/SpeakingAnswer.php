<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpeakingAnswer extends Model
{
    protected $fillable = [
        'user_id', 'test_attempt_id', 'speaking_question_id',
        'audio_path', 'transcript_text', 'duration_seconds',
        'evaluation_json', 'band_score', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'band_score'   => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attempt()
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(SpeakingQuestion::class, 'speaking_question_id');
    }
}
