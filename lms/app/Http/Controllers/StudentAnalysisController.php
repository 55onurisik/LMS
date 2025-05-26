<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentPerformanceAnalyzer;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class StudentAnalysisController extends Controller
{
    public function analyze(Student $student): JsonResponse
    {
        $analyzer = new StudentPerformanceAnalyzer($student);
        $analysis = $analyzer->analyze();

        // Update last analysis timestamp
        $student->update([
            'last_analysis_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $analysis
        ]);
    }
} 