<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $user = auth()->user();

        // 1. Stats Calculation
        $targetScore = $user->target_band_score ?? 0.0;
        $testsTakenCount = $user->testAttempts()->count();
        $daysToExam = $user->exam_date ? \Carbon\Carbon::now()->startOfDay()->diffInDays($user->exam_date->startOfDay(), false) : null;
        
        // Average Band Score logic (mocked for now)
        $avgBandScore = 6.8; // Placeholder

        // 2. Module Breakdown (mocked for now)
        $moduleBreakdown = [
            ['name' => 'Reading', 'score' => 7.5, 'percentage' => 83, 'color' => 'primary'],
            ['name' => 'Listening', 'score' => 8.0, 'percentage' => 88, 'color' => 'primary'],
            ['name' => 'Speaking', 'score' => 6.0, 'percentage' => 66, 'color' => 'orange-500'],
            ['name' => 'Writing', 'score' => 6.5, 'percentage' => 72, 'color' => 'indigo-400'],
        ];

        // 3. Recommended Tests (published and not taken)
        $recommendedTests = \App\Models\Test::where('status', 'published')
            ->latest()
            ->take(3)
            ->get();

        // 4. Recent Test History
        $recentAttempts = $user->testAttempts()
            ->with(['testSet.test'])
            ->latest()
            ->take(5)
            ->get();

        // 5. Score Improvement Chart Data (mocked for now)
        $chartData = [
            ['label' => 'Test 1', 'score' => 5.5, 'height' => '40%'],
            ['label' => 'Test 3', 'score' => 6.0, 'height' => '50%'],
            ['label' => 'Test 5', 'score' => 6.2, 'height' => '55%'],
            ['label' => 'Test 7', 'score' => 6.5, 'height' => '65%'],
            ['label' => 'Test 9', 'score' => 6.8, 'height' => '75%'],
            ['label' => 'Current', 'score' => 7.0, 'height' => '85%'],
        ];

        return view('user.dashboard', compact(
            'user', 
            'targetScore', 
            'testsTakenCount', 
            'daysToExam', 
            'avgBandScore',
            'moduleBreakdown',
            'recommendedTests',
            'recentAttempts',
            'chartData'
        ));
    }
}
