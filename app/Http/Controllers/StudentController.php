<?php

namespace App\Http\Controllers;

use App\Models\Passage;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }
    public function assessment()
    {
        if (session('id')) {
            $grade = session('grade');
            $passages = Passage::select('id', 'grade', 'duration', 'total_question')->where("is_active", 1)->where('grade', $grade);
            $lexile = (clone $passages)->where('curriculum', 'cambridge')->where('subject', 'lexile')->first();
            $math = (clone $passages)->where('curriculum', 'cambridge')->where('subject', 'math')->first();
            return view('assessment', compact('grade', 'lexile', 'math'));
            // return response()->json(compact('grade', 'lexile', 'math'));
        }
        return redirect()->route('student.index');
    }

    public function register(Request $request)
    {
        $student = Student::updateOrCreate(
            ['email' => $request['email']],
            ['name' => $request['name']]
        );
        session(['id' => $student->id]);
        session(['grade' => $request['grade']]);
        return redirect("/assessment");
        return 'ok';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
    }
}
