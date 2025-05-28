<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\StudentAnswer;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Models\Student; // Student modelini kullanarak işlemler yapacağız
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        // Tüm sınavları al
        $exams = Exam::all();

        return view('student.index', compact('exams'));
    }

    public function indexAPI(Request $request)
    {
        // Sanctum’la authenticated user’ı al
        $student = $request->user();
        // ya: $student = auth('api')->user();

        if (! $student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }

        $studentData = [
            'name'          => $student->name,
            'email'         => $student->email,
            'phone'         => $student->phone,
            'class_level'   => $student->class_level,
            'schedule_day'  => $student->schedule_day,
            'schedule_time' => $student->schedule_time,
        ];

        $exams = Exam::all();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'student' => $studentData,
                'exams'   => $exams,
            ],
        ], 200);
    }
}
