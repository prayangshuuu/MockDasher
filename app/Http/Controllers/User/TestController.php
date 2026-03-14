<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function start(Request $request, \App\Models\Test $test)
    {
        // View modules placeholder if it's GET/no module selected
        if (!$request->has('module')) {
            return view('user.tests.placeholder', compact('test'));
        }

        // Handle specific module starts
        if ($request->module === 'writing') {
            /** @var \App\Models\TestAttempt $attempt */
            $attempt = \App\Models\TestAttempt::query()->firstOrCreate([
                'user_id' => auth()->id(),
                'test_id' => $test->id,
                'status' => 'writing'
            ]);
            
            return redirect()->route('user.writing.show', $attempt->id);
        }

        if ($request->module === 'speaking') {
            /** @var \App\Models\TestAttempt $attempt */
            $attempt = \App\Models\TestAttempt::query()->firstOrCreate([
                'user_id' => auth()->id(),
                'test_id' => $test->id,
                'status'  => 'speaking'
            ]);
            
            return redirect()->route('user.speaking.show', $attempt->id);
        }

        if ($request->module === 'listening') {
            /** @var \App\Models\ListeningAttempt $attempt */
            $attempt = \App\Models\ListeningAttempt::query()->firstOrCreate([
                'user_id' => auth()->id(),
                'test_id' => $test->id,
            ]);
            if ($attempt->wasRecentlyCreated) {
                $attempt->update([
                    'status'          => 'in_progress',
                    'current_section' => 1,
                ]);
            }

            return redirect()->route('user.listening.show', $attempt->id);
        }

        if ($request->module === 'reading') {
            /** @var \App\Models\ReadingAttempt $attempt */
            $attempt = \App\Models\ReadingAttempt::query()->firstOrCreate(
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
