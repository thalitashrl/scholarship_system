<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Surat;
use App\Models\Dokumen;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;


class SuratController extends Controller
{
    // SUBMIT NEW SURAT WITH OPTIONAL DOCUMENT ATTACHMENT
    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required|string|max:255',
            'keperluan' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048' // max 2MB
        ]);

        try {
            DB::beginTransaction();

            $surat = Surat::create([
                'user_id' => $request->user()->id,
                'jenis_surat' => $request->jenis_surat,
                'keperluan' => $request->keperluan,
                'status' => 'pending',
                'tanggal_pengajuan' => now(),
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Store file in public/documents folder
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

            return $this->successResponse($surat->load('dokumen'), 'Pengajuan surat berhasil dikirim', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal mengajukan surat: ' . $e->getMessage(), 500);
        }
    }

    // GET LIST OF SURAT BY USER ROLE
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('mahasiswa')) {
            $surat = Surat::where('user_id', $user->id)->with('dokumen')->get();
        } elseif ($user->hasAnyRole(['staff', 'admin', 'wd3'])) {
            $surat = Surat::with(['user', 'dokumen'])->get();
        } else {
            return $this->errorResponse('Akses ditolak', 403);
        }

        return $this->successResponse($surat, 'Daftar pengajuan surat berhasil diambil');
    }

    // UPDATE STATUS WORKFLOW (STAFF/ADMIN/WD3 ONLY)
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->hasAnyRole(['staff', 'admin', 'wd3'])) {
            return $this->errorResponse('Akses ditolak (Role mismatch)', 403);
        }

        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak'
        ]);

        $surat = Surat::find($id);

        if (!$surat) {
            return $this->errorResponse('Pengajuan surat tidak ditemukan', 404);
        }

        $updateData = ['status' => $request->status];
        if ($request->status === 'selesai') {
            $updateData['tanggal_selesai'] = now();
        }

        $surat->update($updateData);

        // Pemicu Notifikasi Otomatis ke Mahasiswa Pemilik Surat
        NotificationService::send(
            $surat->user_id,
            'Status Pengajuan Surat Berubah',
            "Pengajuan surat '" . $surat->jenis_surat . "' Anda telah diperbarui menjadi '" . $request->status . "'.",
            'surat',
            '/surat'
        );

        return $this->successResponse($surat->load('dokumen'), 'Status pengajuan surat berhasil diperbarui');
    }
}


