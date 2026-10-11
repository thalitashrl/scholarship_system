<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Mahasiswa;

class MahasiswaController extends Controller
{
    // GET ALL MAHASISWA WITH PROFILE & USER DATA
    public function index()
    {
        $data = Mahasiswa::with('user.roles')->get();
        return $this->successResponse($data, 'Daftar mahasiswa berhasil diambil');
    }
}

