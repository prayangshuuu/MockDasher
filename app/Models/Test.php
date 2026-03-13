<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = ['ielts_collection_id', 'title', 'number', 'status'];

    public function collection()
    {
        return $this->belongsTo(IeltsCollection::class, 'ielts_collection_id');
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
