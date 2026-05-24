<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiContentCreatorService
{
    protected string $apiKey;

    protected string $endpoint;

    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
        $this->model = 'gemini-2.5-flash';
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    // ─────────────────────────────────────────────────────────────
    //  SYSTEM INSTRUCTION — verbatim as specified
    // ─────────────────────────────────────────────────────────────

    private function getSystemInstruction(): string
    {
        return <<<'TEXT'
You are an expert IELTS Content Creator AI. Your sole function is to generate highly accurate, official-style IELTS test materials (Precontexts) for Speaking and Writing modules to be stored in a backend database or application environment variables (ENV).

Evaluation Rules:

You must read the requested MODULE_TYPE (e.g., "Speaking Part 1", "Speaking Part 2", "Speaking Part 3", "Writing Task 1", "Writing Task 2") and the requested TOPIC.

Generate authentic, exam-standard questions and context matching the official IELTS format for that specific module.

DATABASE SAVING INSTRUCTION: The output you generate will be saved directly into a backend database or ENV. Because of this, you must output ONLY valid, flat, strictly formatted JSON. Do not include markdown code blocks (like ```json) or any conversational preamble. The first character must be { and the last must be }.

Inputs:
MODULE_TYPE: {{MODULE_TYPE}}
TOPIC: {{TOPIC}}

Expected JSON Output Schema for Database/ENV Insertion:
{
  "db_module_type": "{{MODULE_TYPE}}",
  "db_topic_theme": "{{TOPIC}}",
  "db_precontext_instructions": "The official instructions given to the student for this specific part (e.g., 'You should spend about 20 minutes on this task.').",
  "db_generated_questions_or_prompt": "The actual questions, cue card text, or essay prompt the student needs to answer.",
  "db_image_description_data": "If Writing Task 1, provide a detailed text description of the graph/chart/table data here so the evaluator AI can grade it later. If not Task 1, leave as null.",
  "db_ready_to_save": true
}

Proceed with the generation, format exactly as the schema above, and output the JSON now for direct database saving.
TEXT;
    }

    // ─────────────────────────────────────────────────────────────
    //  GENERATE CONTENT
    // ─────────────────────────────────────────────────────────────

    /**
     * Generate IELTS content for the given module type and topic.
     *
     * @param  string  $moduleType  e.g. "Speaking Part 1", "Writing Task 2"
     * @param  string  $topic  e.g. "Technology & Social Media"
     * @return array{success: bool, data: array|null, error: string|null}
     */
    public function generate(string $moduleType, string $topic): array
    {
        $userPrompt = "MODULE_TYPE: {$moduleType}\nTOPIC: {$topic}";

        try {
            $response = Http::retry(1, 1000)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->endpoint}?key={$this->apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $this->getSystemInstruction()]],
                ],
                'contents' => [
                    ['parts' => [['text' => $userPrompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $rawText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $rawText = trim($rawText);

                // Strip markdown fences if present (defensive)
                $rawText = preg_replace('/^```(?:json)?\s*/i', '', $rawText);
                $rawText = preg_replace('/\s*```$/', '', $rawText);

                $parsed = json_decode($rawText, true);

                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($parsed)) {
                    Log::warning('GeminiContentCreator: non-JSON response: '.substr($rawText, 0, 500));

                    return ['success' => false, 'data' => null, 'error' => 'AI returned an invalid response. Please try again.'];
                }

                return ['success' => true, 'data' => $parsed, 'error' => null];
            }

            Log::error('GeminiContentCreator API error: '.$response->body());

            return ['success' => false, 'data' => null, 'error' => 'Gemini API request failed. Check your API key and quota.'];

        } catch (\Exception $e) {
            Log::error('GeminiContentCreator exception: '.$e->getMessage());

            return ['success' => false, 'data' => null, 'error' => 'An unexpected error occurred: '.$e->getMessage()];
        }
    }
}
