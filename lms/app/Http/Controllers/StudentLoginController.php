<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('student.login'); // Student login view
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('student')->attempt($credentials)) {
            $student = Auth::guard('student')->user();

            // Öğrencinin statüsünü kontrol ediyoruz. Onaylı değilse çıkış yap ve hata mesajı göster.
            if ($student->status !== 'approved') {
                Auth::guard('student')->logout();
                return back()->withErrors(['email' => 'Hesabınız henüz onaylanmamış.']);
            }

            return redirect()->intended('/student/dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }


    public function logout(Request $request)
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
