<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListeningSection extends Model
{
    protected $fillable = ['test_id', 'section_number', 'audio_path', 'passage_text'];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }
}
