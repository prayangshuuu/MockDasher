<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = ['ielts_collection_id', 'title', 'number'];

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
}
