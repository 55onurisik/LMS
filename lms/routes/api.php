<?php
// routes/api.php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentLoginController;
use App\Http\Controllers\StudentRegisterController;
use App\Http\Controllers\StudentAnalysisController;
use App\Http\Controllers\AIAnalysisController;
use Illuminate\Support\Facades\Route;

Route::prefix('studentAPI')->group(function () {
    // ➤ Giriş (token üretir)
    Route::post('/login',    [StudentLoginController::class, 'loginAPI']);
    Route::post('/register', [StudentRegisterController::class, 'storeAPI']);

    // ➤ Bunlar artık Sanctum token'ını kontrol edecek
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout',                [StudentLoginController::class, 'logoutAPI']);
        Route::get('/dashboard',              [StudentController::class,      'indexAPI']);
        Route::get('/exams/{exam}/answer',    [ExamController::class,         'showAnswerFormAPI']);
        Route::post('/exams/{exam}/submit',   [ExamController::class,         'submitStudentAnswersAPI']);
        Route::get('/exams/{exam}/review',    [ExamController::class,         'reviewAPI']);

        // ➤ Chat endpoints
        Route::post('/chat/send',             [ChatController::class,         'sendByStudent']);
        Route::get('/chat',                   [ChatController::class,         'studentIndex']);

        // ➤ Yeni: Öğrencinin sınav listesini çekmek için
        Route::get('/exams',                  [AnalysisController::class,   'examsAPI']);

        // ➤ Yeni: Öğrencinin tüm sınav istatistiklerini çekmek için
        Route::get('/statistics',            [AnalysisController::class,   'statisticsAPI']);
    });
});

// Admin API Routes (kimlik doğrulama gerekmeden analiz de eklenebilir)
Route::get('/students/{student}/analysis', [AIAnalysisController::class, 'analyze']);
Route::post('/students/{student}/chat',     [AIAnalysisController::class, 'chat']);

// Test için Gemini modellerini listeleyen demo endpoint
Route::get('/test-gemini', function() {
    $service = new \App\Services\GeminiService();
    $models = $service->listModels();
    return response()->json($models);
});
