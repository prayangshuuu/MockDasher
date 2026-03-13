<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestHistoryController extends Controller
{
    public function index(Request $request)
    {
        $attempts = $request->user()->testAttempts()
            ->with(['test', 'test.collection'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('user.history.index', compact('attempts'));
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
