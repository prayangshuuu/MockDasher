<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::withCount('testSets')->get();
        return view('user.tests.index', compact('tests'));
    }

    public function start(Request $request, Test $test)
    {
        // View modules placeholder if it's GET/no module selected
        if (! $request->has('module')) {
            return view('user.tests.placeholder', compact('test'));
        }

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

        // Check if the requested module has already been started/completed in this TestAttempt container
        $needsNewAttempt = false;
        if ($testAttempt) {
            if ($request->module === 'writing' && ($testAttempt->writingAnswers()->exists() || $testAttempt->aiWritingEvaluation()->exists())) {
                $needsNewAttempt = true;
            } elseif ($request->module === 'speaking' && ($testAttempt->speakingAnswers()->exists() || $testAttempt->aiSpeakingEvaluation()->exists())) {
                $needsNewAttempt = true;
            } elseif ($request->module === 'listening' && $testAttempt->listeningAttempt()->exists()) {
                $needsNewAttempt = true;
            } elseif ($request->module === 'reading' && $testAttempt->readingAttempt()->exists()) {
                $needsNewAttempt = true;
            }
        } else {
            $needsNewAttempt = true;
        }

        if ($needsNewAttempt) {
            $testAttempt = TestAttempt::create([
                'user_id' => auth()->id(),
                'test_set_id' => $testSet->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        // Handle specific module starts
        if ($request->module === 'writing') {
            $testAttempt->update(['status' => 'writing']);
            return redirect()->route('user.writing.show', $testAttempt->id);
        }

        if ($request->module === 'speaking') {
            $testAttempt->update(['status' => 'speaking']);
            return redirect()->route('user.speaking.show', $testAttempt->id);
        }

        if ($request->module === 'listening') {
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
}
