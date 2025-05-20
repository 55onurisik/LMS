<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentApprovalMail;

class AdminStudentController extends Controller
{
    public function index()
    {
        // Sadece bekleyen kayıtları getir
        $students = Student::where('status', 'pending')->get();
        return view('admin.student_requests', compact('students'));
    }

    public function approve($id)
    {
        $student = Student::findOrFail($id);
        $student->status = 'approved';
        $student->approved_at = now();
        $student->save();

        // Onay maili gönder
        //Mail::to($student->email)->send(new StudentApprovalMail($student));

        return redirect()->back()->with('success', 'Öğrenci onaylandı ve e-posta gönderildi.');
    }

    public function reject($id)
    {
        $student = Student::findOrFail($id);
        $student->status = 'rejected';
        $student->save();

        // İsteğe bağlı: reddetme maili gönderilebilir.
        return redirect()->back()->with('success', 'Öğrenci reddedildi.');
    }

    public function scheduleForm($id)
    {
        $student = Student::findOrFail($id);
        if ($student->status !== 'approved') {
            return redirect()->back()->with('error', 'Sadece onaylanmış öğrenciler için zamanlama yapılabilir.');
        }
        return view('admin.schedule', compact('student'));
    }

    public function saveSchedule(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $request->validate([
            'schedule_day' => 'required|string',
            'schedule_time' => 'required|date_format:H:i',
        ]);

        $student->schedule_day = $request->schedule_day;
        $student->schedule_time = $request->schedule_time;
        $student->save();

        return redirect()->back()->with('success', 'Zamanlama bilgileri kaydedildi.');
    }

    public function scheduleOverview()
    {
        $students = \App\Models\Student::where('status', 'approved')
            ->whereNotNull('schedule_day')
            ->whereNotNull('schedule_time')
            ->orderByRaw("FIELD(schedule_day, 'Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar')")
            ->orderBy('schedule_time')
            ->get();

        return view('admin.schedule_overview', compact('students'));
    }

}
