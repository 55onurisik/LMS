<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAnswer;
use App\Models\Exam;

class AnalysisController extends Controller
{
    /**
     * List all exams available to the user (for dropdown or selection).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function examsAPI()
    {
        // Seçilen kolonlar: id, exam_code, exam_title, question_count, review_visibility, created_at
        $exams = Exam::select([
            'id',
            'exam_code',
            'exam_title',
            'question_count',
            'review_visibility',
            'created_at'
        ])->get();

        return response()->json([
            'success' => true,
            'exams'   => $exams,
        ]);
    }

    /**
     * Return detailed student statistics and analysis for all exams.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statisticsAPI(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Fetch all answers by this student, eager load relations
        $answers = StudentAnswer::with(['answer.topic', 'exam'])
            ->where('student_id', $user->id)
            ->orderBy('created_at')
            ->get();

        // Group data by exam -> topic
        $statistics = [];

        foreach ($answers as $a) {
            $exam      = $a->exam;
            $examId    = $exam->id;
            $examCode  = $exam->exam_code;
            $examTitle = $exam->exam_title;
            $topicName = $a->answer->topic->topic_name ?? 'Bilinmeyen';

            // Initialize structures
            if (! isset($statistics[$examId])) {
                $statistics[$examId] = [
                    'exam_id'    => $examId,
                    'exam_code'  => $examCode,
                    'exam_title' => $examTitle,
                    'topics'     => []
                ];
            }
            if (! isset($statistics[$examId]['topics'][$topicName])) {
                $statistics[$examId]['topics'][$topicName] = [
                    'correct'     => 0,
                    'incorrect'   => 0,
                    'unanswered'  => 0,    // ← boş cevaplar için sayaç
                    'history'     => []
                ];
            }

            // Count correct / incorrect / unanswered
            if ($a->is_correct === 1) {
                $statistics[$examId]['topics'][$topicName]['correct']++;
            } elseif ($a->is_correct === 0) {
                $statistics[$examId]['topics'][$topicName]['incorrect']++;
            } else { // is_correct == 2 veya başka
                $statistics[$examId]['topics'][$topicName]['unanswered']++;
            }

            // Add timestamped history record
            $statistics[$examId]['topics'][$topicName]['history'][] = [
                'question_id' => $a->question_id,
                'correct'     => $a->is_correct, // 1,0 veya 2
                'answered_at' => $a->created_at->toDateTimeString(),
            ];
        }

        // Re-index exams and topics as arrays
        $result = array_values(array_map(function ($examStats) {
            $examStats['topics'] = array_values(array_map(function ($stats, $topicName) {
                return array_merge(['topic_name' => $topicName], $stats);
            }, $examStats['topics'], array_keys($examStats['topics'])));

            return $examStats;
        }, $statistics));

        return response()->json([
            'success'    => true,
            'statistics' => $result,
        ]);
    }
}