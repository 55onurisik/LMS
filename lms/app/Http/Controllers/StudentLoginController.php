<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('student.login'); // Student login view
    }

    public function login(Request $request)
    {
        // 1) İstek verilerini doğrula
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 2) Öğrenciyi email ile bul
        $student = Student::where('email', $data['email'])->first();

        // 3) Yoksa veya şifre yanlışsa hata döndür
        if (! $student || ! Hash::check($data['password'], $student->password)) {
            return back()->withErrors([
                'email' => 'Geçersiz kimlik bilgileri.'
            ]);
        }

        // 4) Hesap onayını kontrol et
        if ($student->status !== 'approved') {
            return back()->withErrors([
                'email' => 'Hesabınız henüz onaylanmamış.'
            ]);
        }

        // 5) Session-based login: Auth::guard('student') session driver kullanıyor
        Auth::guard('student')->login($student);

        // 6) Yönlendir
        return redirect()->intended('/student/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }

    // GET  /api/studentAPI/login
    public function showLoginFormAPI()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Login form data',
            // Eğer front-end’e spesifik bir şey göndereceksek buraya ekleriz
        ], 200);
    }

    // POST /api/studentAPI/login
    public function loginAPI(Request $request)
    {
        // 1) İstek verilerini doğrula
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 2) Öğrenciyi email ile bul
        $student = Student::where('email', $data['email'])->first();

        // 3) Eğer öğrenci yok veya şifre yanlışsa 401 dön
        if (! $student || ! Hash::check($data['password'], $student->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Geçersiz kimlik bilgileri.'
            ], 401);
        }

        // 4) Hesap onayını kontrol et
        if ($student->status !== 'approved') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hesabınız henüz onaylanmamış.'
            ], 403);
        }

        // 5) Token oluştur
        $token = $student->createToken('student-token')->plainTextToken;

        // 6) Başarılı yanıt
        return response()->json([
            'status' => 'success',
            'data'   => [
                'student' => $student,
                'token'   => $token,
            ],
        ], 200);
    }
    // POST /api/studentAPI/logout
    public function logoutAPI(Request $request)
    {
        // Geçerli Sanctum token’ı al
        $token = $request->user('student')->currentAccessToken();

        // Varsa sil
        if ($token) {
            $token->delete();
        }

        // JSON ile başarılı çıkış yanıtı
        return response()->json([
            'status'  => 'success',
            'message' => 'Çıkış yapıldı.'
        ], 200);
    }
}
