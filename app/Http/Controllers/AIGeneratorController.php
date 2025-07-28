<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIGeneratorController extends Controller
{
    public function index()
    {

        try {
            $grade = request('grade_level');
            if (!$grade) {
                return view('404.index');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('API_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('API_URL'), [
                "model" => "gpt-4.1-mini-2025-04-14",
                'messages' => [
                    ["role" => "system", "content" => "You are a reading curriculum developer and Lexile expert."],
                    ["role" => "user", "content" => $this->generatePrompt($grade)],
                ],
                "temperature" => 0.7,
                "max_tokens" => 6000
            ]);

            $parsed = null;
            $attempts = 0;
            $max_attempts = 5;
            $result = $response->json();

            do {
                $text = $result['choices'][0]['message']['content'] ?? '';

                if (!$text) {
                    return response()->json(["error" => "No content from OpenAI (attempt $attempts)"]);
                }
                // Check if the response is empty or malformed
                $parsed = $this->parseFixedFormatOutput($text);
                $questionCount = count($parsed['questions']);
                $attempts++;
            } while (($questionCount !== 20) && $attempts < $max_attempts);

            return response()->json([
                'status' => 'success',
                'data' => $result['choices'][0]['message']['content'],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while generating the reading comprehension test.',
                'error' => $th->getMessage(),
            ], $th->getCode() ?: 500);
        }
    }


    private function generatePrompt($grade)
    {
        $topics = [
            1 => ['topic' => 'pets', 'lexile' => 300],
            2 => ['topic' => 'seasons', 'lexile' => 400],
            3 => ['topic' => 'rainforest', 'lexile' => 600],
            4 => ['topic' => 'solar system', 'lexile' => 700],
            5 => ['topic' => 'volcanoes', 'lexile' => 800],
            6 => ['topic' => 'water cycle', 'lexile' => 900],
            7 => ['topic' => 'ecosystems', 'lexile' => 1000],
            8 => ['topic' => 'electricity', 'lexile' => 1100],
            9 => ['topic' => 'genetics', 'lexile' => 1150],
            10 => ['topic' => 'world war', 'lexile' => 1200],
            11 => ['topic' => 'civil rights', 'lexile' => 1250],
            12 => ['topic' => 'artificial intelligence', 'lexile' => 1300]
        ];
        $config = $topics[$grade] ?? $topics[3];
        $topic = $config['topic'];
        $target_lexile = $config['lexile'];

        // === GPT PROMPT ===
        $prompt = "Write a reading comprehension test for grade $grade students. The topic is '$topic'. The passage should be written at an approximate Lexile level of $target_lexile.

Use this exact format:

Passage:
<passage text>

Questions:
1. <Question 1>
A. Option A
B. Option B
C. Option C
D. Option D

... 20 questions

Answers:
1. A
2. B
...

Only respond with plain text following the exact format above. Do not include extra instructions or comments.";
        return $prompt;
    }

    private function parseFixedFormatOutput($text)
    {
        $parts = preg_split('/\n+Questions:\n+/i', $text);
        $passage = trim($parts[0] ?? '');
        $rest = trim($parts[1] ?? '');

        $qa = preg_split('/\n+Answers:\n+/i', $rest);
        $questions_block = trim($qa[0] ?? '');
        $answers_block = trim($qa[1] ?? '');

        // Parse answers
        $answers = [];
        foreach (explode("\n", $answers_block) as $line) {
            if (preg_match('/^(\d+)[\.\)]\s*([A-D])$/', trim($line), $m)) {
                $answers[intval($m[1])] = $m[2];
            }
        }

        // Parse questions and options
        $lines = explode("\n", $questions_block);
        $questions = [];
        $current = null;
        foreach ($lines as $line) {
            if (preg_match('/^(\d+)[\.\)]\s*(.+)$/', $line, $m)) {
                if ($current) $questions[] = $current;
                $current = [
                    'number' => intval($m[1]),
                    'question' => $m[2],
                    'options' => []
                ];
            } elseif (preg_match('/^([A-D])[.\)]\s*(.+)$/', $line, $m)) {
                $current['options'][$m[1]] = $m[2];
            }
        }
        if ($current) $questions[] = $current;

        // Combine with answers
        $structured = [];
        foreach ($questions as $q) {
            $structured[] = [
                'question' => $q['question'],
                'option_a' => $q['options']['A'] ?? '',
                'option_b' => $q['options']['B'] ?? '',
                'option_c' => $q['options']['C'] ?? '',
                'option_d' => $q['options']['D'] ?? '',
                'answer' => $answers[$q['number']] ?? null
            ];
        }

        return [
            'passage' => preg_replace('/^Passage:\s*/i', '', $passage),
            'questions' => $structured
        ];
    }
}
