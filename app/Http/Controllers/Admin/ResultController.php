<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestAttempt;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = TestAttempt::with(['user', 'test']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($outer) use ($search) {
                $outer->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('test', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            });
        }

        $attempts = $query->latest()->paginate(20);

        // Real aggregations from database
        $totalCompleted = TestAttempt::whereNotNull('completed_at')->count();

        // Global accuracy: percentage of attempts that were completed
        $totalAttempts = TestAttempt::count();
        $globalAccuracy = $totalAttempts > 0
            ? round(($totalCompleted / $totalAttempts) * 100).'%'
            : 'N/A';

        // Average time spent on completed attempts (start → complete)
        $avgSeconds = TestAttempt::whereNotNull('completed_at')
            ->whereNotNull('started_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, started_at, completed_at)) as avg_seconds')
            ->value('avg_seconds');

        if ($avgSeconds !== null) {
            $avgMinutes = (int) round($avgSeconds / 60);
            $hours = intdiv($avgMinutes, 60);
            $mins = $avgMinutes % 60;
            $avgTimeSpent = $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
        } else {
            $avgTimeSpent = 'N/A';
        }

        return view('admin.results.index', compact('attempts', 'globalAccuracy', 'avgTimeSpent'));
    }

    public function show(TestAttempt $result)
    {
        $result->load(['user', 'test', 'writingAnswers.writingTask', 'speakingAnswers', 'aiWritingEvaluation', 'aiSpeakingEvaluation']);

        return view('admin.results.show', compact('result'));
    }

    public function exportPdf(TestAttempt $result)
    {
        $result->load([
            'user',
            'test',
            'testSet',
            'writingAnswers.writingTask',
            'speakingAnswers.question',
            'aiWritingEvaluation',
            'aiSpeakingEvaluation',
            'readingAttempt',
            'listeningAttempt',
        ]);

        $logoPath = storage_path('app/public/asset/logo.png');
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.results.pdf', compact('result', 'logoSrc'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('mockdasher-result-'.$result->id.'.pdf');
    }
}
