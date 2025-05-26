<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentPerformanceAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AIAnalysisController extends Controller
{
    protected $analyzer;

    public function __construct(StudentPerformanceAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function index()
    {
        try {
            Log::info('Loading AI Analysis index page');
            $students = Student::all();
            Log::info('Found ' . $students->count() . ' students');
            return view('admin.ai-analysis.index', compact('students'));
        } catch (\Exception $e) {
            Log::error('Error in index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Öğrenci listesi yüklenirken bir hata oluştu.');
        }
    }

    public function analyze(Student $student)
    {
        try {
            Log::info('Starting analysis for student: ' . $student->id);
            
            if (!$student) {
                Log::error('Student not found');
                return response()->json([
                    'success' => false,
                    'message' => 'Öğrenci bulunamadı'
                ], 404);
            }

            Log::info('Student found: ' . $student->name);
            
            $analysis = $this->analyzer->analyze($student);
            
            if (!$analysis) {
                Log::error('Analysis failed for student: ' . $student->id);
                return response()->json([
                    'success' => false,
                    'message' => 'Analiz yapılırken bir hata oluştu'
                ], 500);
            }

            // Update last analysis timestamp
            $student->update([
                'last_analysis_at' => Carbon::now()
            ]);

            Log::info('Analysis completed successfully for student: ' . $student->id);
            
            return response()->json([
                'success' => true,
                'analysis' => $this->formatAnalysisToHtml($analysis)
            ]);
        } catch (\Exception $e) {
            Log::error('Error in analyze: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Beklenmeyen bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function chat(Request $request, Student $student)
    {
        try {
            Log::info('Starting chat for student: ' . $student->id);
            
            if (!$student) {
                Log::error('Student not found');
                return response()->json([
                    'success' => false,
                    'message' => 'Öğrenci bulunamadı'
                ], 404);
            }

            $message = $request->input('message');
            Log::info('Chat message received: ' . $message);

            // Öğrencinin cevaplarını ve ilişkili verileri yükle
            $studentAnswers = $student->answers()
                ->with(['answer.topic'])
                ->get()
                ->groupBy('answer.topic.topic_name');

            // Tüm öğrencilerin ortalama başarı oranlarını hesapla
            $allStudents = Student::with(['answers' => function($query) {
                $query->with(['answer.topic']);
            }])->get();

            $topicAverages = [];
            foreach ($allStudents as $s) {
                foreach ($s->answers as $answer) {
                    $topicName = $answer->answer->topic->topic_name ?? 'Bilinmeyen';
                    if (!isset($topicAverages[$topicName])) {
                        $topicAverages[$topicName] = ['total' => 0, 'correct' => 0];
                    }
                    $topicAverages[$topicName]['total']++;
                    if ($answer->is_correct) {
                        $topicAverages[$topicName]['correct']++;
                    }
                }
            }

            // Chat için prompt hazırla
            $prompt = "Sen bir eğitim asistanısın ve şu anda bir öğretmenle konuşuyorsun. Aşağıdaki öğrenci verilerini kullanarak öğretmenin sorduğu soruyu yanıtla. Öğrencinin performansını diğer öğrencilerle karşılaştırarak değerlendir ve öğretmene rehberlik et.\n\n";
            $prompt .= "Öğrenci: " . $student->name . "\n\n";
            
            foreach ($studentAnswers as $topicName => $answers) {
                $totalQuestions = $answers->count();
                $correctAnswers = $answers->where('is_correct', true)->count();
                $successRate = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
                
                // Konu için ortalama başarı oranını hesapla
                $topicAvg = isset($topicAverages[$topicName]) ? 
                    round(($topicAverages[$topicName]['correct'] / $topicAverages[$topicName]['total']) * 100, 2) : 0;
                
                $prompt .= "Konu: {$topicName}\n";
                $prompt .= "Öğrencinin Başarı Oranı: %{$successRate}\n";
                $prompt .= "Sınıf Ortalaması: %{$topicAvg}\n";
                $prompt .= "Toplam Soru: {$totalQuestions}\n";
                $prompt .= "Doğru Cevap: {$correctAnswers}\n";
                
                // Başarı durumuna göre not ekle
                if ($successRate > $topicAvg + 10) {
                    $prompt .= "Not: Bu konuda sınıf ortalamasının üzerinde performans gösteriyor.\n";
                } elseif ($successRate < $topicAvg - 10) {
                    $prompt .= "Not: Bu konuda sınıf ortalamasının altında performans gösteriyor.\n";
                } else {
                    $prompt .= "Not: Bu konuda sınıf ortalamasına yakın performans gösteriyor.\n";
                }
                $prompt .= "\n";
            }
            
            $prompt .= "\nÖğretmenin Sorusu: " . $message . "\n\n";
            $prompt .= "Lütfen yanıtını Türkçe olarak ver. Öğrencinin performansını diğer öğrencilerle karşılaştırarak değerlendir ve öğretmene rehberlik et. Yanıtın kısa ve öz olsun.";

            Log::info('Sending chat prompt to Gemini API:', ['prompt' => $prompt]);

            // Gemini API'yi çağır
            $response = $this->analyzer->getGeminiService()->analyzeStudentPerformance($prompt);

            if (!$response) {
                throw new \Exception('API yanıt vermedi');
            }

            Log::info('Received response from Gemini API:', ['response' => $response]);

            return response()->json([
                'success' => true,
                'response' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Chat error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Sohbet sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatAnalysisToHtml($analysis)
    {
        $html = '<div class="analysis-content">';
        
        // Genel Başarı Oranı
        $html .= '<div class="card mb-4">';
        $html .= '<div class="card-header bg-primary text-white">';
        $html .= '<h5 class="mb-0">Genel Başarı Oranı</h5>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h3 class="text-center">' . $analysis['overall_success_rate'] . '%</h3>';
        $html .= '<p class="text-center">Toplam Soru: ' . $analysis['total_questions'] . '</p>';
        $html .= '<p class="text-center">Doğru Cevap: ' . $analysis['total_correct'] . '</p>';
        $html .= '</div></div>';

        // Konu Bazlı Analizler
        $html .= '<div class="card mb-4">';
        $html .= '<div class="card-header bg-info text-white">';
        $html .= '<h5 class="mb-0">Konu Bazlı Analizler</h5>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        
        foreach ($analysis['topics'] as $topic) {
            $html .= '<div class="topic-analysis mb-3">';
            $html .= '<h6>' . $topic['topic_name'] . '</h6>';
            $html .= '<div class="progress mb-2">';
            $html .= '<div class="progress-bar ' . ($topic['success_rate'] >= 60 ? 'bg-success' : 'bg-danger') . '" ';
            $html .= 'role="progressbar" style="width: ' . $topic['success_rate'] . '%" ';
            $html .= 'aria-valuenow="' . $topic['success_rate'] . '" aria-valuemin="0" aria-valuemax="100">';
            $html .= $topic['success_rate'] . '%</div></div>';
            $html .= '<p>Toplam Soru: ' . $topic['total_questions'] . ' | Doğru: ' . $topic['correct_answers'] . '</p>';
            
            if (!empty($topic['recommendations'])) {
                $html .= '<div class="recommendations mt-2">';
                $html .= '<strong>Öneriler:</strong><ul>';
                foreach ($topic['recommendations'] as $recommendation) {
                    $html .= '<li>' . $recommendation . '</li>';
                }
                $html .= '</ul></div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div></div>';

        // Yapay Zeka Analizi
        if (isset($analysis['ai_analysis'])) {
            $html .= '<div class="card">';
            $html .= '<div class="card-header bg-success text-white">';
            $html .= '<h5 class="mb-0">Yapay Zeka Analizi</h5>';
            $html .= '</div>';
            $html .= '<div class="card-body">';
            $html .= '<div class="ai-analysis">' . nl2br(e($analysis['ai_analysis'])) . '</div>';
            $html .= '</div></div>';
        }

        $html .= '</div>';
        
        return $html;
    }
} 