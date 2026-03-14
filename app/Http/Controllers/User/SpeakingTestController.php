<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpeakingTestController extends Controller
{
    public function show(\App\Models\TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Initialize timer if first time entering speaking module in this attempt
        // We track statuses, but if this attempt was just spawned from the placeholder, we need to mark it.
        // Assuming 'speaking' status.
        if ($attempt->status !== 'speaking' && $attempt->status !== 'completed') {
            $attempt->update(['status' => 'speaking']);
            if (!$attempt->started_at) {
                $attempt->update(['started_at' => now()]);
            }
        }

        if ($attempt->status === 'completed' || $attempt->completed_at) {
            return redirect()->route('dashboard')->with('error', 'Test already completed.');
        }

        $speakingQuestions = $attempt->test->speakingQuestions()->orderBy('part')->get();
        
        $parts = $speakingQuestions->groupBy('part');

        return view('user.speaking-test.show', compact('attempt', 'parts', 'speakingQuestions'));
    }

    public function submit(Request $request, \App\Models\TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            abort(403);
        }

        $attempt->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('dashboard')->with('success', 'Speaking test submitted successfully.');
    }
}
