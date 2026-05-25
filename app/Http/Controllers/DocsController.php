<?php

namespace App\Http\Controllers;

use App\Models\AiSpeakingEvaluation;
use App\Models\AiWritingEvaluation;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\WritingAnswer;
use App\Models\SpeakingAnswer;

class DocsController extends Controller
{
    public function index()
    {
        $totalUsers          = User::count();
        $totalAttempts       = TestAttempt::count();
        $completedAttempts   = TestAttempt::where('status', 'completed')->count();
        $activeTests         = Test::where('status', 'published')->count();
        $totalQuestions      = Question::count();
        $completionRate      = $totalAttempts > 0 ? round(($completedAttempts / $totalAttempts) * 100, 1) : 0;

        $aiWritingDone   = AiWritingEvaluation::where('evaluation_status', 'completed')->count();
        $aiSpeakingDone  = AiSpeakingEvaluation::where('evaluation_status', 'completed')->count();
        $totalEvaluations = $aiWritingDone + $aiSpeakingDone;

        $avgWritingBand  = AiWritingEvaluation::where('evaluation_status', 'completed')
                            ->whereNotNull('band_score')->avg('band_score');
        $avgSpeakingBand = AiSpeakingEvaluation::where('evaluation_status', 'completed')
                            ->whereNotNull('band_score')->avg('band_score');

        return view('docs', compact(
            'totalUsers',
            'totalAttempts',
            'completedAttempts',
            'activeTests',
            'totalQuestions',
            'completionRate',
            'totalEvaluations',
            'aiWritingDone',
            'aiSpeakingDone',
            'avgWritingBand',
            'avgSpeakingBand',
        ));
    }
}
