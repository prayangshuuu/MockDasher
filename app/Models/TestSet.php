<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSet extends Model
{
    protected $fillable = ['test_id', 'set_number'];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function writingTasks()
    {
        return $this->hasMany(WritingTask::class);
    }

    public function speakingQuestions()
    {
        return $this->hasMany(SpeakingQuestion::class);
    }

    public function listeningSections()
    {
        return $this->hasMany(ListeningSection::class);
    }

    public function readingPassages()
    {
        return $this->hasMany(ReadingPassage::class);
    }
}
