<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpeakingQuestion extends Model
{
    protected $fillable = [
        'test_set_id', 'part', 'question_text', 'audio_path',
        'time_limit', 'preparation_instructions',
    ];

    public function testSet()
    {
        return $this->belongsTo(TestSet::class);
    }
}
