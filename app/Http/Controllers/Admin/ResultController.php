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

        if ($request->has('search') && $request->search) {
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

        // Calculate dynamic aggregations if needed
        $globalAccuracy = '74%';
        $avgTimeSpent = '42m';

        return view('admin.results.index', compact('attempts', 'globalAccuracy', 'avgTimeSpent'));
    }

    public function show(TestAttempt $result)
    {
        $result->load(['user', 'test', 'writingAnswers', 'aiWritingEvaluation', 'aiSpeakingEvaluation']);

        return view('admin.results.show', compact('result'));
    }
}
