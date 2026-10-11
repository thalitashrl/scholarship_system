<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Surat;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'surat_id',
        'nama_file',
        'file_path',
        'format',
        'ukuran',
        'tipe',
    ];

    protected $casts = [
        'ukuran' => 'integer',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'surat_id');
    }
}
