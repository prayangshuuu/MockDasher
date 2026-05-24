<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GeminiContentCreatorService;
use Illuminate\Http\Request;

class AiContentController extends Controller
{
    /**
     * Generate IELTS content via AI and return JSON for the admin form to consume.
     *
     * POST /admin/ai/generate-content
     * Body: { module_type: string, topic: string }
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'module_type' => 'required|string|in:Speaking Part 1,Speaking Part 2,Speaking Part 3,Writing Task 1,Writing Task 2',
            'topic' => 'required|string|max:200',
        ]);

        $service = app(GeminiContentCreatorService::class);
        $result = $service->generate($validated['module_type'], $validated['topic']);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Generation failed. Please try again.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }
}
