<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function studentIndex()
    {
        $student = auth()->user(); // sanctum ile giriş yapan öğrenci

        if (!$student || !$student instanceof \App\Models\Student) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkisiz erişim.'
            ], 401);
        }

        $admin = \App\Models\User::first(); // İlk admin

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin bulunamadı.'
            ], 404);
        }

        $messages = \App\Models\Message::where(function ($q) use ($student, $admin) {
            $q->where('sender_id', $student->id)
                ->where('sender_type', \App\Models\Student::class)
                ->where('receiver_id', $admin->id)
                ->where('receiver_type', \App\Models\User::class);
        })
            ->orWhere(function ($q) use ($student, $admin) {
                $q->where('sender_id', $admin->id)
                    ->where('sender_type', \App\Models\User::class)
                    ->where('receiver_id', $student->id)
                    ->where('receiver_type', \App\Models\Student::class);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    // Sohbet geçmişini getir
    public function index()
    {
        $authUser = Auth::user();
        $admin = User::first();

        $messages = Message::where(function ($q) use ($authUser, $admin) {
            $q->where('sender_id', $authUser->id)
                ->where('sender_type', get_class($authUser))
                ->where('receiver_id', $admin->id)
                ->where('receiver_type', User::class);
        })
            ->orWhere(function ($q) use ($authUser, $admin) {
                $q->where('sender_id', $admin->id)
                    ->where('sender_type', User::class)
                    ->where('receiver_id', $authUser->id)
                    ->where('receiver_type', get_class($authUser));
            })
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
    // 🔹 Sohbet listesi (öğrencileri listeler)
    public function chatList()
    {
        $students = Student::all();
        return view('admin.chat.list', compact('students'));
    }

    // 🔹 Belirli öğrenci ile sohbet ekranı
    public function chatWithUser($studentId)
    {
        $admin = auth()->user(); // Admin girişli
        $student = Student::findOrFail($studentId);

        $messages = Message::where(function ($q) use ($admin, $student) {
            $q->where('sender_id', $admin->id)
                ->where('sender_type', User::class)
                ->where('receiver_id', $student->id)
                ->where('receiver_type', Student::class);
        })
            ->orWhere(function ($q) use ($admin, $student) {
                $q->where('sender_id', $student->id)
                    ->where('sender_type', Student::class)
                    ->where('receiver_id', $admin->id)
                    ->where('receiver_type', User::class);
            })
            ->orderBy('created_at')
            ->get();

        return view('admin.chat.conversation', compact('messages', 'student'));
    }

    // 🔹 Admin → Öğrenci mesaj gönderme
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:students,id',
            'message' => 'required|string',
        ]);

        $admin = auth()->user(); // User (admin) modeli
        $student = Student::findOrFail($request->receiver_id);

        Message::create([
            'sender_id' => $admin->id,
            'sender_type' => User::class,
            'receiver_id' => $student->id,
            'receiver_type' => Student::class,
            'message' => $request->message,
        ]);

        return redirect()->route('admin.chat.with', $student->id);
    }
    public function sendByStudent(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $student = auth()->user(); // sanctum ile giriş yapan öğrenci
        if (!$student || !$student instanceof \App\Models\Student) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkisiz erişim.'
            ], 401);
        }

        $admin = \App\Models\User::first(); // sistemdeki ilk admin varsayılıyor

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Yönetici bulunamadı.'
            ], 404);
        }

        $message = \App\Models\Message::create([
            'sender_id' => $student->id,
            'sender_type' => \App\Models\Student::class,
            'receiver_id' => $admin->id,
            'receiver_type' => \App\Models\User::class,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mesaj gönderildi.',
            'data' => $message
        ]);
    }

    public function getMessages($userId)
    {
        $student = Student::findOrFail($userId);
        $admin = auth()->user();

        $messages = Message::where(function ($q) use ($student, $admin) {
            $q->where('sender_id', $admin->id)
                ->where('sender_type', User::class)
                ->where('receiver_id', $student->id)
                ->where('receiver_type', Student::class);
        })->orWhere(function ($q) use ($student, $admin) {
            $q->where('sender_id', $student->id)
                ->where('sender_type', Student::class)
                ->where('receiver_id', $admin->id)
                ->where('receiver_type', User::class);
        })->orderBy('created_at')->get();

        return view('admin.chat.partials.messages', compact('messages', 'student'));
    }

}
