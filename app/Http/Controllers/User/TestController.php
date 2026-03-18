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
    public function start(Request $request, Test $test)
    {
        // View modules placeholder if it's GET/no module selected
        if (! $request->has('module')) {
            return view('user.tests.placeholder', compact('test'));
        }

        // Handle specific module starts
        if ($request->module === 'writing') {
            /** @var TestAttempt $attempt */
            $attempt = TestAttempt::query()->firstOrCreate([
                'user_id' => auth()->id(),
                'test_id' => $test->id,
                'status' => 'writing',
            ]);

            return redirect()->route('user.writing.show', $attempt->id);
        }

        if ($request->module === 'speaking') {
            /** @var TestAttempt $attempt */
            $attempt = TestAttempt::query()->firstOrCreate([
                'user_id' => auth()->id(),
                'test_id' => $test->id,
                'status' => 'speaking',
            ]);

            return redirect()->route('user.speaking.show', $attempt->id);
        }

        if ($request->module === 'listening') {
            /** @var ListeningAttempt $attempt */
            $attempt = ListeningAttempt::query()->firstOrCreate([
                'user_id' => auth()->id(),
                'test_id' => $test->id,
            ]);
            if ($attempt->wasRecentlyCreated) {
                $attempt->update([
                    'status' => 'in_progress',
                    'current_section' => 1,
                ]);
            }

            return redirect()->route('user.listening.show', $attempt->id);
        }

        if ($request->module === 'reading') {
            /** @var ReadingAttempt $attempt */
            $attempt = ReadingAttempt::query()->firstOrCreate(
                ['user_id' => auth()->id(), 'test_id' => $test->id]
            );
            if ($attempt->wasRecentlyCreated) {
                $attempt->update(['status' => 'in_progress']);
            }

            return redirect()->route('user.reading.show', $attempt->id);
        }

        abort(404, 'Module not found or not yet available.');
    }
}
