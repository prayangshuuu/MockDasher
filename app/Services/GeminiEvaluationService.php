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
        $this->model = 'gemini-1.5-flash';
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Evaluate a single IELTS Writing task instantly (synchronous).
     */
    public function evaluateWritingTask(int $taskNumber, string $prompt, ?string $precontext, string $answer): array
    {
        if ($taskNumber === 1) {
            $firstCriterion = '"task_achievement"';
            $firstLabel = 'Task Achievement (key features covered)';
            $taskNote = 'Writing Task 1 — describe visual/tabular data';
            $dataBlock = $precontext ? "Visual Data Description:\n{$precontext}\n\n" : '';
        } else {
            $firstCriterion = '"task_response"';
            $firstLabel = 'Task Response (addresses all parts of the task)';
            $taskNote = 'Writing Task 2 — academic essay';
            $dataBlock = '';
        }

        $systemInstruction = <<<TEXT
You are an expert IELTS examiner. Evaluate this IELTS Academic {$taskNote} response using official band descriptors.

Criteria:
- {$firstLabel}
- Coherence and Cohesion: organization, paragraphing, cohesive devices
- Lexical Resource: range and accuracy of vocabulary
- Grammatical Range and Accuracy: sentence variety and correctness

Return ONLY valid JSON — no markdown, no code fences:
{
  "band_score": <0.0-9.0, average of all four criteria>,
  {$firstCriterion}: {"score": <0.0-9.0>, "feedback": "<2-4 sentences of specific feedback>"},
  "coherence_cohesion": {"score": <0.0-9.0>, "feedback": "<2-4 sentences>"},
  "lexical_resource": {"score": <0.0-9.0>, "feedback": "<2-4 sentences>"},
  "grammatical_range_accuracy": {"score": <0.0-9.0>, "feedback": "<2-4 sentences>"},
  "grammatical_errors": ["<quote the exact error> — <correction>"],
  "overall_review": "<3-5 sentence overall summary>",
  "improved_version": "<full rewritten improved version of the candidate answer>"
}
TEXT;

        $userPrompt = "Task {$taskNumber} Question:\n{$prompt}\n\n{$dataBlock}Candidate Answer:\n"
            . ($answer ?: 'No answer provided.');

        return $this->callGeminiApi($systemInstruction, $userPrompt);
    }

    /**
     * Evaluate a single IELTS Speaking question instantly (synchronous).
     */
    public function evaluateSpeakingQuestion(int $part, string $question, string $answer): array
    {
        $systemInstruction = <<<TEXT
You are an expert IELTS examiner. Evaluate this single IELTS Speaking Part {$part} answer using official band descriptors.

Criteria:
- Fluency and Coherence: speaks at length, logical flow, minimal repetition
- Lexical Resource: vocabulary range, accuracy, collocation
- Grammatical Range and Accuracy: sentence structure variety and correctness
- Pronunciation: estimated from written transcript (note this limitation)

Return ONLY valid JSON — no markdown, no code fences:
{
  "band_score": <0.0-9.0, average of all four criteria>,
  "fluency_coherence": {"score": <0.0-9.0>, "feedback": "<2-3 sentences>"},
  "lexical_resource": {"score": <0.0-9.0>, "feedback": "<2-3 sentences>"},
  "grammatical_range_accuracy": {"score": <0.0-9.0>, "feedback": "<2-3 sentences>"},
  "pronunciation": {"score": <0.0-9.0>, "feedback": "<estimated from text>"},
  "overall_feedback": "<2-3 sentence summary for this specific answer>"
}
TEXT;

        $userPrompt = "Part {$part} Question: {$question}\n\nCandidate Answer: "
            . ($answer ?: 'No answer recorded.');

        return $this->callGeminiApi($systemInstruction, $userPrompt);
    }

    /**
     * Evaluate IELTS Writing.
     *
     * @param  string       $task1Prompt    The Task 1 question/prompt text
     * @param  string|null  $task1Precontext Admin-provided description of the data (replaces image)
     * @param  string       $task2Prompt    The Task 2 essay prompt
     * @param  string|null  $task1Answer
     * @param  string|null  $task2Answer
     */
    public function evaluateWriting(
        string $task1Prompt,
        ?string $task1Precontext,
        string $task2Prompt,
        ?string $task1Answer,
        ?string $task2Answer
    ): array {
        $systemInstruction = <<<TEXT
You are an expert IELTS examiner. Evaluate the IELTS Academic Writing answers below based on official IELTS band descriptors.

IELTS Writing criteria:
- Task Achievement / Task Response: how well the candidate addresses the task requirements
- Coherence and Cohesion: organization, paragraphing, use of cohesive devices
- Lexical Resource: range and accuracy of vocabulary
- Grammatical Range and Accuracy: variety and correctness of sentence structures
- Grammatical Errors: specific errors found in the text

Return ONLY valid JSON. No extra text, no markdown code fences.

JSON schema:
{
  "overall_band_score": <average of task1 and task2 band scores, rounded to nearest 0.5>,
  "task_1": {
    "band_score": <0-9>,
    "task_achievement": {"score": <0-9>, "feedback": "<detailed>"},
    "coherence_cohesion": {"score": <0-9>, "feedback": "<detailed>"},
    "lexical_resource": {"score": <0-9>, "feedback": "<detailed>"},
    "grammatical_range_accuracy": {"score": <0-9>, "feedback": "<detailed>"},
    "grammatical_errors": ["<error 1>", "<error 2>"],
    "overall_review": "<summary of performance>",
    "improved_version": "<rewritten improved version of the answer>"
  },
  "task_2": {
    "band_score": <0-9>,
    "task_response": {"score": <0-9>, "feedback": "<detailed>"},
    "coherence_cohesion": {"score": <0-9>, "feedback": "<detailed>"},
    "lexical_resource": {"score": <0-9>, "feedback": "<detailed>"},
    "grammatical_range_accuracy": {"score": <0-9>, "feedback": "<detailed>"},
    "grammatical_errors": ["<error 1>", "<error 2>"],
    "overall_review": "<summary of performance>",
    "improved_version": "<rewritten improved version of the answer>"
  }
}
TEXT;

        $task1Context = $task1Precontext
            ? "Data Description (Task 1 context provided by examiner):\n{$task1Precontext}\n\n"
            : '';

        $userPrompt = "TASK 1\n"
            . "Question: {$task1Prompt}\n"
            . $task1Context
            . "Candidate Answer:\n" . ($task1Answer ?? 'No answer provided.')
            . "\n\n---\n\n"
            . "TASK 2\n"
            . "Question: {$task2Prompt}\n"
            . "Candidate Answer:\n" . ($task2Answer ?? 'No answer provided.');

        return $this->callGeminiApi($systemInstruction, $userPrompt);
    }

    /**
     * Evaluate IELTS Speaking.
     *
     * @param  array  $qaItems  Each item: ['part' => int, 'question' => string, 'answer' => string]
     */
    public function evaluateSpeaking(array $qaItems): array
    {
        $systemInstruction = <<<TEXT
You are an expert IELTS examiner. Evaluate the IELTS Speaking answers below based on official IELTS band descriptors.

IELTS Speaking criteria:
- Fluency and Coherence: ability to speak at length with logical flow
- Lexical Resource: range and accuracy of vocabulary
- Grammatical Range and Accuracy: variety and correctness of sentence structures
- Pronunciation: clarity and ease of understanding (estimated from written transcript)

Note: Pronunciation is estimated from the written transcript and is for practice reference only.

Return ONLY valid JSON. No extra text, no markdown code fences.

JSON schema:
{
  "band_score": <overall 0-9, average of all criteria>,
  "fluency_coherence": {"score": <0-9>, "feedback": "<detailed>"},
  "lexical_resource": {"score": <0-9>, "feedback": "<detailed>"},
  "grammatical_range_accuracy": {"score": <0-9>, "feedback": "<detailed>"},
  "pronunciation": {"score": <0-9>, "feedback": "<estimated from text>"},
  "questions": [
    {
      "part": <part number>,
      "question": "<question text>",
      "answer": "<candidate answer>",
      "feedback": "<specific feedback on this answer>"
    }
  ],
  "overall_review": "<summary of overall performance and key improvement areas>"
}
TEXT;

        $lines = [];
        foreach ($qaItems as $i => $item) {
            $n = $i + 1;
            $lines[] = "Q{$n} (Part {$item['part']}): {$item['question']}";
            $lines[] = "A{$n}: " . (trim($item['answer']) ?: 'No answer recorded.');
            $lines[] = '';
        }

        $userPrompt = "IELTS Speaking — Candidate Answers:\n\n" . implode("\n", $lines);

        return $this->callGeminiApi($systemInstruction, $userPrompt);
    }

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
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $rawText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $rawText = trim($rawText);

                // Strip markdown fences if present
                $rawText = preg_replace('/^```(?:json)?\s*/i', '', $rawText);
                $rawText = preg_replace('/\s*```$/', '', $rawText);

                $parsed = json_decode($rawText, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                    Log::warning('Gemini returned non-JSON: ' . substr($rawText, 0, 500));
                    return $this->fallbackResponse();
                }

                $bandScore = $parsed['band_score'] ?? $parsed['overall_band_score'] ?? null;

                return [
                    'success' => true,
                    'evaluation_text' => $rawText,
                    'band_score' => $bandScore ? (float) $bandScore : null,
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
            'success' => false,
            'evaluation_text' => null,
            'band_score' => null,
        ];
    }
}
