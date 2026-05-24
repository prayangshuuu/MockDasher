<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use App\Models\Question;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalAttempts     = TestAttempt::count();
        $completedAttempts = TestAttempt::where('status', 'completed')->count();

        $totalUsers = User::count();
        $activeExams = Test::where('status', 'published')->count();
        $totalQuestions = Question::count();
        $passRate = $totalAttempts > 0 ? round(($completedAttempts / $totalAttempts) * 100, 1) : 0;

        $recentAttempts = TestAttempt::with(['user', 'testSet.test'])->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'activeExams', 'totalQuestions', 'passRate', 'recentAttempts'));
    }
}
