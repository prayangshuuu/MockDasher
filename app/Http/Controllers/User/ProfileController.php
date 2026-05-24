<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('profile.show', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->name = trim($validated['first_name'].' '.$validated['last_name']);
        $user->email = $validated['email'];

        $user->save();

        return back()->with('success', 'Profile information updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Save / update the user's personal Gemini API key.
     *
     * The key is validated by making a lightweight Gemini API call (listModels)
     * before saving — this ensures the key actually works.
     */
    public function updateGeminiKey(Request $request)
    {
        $request->validate([
            'gemini_api_key' => ['nullable', 'string', 'max:200'],
        ]);

        $key = trim($request->input('gemini_api_key', ''));

        // If user is clearing their key, allow it
        if (empty($key)) {
            $request->user()->update(['gemini_api_key' => null]);

            return back()->with('gemini_success', 'Gemini API key removed successfully.');
        }

        // Validate key format (Google API keys start with AIza)
        if (! str_starts_with($key, 'AIza')) {
            return back()
                ->withErrors(['gemini_api_key' => 'This does not look like a valid Google API key. It should start with "AIza".'])
                ->withInput();
        }

        // Verify the key actually works with a lightweight API call
        $verifyUrl = "https://generativelanguage.googleapis.com/v1beta/models?key={$key}";
        try {
            $response = Http::timeout(10)->get($verifyUrl);

            if ($response->status() === 400 || $response->status() === 403) {
                return back()
                    ->withErrors(['gemini_api_key' => 'API key verification failed: '.($response->json('error.message') ?? 'Invalid or unauthorized key.')])
                    ->withInput();
            }

            if (! $response->successful()) {
                return back()
                    ->withErrors(['gemini_api_key' => 'Could not verify the API key (status '.$response->status().'). Please check and try again.'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            return back()
                ->withErrors(['gemini_api_key' => 'Could not connect to Google API to verify the key. Please check your internet connection.'])
                ->withInput();
        }

        // Key is valid — save it
        $request->user()->update(['gemini_api_key' => $key]);

        return back()->with('gemini_success', 'Gemini API key saved and verified successfully. AI evaluation is now active!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = $request->user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        auth()->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
