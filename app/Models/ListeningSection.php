<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListeningSection extends Model
{
    protected $fillable = ['test_set_id', 'section_number', 'instruction_text', 'audio_path', 'passage_text'];

    public function testSet()
    {
        return $this->belongsTo(TestSet::class);
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }
}
