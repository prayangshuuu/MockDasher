<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListeningAttempt extends Model
{
    protected $fillable = [
        'user_id', 'test_set_id', 'current_section', 'status',
        'started_at', 'transfer_started_at', 'completed_at'
    ];

    protected function casts(): array
    {
        return [
            'started_at'           => 'datetime',
            'transfer_started_at'  => 'datetime',
            'completed_at'         => 'datetime',
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
        return $this->hasMany(ListeningAnswer::class, 'test_attempt_id');
    }
}
