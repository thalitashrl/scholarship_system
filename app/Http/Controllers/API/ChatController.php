<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LiveChat;
use App\Models\User;
use App\Models\Message;
use App\Services\NotificationService;
use App\Events\MessageSent;

class ChatController extends Controller
{
    // GET CHATS FOR LOGGED IN USER
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('mahasiswa')) {
            // Mahasiswa sees their own active/completed chats along with messages and staff info
            $chats = LiveChat::where('mahasiswa_id', $user->id)
                ->with(['staff', 'messages.sender'])
                ->get();
        } elseif ($user->hasAnyRole(['staff', 'admin'])) {
            // Staff and Admin see all chats in the system
            $chats = LiveChat::with(['mahasiswa', 'staff', 'messages.sender'])
                ->get();
        } else {
            return $this->errorResponse('Akses ditolak', 403);
        }

        return $this->successResponse($chats, 'Daftar obrolan berhasil diambil');
    }

    // START A NEW CHAT SESSION (STUDENT ONLY)
    public function startChat(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('mahasiswa')) {
            return $this->errorResponse('Hanya mahasiswa yang dapat memulai obrolan baru', 403);
        }

        $request->validate([
            'staff_id' => 'required|exists:users,id'
        ]);

        $staff = User::find($request->staff_id);

        if (!$staff || !$staff->hasRole('staff')) {
            return $this->errorResponse('Penerima haruslah seorang staf kemahasiswaan (Role staff)', 422);
        }

        // Cari chat session yang aktif atau buat baru
        $chat = LiveChat::firstOrCreate([
            'mahasiswa_id' => $user->id,
            'staff_id' => $request->staff_id,
            'status' => 'aktif'
        ]);

        return $this->successResponse($chat->load(['mahasiswa', 'staff', 'messages']), 'Sesi obrolan berhasil dibuat');
    }

    // SEND MESSAGE TO CHAT SESSION
    public function sendMessage(Request $request, $id)
    {
        $user = $request->user();

        $request->validate([
            'message' => 'required|string|max:5000'
        ]);

        $chat = LiveChat::find($id);

        if (!$chat) {
            return $this->errorResponse('Sesi obrolan tidak ditemukan', 404);
        }

        // Pastikan pengirim adalah bagian dari sesi chat tersebut
        if ($chat->mahasiswa_id !== $user->id && $chat->staff_id !== $user->id && !$user->hasRole('admin')) {
            return $this->errorResponse('Akses ditolak untuk mengirim pesan ke obrolan ini', 403);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'sent_at' => now()
        ]);

        // Broadcast event untuk websocket real-time
        broadcast(new MessageSent($message));

        // Cari tahu penerima notifikasi pesan baru (lawan bicara)
        $receiverId = ($user->id === $chat->mahasiswa_id) ? $chat->staff_id : $chat->mahasiswa_id;

        if ($receiverId) {
            NotificationService::send(
                $receiverId,
                'Pesan Baru',
                $user->name . ": " . (strlen($request->message) > 50 ? substr($request->message, 0, 50) . "..." : $request->message),
                'chat',
                '/chat'
            );
        }

        return $this->successResponse($message->load('sender'), 'Pesan berhasil dikirim', 201);
    }
}


