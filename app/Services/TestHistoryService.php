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
        // Use DB-level queries instead of grouping large collections in memory
        $testsCompleted = $user->testAttempts()->where('status', 'completed')->count();

        $averageBandScore = $user->testAttempts()->where('status', 'completed')->avg('overall_band') ?: 0;
        $averageBandScore = round($averageBandScore * 2) / 2;

        // Module stats for strongest module — DB level average
        $readingAvg = ReadingAttempt::query()->where('user_id', $user->id)
            ->where('status', 'completed')
            ->avg('band_score') ?: 0;

        $listeningAvg = ListeningAttempt::query()->where('user_id', $user->id)
            ->where('status', 'completed')
            ->avg('band_score') ?: 0;

        $moduleStats = [];
        if ($readingAvg > 0) {
            $moduleStats['Reading'] = $readingAvg;
        }
        if ($listeningAvg > 0) {
            $moduleStats['Listening'] = $listeningAvg;
        }

        $strongestModuleName = null;
        $strongestModuleScore = null;

        if (!empty($moduleStats)) {
            arsort($moduleStats);
            $strongestModuleName = array_key_first($moduleStats);
            $strongestModuleScore = $moduleStats[$strongestModuleName];
        }

        // Calculate real trend from last two completed attempts
        $lastTwo = $user->testAttempts()
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(2)
            ->get();

        $trend = null;
        if ($lastTwo->count() === 2) {
            $latest = $lastTwo->first()->overall_band;
            $previous = $lastTwo->last()->overall_band;
            if ($latest !== null && $previous !== null) {
                $diff = $latest - $previous;
                $trend = ($diff >= 0 ? '+' : '') . number_format($diff, 1);
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
