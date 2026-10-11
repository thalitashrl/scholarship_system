<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultations';

    protected $fillable = [
        'user_id',
        'tujuan',
        'jenis',
        'jadwal',
        'status',
        'penerima_id',
        'keterangan',
    ];

    protected $casts = [
        'jadwal' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }
}
