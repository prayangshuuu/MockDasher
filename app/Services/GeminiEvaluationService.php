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
        $this->model = 'gemini-3-flash-preview';
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    public function evaluateWriting(string $task1Prompt, string $task2Prompt, ?string $task1Answer, ?string $task2Answer): array
    {
        $systemInstruction = <<<TEXT
You are an expert IELTS examiner evaluating IELTS Writing Task 1 and Task 2.
The AI must evaluate IELTS Writing based on:
- Task Response
- Coherence and Cohesion
- Lexical Resource
- Grammatical Range and Accuracy
- Grammatical Errors

You must return EXACT format:

Task Response: <analysis>

Coherence and Cohesion: <analysis>

Lexical Resource: <analysis>

Grammatical Range and Accuracy: <analysis>

Grammatical Errors: <list of errors>

Band Score:
<number between 0-9>

Overall Review:

<summary>

Optimized Composition:
<improved version of the user's answer>

STRICT OUTPUT RULE:
NOT add extra text
NOT change format
NOT skip sections
ALWAYS include Band Score
TEXT;

        $userPrompt = "Task 1 Prompt:\n$task1Prompt\n\nTask 1 Answer:\n$task1Answer\n\nTask 2 Prompt:\n$task2Prompt\n\nTask 2 Answer:\n$task2Answer";

        return $this->callGeminiApi($systemInstruction, $userPrompt);
    }

    public function evaluateSpeaking(array $questions, string $transcript): array
    {
        $systemInstruction = <<<TEXT
You are an expert IELTS examiner evaluating IELTS Speaking answers.
The AI must evaluate speaking answers per question based on the provided transcript.

STRICT OUTPUT FORMAT:

Comments

Q. <Question>
Answer: <User answer>

Quick Thoughts: <short feedback>

(repeat for each question)

Overall Review:

<summary>

Band Score:
<number between 0-9>

* Since key IELTS Speaking criteria (e.g., Pronunciation) are not included, the Band Score here is for practice reference only and not formal scoring guidance.

STRICT OUTPUT RULE:
NOT add extra text
NOT change format
NOT skip sections
ALWAYS include Band Score
TEXT;

        $questionsText = "Questions:\n" . implode("\n", $questions);
        $userPrompt = "$questionsText\n\nUser Answers Transcript:\n$transcript";

        return $this->callGeminiApi($systemInstruction, $userPrompt);
    }

    protected function callGeminiApi(string $systemInstruction, string $userPrompt): array
    {
        try {
            $response = Http::retry(1, 1000)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->endpoint}?key={$this->apiKey}", [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $userPrompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $evaluationText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                return [
                    'success' => true,
                    'evaluation_text' => trim($evaluationText),
                    'band_score' => $this->extractBandScore($evaluationText)
                ];
            }

            Log::error('Gemini API Error: ' . $response->body());
            
            return $this->fallbackResponse();

        } catch (\Exception $e) {
            Log::error('Exception in Gemini API call: ' . $e->getMessage());
            return $this->fallbackResponse();
        }
    }

    protected function extractBandScore(string $text): ?float
    {
        // Extract Band Score using pattern: Band Score: <number>
        if (preg_match('/Band Score:\s*([\d\.]+)/i', $text, $matches)) {
            return (float) $matches[1];
        }
        return null;
    }

    protected function fallbackResponse(): array
    {
        return [
            'success' => false,
            'evaluation_text' => "Task Response: Evaluation failed.\n\nCoherence and Cohesion: Evaluation failed.\n\nLexical Resource: Evaluation failed.\n\nGrammatical Range and Accuracy: Evaluation failed.\n\nGrammatical Errors: Evaluation failed.\n\nBand Score:\n0\n\nOverall Review:\nThe AI evaluation service is currently unavailable or failed to process your submission. Please try again later.\n\nOptimized Composition:\nEvaluation failed.",
            'band_score' => 0.0
        ];
    }
}
