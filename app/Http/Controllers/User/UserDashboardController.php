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
    public function index(Request $request, \App\Services\DashboardStatsService $dashboardService)
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $user = auth()->user();

        $stats = $dashboardService->getStats($user);
        $moduleBreakdown = $dashboardService->getModuleBreakdown($user);
        $recommendedTests = $dashboardService->getRecommendedTests($user);
        $recentAttempts = $dashboardService->getRecentHistory($user);
        $chartData = $dashboardService->getChartData($user);

        return view('user.dashboard', array_merge([
            'user' => $user,
            'moduleBreakdown' => $moduleBreakdown,
            'recommendedTests' => $recommendedTests,
            'recentAttempts' => $recentAttempts,
            'chartData' => $chartData,
        ], $stats));
    }
}
