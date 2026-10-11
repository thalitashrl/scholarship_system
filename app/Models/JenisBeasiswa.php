<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisBeasiswa extends Model
{
    use HasFactory;

    protected $table = 'master_jenis_beasiswa';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];
}
