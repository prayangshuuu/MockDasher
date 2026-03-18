<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TestAttempt;
use App\Services\TestHistoryService;
use Illuminate\Http\Request;

class TestHistoryController extends Controller
{
    public function index(Request $request, TestHistoryService $historyService)
    {
        $user = $request->user();

        $attempts = $historyService->getPaginatedAttempts($user);
        $stats = $historyService->getStats($user);

        return view('user.history.index', [
            'attempts' => $attempts,
            'stats' => $stats,
        ]);
    }

    public function show(Request $request, TestAttempt $attempt)
    {
        // Ensure the user owns this attempt
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }

        $attempt->load(['testSet.test', 'writingAnswers', 'readingAttempt', 'listeningAttempt']);

        return view('user.history.show', compact('attempt'));
    }
}
