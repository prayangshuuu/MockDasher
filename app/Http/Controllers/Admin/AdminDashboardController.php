<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalAttempts = TestAttempt::count();
        $completedAttempts = TestAttempt::where('status', 'completed')->count();

        $stats = [
            'total_tests' => Test::count(),
            'total_test_sets' => TestSet::count(),
            'users' => User::count(),
            'attempts' => $totalAttempts,
            'completion_rate' => $totalAttempts > 0 ? ($completedAttempts / $totalAttempts) * 100 : 0,
        ];

        $tests = Test::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('tests', 'stats'));
    }
}
