<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    // GET CONSULTATIONS FILTERED BY ROLE
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('mahasiswa')) {
            $data = Consultation::where('user_id', $user->id)
                ->with('penerima')
                ->orderBy('jadwal', 'asc')
                ->get();
        } elseif ($user->hasAnyRole(['staff', 'wd3'])) {
            $data = Consultation::where('penerima_id', $user->id)
                ->with(['mahasiswa', 'penerima'])
                ->orderBy('jadwal', 'asc')
                ->get();
        } elseif ($user->hasRole('admin')) {
            $data = Consultation::with(['mahasiswa', 'penerima'])
                ->orderBy('jadwal', 'asc')
                ->get();
        } else {
            return $this->errorResponse('Akses ditolak', 403);
        }

        return $this->successResponse($data, 'Jadwal konsultasi berhasil diambil');
    }

    // BOOK A NEW CONSULTATION (STUDENT ONLY)
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('mahasiswa')) {
            return $this->errorResponse('Hanya mahasiswa yang dapat melakukan booking konsultasi', 403);
        }

        $request->validate([
            'tujuan' => 'required|in:staff,wd3',
            'jenis' => 'required|in:tatap_muka,online',
            'jadwal' => 'required|date|after:now',
            'penerima_id' => 'nullable|exists:users,id',
            'keterangan' => 'nullable|string'
        ]);

        $penerimaId = $request->penerima_id;

        // Jika penerima_id tidak dispesifikasikan, cari otomatis berdasarkan role tujuan
        if (!$penerimaId) {
            $tujuanRole = $request->tujuan;
            $penerima = User::whereHas('roles', function ($query) use ($tujuanRole) {
                $query->where('name', $tujuanRole);
            })->first();

            $penerimaId = $penerima ? $penerima->id : null;
        }

        if (!$penerimaId) {
            return $this->errorResponse('Penerima konsultasi (Staff/WD3) belum terdaftar di sistem', 422);
        }

        $consultation = Consultation::create([
            'user_id' => $user->id,
            'tujuan' => $request->tujuan,
            'jenis' => $request->jenis,
            'jadwal' => $request->jadwal,
            'status' => 'menunggu',
            'penerima_id' => $penerimaId,
            'keterangan' => $request->keterangan
        ]);

        // Pemicu Notifikasi ke Penerima (Staff / WD3)
        NotificationService::send(
            $penerimaId,
            'Permintaan Konsultasi Baru',
            "Mahasiswa " . $user->name . " mengajukan bimbingan pada " . $consultation->jadwal->format('d-m-Y H:i') . ".",
            'konsultasi',
            '/consultation'
        );

        return $this->successResponse($consultation->load('penerima'), 'Booking konsultasi berhasil diajukan', 201);
    }

    // APPROVE, REJECT, REDIRECT, OR COMPLETE CONSULTATION (STAFF / WD3 / ADMIN ONLY)
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->hasAnyRole(['staff', 'wd3', 'admin'])) {
            return $this->errorResponse('Akses ditolak (Role mismatch)', 403);
        }

        $request->validate([
            'status' => 'required|in:menunggu,disetujui,ditolak,dialihkan,selesai',
            'penerima_id' => 'required_if:status,dialihkan|nullable|exists:users,id',
            'keterangan' => 'nullable|string'
        ]);

        $consultation = Consultation::find($id);

        if (!$consultation) {
            return $this->errorResponse('Data konsultasi tidak ditemukan', 404);
        }

        try {
            DB::beginTransaction();

            $oldPenerimaId = $consultation->penerima_id;
            $newPenerimaId = $request->penerima_id;

            $updateData = [
                'status' => $request->status,
                'keterangan' => $request->keterangan ?? $consultation->keterangan
            ];

            if ($request->status === 'dialihkan' && $newPenerimaId) {
                $updateData['penerima_id'] = $newPenerimaId;
                
                // Cari nama penerima baru
                $newPenerima = User::find($newPenerimaId);
                $newPenerimaName = $newPenerima ? $newPenerima->name : 'staf lain';

                // Kirim notifikasi ke mahasiswa mengenai pengalihan
                NotificationService::send(
                    $consultation->user_id,
                    'Jadwal Konsultasi Dialihkan',
                    "Jadwal bimbingan Anda dialihkan kepada " . $newPenerimaName . ".",
                    'konsultasi',
                    '/consultation'
                );

                // Kirim notifikasi ke staf baru penerima pengalihan
                NotificationService::send(
                    $newPenerimaId,
                    'Delegasi Konsultasi Baru',
                    "Anda menerima limpahan jadwal bimbingan mahasiswa " . $consultation->mahasiswa->name . " pada " . $consultation->jadwal->format('d-m-Y H:i') . ".",
                    'konsultasi',
                    '/consultation'
                );
            } else {
                // Notifikasi standar perubahan status ke mahasiswa
                NotificationService::send(
                    $consultation->user_id,
                    'Status Konsultasi Diperbarui',
                    "Permintaan konsultasi Anda statusnya berubah menjadi '" . $request->status . "'.",
                    'konsultasi',
                    '/consultation'
                );
            }

            $consultation->update($updateData);

            DB::commit();

            return $this->successResponse($consultation->load(['mahasiswa', 'penerima']), 'Status jadwal konsultasi berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal memperbarui status konsultasi: ' . $e->getMessage(), 500);
        }
    }
}
