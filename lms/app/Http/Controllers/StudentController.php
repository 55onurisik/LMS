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
}
