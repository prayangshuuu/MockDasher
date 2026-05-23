<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiEvaluationService
{
    protected string $apiKey;
    protected string $endpoint;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
        $this->model  = 'gemini-1.5-flash';
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    // ─────────────────────────────────────────────────────────────
    //  SHARED SYSTEM INSTRUCTION
    // ─────────────────────────────────────────────────────────────

    /**
     * The canonical IELTS Examiner system prompt — verbatim as specified.
     * The {{}} placeholders in the Inputs block serve as the template structure.
     * Each evaluation method injects actual values via the user turn.
     */
    private function getSystemInstruction(): string
    {
        return <<<'TEXT'
You are an expert, highly rigorous IELTS Examiner AI. Your sole function is to evaluate student responses for IELTS Speaking (Parts 1, 2, 3) and IELTS Writing (Tasks 1 and 2). You evaluate strictly according to official IELTS band descriptors.

Evaluation Rules:

You must read the provided MODULE_TYPE, PRECONTEXT (the questions or data/graph descriptions), and STUDENT_ANSWER.

You will analyze the response and assign a Band Score (0-9, in 0.5 increments) for the overall module, as well as the 4 specific criteria for that module type.

Provide constructive, specific feedback and actionable improvements.

CRITICAL OUTPUT RULE: You must output ONLY valid, strictly formatted JSON. Do not include markdown code blocks (like ```json). Do not include any conversational preamble, greetings, or postscripts. The first character of your response must be { and the last must be }.

Inputs:
MODULE_TYPE: {{MODULE_TYPE}}
PRECONTEXT / QUESTION: {{PRECONTEXT}}
IMAGE_DESCRIPTION (If Writing Task 1): {{IMAGE_DESCRIPTION}}
STUDENT_ANSWER: {{STUDENT_ANSWER}}

Expected JSON Output Schema:
If MODULE_TYPE contains "Speaking":
{
  "evaluation_type": "Speaking",
  "overall_band_score": 0.0,
  "criteria_scores": {
    "fluency_and_coherence": 0.0,
    "lexical_resource": 0.0,
    "grammatical_range_and_accuracy": 0.0,
    "pronunciation": 0.0
  },
  "detailed_feedback": "Specific analysis of their strengths and weaknesses based on the text provided.",
  "vocabulary_corrections": [
    {"incorrect": "word/phrase used", "suggested": "better word/phrase"}
  ],
  "grammar_corrections": [
    {"incorrect": "sentence used", "suggested": "corrected sentence"}
  ],
  "suggestions_for_improvement": "Actionable advice to increase their band score."
}

If MODULE_TYPE contains "Writing":
{
  "evaluation_type": "Writing",
  "overall_band_score": 0.0,
  "criteria_scores": {
    "task_achievement_or_response": 0.0,
    "coherence_and_cohesion": 0.0,
    "lexical_resource": 0.0,
    "grammatical_range_and_accuracy": 0.0
  },
  "detailed_feedback": "Detailed paragraph analyzing task fulfillment, paragraph structure, vocabulary, and grammar.",
  "vocabulary_corrections": [
    {"incorrect": "word/phrase used", "suggested": "better word/phrase"}
  ],
  "grammar_corrections": [
    {"incorrect": "sentence used", "suggested": "corrected sentence"}
  ],
  "suggestions_for_improvement": "Actionable advice on structure, vocabulary, or argument development."
}

Proceed with the evaluation and output the JSON now.
TEXT;
    }

    // ─────────────────────────────────────────────────────────────
    //  PER-TASK WRITING EVALUATION  (used by WritingTestController)
    // ─────────────────────────────────────────────────────────────

    /**
     * Evaluate a single IELTS Writing task instantly (synchronous).
     *
     * @param  int          $taskNumber   1 or 2
     * @param  string       $prompt       The question / task instruction
     * @param  string|null  $precontext   Admin-supplied data description (Task 1 graph/chart text)
     * @param  string       $answer       Candidate's answer text
     */
    public function evaluateWritingTask(int $taskNumber, string $prompt, ?string $precontext, string $answer): array
    {
        $moduleType       = "Writing Task {$taskNumber}";
        $imageDescription = $precontext ? $precontext : 'N/A';

        $userPrompt = <<<TEXT
MODULE_TYPE: {$moduleType}
PRECONTEXT / QUESTION: {$prompt}
IMAGE_DESCRIPTION (If Writing Task 1): {$imageDescription}
STUDENT_ANSWER: {$answer}
TEXT;

        return $this->callGeminiApi($this->getSystemInstruction(), $userPrompt);
    }

    // ─────────────────────────────────────────────────────────────
    //  PER-QUESTION SPEAKING EVALUATION  (used by SpeakingTestController)
    // ─────────────────────────────────────────────────────────────

    /**
     * Evaluate a single IELTS Speaking question instantly (synchronous).
     *
     * @param  int     $part      1, 2, or 3
     * @param  string  $question  The examiner question
     * @param  string  $answer    Candidate's spoken answer (transcript text)
     */
    public function evaluateSpeakingQuestion(int $part, string $question, string $answer): array
    {
        $moduleType = "Speaking Part {$part}";

        $userPrompt = <<<TEXT
MODULE_TYPE: {$moduleType}
PRECONTEXT / QUESTION: {$question}
IMAGE_DESCRIPTION (If Writing Task 1): N/A
STUDENT_ANSWER: {$answer}
TEXT;

        return $this->callGeminiApi($this->getSystemInstruction(), $userPrompt);
    }

    // ─────────────────────────────────────────────────────────────
    //  BATCH WRITING  (legacy — evaluates both tasks in one call)
    // ─────────────────────────────────────────────────────────────

    /**
     * Evaluate both IELTS Writing tasks in a single API call.
     *
     * Returns a combined structure:
     * {
     *   "overall_band_score": <avg>,
     *   "task_1": { <Writing schema> },
     *   "task_2": { <Writing schema> }
     * }
     */
    public function evaluateWriting(
        string  $task1Prompt,
        ?string $task1Precontext,
        string  $task2Prompt,
        ?string $task1Answer,
        ?string $task2Answer
    ): array {
        $systemInstruction = $this->getSystemInstruction() . <<<'TEXT'

ADDITIONAL INSTRUCTION FOR BATCH MODE:
You are evaluating BOTH Task 1 AND Task 2 in a single response.
Return a JSON object with this exact shape (wrapping each task's evaluation under its own key):
{
  "overall_band_score": <average of task_1 and task_2 overall_band_scores, rounded to nearest 0.5>,
  "task_1": { <full Writing evaluation JSON for Task 1> },
  "task_2": { <full Writing evaluation JSON for Task 2> }
}
Each task object must conform to the Writing JSON schema above.
TEXT;

        $task1ImageDesc = $task1Precontext ?? 'N/A';

        $userPrompt = <<<TEXT
--- TASK 1 ---
MODULE_TYPE: Writing Task 1
PRECONTEXT / QUESTION: {$task1Prompt}
IMAGE_DESCRIPTION (If Writing Task 1): {$task1ImageDesc}
STUDENT_ANSWER: {$task1Answer}

--- TASK 2 ---
MODULE_TYPE: Writing Task 2
PRECONTEXT / QUESTION: {$task2Prompt}
IMAGE_DESCRIPTION (If Writing Task 1): N/A
STUDENT_ANSWER: {$task2Answer}
TEXT;

        return $this->callGeminiApi($systemInstruction, $userPrompt);
    }

    // ─────────────────────────────────────────────────────────────
    //  BATCH SPEAKING  (legacy — evaluates all Q&A in one call)
    // ─────────────────────────────────────────────────────────────

    /**
     * Evaluate all IELTS Speaking answers in a single API call.
     *
     * @param  array  $qaItems  Each item: ['part' => int, 'question' => string, 'answer' => string]
     */
    public function evaluateSpeaking(array $qaItems): array
    {
        $systemInstruction = $this->getSystemInstruction() . <<<'TEXT'

ADDITIONAL INSTRUCTION FOR BATCH MODE:
You are evaluating ALL Speaking answers (Parts 1, 2, and 3) together in a single response.
Return a JSON object with this exact shape:
{
  "overall_band_score": <overall average across all criteria, rounded to nearest 0.5>,
  "criteria_scores": {
    "fluency_and_coherence": <0.0–9.0>,
    "lexical_resource": <0.0–9.0>,
    "grammatical_range_and_accuracy": <0.0–9.0>,
    "pronunciation": <0.0–9.0>
  },
  "detailed_feedback": "<holistic feedback across all parts>",
  "vocabulary_corrections": [{"incorrect": "...", "suggested": "..."}],
  "grammar_corrections": [{"incorrect": "...", "suggested": "..."}],
  "suggestions_for_improvement": "<actionable advice>",
  "per_question_feedback": [
    {"part": <part number>, "question": "<question text>", "feedback": "<specific feedback>"}
  ]
}
TEXT;

        $lines = [];
        foreach ($qaItems as $i => $item) {
            $n        = $i + 1;
            $lines[]  = "Q{$n} (Part {$item['part']}): {$item['question']}";
            $lines[]  = "A{$n}: " . (trim($item['answer']) ?: 'No answer recorded.');
            $lines[]  = '';
        }

        $userPrompt = "MODULE_TYPE: Speaking\nSTUDENT_ANSWER (all parts):\n\n" . implode("\n", $lines);

        return $this->callGeminiApi($systemInstruction, $userPrompt);
    }

    // ─────────────────────────────────────────────────────────────
    //  GEMINI API CALL
    // ─────────────────────────────────────────────────────────────

    protected function callGeminiApi(string $systemInstruction, string $userPrompt): array
    {
        try {
            $response = Http::retry(1, 1000)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->endpoint}?key={$this->apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => [
                    ['parts' => [['text' => $userPrompt]]],
                ],
                'generationConfig' => [
                    'temperature'      => 0.2,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $result  = $response->json();
                $rawText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $rawText = trim($rawText);

                // Strip markdown fences if present (defensive)
                $rawText = preg_replace('/^```(?:json)?\s*/i', '', $rawText);
                $rawText = preg_replace('/\s*```$/', '', $rawText);

                $parsed = json_decode($rawText, true);

                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($parsed)) {
                    Log::warning('Gemini returned non-JSON: ' . substr($rawText, 0, 500));
                    return $this->fallbackResponse();
                }

                // New schema uses `overall_band_score`; old schema used `band_score`
                $bandScore = $parsed['overall_band_score']
                    ?? $parsed['band_score']
                    ?? null;

                return [
                    'success'         => true,
                    'evaluation_text' => $rawText,
                    'band_score'      => $bandScore !== null ? (float) $bandScore : null,
                ];
            }

            Log::error('Gemini API Error: ' . $response->body());
            return $this->fallbackResponse();

        } catch (\Exception $e) {
            Log::error('Exception in Gemini API call: ' . $e->getMessage());
            return $this->fallbackResponse();
        }
    }

    protected function fallbackResponse(): array
    {
        return [
            'success'         => false,
            'evaluation_text' => null,
            'band_score'      => null,
        ];
    }
}
