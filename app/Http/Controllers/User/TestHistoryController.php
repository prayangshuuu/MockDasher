<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $attempts = $user->testAttempts()
            ->with(['test', 'testSet'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Calculate Stats
        $completedAttempts = $user->testAttempts()
            ->where('status', 'completed')
            ->get();
            
        $testsCompleted = $completedAttempts->count();
        
        $averageBandScore = $completedAttempts->avg(function($attempt) {
            return $attempt->overall_band;
        }) ?: 0;
        $averageBandScore = round($averageBandScore * 2) / 2; // Round to nearest 0.5
        
        // Modules stats for strongest module
        $readingAvg = \App\Models\ReadingAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->avg('band_score') ?: 0;
            
        $listeningAvg = \App\Models\ListeningAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->avg('band_score') ?: 0;
            
        // Since writing/speaking are manually scored or mocked in attempts, we use placeholders
        $writingAvg = 6.5; 
        $speakingAvg = 7.0;

        $moduleStats = [
            'Reading' => $readingAvg,
            'Listening' => $listeningAvg,
            'Writing' => $writingAvg,
            'Speaking' => $speakingAvg,
        ];
        
        arsort($moduleStats);
        $strongestModuleName = array_key_first($moduleStats);
        $strongestModuleScore = $moduleStats[$strongestModuleName];

        return view('user.history.index', [
            'attempts' => $attempts,
            'stats' => [
                'averageBandScore' => $averageBandScore,
                'testsCompleted' => $testsCompleted,
                'strongestModule' => [
                    'name' => $strongestModuleName,
                    'score' => $strongestModuleScore
                ],
                'trend' => '+0.2' // Mock trend for now
            ]
        ]);
    }

    public function show(Request $request, \App\Models\TestAttempt $attempt)
    {
        // Ensure the user owns this attempt
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }

        $attempt->load(['test.collection', 'answers']);
        
        return view('user.history.show', compact('attempt'));
    }
}
