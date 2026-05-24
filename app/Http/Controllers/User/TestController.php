<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Http\Request;

use App\Models\AiWritingEvaluation;
use App\Models\AiSpeakingEvaluation;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::withCount('testSets')->get();
        return view('user.tests.index', compact('tests'));
    }

    public function start(Request $request, Test $test)
    {
        // Get the first test set for this test (default)
        $testSet = $test->testSets()->first();
        if (! $testSet) {
            return redirect()->route('dashboard')->with('error', 'No test sets found for this test.');
        }

        // Find an active (uncompleted) TestAttempt sitting container for this user & test set.
        $testAttempt = TestAttempt::query()
            ->where('user_id', auth()->id())
            ->where('test_set_id', $testSet->id)
            ->whereNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->first();

        // View modules placeholder if it's GET/no module selected
        if (! $request->has('module')) {
            // Auto-create active sitting container if none exists
            if (!$testAttempt) {
                $testAttempt = TestAttempt::create([
                    'user_id' => auth()->id(),
                    'test_set_id' => $testSet->id,
                    'status' => 'in_progress',
                    'started_at' => now(),
                ]);
            }

            // Get status of each module under this attempt
            $listeningStatus = 'not_started';
            $listeningBand = null;
            if ($testAttempt->listeningAttempt) {
                $listeningStatus = $testAttempt->listeningAttempt->status;
                if ($listeningStatus === 'completed') {
                    $listeningBand = $testAttempt->listeningAttempt->band_score;
                }
            }

            $readingStatus = 'not_started';
            $readingBand = null;
            if ($testAttempt->readingAttempt) {
                $readingStatus = $testAttempt->readingAttempt->status;
                if ($readingStatus === 'completed') {
                    $readingBand = $testAttempt->readingAttempt->band_score;
                }
            }

            $writingStatus = 'not_started';
            $writingBand = null;
            if ($testAttempt->aiWritingEvaluation) {
                $writingStatus = 'completed';
                $writingBand = $testAttempt->aiWritingEvaluation->band_score;
            } elseif ($testAttempt->writingAnswers()->exists()) {
                $writingStatus = 'in_progress';
            }

            $speakingStatus = 'not_started';
            $speakingBand = null;
            if ($testAttempt->aiSpeakingEvaluation) {
                $speakingStatus = 'completed';
                $speakingBand = $testAttempt->aiSpeakingEvaluation->band_score;
            } elseif ($testAttempt->speakingAnswers()->exists()) {
                $speakingStatus = 'in_progress';
            }

            return view('user.tests.placeholder', compact(
                'test', 'testAttempt', 
                'listeningStatus', 'listeningBand',
                'readingStatus', 'readingBand',
                'writingStatus', 'writingBand',
                'speakingStatus', 'speakingBand'
            ));
        }

        // If POST/module select is requested, ensure we have an active attempt
        if (!$testAttempt) {
            $testAttempt = TestAttempt::create([
                'user_id' => auth()->id(),
                'test_set_id' => $testSet->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        // Handle specific module starts
        if ($request->module === 'writing') {
            if ($testAttempt->aiWritingEvaluation()->exists()) {
                return redirect()->route('user.tests.start', $test->id)->with('error', 'You have already completed the Writing module.');
            }
            $testAttempt->update(['status' => 'writing']);
            return redirect()->route('user.writing.show', $testAttempt->id);
        }

        if ($request->module === 'speaking') {
            if ($testAttempt->aiSpeakingEvaluation()->exists()) {
                return redirect()->route('user.tests.start', $test->id)->with('error', 'You have already completed the Speaking module.');
            }
            $testAttempt->update(['status' => 'speaking']);
            return redirect()->route('user.speaking.show', $testAttempt->id);
        }

        if ($request->module === 'listening') {
            if ($testAttempt->listeningAttempt && $testAttempt->listeningAttempt->status === 'completed') {
                return redirect()->route('user.tests.start', $test->id)->with('error', 'You have already completed the Listening module.');
            }

            /** @var ListeningAttempt $attempt */
            $attempt = ListeningAttempt::query()->firstOrCreate([
                'test_attempt_id' => $testAttempt->id,
            ], [
                'user_id' => auth()->id(),
                'test_set_id' => $testSet->id,
                'status' => 'in_progress',
                'current_section' => 1,
                'started_at' => now(),
            ]);

            return redirect()->route('user.listening.show', $attempt->id);
        }

        if ($request->module === 'reading') {
            if ($testAttempt->readingAttempt && $testAttempt->readingAttempt->status === 'completed') {
                return redirect()->route('user.tests.start', $test->id)->with('error', 'You have already completed the Reading module.');
            }

            /** @var ReadingAttempt $attempt */
            $attempt = ReadingAttempt::query()->firstOrCreate([
                'test_attempt_id' => $testAttempt->id,
            ], [
                'user_id' => auth()->id(),
                'test_set_id' => $testSet->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            return redirect()->route('user.reading.show', $attempt->id);
        }

        abort(404, 'Module not found or not yet available.');
    }

    public function finish(TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if ($attempt->status === 'completed' || $attempt->completed_at) {
            return redirect()->route('user.history.show', $attempt->id)->with('error', 'This exam is already finished.');
        }

        // 1. Mark Listening as completed/0.0 if not finished
        $listening = $attempt->listeningAttempt;
        if (!$listening) {
            ListeningAttempt::create([
                'test_attempt_id' => $attempt->id,
                'user_id' => $attempt->user_id,
                'test_set_id' => $attempt->test_set_id,
                'status' => 'completed',
                'total_correct' => 0,
                'band_score' => 0.0,
                'started_at' => now(),
                'completed_at' => now(),
            ]);
        } elseif ($listening->status !== 'completed') {
            $listening->update([
                'status' => 'completed',
                'total_correct' => 0,
                'band_score' => 0.0,
                'completed_at' => now(),
            ]);
        }

        // 2. Mark Reading as completed/0.0 if not finished
        $reading = $attempt->readingAttempt;
        if (!$reading) {
            ReadingAttempt::create([
                'test_attempt_id' => $attempt->id,
                'user_id' => $attempt->user_id,
                'test_set_id' => $attempt->test_set_id,
                'status' => 'completed',
                'total_correct' => 0,
                'band_score' => 0.0,
                'started_at' => now(),
                'completed_at' => now(),
            ]);
        } elseif ($reading->status !== 'completed') {
            $reading->update([
                'status' => 'completed',
                'total_correct' => 0,
                'band_score' => 0.0,
                'completed_at' => now(),
            ]);
        }

        // 3. Mark Writing as completed/0.0 if not finished
        $writingEval = $attempt->aiWritingEvaluation;
        if (!$writingEval) {
            AiWritingEvaluation::create([
                'test_attempt_id' => $attempt->id,
                'user_id' => $attempt->user_id,
                'band_score' => 0.0,
            ]);
        }

        // 4. Mark Speaking as completed/0.0 if not finished
        $speakingEval = $attempt->aiSpeakingEvaluation;
        if (!$speakingEval) {
            AiSpeakingEvaluation::create([
                'test_attempt_id' => $attempt->id,
                'user_id' => $attempt->user_id,
                'band_score' => 0.0,
            ]);
        }

        // 5. Complete the overall TestAttempt sitting
        $attempt->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('user.history.show', $attempt->id)->with('success', 'Full exam sitting completed! Unfinished modules were graded as 0.0.');
    }
}
