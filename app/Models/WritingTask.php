<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WritingTask extends Model
{
    protected $fillable = [
        'test_set_id', 'task_number', 'task_title', 'task_description', 
        'task_prompt', 'instruction_text', 'minimum_word_count'
    ];

    public function testSet()
    {
        return $this->belongsTo(TestSet::class);
    }

    public function images()
    {
        return $this->hasMany(WritingTaskImage::class);
    }

    public function answers()
    {
        return $this->hasMany(WritingAnswer::class);
    }
}
