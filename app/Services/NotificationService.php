<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationReceived;

class NotificationService
{
    /**
     * Kirim notifikasi ke user tertentu.
     */
    public static function send(int $userId, string $title, string $message, string $type, ?string $link = null)
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'status' => 'unread',
            'link' => $link
        ]);

        // Broadcast event untuk websocket real-time
        broadcast(new NotificationReceived($notification));

        return $notification;
    }

    /**
     * Kirim notifikasi siaran (broadcast) ke semua mahasiswa.
     */
    public static function notifyAllStudents(string $title, string $message, string $type, ?string $link = null)
    {
        // Cari semua user yang memiliki role 'mahasiswa'
        $students = User::whereHas('roles', function ($query) {
            $query->where('name', 'mahasiswa');
        })->get();

        foreach ($students as $student) {
            self::send($student->id, $title, $message, $type, $link);
        }
    }
}
