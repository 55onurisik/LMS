<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class StudentRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('student.register');
    }

    public function store(Request $request)
    {
        // 1) reCAPTCHA kontrolü
        $recaptchaResponse = $request->input('g-recaptcha-response');
        if (!$recaptchaResponse) {
            return back()->withErrors(['recaptcha' => 'Lütfen robot olmadığınızı doğrulayın.'])->withInput();
        }

        // 2) Google API'sine doğrulama isteği
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => '6LelzaMqAAAAAL3YsQhqk8zduXGogsVb9dzSe2hc',
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip(),
        ]);

        $responseBody = $response->json();
        // 3) reCAPTCHA sonucu kontrolü
        if (!$responseBody['success']) {
            return back()->withErrors(['recaptcha' => 'reCAPTCHA doğrulaması başarısız oldu.'])->withInput();
        }

        // 4) Form verilerini doğrula
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:50',
            'email'         => 'required|string|email|max:255|unique:students',
            'password'      => 'required|string|min:6|confirmed',
            'class_level'   => 'required|in:9,10,11,12,Mezun',
        ]);

        // 5) Yeni öğrenci kaydı oluştur
        $student = Student::create([
            'name'        => $request->name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'class_level' => $request->class_level,
            'status'      => 'pending', // Kayıt onay bekliyor
        ]);

        // 6) Başarılı yönlendirme
        return redirect()
            ->route('student.register.success')
            ->with('success', 'Kayıt talebiniz gönderildi. Onay bekleniyor.');
    }

    public function storeAPI(Request $request)
    {
        // 1) Gelen verileri doğrula
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'phone'                 => 'required|string|max:50',
            'email'                 => 'required|email|max:255|unique:students',
            'password'              => 'required|string|min:6|confirmed',
            'class_level'           => 'required|in:9,10,11,12,Mezun',
            'schedule_day'          => 'nullable|string',
            'schedule_time'         => 'nullable|string',
        ]);

        // 2) Yeni öğrenci kaydı oluştur
        $student = Student::create([
            'name'          => $data['name'],
            'phone'         => $data['phone'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'class_level'   => $data['class_level'],
            'status'        => 'pending',
            'schedule_day'  => $data['schedule_day']  ?? null,
            'schedule_time' => $data['schedule_time'] ?? null,
        ]);

        // 3) Başarılı JSON yanıtı
        return response()->json([
            'status'  => 'success',
            'data'    => [
                'student' => [
                    'id'           => $student->id,
                    'name'         => $student->name,
                    'email'        => $student->email,
                    'phone'        => $student->phone,
                    'class_level'  => $student->class_level,
                    'status'       => $student->status,
                ],
            ],
            'message' => 'Kayıt talebiniz başarıyla alındı. Onay bekleniyor.',
        ], 201);
    }
}
