<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Beasiswa;
use App\Models\Surat;
use App\Models\Dokumen;
use App\Models\Consultation;
use App\Models\Mahasiswa;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Tampilkan Dashboard Ringkasan Statistik.
     */
    public function dashboard()
    {
        // Caching pintar untuk data statistik selama 10 menit
        $stats = Cache::remember('admin_dashboard_stats', 600, function () {
            $totalMahasiswa = User::whereHas('roles', function ($q) {
                $q->where('name', 'mahasiswa');
            })->count();

            $beasiswaAktif = Beasiswa::where('status', 'aktif')->count();
            $suratPending = Surat::where('status', 'pending')->count();
            $konsultasiTerjadwal = Consultation::where('status', 'disetujui')->count();

            // Query analitik Chart.js
            $prodiDistribution = Mahasiswa::select('prodi', DB::raw('count(*) as total'))
                ->groupBy('prodi')
                ->get();

            $suratDistribution = Surat::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();

            return [
                'totalMahasiswa' => $totalMahasiswa,
                'beasiswaAktif' => $beasiswaAktif,
                'suratPending' => $suratPending,
                'konsultasiTerjadwal' => $konsultasiTerjadwal,
                'prodiDistribution' => $prodiDistribution,
                'suratDistribution' => $suratDistribution,
            ];
        });

        // Query surat terbaru secara real-time
        $suratTerbaru = Surat::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', array_merge($stats, [
            'suratTerbaru' => $suratTerbaru
        ]));
    }

    /**
     * Tampilkan Kelola Beasiswa.
     */
    public function beasiswaIndex()
    {
        $beasiswa = Beasiswa::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.beasiswa.index', compact('beasiswa'));
    }

    /**
     * Tambah Beasiswa Baru.
     */
    public function beasiswaStore(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|in:internal,eksternal',
            'deskripsi' => 'required|string',
            'kuota' => 'required|integer|min:1',
            'deadline' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $beasiswa = Beasiswa::create([
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            'kuota' => $request->kuota,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        // Kirim notifikasi ke semua mahasiswa jika status beasiswa aktif
        if ($beasiswa->status === 'aktif') {
            NotificationService::notifyAllStudents(
                'Beasiswa Baru Tersedia',
                "Beasiswa baru '" . $beasiswa->judul . "' telah dibuka. Batas pendaftaran: " . $beasiswa->deadline->format('d-m-Y') . ".",
                'beasiswa',
                '/beasiswa'
            );
        }

        // Hapus cache statistik dasbor
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.beasiswa.index')->with('success', 'Beasiswa berhasil ditambahkan.');
    }

    /**
     * Edit/Update Beasiswa.
     */
    public function beasiswaUpdate(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|in:internal,eksternal',
            'deskripsi' => 'required|string',
            'kuota' => 'required|integer|min:1',
            'deadline' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $beasiswa = Beasiswa::find($id);

        if (!$beasiswa) {
            return back()->with('error', 'Beasiswa tidak ditemukan.');
        }

        $beasiswa->update($request->all());

        // Hapus cache statistik dasbor
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.beasiswa.index')->with('success', 'Beasiswa berhasil diperbarui.');
    }

    /**
     * Hapus Beasiswa.
     */
    public function beasiswaDestroy($id)
    {
        $beasiswa = Beasiswa::find($id);

        if (!$beasiswa) {
            return back()->with('error', 'Beasiswa tidak ditemukan.');
        }

        $beasiswa->delete();

        // Hapus cache statistik dasbor
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.beasiswa.index')->with('success', 'Beasiswa berhasil dihapus.');
    }

    /**
     * Tampilkan Pengolahan Surat.
     */
    public function suratIndex()
    {
        $surat = Surat::with(['user', 'dokumen'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.surat.index', compact('surat'));
    }

    /**
     * Update Status Pengajuan Surat Mahasiswa.
     */
    public function suratUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak',
        ]);

        $surat = Surat::find($id);

        if (!$surat) {
            return back()->with('error', 'Pengajuan surat tidak ditemukan.');
        }

        $updateData = ['status' => $request->status];
        if ($request->status === 'selesai') {
            $updateData['tanggal_selesai'] = now();
        }

        $surat->update($updateData);

        // Notifikasi ke mahasiswa tentang perubahan status
        NotificationService::send(
            $surat->user_id,
            'Status Pengajuan Surat Berubah',
            "Pengajuan surat '" . $surat->jenis_surat . "' Anda statusnya berubah menjadi '" . $request->status . "'.",
            'surat',
            '/surat'
        );

        // Hapus cache statistik dasbor
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.surat.index')->with('success', 'Status pengajuan surat berhasil diperbarui.');
    }

    /**
     * Unggah file hasil pengurusan surat dan perbarui status menjadi selesai.
     */
    public function suratUpload(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // max 2MB
            'status_selesai' => 'nullable|string'
        ]);

        $surat = Surat::find($id);

        if (!$surat) {
            return back()->with('error', 'Pengajuan surat tidak ditemukan.');
        }

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            
            // Simpan file ke storage/public/documents
            $path = $file->store('documents', 'public');
            $format = strtoupper($file->getClientOriginalExtension());
            if ($format === 'JPEG') {
                $format = 'JPG';
            }

            // Simpan dokumen baru dengan tipe 'balasan'
            Dokumen::create([
                'surat_id' => $surat->id,
                'nama_file' => $file->getClientOriginalName(),
                'file_path' => $path,
                'format' => $format,
                'ukuran' => $file->getSize(),
                'tipe' => 'balasan',
            ]);

            // Jika checkbox aktif, perbarui status menjadi selesai
            if ($request->has('status_selesai')) {
                $surat->update([
                    'status' => 'selesai',
                    'tanggal_selesai' => now(),
                ]);
                
                // Notifikasi ke mahasiswa bahwa statusnya berubah menjadi selesai
                NotificationService::send(
                    $surat->user_id,
                    'Status Pengajuan Surat Berubah',
                    "Pengajuan surat '" . $surat->jenis_surat . "' Anda telah SELESAI dan surat hasil dapat diunduh.",
                    'surat',
                    '/surat'
                );
            } else {
                // Notifikasi ke mahasiswa bahwa ada lampiran baru
                NotificationService::send(
                    $surat->user_id,
                    'Surat Tanggapan Baru',
                    "Staf/Admin mengunggah dokumen baru pada pengajuan surat '" . $surat->jenis_surat . "' Anda.",
                    'surat',
                    '/surat'
                );
            }

            DB::commit();

            // Hapus cache statistik dasbor
            Cache::forget('admin_dashboard_stats');

            return redirect()->route('admin.surat.index')->with('success', 'File surat berhasil diunggah.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengunggah surat: ' . $e->getMessage());
        }
    }
}
