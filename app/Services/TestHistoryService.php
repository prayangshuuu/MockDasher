<?php

namespace App\Services;

use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;

class TestHistoryService
{
    /**
     * Get paginated test attempts for the user.
     */
    public function getPaginatedAttempts($user, $perPage = 10)
    {
        return $user->testAttempts()
            ->with(['testSet.test'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get aggregated stats for the test history page.
     */
    public function getStats($user)
    {
        // 1. Fetch completed test attempts
        $completedAttempts = $user->testAttempts()
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();

        $testsCompleted = $completedAttempts->count();

        // 2. Fetch related reading & listening attempts to prevent N+1 manually
        $testSetIds = $completedAttempts->pluck('test_set_id')->unique();

        $readingAttempts = ReadingAttempt::where('user_id', $user->id)
            ->whereIn('test_set_id', $testSetIds)
            ->where('status', 'completed')
            ->get()
            ->keyBy('test_set_id');

        $listeningAttempts = ListeningAttempt::where('user_id', $user->id)
            ->whereIn('test_set_id', $testSetIds)
            ->where('status', 'completed')
            ->get()
            ->keyBy('test_set_id');

        // Link relations manually to calculate overall_band efficiently via accessor
        $completedAttempts->each(function ($attempt) use ($readingAttempts, $listeningAttempts) {
            $attempt->setRelation('readingAttempt', $readingAttempts->get($attempt->test_set_id));
            $attempt->setRelation('listeningAttempt', $listeningAttempts->get($attempt->test_set_id));
        });

        // Calculate Average
        $validAttempts = $completedAttempts->filter(fn ($a) => $a->overall_band !== null);
        $averageBandScore = $validAttempts->count() > 0 ? $validAttempts->avg('overall_band') : null;
        if ($averageBandScore !== null) {
            $averageBandScore = round($averageBandScore * 2) / 2;
        }

        // Module stats for strongest module (reusing fetched data)
        $readingAvg = $readingAttempts->avg('band_score') ?: 0;
        $listeningAvg = $listeningAttempts->avg('band_score') ?: 0;

        $moduleStats = [];
        if ($readingAvg > 0) {
            $moduleStats['Reading'] = $readingAvg;
        }
        if ($listeningAvg > 0) {
            $moduleStats['Listening'] = $listeningAvg;
        }

        $strongestModuleName = null;
        $strongestModuleScore = null;

        if (! empty($moduleStats)) {
            arsort($moduleStats);
            $strongestModuleName = array_key_first($moduleStats);
            $strongestModuleScore = $moduleStats[$strongestModuleName];
        }

        // Calculate real trend from last two completed attempts
        $lastTwo = $completedAttempts->take(2);

        $trend = null;
        if ($lastTwo->count() === 2) {
            $latest = $lastTwo->first()->overall_band;
            $previous = $lastTwo->last()->overall_band;
            if ($latest !== null && $previous !== null) {
                $diff = $latest - $previous;
                $trend = ($diff >= 0 ? '+' : '').number_format($diff, 1);
            }
        }

        return [
            'averageBandScore' => $averageBandScore,
            'testsCompleted' => $testsCompleted,
            'strongestModule' => [
                'name' => $strongestModuleName,
                'score' => $strongestModuleScore,
            ],
            'trend' => $trend,
        ];
    }
}
