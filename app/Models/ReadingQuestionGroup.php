<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingQuestionGroup extends Model
{
    protected $fillable = ['reading_passage_id', 'group_instruction', 'question_type', 'sort_order'];

    public function passage()
    {
        return $this->belongsTo(ReadingPassage::class, 'reading_passage_id');
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable')->orderBy('id');
    }
}
