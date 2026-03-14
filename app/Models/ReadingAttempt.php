<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingAttempt extends Model
{
    protected $fillable = ['user_id', 'test_set_id', 'status', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testSet()
    {
        return $this->belongsTo(TestSet::class);
    }

    public function answers()
    {
        return $this->hasMany(ReadingAnswer::class, 'test_attempt_id');
    }
}
