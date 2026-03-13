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
            $attempt = \App\Models\TestAttempt::firstOrCreate([
                'user_id' => auth()->id(),
                'test_id' => $test->id,
                'status' => 'writing'
            ]);
            
            return redirect()->route('user.writing.show', $attempt->id);
        }

        abort(404, 'Module not found or not yet available.');
    }
}
