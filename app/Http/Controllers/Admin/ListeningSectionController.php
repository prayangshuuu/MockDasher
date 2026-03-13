<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListeningSectionController extends Controller
{
    public function create($testId)
    {
        $test = \App\Models\Test::findOrFail($testId);
        return view('admin.listening-sections.create', compact('test'));
    }

    public function store(Request $request, $testId)
    {
        $test = \App\Models\Test::findOrFail($testId);

        $validated = $request->validate([
            'section_number' => 'required|in:1,2,3,4',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:10240', // 10MB
            'passage_text' => 'nullable|string',
        ]);

        $data = \Illuminate\Support\Arr::except($validated, ['audio_file']);

        if ($request->hasFile('audio_file')) {
            $data['audio_path'] = $request->file('audio_file')->store('listening_audio', 'public');
        }

        $test->listeningSections()->create($data);

        return redirect()->route('admin.tests.show', $testId)->with('success', 'Listening section added successfully.');
    }

    public function edit(\App\Models\ListeningSection $listening_section)
    {
        return view('admin.listening-sections.edit', compact('listening_section'));
    }

    public function update(Request $request, \App\Models\ListeningSection $listening_section)
    {
        $validated = $request->validate([
            'section_number' => 'required|in:1,2,3,4',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:10240',
            'passage_text' => 'nullable|string',
        ]);

        $data = \Illuminate\Support\Arr::except($validated, ['audio_file']);

        if ($request->hasFile('audio_file')) {
            if ($listening_section->audio_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($listening_section->audio_path);
            }
            $data['audio_path'] = $request->file('audio_file')->store('listening_audio', 'public');
        }

        $listening_section->update($data);

        return redirect()->route('admin.tests.show', $listening_section->test_id)->with('success', 'Listening section updated successfully.');
    }

    public function destroy(\App\Models\ListeningSection $listening_section)
    {
        $testId = $listening_section->test_id;
        if ($listening_section->audio_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($listening_section->audio_path);
        }
        $listening_section->delete();

        return redirect()->route('admin.tests.show', $testId)->with('success', 'Listening section deleted successfully.');
    }
}
