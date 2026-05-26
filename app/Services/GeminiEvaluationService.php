<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeminiEvaluationService
 *
 * Evaluates IELTS Speaking (Parts 1–3) and Writing (Tasks 1–2) answers
 * using the Gemini API. Precontexts are loaded from .env to save tokens.
 *
 * Flow:
 *   Speaking: evaluateSpeakingQuestion(part, question, answer) → JSON
 *   Writing:  evaluateWritingTask(taskNumber, question, altText, answer) → JSON
 */
class GeminiEvaluationService
{
    protected string $apiKey;

    protected string $endpoint;

    protected string $model;

    private const CIRCUIT_FAILURE_THRESHOLD = 5;

    private const CIRCUIT_OPEN_TTL = 60;

    /**
     * @param  string|null  $apiKey  User-provided API key. Falls back to global GEMINI_API_KEY env.
     */
    public function __construct(?string $apiKey = null)
    {
        // Prefer per-user key; fall back to global config key
        $this->apiKey = $apiKey ?? config('services.gemini.key', '');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Create a service instance using a specific user's API key.
     * Falls back to the global env key if the user has no key set.
     */
    public static function forUser(?User $user = null): static
    {
        $key = $user?->getRawOriginal('gemini_api_key') ?? null;

        return new static($key ?: null);
    }

    // ─────────────────────────────────────────────────────────────
    //  SYSTEM INSTRUCTION (loaded from env — compact, token-efficient)
    // ─────────────────────────────────────────────────────────────

    /**
     * The canonical IELTS Examiner system prompt.
     * We keep it minimal here; full IELTS part/task context is injected per-call.
     */
    private function getSystemInstruction(): string
    {
        return 'You are an expert, highly rigorous IELTS Examiner AI. '
            .'Your sole function is to evaluate student responses for IELTS Speaking (Parts 1, 2, 3) and IELTS Writing (Tasks 1 and 2). '
            .'You evaluate strictly according to official IELTS band descriptors. '
            ."\n\nEvaluation Rules:\n"
            ."1. Read the MODULE_TYPE, PART_CONTEXT, QUESTION, IMAGE_DESCRIPTION (if any), and STUDENT_ANSWER.\n"
            ."2. Assign a Band Score (0–9, in 0.5 increments) for overall and all 4 criteria.\n"
            ."3. Provide constructive, specific feedback and actionable improvements.\n"
            ."4. CRITICAL: Output ONLY valid JSON. No markdown fences, no preamble. First char must be { and last must be }.\n"
            ."\n--- OUTPUT SCHEMA ---\n"
            ."If MODULE_TYPE contains \"Speaking\":\n"
            ."{\n"
            ."  \"evaluation_type\": \"Speaking\",\n"
            ."  \"module_type\": \"Speaking Part N\",\n"
            ."  \"overall_band_score\": 0.0,\n"
            ."  \"criteria_scores\": {\n"
            ."    \"fluency_and_coherence\": 0.0,\n"
            ."    \"lexical_resource\": 0.0,\n"
            ."    \"grammatical_range_and_accuracy\": 0.0,\n"
            ."    \"pronunciation\": 0.0\n"
            ."  },\n"
            ."  \"detailed_feedback\": \"Specific analysis of strengths and weaknesses.\",\n"
            ."  \"vocabulary_corrections\": [{\"incorrect\": \"...\", \"suggested\": \"...\"}],\n"
            ."  \"grammar_corrections\": [{\"incorrect\": \"...\", \"suggested\": \"...\"}],\n"
            ."  \"suggestions_for_improvement\": \"Actionable advice.\"\n"
            ."}\n"
            ."\nIf MODULE_TYPE contains \"Writing\":\n"
            ."{\n"
            ."  \"evaluation_type\": \"Writing\",\n"
            ."  \"module_type\": \"Writing Task N\",\n"
            ."  \"overall_band_score\": 0.0,\n"
            ."  \"criteria_scores\": {\n"
            ."    \"task_achievement_or_response\": 0.0,\n"
            ."    \"coherence_and_cohesion\": 0.0,\n"
            ."    \"lexical_resource\": 0.0,\n"
            ."    \"grammatical_range_and_accuracy\": 0.0\n"
            ."  },\n"
            ."  \"detailed_feedback\": \"Detailed analysis of task fulfillment, structure, vocabulary, grammar.\",\n"
            ."  \"vocabulary_corrections\": [{\"incorrect\": \"...\", \"suggested\": \"...\"}],\n"
            ."  \"grammar_corrections\": [{\"incorrect\": \"...\", \"suggested\": \"...\"}],\n"
            ."  \"suggestions_for_improvement\": \"Actionable advice.\"\n"
            ."}\n"
            ."\nProceed with the evaluation and output the JSON now.";
    }

    // ─────────────────────────────────────────────────────────────
    //  SPEAKING EVALUATION  (per-question, synchronous)
    // ─────────────────────────────────────────────────────────────

    /**
     * Evaluate a single IELTS Speaking question.
     *
     * @param  int  $part  1, 2, or 3
     * @param  string  $question  The examiner question / cue card text
     * @param  string  $answer  Candidate's spoken answer (transcript text)
     * @return array{success: bool, evaluation_text: string|null, band_score: float|null, parsed: array|null}
     */
    public function evaluateSpeakingQuestion(int $part, string $question, string $answer): array
    {
        $moduleType = "Speaking Part {$part}";

        // Load IELTS part context from env (token-efficient, admin-configurable)
        $partContext = match ($part) {
            1 => config('services.ielts.speaking_part1_context', 'IELTS Speaking Part 1: The examiner asks about familiar topics.'),
            2 => config('services.ielts.speaking_part2_context', 'IELTS Speaking Part 2: The candidate speaks for 1-2 minutes on a topic.'),
            3 => config('services.ielts.speaking_part3_context', 'IELTS Speaking Part 3: The examiner and candidate discuss abstract topics.'),
            default => "IELTS Speaking Part {$part}",
        };

        // Gracefully handle empty transcript
        $answerText = trim($answer);
        if (empty($answerText)) {
            $answerText = '[No speech recorded — the candidate did not provide an answer.]';
        }

        $userPrompt = implode("\n", [
            "MODULE_TYPE: {$moduleType}",
            "PART_CONTEXT: {$partContext}",
            "QUESTION: {$question}",
            'IMAGE_DESCRIPTION: N/A',
            "STUDENT_ANSWER: {$answerText}",
        ]);

        return $this->callGeminiApi($this->getSystemInstruction(), $userPrompt);
    }

    // ─────────────────────────────────────────────────────────────
    //  WRITING EVALUATION  (per-task, synchronous)
    // ─────────────────────────────────────────────────────────────

    /**
     * Evaluate a single IELTS Writing task.
     *
     * @param  int  $taskNumber  1 or 2
     * @param  string  $question  The full task prompt / essay question
     * @param  string|null  $imageAltText  Admin description of the chart/graph (Task 1 only)
     * @param  string  $answer  Candidate's written answer
     * @return array{success: bool, evaluation_text: string|null, band_score: float|null, parsed: array|null}
     */
    public function evaluateWritingTask(int $taskNumber, string $question, ?string $imageAltText, string $answer): array
    {
        $moduleType = "Writing Task {$taskNumber}";

        // Load IELTS task context from env
        $taskContext = match ($taskNumber) {
            1 => config('services.ielts.writing_task1_context', 'IELTS Writing Task 1: Summarise the graph/chart in at least 150 words.'),
            2 => config('services.ielts.writing_task2_context', 'IELTS Writing Task 2: Write an essay of at least 250 words.'),
            default => "IELTS Writing Task {$taskNumber}",
        };

        // Image/graph description for Task 1
        $imageDescription = ($taskNumber === 1 && ! empty(trim($imageAltText ?? '')))
            ? trim($imageAltText)
            : 'N/A';

        // Gracefully handle empty answer
        $answerText = trim($answer);
        if (empty($answerText)) {
            $answerText = '[No answer submitted — the candidate did not write a response.]';
        }

        $userPrompt = implode("\n", [
            "MODULE_TYPE: {$moduleType}",
            "TASK_CONTEXT: {$taskContext}",
            "QUESTION / PROMPT: {$question}",
            "IMAGE_DESCRIPTION (Task 1 graph/chart data): {$imageDescription}",
            "STUDENT_ANSWER: {$answerText}",
        ]);

        return $this->callGeminiApi($this->getSystemInstruction(), $userPrompt);
    }

    // ─────────────────────────────────────────────────────────────
    //  BATCH WRITING  (legacy — evaluates both tasks in one call)
    // ─────────────────────────────────────────────────────────────

    /**
     * Evaluate both IELTS Writing tasks in a single API call.
     *
     * Returns a combined structure with task_1 and task_2 sub-objects.
     *
     * @deprecated Use evaluateWritingTask() per task for better UX.
     */
    public function evaluateWriting(
        string $task1Question,
        ?string $task1ImageAltText,
        string $task2Question,
        ?string $task1Answer,
        ?string $task2Answer
    ): array {
        $task1Context = config('services.ielts.writing_task1_context', 'IELTS Writing Task 1: Summarise the graph/chart in at least 150 words.');
        $task2Context = config('services.ielts.writing_task2_context', 'IELTS Writing Task 2: Write an essay of at least 250 words.');

        $batchInstruction = $this->getSystemInstruction()."\n\n"
            ."BATCH MODE: Evaluate BOTH Task 1 AND Task 2. Return JSON with shape:\n"
            .'{ "overall_band_score": <avg rounded to 0.5>, "task_1": {<full Writing schema>}, "task_2": {<full Writing schema>} }';

        $img1 = ! empty(trim($task1ImageAltText ?? '')) ? trim($task1ImageAltText) : 'N/A';
        $a1 = trim($task1Answer ?? '') ?: '[No answer submitted.]';
        $a2 = trim($task2Answer ?? '') ?: '[No answer submitted.]';

        $userPrompt = implode("\n\n", [
            "--- TASK 1 ---\nMODULE_TYPE: Writing Task 1\nTASK_CONTEXT: {$task1Context}\nQUESTION: {$task1Question}\nIMAGE_DESCRIPTION: {$img1}\nSTUDENT_ANSWER: {$a1}",
            "--- TASK 2 ---\nMODULE_TYPE: Writing Task 2\nTASK_CONTEXT: {$task2Context}\nQUESTION: {$task2Question}\nIMAGE_DESCRIPTION: N/A\nSTUDENT_ANSWER: {$a2}",
        ]);

        return $this->callGeminiApi($batchInstruction, $userPrompt);
    }

    // ─────────────────────────────────────────────────────────────
    //  BATCH SPEAKING  (legacy — evaluates all Q&A in one call)
    // ─────────────────────────────────────────────────────────────

    /**
     * Evaluate all IELTS Speaking answers in a single API call.
     *
     * @param  array  $qaItems  Each item: ['part' => int, 'question' => string, 'answer' => string]
     *
     * @deprecated Use evaluateSpeakingQuestion() per question for better UX.
     */
    public function evaluateSpeaking(array $qaItems): array
    {
        $batchInstruction = $this->getSystemInstruction()."\n\n"
            ."BATCH MODE: Evaluate ALL Speaking answers (Parts 1–3) together. Return JSON:\n"
            .'{ "overall_band_score": <avg>, "criteria_scores": { ... }, '
            .'"detailed_feedback": "...", "vocabulary_corrections": [...], '
            .'"grammar_corrections": [...], "suggestions_for_improvement": "...", '
            .'"per_question_feedback": [{"part": N, "question": "...", "feedback": "..."}] }';

        $lines = [];
        foreach ($qaItems as $i => $item) {
            $n = $i + 1;
            $ans = trim($item['answer'] ?? '') ?: '[No answer recorded.]';
            $lines[] = "Q{$n} (Part {$item['part']}): {$item['question']}";
            $lines[] = "A{$n}: {$ans}";
            $lines[] = '';
        }

        $userPrompt = "MODULE_TYPE: Speaking\nSTUDENT_ANSWER (all parts):\n\n".implode("\n", $lines);

        return $this->callGeminiApi($batchInstruction, $userPrompt);
    }

    // ─────────────────────────────────────────────────────────────
    //  CIRCUIT BREAKER
    // ─────────────────────────────────────────────────────────────

    private function circuitPrefix(): string
    {
        return 'gemini:cb:'.md5($this->apiKey);
    }

    private function isCircuitOpen(): bool
    {
        return (bool) Cache::get($this->circuitPrefix().':open');
    }

    private function recordCircuitSuccess(): void
    {
        Cache::forget($this->circuitPrefix().':failures');
        Cache::forget($this->circuitPrefix().':open');
    }

    private function recordCircuitFailure(): void
    {
        $failKey  = $this->circuitPrefix().':failures';
        $failures = (int) Cache::get($failKey, 0) + 1;
        Cache::put($failKey, $failures, 300);

        if ($failures >= self::CIRCUIT_FAILURE_THRESHOLD) {
            Cache::put($this->circuitPrefix().':open', true, self::CIRCUIT_OPEN_TTL);
            Log::warning('[GeminiCircuit] Circuit opened after '.$failures.' consecutive failures');
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  GEMINI API CALL
    // ─────────────────────────────────────────────────────────────

    /**
     * Call the Gemini API and return a normalized result array.
     *
     * @return array{
     *   success: bool,
     *   evaluation_text: string|null,
     *   band_score: float|null,
     *   parsed: array|null
     * }
     */
    protected function callGeminiApi(string $systemInstruction, string $userPrompt): array
    {
        if ($this->isCircuitOpen()) {
            Log::warning('[GeminiCircuit] Circuit open — skipping API call');

            return $this->fallbackResponse('Gemini API temporarily unavailable (circuit open).');
        }

        try {
            $response = Http::timeout(90)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->endpoint}?key={$this->apiKey}", [
                    'system_instruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'maxOutputTokens' => 4096,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if ($response->successful()) {
                $result = $response->json();

                // Check for blocked / filtered responses
                $candidate = $result['candidates'][0] ?? null;
                if (! $candidate) {
                    $blockReason = $result['promptFeedback']['blockReason'] ?? 'UNKNOWN';
                    Log::warning('[GeminiEval] No candidates returned', ['blockReason' => $blockReason]);
                    $this->recordCircuitFailure();

                    return $this->fallbackResponse("Response blocked by Gemini safety filters: {$blockReason}");
                }

                $finishReason = $candidate['finishReason'] ?? 'STOP';
                if (in_array($finishReason, ['SAFETY', 'RECITATION'])) {
                    Log::warning('[GeminiEval] Blocked candidate', ['finishReason' => $finishReason]);
                    $this->recordCircuitFailure();

                    return $this->fallbackResponse("Gemini candidate blocked: {$finishReason}");
                }

                $rawText = $candidate['content']['parts'][0]['text'] ?? '';
                $rawText = $this->extractJson($rawText);

                $parsed = json_decode($rawText, true);

                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($parsed)) {
                    Log::warning('[GeminiEval] Non-JSON response', [
                        'finish_reason' => $finishReason,
                        'preview' => substr($rawText, 0, 500),
                    ]);
                    $this->recordCircuitFailure();

                    return $this->fallbackResponse('Gemini returned a non-JSON response.');
                }

                $bandScore = $this->extractBandScore($parsed);

                if ($bandScore === null) {
                    Log::warning('[GeminiEval] Invalid or missing band score', [
                        'preview' => substr($rawText, 0, 500),
                    ]);
                    $this->recordCircuitFailure();

                    return $this->fallbackResponse('Gemini returned an invalid band score.');
                }

                $this->recordCircuitSuccess();

                return [
                    'success' => true,
                    'evaluation_text' => $rawText,
                    'band_score' => $bandScore,
                    'parsed' => $parsed,
                ];
            }

            $body = $response->body();
            Log::error('[GeminiEval] API Error', [
                'status' => $response->status(),
                'body' => substr($body, 0, 1000),
            ]);

            $this->recordCircuitFailure();

            return $this->fallbackResponse('Gemini API returned status '.$response->status());

        } catch (\Exception $e) {
            Log::error('[GeminiEval] Exception', ['message' => $e->getMessage()]);

            $this->recordCircuitFailure();

            return $this->fallbackResponse($e->getMessage());
        }
    }

