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

    // Legacy direct questions (still works for existing data)
    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }

    // New: question groups
    public function questionGroups()
    {
        return $this->hasMany(ReadingQuestionGroup::class)->orderBy('sort_order');
    }
}
