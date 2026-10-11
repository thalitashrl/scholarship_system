<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Beasiswa extends Model
{
    use HasFactory;

    protected $table = 'beasiswa';

    protected $fillable = [
        'judul',
        'jenis',
        'deskripsi',
        'kuota',
        'deadline',
        'status',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
        'kuota' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
