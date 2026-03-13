<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IeltsCollectionController extends Controller
{
    public function index()
    {
        $collections = \App\Models\IeltsCollection::withCount('tests')->orderBy('created_at', 'desc')->get();
        return view('admin.collections.index', compact('collections'));
    }

    public function create()
    {
        return view('admin.collections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_type' => 'required|string|in:Academic,General',
            'year' => 'nullable|integer',
            'description' => 'required|string',
        ]);

        \App\Models\IeltsCollection::create($validated);

        return redirect()->route('admin.collections.index')->with('success', 'Collection created successfully.');
    }

    public function show(\App\Models\IeltsCollection $collection)
    {
        $tests = $collection->tests()->orderBy('number')->get();
        return view('admin.collections.show', compact('collection', 'tests'));
    }

    public function edit(\App\Models\IeltsCollection $collection)
    {
        return view('admin.collections.edit', compact('collection'));
    }

    public function update(Request $request, \App\Models\IeltsCollection $collection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_type' => 'required|string|in:Academic,General',
            'year' => 'nullable|integer',
            'description' => 'required|string',
        ]);

        $collection->update($validated);

        return redirect()->route('admin.collections.index')->with('success', 'Collection updated successfully.');
    }

    public function destroy(\App\Models\IeltsCollection $collection)
    {
        $collection->delete();
        return redirect()->route('admin.collections.index')->with('success', 'Collection deleted successfully.');
    }
}
