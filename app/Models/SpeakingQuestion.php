<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpeakingQuestion extends Model
{
    protected $fillable = [
        'test_id', 'part', 'question_text', 'audio_path', 
        'time_limit', 'preparation_instructions'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
