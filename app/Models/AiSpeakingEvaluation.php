<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSpeakingEvaluation extends Model
{
    protected $fillable = [
        'user_id',
        'test_attempt_id',
        'full_transcript',
        'evaluation_json',   // Full aggregated Gemini JSON for all speaking parts
        'evaluation_text',   // Legacy text field
        'band_score',        // Overall averaged band score
        'evaluation_status',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'band_score' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }
}
