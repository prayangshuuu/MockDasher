<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    protected $fillable = ['user_id', 'test_set_id', 'status', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
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

    public function test()
    {
        return $this->hasOneThrough(
            Test::class,
            TestSet::class,
            'id', // Foreign key on test_sets table...
            'id', // Foreign key on tests table...
            'test_set_id', // Local key on test_attempts table...
            'test_id' // Local key on test_sets table...
        );
    }

    public function writingAnswers()
    {
        return $this->hasMany(WritingAnswer::class, 'test_attempt_id');
    }

    public function readingAttempt()
    {
        return $this->hasOne(ReadingAttempt::class, 'test_set_id', 'test_set_id')
                    ->where('user_id', $this->user_id);
    }

    public function listeningAttempt()
    {
        return $this->hasOne(ListeningAttempt::class, 'test_set_id', 'test_set_id')
                    ->where('user_id', $this->user_id);
    }

    public function getOverallBandAttribute(): ?float
    {
        $scores = [];
        
        $ra = $this->readingAttempt;
        if ($ra && $ra->status === 'completed') {
            $scores[] = $ra->band_score;
        }
        
        $la = $this->listeningAttempt;
        if ($la && $la->status === 'completed') {
            $scores[] = $la->band_score;
        }

        if (empty($scores)) return null;

        $average = array_sum($scores) / count($scores);
        
        // IELTS rounding: round to nearest 0.5
        return round($average * 2) / 2;
    }

    public function getReadingBandAttribute(): ?float
    {
        $ra = $this->readingAttempt;
        return ($ra && $ra->status === 'completed') ? $ra->band_score : null;
    }

    public function getListeningBandAttribute(): ?float
    {
        $la = $this->listeningAttempt;
        return ($la && $la->status === 'completed') ? $la->band_score : null;
    }

    public function getReadingScoreAttribute(): ?int
    {
        $ra = $this->readingAttempt;
        return ($ra && $ra->status === 'completed') ? $ra->score : null;
    }

    public function getListeningScoreAttribute(): ?int
    {
        $la = $this->listeningAttempt;
        return ($la && $la->status === 'completed') ? $la->score : null;
    }

    public function getTimeSpentAttribute(): ?string
    {
        if ($this->started_at && $this->completed_at) {
            $diff = $this->started_at->diff($this->completed_at);
            $hours = $diff->h + ($diff->days * 24);
            $minutes = $diff->i;
            if ($hours > 0) {
                return "{$hours}h {$minutes}m";
            }
            return "{$minutes}m";
        }
        return null;
    }
}
