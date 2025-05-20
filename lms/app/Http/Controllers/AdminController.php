<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamReview;
use App\Models\StudentAnswer;
use App\Models\Topic;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $studentCount = Student::count();
        $examCount = Exam::count();

        return view('admin.dashboard', compact('studentCount', 'examCount'));
    }

    public function manageStudents()
    {
        $students = Student::where('status', 'approved')->get();
        return view('admin.manageStudents', compact('students'));
    }


    public function dashboard()
    {
        $studentCount = Student::count();
        $examCount = Exam::count();

        return view('admin.dashboard', compact('studentCount', 'examCount'));
    }

    public function adminPage()
    {
        $students = Student::where('status', 'approved')->get();
        return view('admin.students', compact('students'));
    }


    public function studentProfile($studentId, Request $request)
    {
        $student = Student::findOrFail($studentId);
        $exams = Exam::all();
        $selectedExamId = request('exam_id') ?? $exams->first()->id;

        $topics = Topic::all();
        $topicLabels = [];
        $topicPercentages = [];
        $backgroundColors = [];

        foreach ($topics as $topic) {
            $totalQuestions = $topic->answers->count();
            $correctAnswers = DB::table('student_answers')
                ->join('answers', 'student_answers.answer_id', '=', 'answers.id')
                ->where('student_answers.exam_id', $selectedExamId)
                ->where('answers.topic_id', $topic->id)
                ->where('student_answers.is_correct', true)
                ->get();

            $percentage = $totalQuestions > 0 ? ($correctAnswers->count() / $totalQuestions) * 100 : 0;

            $topicLabels[] = $topic->topic_name;
            $topicPercentages[] = $percentage;
            $backgroundColors[] = $percentage >= 75
                ? 'rgba(75, 192, 192, 0.6)'
                : ($percentage >= 50
                    ? 'rgba(255, 206, 86, 0.6)'
                    : 'rgba(255, 99, 132, 0.6)');
        }
        return view('admin.studentProfile', compact('student', 'exams', 'selectedExamId', 'topicLabels', 'topicPercentages', 'backgroundColors', 'correctAnswers'));
    }

    public function showExamResults($studentId, Request $request)
    {
        $topics = Topic::all();

        try {
            $student = Student::findOrFail($studentId);
            $exams = Exam::whereHas('studentAnswers', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })->get();

            $selectedExamId = $request->query('exam_id', $exams->first()->id ?? null);

            if (!$selectedExamId) {
                return view('admin.studentProfile', [
                    'student' => $student,
                    'exams' => $exams,
                    'selectedExamId' => null,
                    'correctAnswers' => [],
                    'wrongAnswers' => [],
                ]);
            }

            $correctAnswers = StudentAnswer::where('student_id', $studentId)
                ->where('exam_id', $selectedExamId)
                ->where('is_correct', true)
                ->with('answer')
                ->get();

            $wrongAnswers = StudentAnswer::where('student_id', $studentId)
                ->where('exam_id', $selectedExamId)
                ->where('is_correct', false)
                ->with('answer')
                ->get();

            foreach ($topics as $topic) {
                $totalQuestions = $topic->answers->count();
                $correctAnswers = DB::table('student_answers')
                    ->join('answers', 'student_answers.answer_id', '=', 'answers.id')
                    ->where('student_answers.exam_id', $selectedExamId)
                    ->where('answers.topic_id', $topic->id)
                    ->where('student_answers.is_correct', true)
                    ->get();

                $percentage = $totalQuestions > 0 ? ($correctAnswers->count() / $totalQuestions) * 100 : 0;

                $topicLabels[] = $topic->topic_name;
                $topicPercentages[] = $percentage;
                $backgroundColors[] = $percentage >= 75
                    ? 'rgba(75, 192, 192, 0.6)'
                    : ($percentage >= 50
                        ? 'rgba(255, 206, 86, 0.6)'
                        : 'rgba(255, 99, 132, 0.6)');
            }
            return view('admin.studentProfile', compact('student', 'exams', 'correctAnswers', 'topicPercentages', 'wrongAnswers', 'selectedExamId', 'backgroundColors'));

        } catch (\Exception $e) {
            return response()->json(['error' => 'Sınav sonuçları alınırken bir hata oluştu.'], 500);
        }
    }

    // Yeni: Öğrencinin tüm sınavlarını (çözdüyse veya çözmediyse) listeleyen metod
    public function showStudentExams($studentId)
    {
        $student = Student::findOrFail($studentId);
        // Tüm sınavları, ilgili öğrenciye ait cevapları (varsa) ile birlikte alıyoruz.
        $exams = Exam::with(['studentAnswers' => function($query) use ($studentId) {
            $query->where('student_id', $studentId);
        }])->get();

        return view('admin.student_exams', compact('student', 'exams'));
    }

    public function createStudent()
    {
        return view('admin.create-student');
    }

    public function storeStudent(Request $request)
    {
        Student::create([
            'name' => $request->input('name'),
            'email' => strtolower($request->input('email')),
            'password' => bcrypt($request->input('password')),
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Öğrenci başarıyla oluşturuldu!');
    }

    public function showPercentage(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $exam  = $request->exam;
        $class = $request->class;

        $studentAnswers = StudentAnswer::where('student_id', $studentId)
            ->with('answer')
            ->get();

        $topics = [];
        foreach ($studentAnswers as $studentAnswer) {
            $answer = $studentAnswer->answer;
            if ($answer && $answer->topic) {
                $topicId = $answer->topic->id;

                if (!isset($topics[$topicId])) {
                    $topics[$topicId] = [
                        'topic_name'      => $answer->topic->topic_name,
                        'total_questions' => 0,
                        'wrong_answers'   => 0,
                    ];
                }

                $topics[$topicId]['total_questions']++;

                if ($studentAnswer->is_correct === 0 || $studentAnswer->is_correct === 2) {
                    $topics[$topicId]['wrong_answers']++;
                }
            }
        }

        foreach ($topics as $topicId => &$topic) {
            $total = $topic['total_questions'];
            $wrong = $topic['wrong_answers'];
            $topic['percentage'] = $total > 0 ? (1 - ($wrong / $total)) * 100 : 0;
        }

        $allTopicsQuery = Topic::query();

        if ($exam === 'tyt') {
            $allTopicsQuery->whereHas('unit', function ($query) {
                $query->whereIn('class_level', [9, 10]);
            });
        } elseif ($exam === 'ayt') {
            $allTopicsQuery->whereHas('unit', function ($query) {
                $query->whereIn('class_level', [11, 12]);
            });
        }

        if (!empty($class)) {
            $allTopicsQuery->whereHas('unit', function ($query) use ($class) {
                $query->where('class_level', $class);
            });
        }

        $allTopics = $allTopicsQuery->get();

        return view('admin.topic-percentage', [
            'student'   => $student,
            'topics'    => $topics,
            'allTopics' => $allTopics,
        ]);
    }

    public function showTopicChart($studentId, $topicId)
    {
        $student = Student::findOrFail($studentId);
        $topic = Topic::findOrFail($topicId);

        $exams = Exam::with(['answers' => function ($query) use ($topicId) {
            $query->where('topic_id', $topicId);
        }])->get();

        $chartData = [];
        $averageData = [];

        foreach ($exams as $exam) {
            $totalQuestionsStudent = 0;
            $correctAnswersStudent = 0;

            $totalQuestionsAll = 0;
            $correctAnswersAll = 0;

            foreach ($exam->answers as $answer) {
                foreach ($answer->studentAnswers as $studentAnswer) {
                    if ($studentAnswer->student_id == $studentId) {
                        $totalQuestionsStudent++;
                        if ($studentAnswer->is_correct == 1) {
                            $correctAnswersStudent++;
                        }
                    }
                    $totalQuestionsAll++;
                    if ($studentAnswer->is_correct == 1) {
                        $correctAnswersAll++;
                    }
                }
            }

            if ($totalQuestionsStudent > 0) {
                $percentageStudent = ($correctAnswersStudent / $totalQuestionsStudent) * 100;
                $chartData[] = [
                    'exam_code' => $exam->exam_code,
                    'percentage' => $percentageStudent,
                ];
            }

            if ($totalQuestionsAll > 0) {
                $percentageAll = ($correctAnswersAll / $totalQuestionsAll) * 100;
                $averageData[] = [
                    'exam_code' => $exam->exam_code,
                    'percentage' => $percentageAll,
                ];
            }
        }

        return view('admin.topic_chart', [
            'student' => $student,
            'topic' => $topic,
            'chartData' => $chartData,
            'averageData' => $averageData,
        ]);
    }

    public function examAverages($studentId, $examId)
    {
        $student = Student::findOrFail($studentId);
        $exam = Exam::findOrFail($examId);

        $topics = Topic::whereHas('answers', function ($query) use ($examId) {
            $query->where('exam_id', $examId);
        })->with('answers')->get();

        $studentAnswers = DB::table('student_answers')
            ->join('answers', 'student_answers.answer_id', '=', 'answers.id')
            ->where('student_answers.student_id', $studentId)
            ->where('answers.exam_id', $examId)
            ->select('answers.topic_id', DB::raw('SUM(student_answers.is_correct) as student_correct_answers'), DB::raw('COUNT(*) as student_total_answers'))
            ->groupBy('answers.topic_id')
            ->get();

        $topicAverages = $topics->map(function($topic) use ($studentAnswers, $examId) {
            $totalQuestions = $topic->answers->count();

            if ($totalQuestions > 0) {
                $studentData = $studentAnswers->where('topic_id', $topic->id)->first();
                $studentCorrectAnswers = $studentData ? $studentData->student_correct_answers : 0;
                $studentTotalAnswers = $studentData ? $studentData->student_total_answers : 0;
                $studentAverageCorrect = $studentTotalAnswers > 0 ? ($studentCorrectAnswers / $studentTotalAnswers) * 100 : 0;

                $totalAnswersData = DB::table('student_answers')
                    ->join('answers', 'student_answers.answer_id', '=', 'answers.id')
                    ->where('answers.topic_id', $topic->id)
                    ->where('answers.exam_id', $examId)
                    ->select(DB::raw('SUM(student_answers.is_correct) as total_correct_answers'), DB::raw('COUNT(*) as total_answers'))
                    ->groupBy('answers.topic_id')
                    ->get();

                $totalAnswersCount = $totalAnswersData->sum('total_answers');
                $totalCorrectCount = $totalAnswersData->sum('total_correct_answers');

                $averageCorrect = $totalAnswersCount > 0 ? ($totalCorrectCount / $totalAnswersCount) * 100 : 0;
            } else {
                $averageCorrect = 0;
                $studentAverageCorrect = 0;
            }

            return [
                'topic_id' => $topic->id,
                'topic_name' => $topic->topic_name,
                'average_correct' => $averageCorrect,
                'student_average_correct' => $studentAverageCorrect
            ];
        });

        return view('admin.exam_averages', compact('student', 'exam', 'topicAverages'));
    }

    public function showStudentExamDetails($studentId, $examId)
    {
        $student = Student::findOrFail($studentId);
        $exam = Exam::findOrFail($examId);

        $studentAnswers = StudentAnswer::where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->with(['answer.questions', 'answer.topic'])
            ->get();

        $examReviews = ExamReview::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get();

        return view('admin.student-exam-details', [
            'student'        => $student,
            'exam'           => $exam,
            'studentAnswers' => $studentAnswers,
            'examReviews'    => $examReviews,
        ]);
    }

    public function exams()
    {
        $exams = Exam::all();

        return view('admin.exams', compact('exams'));
    }

    public function editStudentPage($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.edit-student', compact('student'));
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $student->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->filled('password') ? bcrypt($request->input('password')) : $student->password,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Öğrenci başarıyla güncellendi!');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Öğrenci başarıyla silindi!');
    }
}
