<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\TestAttempt::with(['user', 'test']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('test', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $attempts = $query->latest()->paginate(20);
        return view('admin.results.index', compact('attempts'));
    }

    public function show(\App\Models\TestAttempt $result)
    {
        $result->load(['user', 'test', 'answers']); // Assuming 'answers' relationship exists or similar for detailed breakdown
        return view('admin.results.show', compact('result'));
    }
}
