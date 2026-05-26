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
        // Ensure the user owns this attempt (cast both sides to int to prevent type-juggling bypass)
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $attempt->load([
            'testSet.test',
            'writingAnswers.writingTask',
            'readingAttempt',
            'listeningAttempt',
            'aiWritingEvaluation',
            'aiSpeakingEvaluation',
        ]);

        return view('user.history.show', compact('attempt'));
    }

    public function exportPdf(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $attempt->load([
            'user',
            'testSet.test',
            'writingAnswers.writingTask',
            'speakingAnswers',
            'readingAttempt',
            'listeningAttempt',
            'aiWritingEvaluation',
            'aiSpeakingEvaluation',
        ]);

        $logoPath = storage_path('app/public/asset/logo.png');
        $logoSrc  = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.history.pdf', compact('attempt', 'logoSrc'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('mockdasher-my-result-' . $attempt->id . '.pdf');
    }
}
