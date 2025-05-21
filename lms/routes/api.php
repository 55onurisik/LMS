<?php
// routes/api.php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentLoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('studentAPI')->group(function () {
    // ➤ Giriş (token üretir)
    Route::post('/login',  [StudentLoginController::class, 'loginAPI']);

    // ➤ Bunlar artık Sanctum token’ını kontrol edecek
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout',                        [StudentLoginController::class, 'logoutAPI']);
        Route::get('/dashboard',                      [StudentController::class,      'indexAPI']);
        Route::get('/exams/{exam}/answer',            [ExamController::class,         'showAnswerFormAPI']);
        Route::post('/exams/{exam}/submit',           [ExamController::class,         'submitStudentAnswersAPI']);
        Route::get('/exams/{exam}/review',            [ExamController::class,         'reviewAPI']);
    });
});
