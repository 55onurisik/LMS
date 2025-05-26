<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentAnswer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class StudentPerformanceAnalyzer
{
    private Student $student;
    private Collection $answers;
    private GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function getGeminiService()
    {
        return $this->geminiService;
    }

    private function loadAnswers(Student $student): void
    {
        Log::info('Loading answers for student: ' . $student->id);
        $this->student = $student;
        $this->answers = StudentAnswer::with(['question.topic'])
            ->where('student_id', $student->id)
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($a) => $a->question->topic->name ?? 'Bilinmeyen');
        
        Log::info('Loaded answers count: ' . $this->answers->count());
    }

    public function analyze(Student $student): array
    {
        Log::info('Starting analysis for student: ' . $student->id);
        
        try {
            $this->loadAnswers($student);
            
            $analysis = [
                'student_name' => $student->name,
                'topics' => [],
                'overall_success_rate' => 0,
                'total_questions' => 0,
                'total_correct' => 0,
            ];

            foreach ($this->answers as $topic => $answers) {
                Log::info('Analyzing topic: ' . $topic);
                $topicAnalysis = $this->analyzeTopic($topic, $answers);
                $analysis['topics'][] = $topicAnalysis;
                $analysis['total_questions'] += $topicAnalysis['total_questions'];
                $analysis['total_correct'] += $topicAnalysis['correct_answers'];
            }

            if ($analysis['total_questions'] > 0) {
                $analysis['overall_success_rate'] = round(
                    ($analysis['total_correct'] / $analysis['total_questions']) * 100,
                    2
                );
            }

            Log::info('Basic analysis completed. Getting AI analysis...');

            // Gemini API'den detaylı analiz al
            $geminiAnalysis = $this->getGeminiAnalysis($analysis);
            if ($geminiAnalysis) {
                Log::info('AI analysis received successfully');
                $analysis['ai_analysis'] = $geminiAnalysis;
            } else {
                Log::warning('AI analysis could not be obtained');
            }

            Log::info('Analysis completed successfully');
            return $analysis;
        } catch (\Exception $e) {
            Log::error('Error in analysis: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    private function getGeminiAnalysis(array $analysis): ?string
    {
        try {
            Log::info('Preparing data for Gemini API');
            $studentData = [
                'student_name' => $analysis['student_name'],
                'topics' => array_map(function ($topic) {
                    return [
                        'topic_name' => $topic['topic_name'],
                        'success_rate' => $topic['success_rate'],
                        'total_questions' => $topic['total_questions'],
                        'correct_answers' => $topic['correct_answers'],
                        'answers' => $this->answers[$topic['topic_name']]->map(function ($answer) {
                            return [
                                'question_number' => $answer->answer->question_number ?? 'Bilinmeyen',
                                'is_correct' => $answer->is_correct ? 'Doğru' : 'Yanlış',
                                'date' => $answer->created_at->format('Y-m-d')
                            ];
                        })->toArray()
                    ];
                }, $analysis['topics'])
            ];

            Log::info('Student data prepared for Gemini API:', $studentData);
            
            // Gemini API'ye gönderilecek prompt'u hazırla
            $prompt = $this->formatStudentData($studentData);

            Log::info('Sending prompt to Gemini API:', ['prompt' => $prompt]);
            
            $response = $this->geminiService->analyzeStudentPerformance($prompt);
            
            if (!$response) {
                Log::error('Gemini API returned null response');
                return "Analiz sırasında bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            
            Log::info('Received response from Gemini API:', ['response' => $response]);
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error in getGeminiAnalysis: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return "Analiz sırasında bir hata oluştu: " . $e->getMessage();
        }
    }

    private function analyzeTopic(string $topic, Collection $answers): array
    {
        $totalQuestions = $answers->count();
        $correctAnswers = $answers->where('is_correct', true)->count();
        $successRate = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        Log::info("Topic analysis - {$topic}: {$correctAnswers}/{$totalQuestions} correct ({$successRate}%)");

        // Zaman içindeki gelişimi analiz et
        $progress = $this->analyzeProgress($answers);

        return [
            'topic_name' => $topic,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'success_rate' => $successRate,
            'progress' => $progress,
            'needs_review' => $successRate < 60,
            'recommendations' => $this->generateRecommendations($successRate, $progress),
        ];
    }

    private function analyzeProgress(Collection $answers): array
    {
        $progress = [
            'is_improving' => false,
            'has_decline' => false,
            'trend' => [],
        ];

        $answersByDate = $answers->groupBy(function ($answer) {
            return Carbon::parse($answer->created_at)->format('Y-m-d');
        });

        $previousSuccessRate = null;
        $successRates = [];

        foreach ($answersByDate as $date => $dayAnswers) {
            $correctCount = $dayAnswers->where('is_correct', true)->count();
            $totalCount = $dayAnswers->count();
            $successRate = $totalCount > 0 ? ($correctCount / $totalCount) * 100 : 0;

            $successRates[] = [
                'date' => $date,
                'rate' => $successRate,
            ];

            if ($previousSuccessRate !== null) {
                if ($successRate > $previousSuccessRate) {
                    $progress['is_improving'] = true;
                } elseif ($successRate < $previousSuccessRate) {
                    $progress['has_decline'] = true;
                }
            }

            $previousSuccessRate = $successRate;
        }

        $progress['trend'] = $successRates;
        return $progress;
    }

    private function generateRecommendations(float $successRate, array $progress): array
    {
        $recommendations = [];

        if ($successRate < 60) {
            $recommendations[] = 'Bu konuda daha fazla pratik yapmanız gerekiyor.';
            $recommendations[] = 'Konu tekrarı yapmanızı öneririm.';
        }

        if ($progress['has_decline']) {
            $recommendations[] = 'Son zamanlarda performansınızda düşüş görülüyor. Konuyu tekrar gözden geçirmenizi öneririm.';
        }

        if ($progress['is_improving']) {
            $recommendations[] = 'Bu konuda ilerleme kaydediyorsunuz. Çalışmalarınıza devam edin.';
        }

        return $recommendations;
    }

    private function formatStudentData($studentData)
    {
        $prompt = "Sen bir eğitim analisti ve rehber öğretmensin. Aşağıda bir öğrencinin belirli konulara ait sorulara verdiği cevaplar, bu cevapların doğru/yanlış bilgisi ve cevapların tarih bilgileri yer almaktadır.\n\n";
        $prompt .= "Lütfen aşağıdaki konuları analiz et ve Türkçe olarak yanıt ver:\n";
        $prompt .= "1. Her konu için öğrencinin başarısını yorumla\n";
        $prompt .= "2. Başarısız olduğu konuları ve önerilen tekrar alanlarını belirt\n";
        $prompt .= "3. Cevapların tarihine bakarak öğrencinin zaman içindeki gelişimini analiz et\n";
        $prompt .= "4. Genel başarı oranını değerlendir\n";
        $prompt .= "5. Öğrenciye anlaşılır bir şekilde rehberlik et\n\n";
        $prompt .= "Öğrenci: " . $studentData['student_name'] . "\n\n";

        foreach ($studentData['topics'] as $topic) {
            $prompt .= "Konu: {$topic['topic_name']}\n";
            foreach ($topic['answers'] as $answer) {
                $prompt .= "Soru {$answer['question_number']} - {$answer['is_correct']} - {$answer['date']}\n";
            }
            $prompt .= "\n";
        }

        return $prompt;
    }
} 