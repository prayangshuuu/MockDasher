<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => \App\Models\User::count(),
            'collections' => \App\Models\IeltsCollection::count(),
            'published_tests' => \App\Models\Test::query()->where(function ($q) {
                $q->where('status', '=', 'published');
            })->count(),
            'total_tests' => \App\Models\Test::count(),
            'attempts' => \App\Models\TestAttempt::count(),
            'listening_attempts' => \App\Models\ListeningAttempt::count(),
            'reading_attempts' => \App\Models\ReadingAttempt::count(),
            'writing_tasks' => \App\Models\WritingTask::count(),
            'speaking_questions' => \App\Models\SpeakingQuestion::count(),
            'listening_sections' => \App\Models\ListeningSection::count(),
            'reading_passages' => \App\Models\ReadingPassage::count(),
        ];

        $tests = \App\Models\Test::with('collection')->orderBy('created_at', 'desc')->take(5)->get();
        
        return view('admin.dashboard', compact('tests', 'stats'));
    }
}
