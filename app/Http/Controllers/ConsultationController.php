<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    /**
     * Tampilkan Halaman Utama Konsultasi.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil daftar Staff dan WD3
        $recipients = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['staff', 'wd3']);
        })->get();

        // Tentukan penerima terpilih
        if ($user->hasAnyRole(['staff', 'wd3'])) {
            $selectedPenerimaId = $request->get('penerima_id', $user->id);
        } else {
            $selectedPenerimaId = $request->get('penerima_id', $recipients->first()?->id);
        }

        $selectedPenerima = User::find($selectedPenerimaId);

        // Buat daftar tanggal kerja (Senin-Jumat) untuk 7 hari ke depan
        $dates = [];
        $tempDate = Carbon::today();
        
        // Cari 7 hari kerja ke depan
        while (count($dates) < 7) {
            if (!$tempDate->isWeekend()) {
                $dates[] = $tempDate->copy();
            }
            $tempDate->addDay();
        }

        // Jam slot standar
        $slots = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00'];

        // Ambil konsultasi dalam rentang waktu tersebut untuk penerima terpilih
        $startDate = $dates[0]->copy()->startOfDay();
        $endDate = end($dates)->copy()->endOfDay();

        $consultations = Consultation::where('penerima_id', $selectedPenerimaId)
            ->whereBetween('jadwal', [$startDate, $endDate])
            ->with('mahasiswa')
            ->get()
            ->groupBy(function ($item) {
                return $item->jadwal->format('Y-m-d H:i');
            });

        // Riwayat Konsultasi milik User saat ini
        if ($user->hasRole('mahasiswa')) {
            $myConsultations = Consultation::where('user_id', $user->id)
                ->with('penerima')
                ->orderBy('jadwal', 'desc')
                ->paginate(5, ['*'], 'history_page');
        } elseif ($user->hasAnyRole(['staff', 'wd3'])) {
            $myConsultations = Consultation::where('penerima_id', $user->id)
                ->with('mahasiswa')
                ->orderBy('jadwal', 'desc')
                ->paginate(5, ['*'], 'history_page');
        } else {
            // Admin sees all
            $myConsultations = Consultation::with(['mahasiswa', 'penerima'])
                ->orderBy('jadwal', 'desc')
                ->paginate(10, ['*'], 'history_page');
        }

        return view('consultation.index', compact(
            'recipients',
            'selectedPenerimaId',
            'selectedPenerima',
            'dates',
            'slots',
            'consultations',
            'myConsultations'
        ));
    }

    /**
     * Simpan Booking Konsultasi Baru (Mahasiswa Only).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('mahasiswa')) {
            return back()->with('error', 'Hanya mahasiswa yang dapat melakukan booking konsultasi.');
        }

        $request->validate([
            'penerima_id' => 'required|exists:users,id',
            'jenis' => 'required|in:tatap_muka,online',
            'jadwal_date' => 'required|date|after_or_equal:today',
            'jadwal_time' => 'required|string',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Cek hari kerja
        $date = Carbon::parse($request->jadwal_date);
        if ($date->isWeekend()) {
            return back()->with('error', 'Konsultasi hanya dapat dijadwalkan pada hari kerja (Senin - Jumat).');
        }

        // Gabungkan tanggal dan jam
        $jadwalStr = $request->jadwal_date . ' ' . $request->jadwal_time;
        $jadwal = Carbon::createFromFormat('Y-m-d H:i', $jadwalStr);

        if ($jadwal->isPast()) {
            return back()->with('error', 'Waktu konsultasi tidak boleh di masa lalu.');
        }

        // Periksa apakah slot ini sudah di-booking oleh konsultasi aktif
        $exists = Consultation::where('penerima_id', $request->penerima_id)
            ->where('jadwal', $jadwal)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Slot jadwal ini sudah di-booking atau sedang menunggu persetujuan.');
        }

        $penerima = User::findOrFail($request->penerima_id);
        $tujuan = $penerima->hasRole('wd3') ? 'wd3' : 'staff';

        $consultation = Consultation::create([
            'user_id' => $user->id,
            'tujuan' => $tujuan,
            'jenis' => $request->jenis,
            'jadwal' => $jadwal,
            'status' => 'menunggu',
            'penerima_id' => $request->penerima_id,
            'keterangan' => $request->keterangan,
        ]);

        // Pemicu Notifikasi ke Penerima (Staff / WD3)
        NotificationService::send(
            $request->penerima_id,
            'Permintaan Konsultasi Baru',
            "Mahasiswa " . $user->name . " mengajukan bimbingan pada " . $consultation->jadwal->format('d-m-Y H:i') . ".",
            'konsultasi',
            '/consultation'
        );

        return redirect()->route('consultation.index')->with('success', 'Booking konsultasi berhasil diajukan. Menunggu persetujuan.');
    }

    /**
     * Ubah Status Bimbingan (Disetujui/Ditolak/Selesai).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['staff', 'wd3', 'admin'])) {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak,selesai',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $consultation = Consultation::findOrFail($id);

        // Validasi kepemilikan persetujuan
        if ($user->id !== $consultation->penerima_id && !$user->hasRole('admin')) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk memproses konsultasi ini.');
        }

        $consultation->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan ?? $consultation->keterangan,
        ]);

        // Pemicu Notifikasi ke Mahasiswa
        NotificationService::send(
            $consultation->user_id,
            'Status Konsultasi Diperbarui',
            "Status konsultasi Anda pada " . $consultation->jadwal->format('d-m-Y H:i') . " diubah menjadi: " . strtoupper($request->status) . ".",
            'konsultasi',
            '/consultation'
        );

        return redirect()->route('consultation.index')->with('success', 'Status konsultasi berhasil diperbarui.');
    }
}
