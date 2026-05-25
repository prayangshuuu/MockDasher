<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'sometimes|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $deviceName = $request->input('device_name', $request->userAgent() ?? 'API Client');
        $token = $user->createToken($deviceName, ['*'], now()->addDays(30));

        return response()->json([
            'token'      => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->accessToken->expires_at,
            'user'       => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'country'           => $user->country,
                'target_band_score' => $user->target_band_score,
                'exam_type'         => $user->exam_type,
                'exam_date'         => $user->exam_date?->toDateString(),
                'is_admin'          => $user->isAdmin(),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'country'           => $user->country,
            'target_band_score' => $user->target_band_score,
            'exam_type'         => $user->exam_type,
            'exam_date'         => $user->exam_date?->toDateString(),
            'is_admin'          => $user->isAdmin(),
            'avatar_url'        => $user->getAvatarUrl(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'All sessions revoked.']);
    }
}
