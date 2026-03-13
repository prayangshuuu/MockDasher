<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => \App\Models\User::count(),
            'collections' => \App\Models\IeltsCollection::count(),
            'published_tests' => \App\Models\Test::where('status', 'published')->count(),
            'attempts' => \App\Models\TestAttempt::count(),
        ];

        $tests = \App\Models\Test::with('collection')->orderBy('created_at', 'desc')->take(5)->get();
        
        return view('admin.dashboard', compact('tests', 'stats'));
    }
}