    /**
     * Aggressively extract the first valid JSON object from a raw string.
     * Handles markdown fences, leading/trailing prose, and partial wrapping.
     */
    private function extractJson(string $raw): string
    {
        $raw = trim($raw);

        // Strip ```json ... ``` fences
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```\s*$/i', '', $raw);
        $raw = trim($raw);

        // If it already starts with { return as-is
        if (str_starts_with($raw, '{')) {
            return $raw;
        }

        // Try to find the first { ... } block
        $start = strpos($raw, '{');
        $end   = strrpos($raw, '}');
        if ($start !== false && $end !== false && $end > $start) {
            return substr($raw, $start, $end - $start + 1);
        }

        return $raw;
    }

    /**
     * Return a normalized failure response.
     */
    protected function fallbackResponse(string $reason = 'Unknown error'): array
    {
        Log::warning('[GeminiEval] Fallback triggered', ['reason' => $reason]);

        return [
            'success' => false,
            'evaluation_text' => null,
            'band_score' => null,
            'parsed' => null,
        ];
    }

    /**
     * Extract and normalise the band score from a parsed Gemini response.
     *
     * Accepts any numeric value 0-9 and rounds it to the nearest valid IELTS
     * 0.5-increment (0, 0.5, 1.0 … 9.0) rather than rejecting edge cases.
     */
    private function extractBandScore(array $parsed): ?float
    {
        $raw = $parsed['overall_band_score']
            ?? $parsed['band_score']
            ?? $parsed['overall_score']
            ?? null;

        if (! is_numeric($raw)) {
            return null;
        }

        $score = (float) $raw;

        if ($score < 0.0 || $score > 9.0) {
            return null;
        }

        // Round to nearest IELTS 0.5 increment
        return round($score * 2) / 2;
    }
}
