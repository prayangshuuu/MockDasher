<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use App\Models\Test;
use App\Models\TestAttempt;
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

        // Average Band Score — computed from completed TestAttempts
        $completedAttempts = $user->testAttempts()->where('status', 'completed')->get();
        $scores = $completedAttempts->map(fn($a) => $a->overall_band)->filter()->values();
        $avgBandScore = $scores->isNotEmpty()
            ? round($scores->avg() * 2) / 2
            : null;

        // 2. Module Breakdown — real data from Reading/Listening attempts
        $readingAvg = ReadingAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->avg('band_score');

        $listeningAvg = ListeningAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->avg('band_score');

        $moduleBreakdown = [];

        if ($readingAvg) {
            $moduleBreakdown[] = [
                'name' => 'Reading',
                'score' => round($readingAvg * 2) / 2,
                'percentage' => round(($readingAvg / 9) * 100),
                'color' => 'primary',
            ];
        }

        if ($listeningAvg) {
            $moduleBreakdown[] = [
                'name' => 'Listening',
                'score' => round($listeningAvg * 2) / 2,
                'percentage' => round(($listeningAvg / 9) * 100),
                'color' => 'primary',
            ];
        }

        // Writing & Speaking don't have automated scoring yet
        $moduleBreakdown[] = [
            'name' => 'Writing',
            'score' => null,
            'percentage' => 0,
            'color' => 'slate-300',
        ];
        $moduleBreakdown[] = [
            'name' => 'Speaking',
            'score' => null,
            'percentage' => 0,
            'color' => 'slate-300',
        ];

        // 3. Recommended Tests (published tests the user hasn't completed)
        $completedTestSetIds = $user->testAttempts()
            ->where('status', 'completed')
            ->pluck('test_set_id')
            ->unique();

        $completedTestIds = \App\Models\TestSet::whereIn('id', $completedTestSetIds)
            ->pluck('test_id')
            ->unique();

        $recommendedTests = Test::where('status', 'published')
            ->whereNotIn('id', $completedTestIds)
            ->latest()
            ->take(3)
            ->get();

        // If not enough untaken tests, just show latest published
        if ($recommendedTests->count() < 3) {
            $recommendedTests = Test::where('status', 'published')
                ->latest()
                ->take(3)
                ->get();
        }

        // 4. Recent Test History
        $recentAttempts = $user->testAttempts()
            ->with(['testSet.test'])
            ->latest()
            ->take(5)
            ->get();

        // 5. Score Improvement Chart Data — real scores from completed attempts
        $chartAttempts = $user->testAttempts()
            ->where('status', 'completed')
            ->orderBy('completed_at', 'asc')
            ->take(6)
            ->get();

        $chartData = $chartAttempts->map(function ($attempt, $index) use ($chartAttempts) {
            $band = $attempt->overall_band ?? 0;
            $heightPercent = $band > 0 ? round(($band / 9) * 100) : 0;
            $isLast = $index === $chartAttempts->count() - 1;
            return [
                'label' => $isLast ? 'Latest' : 'Test ' . ($index + 1),
                'score' => $band,
                'height' => $heightPercent . '%',
            ];
        })->values()->toArray();

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
