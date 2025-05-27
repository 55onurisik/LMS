<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAnswer;
use App\Models\Exam;
use Illuminate\Support\Facades\Http;

class AnalysisController extends Controller
{
    /**
     * List all exams available to the user (for dropdown or selection).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function examsAPI()
    {
        $exams = Exam::all(['id', 'name', 'description', 'created_at']);

        return response()->json([
            'success' => true,
            'exams' => $exams,
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
            $examId = $a->exam->id;
            $examName = $a->exam->name;
            $topicName = $a->answer->topic->topic_name ?? 'Bilinmeyen';

            // Initialize structures
            if (! isset($statistics[$examId])) {
                $statistics[$examId] = [
                    'exam_id'   => $examId,
                    'exam_name' => $examName,
                    'topics'    => []
                ];
            }
            if (! isset($statistics[$examId]['topics'][$topicName])) {
                $statistics[$examId]['topics'][$topicName] = [
                    'correct'   => 0,
                    'incorrect' => 0,
                    'history'   => []
                ];
            }

            // Count correct/incorrect
            if ($a->is_correct) {
                $statistics[$examId]['topics'][$topicName]['correct']++;
            } else {
                $statistics[$examId]['topics'][$topicName]['incorrect']++;
            }

            // Add timestamped history record
            $statistics[$examId]['topics'][$topicName]['history'][] = [
                'question_id' => $a->question_id,
                'correct'     => (bool) $a->is_correct,
                'answered_at' => $a->created_at->toDateTimeString(),
            ];
        }

        // Re-index topics as array
        $result = array_map(function ($exam) {
            $exam['topics'] = array_map(function ($stats, $topicName) {
                return array_merge(['topic_name' => $topicName], $stats);
            }, $exam['topics'], array_keys($exam['topics']));

            return $exam;
        }, $statistics);

        return response()->json([
            'success'    => true,
            'statistics' => array_values($result),
        ]);
    }
}
