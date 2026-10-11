<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Message;

class LiveChat extends Model
{
    use HasFactory;

    protected $table = 'live_chats';

    protected $fillable = [
        'mahasiswa_id',
        'staff_id',
        'status',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id');
    }
}
