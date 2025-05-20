<?php

namespace App\Http\Controllers;

use App\Models\ExamReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamReviewController extends Controller
{
    public function store(Request $request)
    {
        // Validation: question_id boş ise genel değerlendirme, dolu ise soru incelemesi
        $request->validate([
            'exam_id'      => 'required|exists:exams,id',
            'student_id'   => 'required|exists:students,id',
            'review_text'  => 'required|string',
            'question_id'  => 'nullable|exists:answers,id',
            // review_media: resim ve video dosyaları kabul edilsin, maksimum boyut 20MB
            'review_media' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt|max:20480',
        ]);

        // Formdan gelen verileri alıyoruz
        $data = $request->only(['exam_id', 'student_id', 'review_text', 'question_id']);

        // Dosya yüklemesi varsa, dosyayı sıkıştırma yapmadan direkt saklıyoruz
        if ($request->hasFile('review_media')) {
            $file = $request->file('review_media');
            $path = $file->store('exam_reviews', 'public');
            $data['review_media'] = $path;
        }

        // Aynı sınav, öğrenci ve soru için daha önce inceleme varsa güncelle, yoksa oluştur.
        $existingReview = ExamReview::where('exam_id', $data['exam_id'])
            ->where('student_id', $data['student_id'])
            ->where('question_id', $data['question_id'])
            ->first();

        if ($existingReview) {
            $existingReview->update($data);
        } else {
            ExamReview::create($data);
        }

        // Broadcast: Eğer "Diğer öğrenciler de bu incelemeyi alsın mı?" seçeneği işaretlendiyse,
        // ilgili soru için kaydı yapan öğrenci dışındaki diğer öğrencilere de kopyala.
        if ($request->input('broadcast', 'no') === 'yes') {
            if (!empty($data['question_id'])) {
                $otherStudentAnswers = \App\Models\StudentAnswer::where('exam_id', $data['exam_id'])
                    ->where('answer_id', $data['question_id'])
                    ->where('student_id', '!=', $data['student_id'])
                    ->get();

                foreach ($otherStudentAnswers as $otherAnswer) {
                    $existing = ExamReview::where('exam_id', $data['exam_id'])
                        ->where('student_id', $otherAnswer->student_id)
                        ->where('question_id', $data['question_id'])
                        ->first();

                    if (!$existing) {
                        ExamReview::create([
                            'exam_id'     => $data['exam_id'],
                            'student_id'  => $otherAnswer->student_id,
                            'question_id' => $data['question_id'],
                            'review_text' => $data['review_text'],
                            'review_media'=> $data['review_media'],
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'İnceleme başarıyla kaydedildi!');
    }
}
