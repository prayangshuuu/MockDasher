<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileApiController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'                  => $user->id,
            'name'                => $user->name,
            'first_name'          => $user->first_name,
            'last_name'           => $user->last_name,
            'email'               => $user->email,
            'country'             => $user->country,
            'target_band_score'   => $user->target_band_score,
            'exam_type'           => $user->exam_type,
            'exam_date'           => $user->exam_date?->toDateString(),
            'avatar_url'          => $user->getAvatarUrl(),
            'is_admin'            => $user->isAdmin(),
            'email_verified_at'   => $user->email_verified_at?->toIso8601String(),
            'created_at'          => $user->created_at?->toIso8601String(),
            'has_gemini_key'      => ! empty($user->getRawOriginal('gemini_api_key')),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'country'           => 'nullable|string|max:100',
            'target_band_score' => 'nullable|numeric|min:1|max:9',
            'exam_type'         => 'nullable|string|max:50',
            'exam_date'         => 'nullable|date',
            'photo'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->first_name        = $validated['first_name'];
        $user->last_name         = $validated['last_name'];
        $user->name              = trim($validated['first_name'].' '.$validated['last_name']);
        $user->email             = $validated['email'];
        $user->country           = $validated['country'] ?? $user->country;
        $user->target_band_score = $validated['target_band_score'] ?? $user->target_band_score;
        $user->exam_type         = $validated['exam_type'] ?? $user->exam_type;
        $user->exam_date         = $validated['exam_date'] ?? $user->exam_date;

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => [
                'id'                => $user->id,
                'name'              => $user->name,
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'email'             => $user->email,
                'country'           => $user->country,
                'target_band_score' => $user->target_band_score,
                'exam_type'         => $user->exam_type,
                'exam_date'         => $user->exam_date?->toDateString(),
                'avatar_url'        => $user->getAvatarUrl(),
            ],
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The provided password does not match your current password.',
                'errors'  => ['current_password' => ['The provided password does not match your current password.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function updateGeminiKey(Request $request): JsonResponse
    {
        $request->validate([
            'gemini_api_key' => ['nullable', 'string', 'max:200'],
        ]);

        $key = trim($request->input('gemini_api_key', ''));

        // Allow clearing the key
        if (empty($key)) {
            $request->user()->update(['gemini_api_key' => null]);

            return response()->json(['message' => 'Gemini API key removed successfully.']);
        }

        // Format check
        if (! str_starts_with($key, 'AIza')) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['gemini_api_key' => ['This does not look like a valid Google API key. It should start with "AIza".']],
            ], 422);
        }

        // Verify against Google API
        $verifyUrl = "https://generativelanguage.googleapis.com/v1beta/models?key={$key}";

        try {
            $response = Http::timeout(10)->get($verifyUrl);

            if ($response->status() === 400 || $response->status() === 403) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => ['gemini_api_key' => ['API key verification failed: '.($response->json('error.message') ?? 'Invalid or unauthorized key.')]],
                ], 422);
            }

            if (! $response->successful()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => ['gemini_api_key' => ['Could not verify the API key (status '.$response->status().'). Please check and try again.']],
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['gemini_api_key' => ['Could not connect to Google API to verify the key. Please check your internet connection.']],
            ], 422);
        }

        $request->user()->update(['gemini_api_key' => $key]);

        return response()->json([
            'message'      => 'Gemini API key saved and verified successfully. AI evaluation is now active!',
            'has_gemini_key' => true,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The provided password is incorrect.',
                'errors'  => ['current_password' => ['The provided password is incorrect.']],
            ], 422);
        }

        // Revoke all API tokens
        $user->tokens()->delete();

        // Delete profile photo if exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        return response()->json(['message' => 'Account deleted successfully.']);
    }
}
