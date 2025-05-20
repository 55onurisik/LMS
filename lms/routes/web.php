<?php

use App\Http\Controllers\ExamReviewController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentLoginController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\StudentRegisterController;
use App\Http\Controllers\AdminStudentController;
use Illuminate\Support\Facades\Route;

// Admin Login Routes
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Student Login Routes
Route::get('/student/login', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login.submit');
Route::post('/student/logout', [StudentLoginController::class, 'logout'])->name('student.logout');

// Home Page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Genel Auth Routes (Profil işlemleri vb.)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Student Registration Routes
Route::get('/student/register', [StudentRegisterController::class, 'showRegistrationForm'])->name('student.register');
Route::post('/student/register', [StudentRegisterController::class, 'store'])->name('student.register.store');
Route::get('/student/register/success', function () {
    // Bu view, "Kaydınız onaylandığında e-posta ile bilgilendirileceksiniz." mesajını içerir.
    return view('student.register_success');
})->name('student.register.success');

// Admin Routes (auth:admin middleware)
Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin']], function () {


    Route::get('/students/schedule-overview', [\App\Http\Controllers\AdminStudentController::class, 'scheduleOverview'])->name('admin.schedule.overview');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Sınav Rotaları
    Route::get('/exam/{examId}/statistics', [ExamController::class, 'statistics'])->name('admin.exams.statistics');
    Route::get('/exams', [ExamController::class, 'index'])->name('admin.exams.index');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('admin.exams.create');
    Route::post('/exams/store', [ExamController::class, 'store'])->name('admin.exams.store');
    Route::delete('/admin/exams/{exam}', [ExamController::class, 'destroy'])->name('admin.exams.destroy');
    Route::get('/admin/students/{studentId}/exam/{examId}/details', [AdminController::class, 'showStudentExamDetails'])->name('student.exam.details');
    Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])->name('admin.exams.edit');
    Route::put('exams/{exam}', [ExamController::class, 'update'])->name('admin.exams.update');
    Route::get('admin/exams/{examId}/toggle-visibility', [ExamController::class, 'toggleReviewVisibility'])->name('admin.exams.toggleVisibility');

    // Admin Öğrenci Yönetim Rotaları
    Route::get('/students', [AdminController::class, 'adminPage'])->name('admin.students.index');
    Route::get('/manageStudents', [AdminController::class, 'manageStudents'])->name('admin.students.manage');
    Route::get('/students/create', [AdminController::class, 'createStudent'])->name('admin.students.create');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
    Route::get('/students/{id}/edit', [AdminController::class, 'editStudentPage'])->name('admin.students.edit');
    Route::put('/students/{id}', [AdminController::class, 'updateStudent'])->name('admin.students.update');
    Route::delete('/students/{id}', [AdminController::class, 'destroyStudent'])->name('admin.students.destroy');
    Route::get('/students/profile/{id}', [AdminController::class, 'studentProfile'])->name('admin.students.profile');
    Route::get('/student/{studentId}/exam-results', [AdminController::class, 'showExamResults'])->name('student.exam.results');
    Route::get('/student/{studentId}/topic-percentages', [AdminController::class, 'showPercentage'])->name('student.topic.percentages');
    Route::get('/admin/students/{studentId}/exam/{examId}/averages', [AdminController::class, 'examAverages'])->name('student.exam.averages');
    Route::get('/admin/students/{studentId}/topics/{topicId}/chart', [AdminController::class, 'showTopicChart'])->name('admin.topic.chart');
    Route::get('student/{studentId}/performance', [ChartController::class, 'studentPerformance'])->name('student.performance.chart');
    Route::get('/students/{studentId}/exams', [AdminController::class, 'showStudentExams'])->name('admin.students.exams');

    // Exam Review Rotaları
    Route::post('/exam-reviews', [ExamReviewController::class, 'store'])->name('exam-reviews.store');
    Route::put('/exam-reviews/{review}', [ExamReviewController::class, 'update'])->name('exam-reviews.update');

    // Admin Öğrenci Kayıt Taleplerini Yönetme
    Route::get('/students/requests', [AdminStudentController::class, 'index'])->name('admin.students.requests');
    Route::post('/students/{id}/approve', [AdminStudentController::class, 'approve'])->name('admin.students.approve');
    Route::post('/students/{id}/reject', [AdminStudentController::class, 'reject'])->name('admin.students.reject');
    Route::get('/students/{id}/schedule', [AdminStudentController::class, 'scheduleForm'])->name('admin.students.schedule');
    Route::post('/students/{id}/schedule', [AdminStudentController::class, 'saveSchedule'])->name('admin.students.schedule.save');

    //unit topic

    Route::get('/unit-topic/create', [UnitController::class, 'create'])
        ->name('unit-topic.create');

    Route::post('/unit-topic/store-unit', [UnitController::class, 'storeUnit'])
        ->name('unit-topic.store-unit');

    Route::post('/unit-topic/store-topic', [UnitController::class, 'storeTopic'])
        ->name('unit-topic.store-topic');

    Route::get('/unit-topic/units/{class_level}', [UnitController::class, 'getUnits'])
        ->name('unit-topic.units');
});

// Student Routes (auth:student middleware)
Route::group(['prefix' => 'student', 'middleware' => ['auth:student']], function () {
    Route::get('/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::get('/exams/{exam}/answer', [ExamController::class, 'showAnswerForm'])->name('student.exams.answerForm');
    Route::post('student/exams/{exam}/submit', [ExamController::class, 'submitStudentAnswers'])->name('exams.submitStudentAnswers');
    Route::get('/exams/{exam}/review', [ExamController::class, 'review'])->name('student.exams.review');
});

// API Routes
Route::get('/api/topics', [TopicController::class, 'index']);
Route::get('/api/units', [UnitController::class, 'index']);
