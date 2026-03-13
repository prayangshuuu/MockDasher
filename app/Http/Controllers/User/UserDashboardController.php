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

        $collections = \App\Models\IeltsCollection::with('tests')->orderBy('created_at', 'desc')->get();
        $recentAttempts = $request->user()->testAttempts()->with('test')->latest()->take(3)->get();

        return view('user.dashboard', compact('collections', 'recentAttempts'));
    }
}
