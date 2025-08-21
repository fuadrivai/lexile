<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class AIMathGeneratorController extends Controller
{

    public $questionNumber = 50;
    public function index()
    {

        try {
            $grade = request('grade');
            $curriculum = request('curric');
            if (!$grade) {
                return view('404.index');
            }

            // get first question from 1-50
            $generatPrompt = $this->generatePrompt($grade, $curriculum);

            // $result = $this->httpRequest($generatPrompt['prompt']);
            // $content = $result["choices"][0]['message']['content'];
            // $contentWithPassage = json_decode($content);
            // $passage = $contentWithPassage->passage;

            // generate second part question 51-100
            // $secondResult = $this->httpRequest($this->generatePromtSecondPart($passage));
            // $contentQuestionOnly = $secondResult["choices"][0]['message']['content'];
            // $secondContentJson = json_decode($contentQuestionOnly);
            // $contentWithPassage->questions = array_merge($contentWithPassage->questions, $secondContentJson);

            // $path = public_path('questions.json');
            // $json = file_get_contents($path);
            // $contentWithPassage = json_decode($json, true);

            // DB::beginTransaction();
            // $passage = new Passage();
            // $passage->grade = $grade;
            // $passage->topic = $generatPrompt['topic'];
            // $passage->lexile_level = 0;
            // $passage->min_lexile = 0;
            // $passage->duration = 15;
            // $passage->passage = $contentWithPassage['passage'];
            // $passage->is_active = 1;
            // $passage->curriculum = $curriculum;
            // $passage->subject = "math";
            // $passage->save();

            // foreach ($contentWithPassage['questions'] as $d) {
            //     $detail = new Question();
            //     $detail->passage_id = $passage->id;
            //     $detail->topic = $d['topic'];
            //     $detail->question = $d['question'];
            //     $detail->option_a = $d['option_A'];
            //     $detail->option_b = $d['option_B'];
            //     $detail->option_c = $d['option_C'];
            //     $detail->option_d = $d['option_D'];
            //     $detail->correct_answer = $d['correct_answer'];
            //     $detail->correct_answer_text = $d['correct_answer_text'];
            //     $detail->save();
            // }
            // DB::commit();
            return response()->json($generatPrompt);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'error' => $th->getMessage(),
            ], $th->getCode() ?: 500);
        }
    }

    private function httpRequest($content)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('API_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('API_URL'), [
            "model" => "gpt-4.1-mini-2025-04-14",
            'messages' => [
                ["role" => "system", "content" => "You are a reading curriculum developer and Lexile expert."],
                ["role" => "user", "content" => $content,]
            ],
            "temperature" => 0.7,
            "max_tokens" => 6000
        ]);
        return $response->json();
    }

    private function generatePrompt($grade, $curriculum)
    {
        $topic = null;
        $prevGrade = "";
        switch ($grade) {
            case 7:
                $prevGrade = "Primary level";
                $topic = $curriculum == "cambridge" ? "Integer operations, inverse operations, order of operations, ratio, proportion, percent, algebra simplification" : "BIDMAS, prime factorization, factors & multiples, coordinates, solving equations, metric conversions";
                break;
            case 8:
                $prevGrade = "Primary level and Grade 7";
                $topic = $curriculum == "cambridge" ? "Formula recall (area, volume), linear graphs, probability" : "Fractions/decimals/percents, sequences, unit rates, Venn diagrams";
                break;
            case 9:
            case 10:
                $prevGrade = "Primary level and Lower Secondary Level";
                $topic =  $curriculum == "cambridge" ? "Algebraic expressions, percent change, Pythagoras, trigonometry, formula recall: surface area, volumes, graph equations" : "Laws of indices, transformations, linear/quadratic expressions, standard form, trigonometric ratios, vectors, basic probability";
                break;
            case 11:
            case 12:
                $prevGrade = "Primary level and Grade 10";
                $topic = $curriculum == "cambridge" ? "Core identities, differentiation, algebra techniques, binomial expansion, logarithms, integration rules" : "Indices, quadratics, functions, trig, calculus, vectors, exponentials, differentiation/integration";
                break;
            default:
        }
        // === GPT PROMPT ===
        $prompt = "Write a Recall Factual Fluency Test for  Grade $grade from $prevGrade. The topic is $topic and base on $curriculum curriculum. Give the json format to be saved into database with this format : { 'passage':'<passage text>','questions':[ { 'question':'.......', 'topic':'Integer or inverse or ....', 'option_A':'.......', 'option_B':'.......', 'option_C':'.......', 'option_D':'.......', 'correct_answer':'...' ,'correct_answer_text':'<get text from correct option>' },]} generate $this->questionNumber question in a passage Only respond with plain text following the exact format above. Do not include extra instructions or comments.";
        return [
            "prompt" => $prompt,
            "topic" => $topic
        ];
    }

    private function generatePromtSecondPart($passage)
    {
        $prompt = "$passage please generate $this->questionNumber question of that passage above, but make sure do not generate same question with previous questions. Give the json format to be saved into database with this format : [{ 'question':'.......', 'topic':'Integer or inverse or ....', 'option_A':'.......', 'option_B':'.......', 'option_C':'.......', 'option_D':'.......', 'correct_answer':'...','correct_answer_text':'<get text from correct option>' },]Only respond with plain text following the exact format above. Do not include extra instructions or comments.";
        return [
            "prompt" => $prompt,
            "topic" => ""
        ];
    }
}
