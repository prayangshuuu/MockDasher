<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WritingTaskImage extends Model
{
    protected $fillable = ['writing_task_id', 'image_path'];

    public function task()
    {
        return $this->belongsTo(WritingTask::class, 'writing_task_id');
    }
}
