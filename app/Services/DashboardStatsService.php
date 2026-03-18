<?php

namespace App\Services;

use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use App\Models\Test;
use App\Models\TestSet;
use Carbon\Carbon;

class DashboardStatsService
{
    /**
     * Get basic stats for a user.
     */
    public function getStats($user)
    {
        $targetScore = $user->target_band_score ?? 0.0;
        $testsTakenCount = $user->testAttempts()->count();
        $daysToExam = $user->exam_date ? Carbon::now()->startOfDay()->diffInDays($user->exam_date->startOfDay(), false) : null;

        $completedAttempts = $user->testAttempts()
            ->where(['status' => 'completed'])
            ->get();

        $validAttempts = $completedAttempts->filter(fn ($a) => $a->overall_band !== null);
        $avgBandScore = $validAttempts->count() > 0 ? $validAttempts->avg('overall_band') : null;

        if ($avgBandScore !== null) {
            $avgBandScore = round($avgBandScore * 2) / 2;
        }

        return [
            'targetScore' => $targetScore,
            'testsTakenCount' => $testsTakenCount,
            'daysToExam' => $daysToExam,
            'avgBandScore' => $avgBandScore,
        ];
    }

    /**
     * Get module breakdown for a user.
     */
    public function getModuleBreakdown($user)
    {
        $readingAttempts = ReadingAttempt::where(fn ($q) => $q->where('user_id', $user->id)
            ->where('status', 'completed'))
            ->get();
        $readingAvg = $readingAttempts->count() > 0 ? $readingAttempts->avg('band_score') : null;

        $listeningAttempts = ListeningAttempt::where(fn ($q) => $q->where('user_id', $user->id)
            ->where('status', 'completed'))
            ->get();
        $listeningAvg = $listeningAttempts->count() > 0 ? $listeningAttempts->avg('band_score') : null;

        $moduleBreakdown = [];

        if ($readingAvg) {
            $moduleBreakdown[] = [
                'name' => 'Reading',
                'score' => round($readingAvg * 2) / 2,
                'percentage' => round(($readingAvg / 9) * 100),
                'type' => 'primary',
            ];
        }

        if ($listeningAvg) {
            $moduleBreakdown[] = [
                'name' => 'Listening',
                'score' => round($listeningAvg * 2) / 2,
                'percentage' => round(($listeningAvg / 9) * 100),
                'type' => 'primary',
            ];
        }

        // Writing & Speaking don't have automated scoring yet
        $moduleBreakdown[] = [
            'name' => 'Writing',
            'score' => null,
            'percentage' => 0,
            'type' => 'muted',
        ];
        $moduleBreakdown[] = [
            'name' => 'Speaking',
            'score' => null,
            'percentage' => 0,
            'type' => 'muted',
        ];

        return $moduleBreakdown;
    }

    /**
     * Get recommended tests for a user.
     */
    public function getRecommendedTests($user)
    {
        $completedTestSetIds = $user->testAttempts()
            ->where(fn ($q) => $q->where('status', 'completed'))
            ->pluck('test_set_id');

        $completedTestIds = TestSet::query()->whereIn('id', $completedTestSetIds)
            ->pluck('test_id')
            ->unique();

        $recommendedTests = Test::where(fn ($q) => $q->where('status', 'published'))
            ->whereNotIn('id', $completedTestIds)
            ->latest()
            ->take(3)
            ->get();

        if ($recommendedTests->count() < 3) {
            $recommendedTests = Test::query()->where(fn ($q) => $q->where('status', 'published'))
                ->latest()
                ->take(3)
                ->get();
        }

        return $recommendedTests;
    }

    /**
     * Get recent test attempts.
     */
    public function getRecentHistory($user)
    {
        return $user->testAttempts()
            ->with(['testSet.test'])
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Get chart data for past attempts.
     */
    public function getChartData($user)
    {
        $chartAttempts = $user->testAttempts()
            ->where(fn ($q) => $q->where('status', 'completed'))
            ->orderBy('completed_at', 'asc')
            ->take(6)
            ->get();

        return $chartAttempts->map(function ($attempt, $index) use ($chartAttempts) {
            $band = $attempt->overall_band ?? 0;
            $heightPercent = $band > 0 ? round(($band / 9) * 100) : 0;
            $isLast = $index === $chartAttempts->count() - 1;

            return [
                'label' => $isLast ? 'Latest' : 'Test '.($index + 1),
                'score' => $band,
                'height' => $heightPercent, // Returning number instead of string with "%" to remove hardcoding
            ];
        })->values()->toArray();
    }
}
