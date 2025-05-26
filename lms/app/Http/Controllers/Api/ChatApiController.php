<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;

class ChatApiController extends Controller
{
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

    // Mesaj gönder (hem öğrenci hem admin olabilir)
    public function send(Request $request)
    {
        dd(0);
        $request->validate([
            'message' => 'required|string',
        ]);

        // Kim giriş yaptı?
        $sender = Auth::user();

        if (!$sender) {
            $request->user()->id;
        }

        $senderType = get_class($sender); // Örn: "App\Models\Student" veya "App\Models\User"

        // Gönderen öğrenci mi admin mi?
        if ($sender instanceof \App\Models\Student) {
            $receiver = User::first();
        } else {
            // Admin gönderiyor → receiver_id parametresi ZORUNLU
            if (!$request->receiver_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Öğrenci seçilmedi.'
                ], 422);
            }

            $receiver = Student::find($request->receiver_id);
            if (!$receiver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Öğrenci bulunamadı.'
                ], 404);
            }
        }

        $receiverType = get_class($receiver);

        // DEBUG: Test etmek için log da yazabilirsin
        // \Log::info('Mesaj gönderimi', compact('senderType', 'receiverType'));
        // Mesajı oluştur
        $message = Message::create([
            'sender_id' => $sender->id,
            'sender_type' => $senderType,
            'receiver_id' => $receiver->id,
            'receiver_type' => $receiverType,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mesaj gönderildi',
            'data' => $message,
        ]);
    }
}
