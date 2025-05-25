<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExamReview;
use App\Models\StudentAnswer;
use App\Models\Exam;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function create()
    {
        return view('admin.index');
    }

    public function index()
    {
        // Veritabanından sınavları çek
        $exams = Exam::all();
        return view('admin.exams', compact('exams'));
    }

    public function showAnswerForm($examId)
    {
        $userId = auth()->id(); // Giriş yapan kullanıcının ID'sini al
        $exam = Exam::findOrFail($examId);

        // Kullanıcının bu sınavı daha önce çözüp çözmediğini kontrol et
        $hasAnswered = StudentAnswer::where('exam_id', $examId)
            ->where('student_id', $userId)
            ->exists();

        if ($hasAnswered) {
            // Kullanıcı daha önce bu sınavı çözmüşse uyarı mesajı ile yönlendir
            return redirect()->route('student.dashboard')
                ->with('error', 'Bu sınavı zaten çözdünüz!');
        }

        $questions = Answer::where('exam_id', $examId)->get(); // Soruları alıyoruz

        return view('student.answerForm', compact('exam', 'questions'));
    }

    // Cevapları işleyen metot
    public function submitAnswers(Request $request)
    {
        // Kod burada olacak
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'exam_title' => 'required|string|max:255',
                'questions' => 'required|array',
                'questions.*.class_id' => 'required|integer',
                'questions.*.unit_id' => 'required|integer',
                'questions.*.topic_id' => 'required|integer',
                'questions.*.answer_text' => 'required|string',
                'questions.*.image_path' => 'nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        try {
            $exam = Exam::create([
                'exam_code' => Str::upper(Str::random(4)),
                'exam_title' => $request->input('exam_title')
            ]);

            $counter = 1;
            foreach ($validatedData['questions'] as $question) {
                Answer::create([
                    'exam_id' => $exam->id,
                    'question_number' => $counter,
                    'unit_id' => $question['unit_id'],
                    'answer_text' => $question['answer_text'],
                    'topic_id' => $question['topic_id'],
                    'image_path' => $question['image_path'] ?? null,
                ]);
                $counter++;
            }

            $exam->question_count = $counter - 1;
            $exam->save();

            return response()->json(['message' => 'Sınav başarıyla kaydedildi.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Sınav kaydedilirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Sınav başarıyla silindi.');
    }

    public function submitStudentAnswers(Request $request, $examId)
    {
        // Giriş yapan öğrencinin ID'si
        $studentId = $request->user()->id;

        // Formdan gelen cevaplar (örneğin: [ questionId => 'a', questionId2 => 'b', ... ])
        $answers = $request->input('answers');

        foreach ($answers as $questionId => $answer) {
            // Doğru cevabı Answer tablosundan alıyoruz
            $correctAnswer = Answer::where('id', $questionId)->value('answer_text');

            // is_correct değerini atayalım (0 => yanlış, 1 => doğru, 2 => boş)
            $isCorrect = (!$answer)
                ? 2
                : (($answer === $correctAnswer) ? 1 : 0);

            StudentAnswer::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'exam_id' => $examId,
                    'answer_id' => $questionId,
                ],
                [
                    'is_correct'       => $isCorrect,
                    'students_answer'  => $answer // a, b, c, d, e
                ]
            );
        }

        // Öğrenci dashboarduna yönlendirme veya başka bir sayfaya
        return redirect()->route('student.dashboard')
            ->with('success', 'Cevaplarınız kaydedilmiştir.');
    }

    private function checkAnswer($examId, $answerId, $answer)
    {
        // Bu fonksiyon, verilen exam_id ve answer_id için cevabın doğru olup olmadığını kontrol etmelidir.
        $correctAnswer = Answer::where('id', $answerId)->value('answer_text');
        return $answer === $correctAnswer;
    }

    public function showResults($examId)
    {
        $exam = Exam::findOrFail($examId);
        $correctAnswers = Answer::where('exam_id', $examId)->get();

        // Burada, öğrenci tarafından gönderilen cevapları almak için Session veya başka yöntem kullanabilirsiniz.
        $submittedAnswers = session('submitted_answers', []);

        // Yanlış cevapları belirle
        $wrongAnswers = [];
        foreach ($correctAnswers as $correctAnswer) {
            $questionNumber = $correctAnswer->question_number;
            $submittedAnswer = $submittedAnswers[$questionNumber] ?? null;

            if ($submittedAnswer !== $correctAnswer->answer_text) {
                $wrongAnswers[] = [
                    'question_number' => $questionNumber,
                    'submitted_answer' => $submittedAnswer,
                    'correct_answer' => $correctAnswer->answer_text,
                ];
            }
        }

        return view('user.results', compact('exam', 'wrongAnswers'));
    }

    public function statistics($examId)
    {
        // Sınav id'sine göre tüm öğrenci cevaplarını al
        $studentAnswers = StudentAnswer::where('exam_id', $examId)->get();

        // Sınav bilgilerini al
        $exam = Exam::findOrFail($examId);

        // Öğrenciler ve doğru/yanlış/boş cevap sayıları
        $statistics = $studentAnswers->groupBy('student_id')->map(function ($answers) {
            $correctCount = $answers->where('is_correct', 1)->count();
            $wrongCount   = $answers->where('is_correct', 0)->count();
            $blankCount   = $answers->where('is_correct', 2)->count();
            // Net puan (örneğin yanlış -0.25)
            $netScore = $correctCount - ($wrongCount * 0.25);

            return [
                'correct_count' => $correctCount,
                'wrong_count'   => $wrongCount,
                'blank_count'   => $blankCount,
                'total_count'   => $answers->count(),
                'net_score'     => $netScore,
                'student'       => $answers->first()->student // Öğrenci modeli
            ];
        })->sortByDesc('net_score');

        return view('admin.exam_statistics', compact('statistics', 'exam'));
    }

    public function toggleReviewVisibility($examId)
    {
        $exam = Exam::findOrFail($examId);
        // Tersine çevir
        $exam->review_visibility = !$exam->review_visibility;
        $exam->save();

        return redirect()->back()->with('success', 'Görünürlük durumu güncellendi.');
    }

    /**
     *  AŞAĞIDA EKLEDİĞİMİZ YENİ METOTLAR:
     */

    // 1) Öğrenciye sınavları listeleyen metot
    public function studentIndex()
    {
        // Tüm sınavları (veya sadece aktifleri) çekiyoruz
        $exams = Exam::all();
        return view('student.dashboard', compact('exams'));
    }

    // 2) Öğrencinin sınav değerlendirmesini görebileceği metot
    public function review(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        if (!$exam->review_visibility) {
            abort(403, 'Bu sınavın değerlendirmeleri şu anda görünür değil.');
        }

        $studentId = auth()->id();

        // Bu öğrencinin sınavdaki cevaplarını alıyoruz.
        $studentAnswers = \App\Models\StudentAnswer::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->with('answer')
            ->get();

        // Broadcast parametresi: form gönderiminde broadcast checkbox işaretlenmişse "yes" gönderiliyor.
        $broadcast = $request->input('broadcast', 'no') === 'yes';
        \Log::info("Broadcast value in review: " . ($broadcast ? 'yes' : 'no'));

        foreach ($studentAnswers as $sa) {
            // Kendi inceleme kaydını alıyoruz.
            $review = \App\Models\ExamReview::where('exam_id', $exam->id)
                ->where('student_id', $studentId)
                ->where('question_id', $sa->answer_id)
                ->first();

            // Bu öğrencinin kendi inceleme bilgilerini ekliyoruz.
            $sa->review_text  = $review ? $review->review_text : null;
            $sa->review_media = $review ? $review->review_media : null;

            // Broadcast seçeneği işaretlendiyse, kopyalama işlemini yapıyoruz.
            if ($broadcast && $review) {
                // Bu soru için sınavı çözmüş diğer öğrencileri alıyoruz.
                $otherStudentAnswers = \App\Models\StudentAnswer::where('exam_id', $exam->id)
                    ->where('answer_id', $sa->answer_id)
                    ->where('student_id', '!=', $studentId)
                    ->get();

                foreach ($otherStudentAnswers as $otherAnswer) {
                    // Eğer bu diğer öğrenci için zaten bir inceleme kaydı yoksa kopyalıyoruz.
                    $existing = \App\Models\ExamReview::where('exam_id', $exam->id)
                        ->where('student_id', $otherAnswer->student_id)
                        ->where('question_id', $sa->answer_id)
                        ->first();

                    if (!$existing) {
                        \App\Models\ExamReview::create([
                            'exam_id'     => $exam->id,
                            'student_id'  => $otherAnswer->student_id,
                            'question_id' => $sa->answer_id,
                            'review_text' => $review->review_text,
                            'review_media'=> $review->review_media,
                        ]);
                    }
                }
            }
        }

        return view('student.exams.review', [
            'exam'           => $exam,
            'studentAnswers' => $studentAnswers,
        ]);
    }


    // Yeni metot: Sınav düzenleme formunu göster
    public function edit($examId)
    {
        // İlgili sınavı ve soruları çekiyoruz
        $exam = Exam::findOrFail($examId);
        $questions = Answer::where('exam_id', $examId)->orderBy('question_number')->get();
        return view('admin.edit_exam', compact('exam', 'questions'));
    }

    // Güncelleme metodunda sınav cevabı güncellendikten sonra ilgili öğrenci cevaplarının is_correct bilgisini de yeniden hesaplıyoruz.
    public function update(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        $validatedData = $request->validate([
            'exam_title' => 'required|string|max:255',
            'questions'  => 'required|array',
            'questions.*.id' => 'required|integer|exists:answers,id',
            // Sadece 'a, b, c, d, e' seçeneklerine izin veriyoruz.
            'questions.*.answer_text' => 'required|in:a,b,c,d,e',
        ]);

        // Sınav başlığını güncelle
        $exam->exam_title = $validatedData['exam_title'];
        $exam->save();

        // Her soru için, seçilen doğru cevabı güncelle
        foreach ($validatedData['questions'] as $questionData) {
            Answer::where('id', $questionData['id'])->update([
                'answer_text' => $questionData['answer_text']
            ]);
        }

        // Sınav cevapları güncellendikten sonra, bu sınava ait tüm öğrenci cevaplarının is_correct bilgisini yeniden hesaplıyoruz.
        $this->recalcStudentAnswers($exam->id);

        return redirect()->route('admin.exams.index')->with('success', 'Sınav başarıyla güncellendi.');
    }

    // Private metot: Verilen sınavdaki tüm öğrenci cevaplarının is_correct bilgisini günceller.
    private function recalcStudentAnswers($examId)
    {
        $studentAnswers = StudentAnswer::where('exam_id', $examId)->get();

        foreach ($studentAnswers as $studentAnswer) {
            // Güncel doğru cevabı Answer tablosundan alıyoruz
            $correctAnswer = Answer::where('id', $studentAnswer->answer_id)->value('answer_text');
            $isCorrect = (!$studentAnswer->students_answer)
                ? 2
                : ($studentAnswer->students_answer === $correctAnswer ? 1 : 0);

            $studentAnswer->update(['is_correct' => $isCorrect]);
        }
    }

    public function showAnswerFormAPI($examId)
    {
        $userId = auth()->id();
        $exam = Exam::findOrFail($examId);

        $hasAnswered = StudentAnswer::where('exam_id', $examId)
            ->where('student_id', $userId)
            ->exists();

        if ($hasAnswered) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bu sınavı zaten çözdünüz!'
            ], 403);
        }

        $questions = Answer::where('exam_id', $examId)->get();

        return response()->json([
            'status'    => 'success',
            'exam'      => $exam,
            'questions' => $questions,
        ], 200);
    }

    public function submitStudentAnswersAPI(Request $request, $examId)
    {
        $studentId = $request->user()->id;
        $answers   = $request->input('answers', []);

        foreach ($answers as $questionId => $answer) {
            $correctAnswer = Answer::where('id', $questionId)->value('answer_text');
            $isCorrect     = (!$answer)
                ? 2
                : (($answer === $correctAnswer) ? 1 : 0);

            StudentAnswer::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'exam_id'    => $examId,
                    'answer_id'  => $questionId,
                ],
                [
                    'is_correct'      => $isCorrect,
                    'students_answer' => $answer,
                ]
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Cevaplarınız kaydedilmiştir.',
        ], 201);
    }

    public function reviewAPI(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        if (! $exam->review_visibility) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bu sınavın değerlendirmeleri şu anda görünür değil.'
            ], 403);
        }

        // Sanctum ile authenticate edilmiş öğrenci
        $student = $request->user();
        $studentId = $student->id;

        // Öğrencinin cevapları, sorularla birlikte
        $studentAnswers = StudentAnswer::with('answer')
            ->where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->get();

        $broadcast = $request->input('broadcast') === 'yes';

        // Broadcast seçeneği işaretliyse diğer öğrencilere de review kopyalama
        if ($broadcast) {
            foreach ($studentAnswers as $sa) {
                $review = ExamReview::where([
                    ['exam_id',    $exam->id],
                    ['student_id', $studentId],
                    ['question_id',$sa->answer_id],
                ])->first();

                if ($review) {
                    $others = StudentAnswer::where('exam_id', $exam->id)
                        ->where('answer_id', $sa->answer_id)
                        ->where('student_id', '!=', $studentId)
                        ->get();

                    foreach ($others as $other) {
                        ExamReview::firstOrCreate(
                            [
                                'exam_id'     => $exam->id,
                                'student_id'  => $other->student_id,
                                'question_id' => $sa->answer_id,
                            ],
                            [
                                'review_text'  => $review->review_text,
                                'review_media' => $review->review_media,
                            ]
                        );
                    }
                }
            }
        }

        // Her cevaba review_text ve review_media ekleyelim
        $studentAnswers->transform(function($sa) use ($exam, $studentId) {
            $review = ExamReview::where([
                ['exam_id',    $exam->id],
                ['student_id', $studentId],
                ['question_id',$sa->answer_id],
            ])->first();

            $sa->review_text  = $review ? $review->review_text  : null;
            $sa->review_media = $review ? $review->review_media : null;
            return $sa;
        });

        return response()->json([
            'status'         => 'success',
            'exam'           => $exam,
            'studentAnswers' => $studentAnswers,
        ], 200);
    }
}
