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

        $collections = \App\Models\IeltsCollection::with(['tests' => function($q) {
            $q->where('status', 'published');
        }])->orderBy('created_at', 'desc')->get();

        // Standalone published tests (not in any collection)
        $standaloneTests = \App\Models\Test::whereNull('ielts_collection_id')
            ->where(function ($q) {
                $q->where('status', '=', 'published');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $recentAttempts = $request->user()->testAttempts()->with('test')->latest()->take(5)->get();
        $testsTakenCount = $request->user()->testAttempts()->count();

        return view('user.dashboard', compact('collections', 'recentAttempts', 'testsTakenCount', 'standaloneTests'));
    }
}
