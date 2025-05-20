<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // ReCaptcha doğrulaması
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => '6LelzaMqAAAAAL3YsQhqk8zduXGogsVb9dzSe2hc',
            'response' => $request->input('g-recaptcha-response'),
        ]);

        $captchaSuccess = $response->json()['success'] ?? false;

        if (!$captchaSuccess) {
            return redirect()->back()->withErrors(['captcha' => 'ReCaptcha doğrulaması başarısız.']);
        }

        // Kullanıcı giriş kontrolü
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
    }
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

}

