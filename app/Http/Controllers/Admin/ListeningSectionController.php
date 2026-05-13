<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListeningSection;
use App\Models\TestSet;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ListeningSectionController extends Controller
{
    public function create($testSetId)
    {
        $testSet = TestSet::with('test')->findOrFail($testSetId);
        $sections = $testSet->listeningSections()->with(['questions.options'])->orderBy('section_number')->get();

        return view('admin.listening-sections.create', compact('testSet', 'sections'));
    }

    public function store(Request $request, $testSetId)
    {
        $testSet = TestSet::findOrFail($testSetId);

        $validated = $request->validate([
            'section_number' => 'required|in:1,2,3,4',
            'instruction_text' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:10240',
            'passage_text' => 'nullable|string',
        ]);

        $data = Arr::except($validated, ['audio_file']);

        if ($request->hasFile('audio_file')) {
            $data['audio_path'] = $request->file('audio_file')->store('listening_audio', 'public');
        }

        $testSet->listeningSections()->create($data);

        return redirect()->route('admin.test_sets.show', $testSetId)->with('success', 'Listening section added successfully.');
    }

    public function edit(ListeningSection $listening_section)
    {
        return view('admin.listening-sections.edit', compact('listening_section'));
    }

    public function update(Request $request, ListeningSection $listening_section)
    {
        $validated = $request->validate([
            'section_number' => 'required|in:1,2,3,4',
            'instruction_text' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:10240',
            'passage_text' => 'nullable|string',
        ]);

        $data = Arr::except($validated, ['audio_file']);

        if ($request->hasFile('audio_file')) {
            if ($listening_section->audio_path) {
                Storage::disk('public')->delete($listening_section->audio_path);
            }
            $data['audio_path'] = $request->file('audio_file')->store('listening_audio', 'public');
        }

        $listening_section->update($data);

        return redirect()->route('admin.test_sets.show', $listening_section->test_set_id)->with('success', 'Listening section updated successfully.');
    }

    public function destroy(ListeningSection $listening_section)
    {
        $testSetId = $listening_section->test_set_id;
        if ($listening_section->audio_path) {
            Storage::disk('public')->delete($listening_section->audio_path);
        }
        $listening_section->delete();

        return redirect()->route('admin.test_sets.show', $testSetId)->with('success', 'Listening section deleted successfully.');
    }
}
