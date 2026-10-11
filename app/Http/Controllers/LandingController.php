<?php

namespace App\Http\Controllers;

use App\Models\Beasiswa;
use App\Models\Mahasiswa;
use App\Models\Surat;
use App\Models\Consultation;

class LandingController extends Controller
{
    /**
     * Tampilkan Halaman Landing Page.
     */
    public function index()
    {
        $beasiswa = Beasiswa::where('status', 'aktif')
            ->orderBy('deadline', 'asc')
            ->take(6)
            ->get();

        $stats = [
            'totalBeasiswa' => Beasiswa::count(),
            'totalMahasiswa' => Mahasiswa::count(),
            'totalSurat' => Surat::count(),
            'totalBimbingan' => Consultation::count(),
        ];

        return view('landing', compact('beasiswa', 'stats'));
    }
}
