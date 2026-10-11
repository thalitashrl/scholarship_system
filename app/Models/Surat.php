<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Dokumen;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surat';

    protected $fillable = [
        'user_id',
        'jenis_surat',
        'keperluan',
        'status',
        'tanggal_pengajuan',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'surat_id');
    }
}
