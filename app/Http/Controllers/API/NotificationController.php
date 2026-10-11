<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Notification;

class NotificationController extends Controller
{
    // GET NOTIFICATIONS FOR LOGGED IN USER
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse($notifications, 'Daftar notifikasi berhasil diambil');
    }
}

