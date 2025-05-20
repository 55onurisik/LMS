<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Topic;
use ConsoleTVs\Charts\Facades\Charts;

class ChartController extends Controller
{
    public function studentPerformance($studentId)
    {
        $student = Student::findOrFail($studentId);

        // Konu bazlı öğrenci başarı oranını al
        $topics = Topic::with('answers')->get();

        $chart = Charts::create('bar', 'chartjs')
            ->title('Konu Bazlı Başarı Oranı')
            ->labels($topics->pluck('topic_name'))
            ->values($topics->map(function($topic) use ($student) {
                $totalQuestions = $topic->answers->count();
                $correctAnswers = $student->answers()
                    ->whereIn('answer_id', $topic->answers->pluck('id'))
                    ->where('is_correct', true)
                    ->count();
                return $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
            }))
            ->dimensions(1000, 500)
            ->responsive(true);

        return view('charts.student_performance', compact('chart', 'student'));
    }
}
