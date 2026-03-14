<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tests' => \App\Models\Test::count(),
            'total_test_sets' => \App\Models\TestSet::count(),
            'users' => \App\Models\User::count(),
            'attempts' => \App\Models\TestAttempt::count(),
        ];

        $tests = \App\Models\Test::orderBy('created_at', 'desc')->take(5)->get();
        
        return view('admin.dashboard', compact('tests', 'stats'));
    }
}
