<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Models\Answer;
use App\Models\AnswerDetail;
use App\Models\Passage;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grades = [];
        for ($i = 1; $i <= 12; $i++) {
            $grades[] = $i;
        }
        return view('reading-test.index', [
            'grades' => $grades,
        ]);
    }

    public function getQuestion($grade)
    {
        try {
            $passage = Passage::where('grade', $grade)->with('questions')->firstOrFail();
            return response()->json($passage);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage() ?: 'Grade not found'], $e->getCode() ?: 404);
        }
    }

    public function showTest()
    {

        $grade = request('grade_level');
        if (!$grade) {
            return view('404.index');
        } else {
            $passage = Passage::where('grade', $grade)->with('questions')->first();

            $perPage = 5;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $passage->questions->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $paginated = new LengthAwarePaginator(
                $currentItems,
                $passage->questions->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            return view('reading-test.grade-test', [
                'questions' => $paginated,
                'grade' => $grade,
                'passage' => $passage,
            ]);
        }
    }

    public function postAnswer(Request $request)
    {
        DB::beginTransaction();
        try {
            $passage = Passage::with('questions')->find($request->passage_id);
            if (!$passage) {
                return response()->json(['error' => 'Passage not found'], 404);
            }
            $student = Student::updateOrCreate(
                ['email' => $request->user['email']],
                ['name' => $request->user['name']]
            );
            $answer = new Answer();
            $answer->grade = $request->grade;
            $answer->student_id = $student->id;
            $answer->total_questions = $request->total_questions;
            $answer->durations = $passage->duration;
            $answer->total_time = $request->total_time;
            $answer->passage_id = $request->passage_id;
            $answer->lexile_level = $passage->lexile_level;
            $answer->topic = $passage->topic;
            $answer->total_answered = count($request->details);

            $answer->save();
            $correctAnswers = 0;

            for ($i = 0; $i < count($request->details); $i++) {
                $d = $request->details[$i];
                $question = $passage->questions->firstWhere('id', $d['question']['id']);
                $detail = new AnswerDetail();
                $detail->answer_id = $answer->id;
                $detail->question_id = $d['question']['id'];
                $detail->question_text = $d['question']['question'];
                $detail->selected_option = $d['selected_option'];
                $detail->selected_option_text = $d['selected_option_text'];
                $detail->correct_option = $question->correct_answer;
                $option = 'option_' . strtolower($question->correct_answer);
                $detail->correct_option_text = $question->$option;
                $detail->is_correct = $d['selected_option'] == $question->correct_answer;
                if ($detail->is_correct) {
                    $correctAnswers++;
                }
                $detail->save();
            }

            $percent = ($correctAnswers / $answer->total_questions) * 100;
            $estimatedLexile = null;
            $performanceLevel = null;

            if ($percent >= 90) {
                $estimatedLexile = $answer->lexile_level + 150;
                $performanceLevel = "Mastery";
            } elseif ($percent >= 80) {
                $estimatedLexile = $answer->lexile_level + 75;
                $performanceLevel = "Proficient";
            } elseif ($percent >= 65) {
                $estimatedLexile = $answer->lexile_level;
                $performanceLevel = "At Grade Level";
            } elseif ($percent >= 50) {
                $estimatedLexile = $answer->lexile_level - 100;
                $performanceLevel = "Struggling";
            } else {
                $estimatedLexile = $answer->lexile_level - 200;
                $performanceLevel = "Frustration Level";
            }
            $estimatedLexile = max($estimatedLexile, $passage->min_lexile);
            $answer->update([
                'score' => $percent,
                'performance' => $performanceLevel,
                'estimated_lexile' => $estimatedLexile,
                'correct_answers' => $correctAnswers,
            ]);

            DB::commit();

            return response()->json($answer->refresh(), 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong.', 'message' => $th->getMessage()], 500);
        }
    }

    public function history()
    {
        return view('reading-test.history');
    }
    public function history_grade($gradeId)
    {
        try {
            $answers = Answer::with(['student'])->where('grade', $gradeId)->get();
            return response()->json($answers);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], $th->getCode());
        }
    }
    public function answer_detail($answerId)
    {
        try {
            $answer = Answer::with(['student', 'passage.questions', 'details'])->find($answerId);
            $answer->passage->questions->each->makeVisible(['correct_answer']);
            return view('reading-test.detail-answer', ['answer' => $answer]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], $th->getCode());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGradeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grade $grade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade)
    {
        //
    }
}
