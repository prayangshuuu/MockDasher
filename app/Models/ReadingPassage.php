<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingPassage extends Model
{
    protected $fillable = ['test_id', 'passage_number', 'title', 'content'];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }
}
