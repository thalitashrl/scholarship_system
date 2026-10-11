<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat;
use App\Models\Dokumen;
use App\Models\Beasiswa;
use App\Models\JenisSurat;
use App\Models\Notification;
use App\Models\LiveChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaPortalController extends Controller
{
    /**
     * Tampilkan Dashboard Ringkasan Statistik Portal Mahasiswa.
     */
    public function dashboard()
    {
        $userId = Auth::id();

        // 1. Hitung Statistik
        $totalSurat = Surat::where('user_id', $userId)->count();
        
        $activeChats = LiveChat::where(function ($q) use ($userId) {
            $q->where('mahasiswa_id', $userId)
              ->orWhere('staff_id', $userId);
        })->where('status', 'aktif')->count();

        $unreadNotifications = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();

        $beasiswaAktifCount = Beasiswa::where('status', 'aktif')->count();

        // 2. Ambil Daftar Beasiswa Aktif Terkini
        $beasiswaList = Beasiswa::where('status', 'aktif')
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        // 3. Ambil Riwayat Surat Terbaru
        $suratTerbaru = Surat::where('user_id', $userId)
            ->with('dokumen')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact(
            'totalSurat',
            'activeChats',
            'unreadNotifications',
            'beasiswaAktifCount',
            'beasiswaList',
            'suratTerbaru'
        ));
    }

    /**
     * Tampilkan Halaman Pengajuan Surat Mahasiswa.
     */
    public function suratIndex()
    {
        $userId = Auth::id();
        $jenisSurat = JenisSurat::all();
        $suratList = Surat::where('user_id', $userId)
            ->with('dokumen')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mahasiswa.surat.index', compact('jenisSurat', 'suratList'));
    }

    /**
     * Simpan Pengajuan Surat Baru beserta Lampiran Dokumen.
     */
    public function suratStore(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required|string|max:255',
            'keperluan' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048' // max 2MB
        ]);

        try {
            DB::beginTransaction();

            $surat = Surat::create([
                'user_id' => Auth::id(),
                'jenis_surat' => $request->jenis_surat,
                'keperluan' => $request->keperluan,
                'status' => 'pending',
                'tanggal_pengajuan' => now(),
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Simpan file ke storage/public/documents
                $path = $file->store('documents', 'public');
                $format = strtoupper($file->getClientOriginalExtension());
                if ($format === 'JPEG') {
                    $format = 'JPG';
                }

                Dokumen::create([
                    'surat_id' => $surat->id,
                    'nama_file' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'format' => $format,
                    'ukuran' => $file->getSize(),
                ]);
            }

            DB::commit();

            return redirect()->route('mahasiswa.surat.index')->with('success', 'Pengajuan surat berhasil dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage());
        }
    }
}
