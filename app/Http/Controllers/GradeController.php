<?php

namespace App\Http\Controllers;

use App\Exports\AnswerExport;
use App\Models\Answer;
use App\Models\AnswerDetail;
use App\Models\Passage;
use App\Models\Question;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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

    public function showTest($lexile_id)
    {

        $grade = session('grade');
        $id = session('id');
        if (!$grade) {
            return view('404.index');
        } else {
            return view('reading-test.grade-test', [
                'grade' => $grade,
                'passage_id' => $lexile_id,
                'student_id' => $id
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
    public function history_grade(Request $request)
    {
        try {
            $answers = Answer::with(['student'])->where('grade', $request->grade);
            if ($request->subject != "all") {
                $answers = $answers->where('subject', $request->subject);
            }
            return response()->json($answers->get());
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], $th->getCode());
        }
    }
    public function export_answer($gradeId, $subject)
    {
        try {
            $file = Excel::raw(new AnswerExport($gradeId, $subject), \Maatwebsite\Excel\Excel::XLSX);

            return response($file, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="answer.xlsx"',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], $th->getCode());
        }
    }
    public function answer_detail($answerId)
    {
        try {
            $answer = Answer::with(['student', 'details', 'passage.questions' => function ($query) use ($answerId) {
                $answer = Answer::find($answerId);
                if ($answer && is_array($answer->question_ids)) {
                    $query->whereIn('id', $answer->question_ids);
                }
            }])->find($answerId);
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
    public function store(Request $request)
    {
        $id = session('id');
        DB::beginTransaction();
        try {
            $passage = Passage::with('questions')->find($request->passage_id);
            if (!$passage) {
                return response()->json(['error' => 'Passage not found'], 404);
            }
            $answer = new Answer();
            $answer->grade = $request->grade;
            $answer->subject = $request->subject;
            $answer->student_id = $request->student_id;
            $answer->total_questions = $request->total_questions;
            $answer->durations = $passage->duration;
            $answer->total_time = $request->total_time;
            $answer->passage_id = $request->passage_id;
            $answer->lexile_level = $passage->lexile_level;
            $answer->topic = $passage->topic;
            $answer->question_ids = $request->question_ids;
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

    /**
     * Display the specified resource.
     */
    public function show($passage_id)
    {
        try {
            $passage = Passage::with([
                'questions' => function ($query) {
                    $query->inRandomOrder()->limit(20);
                }
            ])->find($passage_id);
            return response()->json($passage);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage() ?: 'Passage not found'], $e->getCode() ?: 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($grade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $grade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($grade)
    {
        //
    }
}
