<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function create(\App\Models\IeltsCollection $collection)
    {
        return view('admin.tests.create', compact('collection'));
    }

    public function store(Request $request, \App\Models\IeltsCollection $collection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'number' => 'required|integer',
        ]);

        $collection->tests()->create($validated);

        return redirect()->route('admin.collections.index')->with('success', 'Test created successfully.');
    }
}
