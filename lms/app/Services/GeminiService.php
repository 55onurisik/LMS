<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    }

    public function listModels()
    {
        try {
            $url = $this->baseUrl . '/models?key=' . $this->apiKey;
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->get($url);

            if (!$response->successful()) {
                Log::error('Failed to list models', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error listing models: ' . $e->getMessage());
            return null;
        }
    }

    public function analyzeStudentPerformance(string $prompt): ?string
    {
        try {
            Log::info('Initializing Gemini API request');
            
            $url = $this->baseUrl . '/models/gemini-1.5-pro:generateContent?key=' . $this->apiKey;
            
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                ]
            ];

            Log::info('Sending request to Gemini API', [
                'url' => $url,
                'prompt' => $prompt
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $requestData);

            if (!$response->successful()) {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return "API isteği başarısız oldu: " . $response->status() . " - " . $response->body();
            }

            $responseData = $response->json();
            Log::info('Received response from Gemini API', ['response' => $responseData]);

            if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Invalid response format from Gemini API', ['response' => $responseData]);
                return "API yanıtı geçersiz format içeriyor";
            }

            return $responseData['candidates'][0]['content']['parts'][0]['text'];

        } catch (\Exception $e) {
            Log::error('Error in Gemini API call: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return "API çağrısı sırasında hata: " . $e->getMessage();
        }
    }

    private function testApiConnection()
    {
        try {
            $url = $this->baseUrl . '/models/gemini-1.0-pro:generateContent?key=' . $this->apiKey;
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => 'Merhaba, bu bir test mesajıdır. Lütfen "Test başarılı" yanıtını ver.'
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    Log::info('API test successful');
                    return true;
                }
            }

            Log::error('API test failed: ' . $response->status() . ' - ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('API test exception: ' . $e->getMessage());
            return false;
        }
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