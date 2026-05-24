<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AiSpeakingEvaluation;
use App\Models\AiWritingEvaluation;
use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::where('status', 'published')
            ->withCount('testSets')
            ->latest()
            ->get();

        return view('user.tests.index', compact('tests'));
    }

    public function start(Request $request, Test $test)
    {
        // Only published tests are accessible to users
        abort_if($test->status !== 'published', 404);

        $testSet = $test->testSets()->first();
        if (! $testSet) {
            return redirect()->route('dashboard')->with('error', 'No test sets found for this test.');
        }

        // Find an active (uncompleted) TestAttempt for this user & test set.
        $testAttempt = TestAttempt::query()
            ->where('user_id', auth()->id())
            ->where('test_set_id', $testSet->id)
            ->whereNull('completed_at')
            ->orderByDesc('created_at')
            ->first();

        // GET — show module selector dashboard
        if (! $request->has('module')) {
            if (! $testAttempt) {
                $testAttempt = TestAttempt::create([
                    'user_id'     => auth()->id(),
                    'test_set_id' => $testSet->id,
                    'status'      => 'in_progress',
                    'started_at'  => now(),
                ]);
            }

            // Eager-load all relations in one shot to avoid N+1
            $testAttempt->loadMissing([
                'listeningAttempt',
                'readingAttempt',
                'aiWritingEvaluation',
                'aiSpeakingEvaluation',
            ]);

            $listeningStatus = 'not_started';
            $listeningBand   = null;
            if ($la = $testAttempt->listeningAttempt) {
                $listeningStatus = $la->status;
                $listeningBand   = $la->status === 'completed' ? $la->band_score : null;
            }

            $readingStatus = 'not_started';
            $readingBand   = null;
            if ($ra = $testAttempt->readingAttempt) {
                $readingStatus = $ra->status;
                $readingBand   = $ra->status === 'completed' ? $ra->band_score : null;
            }

            $writingStatus = 'not_started';
            $writingBand   = null;
            if ($testAttempt->aiWritingEvaluation?->band_score !== null) {
                $writingStatus = 'completed';
                $writingBand   = $testAttempt->aiWritingEvaluation->band_score;
            } elseif ($testAttempt->writingAnswers()->exists()) {
                $writingStatus = 'in_progress';
            }

            $speakingStatus = 'not_started';
            $speakingBand   = null;
            if ($testAttempt->aiSpeakingEvaluation?->band_score !== null) {
                $speakingStatus = 'completed';
                $speakingBand   = $testAttempt->aiSpeakingEvaluation->band_score;
            } elseif ($testAttempt->speakingAnswers()->exists()) {
                $speakingStatus = 'in_progress';
            }

            return view('user.tests.placeholder', compact(
                'test', 'testAttempt',
                'listeningStatus', 'listeningBand',
                'readingStatus',   'readingBand',
                'writingStatus',   'writingBand',
                'speakingStatus',  'speakingBand'
            ));
        }

        // POST — validate the module selection against an explicit allow-list
        $request->validate([
            'module' => 'required|string|in:writing,speaking,listening,reading',
        ]);

        if (! $testAttempt) {
            $testAttempt = TestAttempt::create([
                'user_id'     => auth()->id(),
                'test_set_id' => $testSet->id,
                'status'      => 'in_progress',
                'started_at'  => now(),
            ]);
        }

        $testAttempt->loadMissing(['listeningAttempt', 'readingAttempt', 'aiWritingEvaluation', 'aiSpeakingEvaluation']);

        switch ($request->module) {
            case 'writing':
                if ($testAttempt->aiWritingEvaluation?->band_score !== null) {
                    return redirect()->route('user.tests.start', $test->id)
                        ->with('error', 'You have already completed the Writing module.');
                }
                $testAttempt->update(['status' => 'writing']);
                return redirect()->route('user.writing.show', $testAttempt->id);

            case 'speaking':
                if ($testAttempt->aiSpeakingEvaluation?->band_score !== null) {
                    return redirect()->route('user.tests.start', $test->id)
                        ->with('error', 'You have already completed the Speaking module.');
                }
                $testAttempt->update(['status' => 'speaking']);
                return redirect()->route('user.speaking.show', $testAttempt->id);

            case 'listening':
                if ($testAttempt->listeningAttempt?->status === 'completed') {
                    return redirect()->route('user.tests.start', $test->id)
                        ->with('error', 'You have already completed the Listening module.');
                }
                $listeningAttempt = ListeningAttempt::firstOrCreate(
                    ['test_attempt_id' => $testAttempt->id],
                    [
                        'user_id'         => auth()->id(),
                        'test_set_id'     => $testSet->id,
                        'status'          => 'in_progress',
                        'current_section' => 1,
                        'started_at'      => now(),
                    ]
                );
                return redirect()->route('user.listening.show', $listeningAttempt->id);

            case 'reading':
                if ($testAttempt->readingAttempt?->status === 'completed') {
                    return redirect()->route('user.tests.start', $test->id)
                        ->with('error', 'You have already completed the Reading module.');
                }
                $readingAttempt = ReadingAttempt::firstOrCreate(
                    ['test_attempt_id' => $testAttempt->id],
                    [
                        'user_id'     => auth()->id(),
                        'test_set_id' => $testSet->id,
                        'status'      => 'in_progress',
                        'started_at'  => now(),
                    ]
                );
                return redirect()->route('user.reading.show', $readingAttempt->id);
        }

        abort(404, 'Module not found.');
    }

    public function finish(TestAttempt $attempt)
    {
        $this->authorize('interact', $attempt);

        if ($attempt->completed_at) {
            return redirect()->route('user.history.show', $attempt->id)
                ->with('error', 'This exam is already finished.');
        }

        DB::transaction(function () use ($attempt) {
            $this->zeroFillIncompleteModules($attempt);
            $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        });

        return redirect()->route('user.history.show', $attempt->id)
            ->with('success', 'Full exam sitting completed! Unfinished modules were graded as 0.0.');
    }

    /**
     * Record a proctoring violation server-side.
     * Returns JSON; the client uses the response to show the correct warning count.
     */
    public function recordViolation(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json(['status' => 'already_completed'], 200);
        }

        $maxViolations = 3;

        DB::transaction(function () use ($attempt) {
            $fresh = TestAttempt::lockForUpdate()->find($attempt->id);
            if ($fresh->completed_at) {
                return (int) $fresh->proctoring_violations;
            }
            $fresh->increment('proctoring_violations');
        });

        // Re-fetch after the increment to get the authoritative count
        $attempt->refresh();
        $violations = (int) $attempt->proctoring_violations;

        if ($violations >= $maxViolations && ! $attempt->completed_at) {
            DB::transaction(function () use ($attempt) {
                $fresh = TestAttempt::lockForUpdate()->find($attempt->id);
                if ($fresh->completed_at) return;
                $this->zeroFillIncompleteModules($fresh);
                $fresh->update(['status' => 'completed', 'completed_at' => now()]);
            });

            return response()->json([
                'status'     => 'terminated',
                'violations' => $violations,
                'message'    => 'Exam terminated due to proctoring violations.',
            ]);
        }

        return response()->json([
            'status'     => 'warned',
            'violations' => $violations,
            'remaining'  => max(0, $maxViolations - $violations),
        ]);
    }

    /**
     * Zero-fill all incomplete modules for an attempt.
     * Called from both finish() and recordViolation() so logic lives in one place.
     */
    private function zeroFillIncompleteModules(TestAttempt $attempt): void
    {
        $attempt->loadMissing(['listeningAttempt', 'readingAttempt', 'aiWritingEvaluation', 'aiSpeakingEvaluation']);

        $listening = $attempt->listeningAttempt;
        if (! $listening) {
            ListeningAttempt::create([
                'test_attempt_id' => $attempt->id,
                'user_id'         => $attempt->user_id,
                'test_set_id'     => $attempt->test_set_id,
                'status'          => 'completed',
                'total_correct'   => 0,
                'band_score'      => 0.0,
                'started_at'      => now(),
                'completed_at'    => now(),
            ]);
        } elseif ($listening->status !== 'completed') {
            $listening->update([
                'status'        => 'completed',
                'total_correct' => 0,
                'band_score'    => 0.0,
                'completed_at'  => now(),
            ]);
        }

        $reading = $attempt->readingAttempt;
        if (! $reading) {
            ReadingAttempt::create([
                'test_attempt_id' => $attempt->id,
                'user_id'         => $attempt->user_id,
                'test_set_id'     => $attempt->test_set_id,
                'status'          => 'completed',
                'total_correct'   => 0,
                'band_score'      => 0.0,
                'started_at'      => now(),
                'completed_at'    => now(),
            ]);
        } elseif ($reading->status !== 'completed') {
            $reading->update([
                'status'        => 'completed',
                'total_correct' => 0,
                'band_score'    => 0.0,
                'completed_at'  => now(),
            ]);
        }

        if (! $attempt->aiWritingEvaluation) {
            AiWritingEvaluation::create([
                'test_attempt_id' => $attempt->id,
                'user_id'         => $attempt->user_id,
                'band_score'      => 0.0,
            ]);
        } elseif ($attempt->aiWritingEvaluation->band_score === null) {
            $attempt->aiWritingEvaluation->update(['band_score' => 0.0]);
        }

        if (! $attempt->aiSpeakingEvaluation) {
            AiSpeakingEvaluation::create([
                'test_attempt_id' => $attempt->id,
                'user_id'         => $attempt->user_id,
                'band_score'      => 0.0,
            ]);
        } elseif ($attempt->aiSpeakingEvaluation->band_score === null) {
            $attempt->aiSpeakingEvaluation->update(['band_score' => 0.0]);
        }
    }
}
