<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use Illuminate\Http\Request;

class TestHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $attempts = $user->testAttempts()
            ->with(['testSet.test'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate Stats from real data
        $completedAttempts = $user->testAttempts()
            ->where('status', 'completed')
            ->get();

        $testsCompleted = $completedAttempts->count();

        $averageBandScore = $completedAttempts->avg(function ($attempt) {
            return $attempt->overall_band;
        }) ?: 0;
        $averageBandScore = round($averageBandScore * 2) / 2; // Round to nearest 0.5

        // Module stats for strongest module — real data only
        $readingAvg = ReadingAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->avg('band_score') ?: 0;

        $listeningAvg = ListeningAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->avg('band_score') ?: 0;

        // Only include modules that have real scores
        $moduleStats = [];
        if ($readingAvg > 0) {
            $moduleStats['Reading'] = $readingAvg;
        }
        if ($listeningAvg > 0) {
            $moduleStats['Listening'] = $listeningAvg;
        }

        if (!empty($moduleStats)) {
            arsort($moduleStats);
            $strongestModuleName = array_key_first($moduleStats);
            $strongestModuleScore = $moduleStats[$strongestModuleName];
        } else {
            $strongestModuleName = null;
            $strongestModuleScore = null;
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

        return view('user.history.index', [
            'attempts' => $attempts,
            'stats' => [
                'averageBandScore' => $averageBandScore,
                'testsCompleted' => $testsCompleted,
                'strongestModule' => [
                    'name' => $strongestModuleName,
                    'score' => $strongestModuleScore,
                ],
                'trend' => $trend,
            ],
        ]);
    }

    public function show(Request $request, \App\Models\TestAttempt $attempt)
    {
        // Ensure the user owns this attempt
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }

        $attempt->load(['testSet.test', 'writingAnswers', 'readingAttempt', 'listeningAttempt']);

        return view('user.history.show', compact('attempt'));
    }
}
