<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IeltsCollectionController extends Controller
{
    public function index()
    {
        $collections = \App\Models\IeltsCollection::all();
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
            'description' => 'required|string',
        ]);

        \App\Models\IeltsCollection::create($validated);

        return redirect()->route('admin.collections.index')->with('success', 'Collection created successfully.');
    }
}
