<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveChat;
use App\Models\User;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatPortalController extends Controller
{
    /**
     * Tampilkan antarmuka obrolan dan obrolan aktif.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil semua sesi obrolan aktif milik pengguna saat ini
        $chats = LiveChat::where(function ($q) use ($user) {
            $q->where('mahasiswa_id', $user->id)
              ->orWhere('staff_id', $user->id);
        })
        ->where('status', 'aktif')
        ->with(['mahasiswa', 'staff', 'messages' => function ($q) {
            $q->orderBy('sent_at', 'asc');
        }])
        ->orderBy('updated_at', 'desc')
        ->get();

        // 2. Tentukan kontak yang diizinkan untuk diajak obrolan baru
        if ($user->hasRole('mahasiswa')) {
            // Mahasiswa hanya bisa chat dengan non-mahasiswa (admin, staff, wd3)
            $contacts = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'staff', 'wd3']);
            })->orderBy('name', 'asc')->get();
        } else {
            // Non-mahasiswa bisa chat dengan siapa saja kecuali diri sendiri
            $contacts = User::where('id', '!=', $user->id)
                ->orderBy('name', 'asc')
                ->get();
        }

        // 3. Ambil obrolan aktif yang dipilih
        $activeChat = null;
        if ($request->has('id')) {
            $activeChat = LiveChat::where(function ($q) use ($user) {
                $q->where('mahasiswa_id', $user->id)
                  ->orWhere('staff_id', $user->id);
            })
            ->with(['mahasiswa', 'staff', 'messages.sender'])
            ->find($request->id);
        } elseif ($chats->isNotEmpty()) {
            $activeChat = $chats->first();
        }

        return view('chat.index', compact('chats', 'contacts', 'activeChat'));
    }

    /**
     * Memulai sesi obrolan baru dengan pengguna lain.
     */
    public function startChat(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $recipientId = $request->recipient_id;

        if ($recipientId == $user->id) {
            return back()->with('error', 'Anda tidak bisa memulai obrolan dengan diri sendiri.');
        }

        $recipient = User::findOrFail($recipientId);

        // Aturan: Mahasiswa tidak boleh chat dengan sesama mahasiswa
        if ($user->hasRole('mahasiswa') && $recipient->hasRole('mahasiswa')) {
            return back()->with('error', 'Mahasiswa tidak diperkenankan memulai obrolan dengan sesama mahasiswa.');
        }

        // Cari sesi chat aktif yang sudah ada di antara kedua pengguna
        $chat = LiveChat::where(function ($q) use ($user, $recipientId) {
            $q->where('mahasiswa_id', $user->id)->where('staff_id', $recipientId);
        })
        ->orWhere(function ($q) use ($user, $recipientId) {
            $q->where('mahasiswa_id', $recipientId)->where('staff_id', $user->id);
        })
        ->where('status', 'aktif')
        ->first();

        // Jika belum ada, buat baru
        if (!$chat) {
            $chat = LiveChat::create([
                'mahasiswa_id' => $user->id,
                'staff_id' => $recipientId,
                'status' => 'aktif',
            ]);
        }

        return redirect()->route('chat.index', ['id' => $chat->id]);
    }

    /**
     * Kirim pesan baru ke dalam sesi obrolan.
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $chat = LiveChat::findOrFail($id);
        $user = Auth::user();

        // Validasi keanggotaan obrolan
        if ($chat->mahasiswa_id != $user->id && $chat->staff_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'sent_at' => now(),
        ]);

        // Sentuh timestamps sesi obrolan untuk mengurutkan sesi chat aktif terbaru
        $chat->touch();

        // Tentukan penerima pesan
        $receiverId = ($chat->mahasiswa_id == $user->id) ? $chat->staff_id : $chat->mahasiswa_id;

        if ($receiverId) {
            NotificationService::send(
                $receiverId,
                'Pesan Baru',
                $user->name . ': ' . (strlen($request->message) > 50 ? substr($request->message, 0, 50) . '...' : $request->message),
                'chat',
                '/chat?id=' . $chat->id
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ], 201);
        }

        return back()->with('success', 'Pesan terkirim.');
    }

    /**
     * Ambil daftar pesan dalam sesi obrolan (untuk AJAX Long-Polling).
     */
    public function getMessages($id)
    {
        $chat = LiveChat::findOrFail($id);
        $user = Auth::user();

        if ($chat->mahasiswa_id != $user->id && $chat->staff_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = Message::where('chat_id', $chat->id)
            ->with('sender')
            ->orderBy('sent_at', 'asc')
            ->get();

        $opponent = ($chat->mahasiswa_id == $user->id) ? $chat->staff : $chat->mahasiswa;
        $opponentOnline = $opponent ? $opponent->isOnline() : false;

        return response()->json([
            'messages' => $messages,
            'current_user_id' => $user->id,
            'opponent_online' => $opponentOnline,
        ]);
    }
}
