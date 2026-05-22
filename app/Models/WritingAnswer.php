<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WritingAnswer extends Model
{
    protected $fillable = [
        'user_id', 'test_attempt_id', 'writing_task_id',
        'answer_text', 'word_count', 'submitted_at',
        'evaluation_json', 'band_score',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
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

    public function task()
    {
        return $this->belongsTo(WritingTask::class, 'writing_task_id');
    }
}
