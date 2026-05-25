<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TestApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tests = Cache::remember('api:tests:published', 300, function () {
            return Test::where('status', 'published')
                ->withCount('testSets')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($t) => [
                    'id'         => $t->id,
                    'title'      => $t->title,
                    'status'     => $t->status,
                    'sets_count' => $t->test_sets_count,
                    'created_at' => $t->created_at->toIso8601String(),
                ]);
        });

        return response()->json(['data' => $tests]);
    }

    public function show(int $id): JsonResponse
    {
        $test = Cache::remember("api:test:{$id}", 300, function () use ($id) {
            $t = Test::where('status', 'published')
                ->with(['testSets'])
                ->findOrFail($id);

            return [
                'id'         => $t->id,
                'title'      => $t->title,
                'status'     => $t->status,
                'test_sets'  => $t->testSets->map(fn ($s) => [
                    'id'   => $s->id,
                    'name' => $s->name,
                ]),
                'created_at' => $t->created_at->toIso8601String(),
            ];
        });

        return response()->json(['data' => $test]);
    }
}
