@extends('layouts.admin')

@section('title', 'Jadwal Konsultasi')

@section('styles')
<style>
    .schedule-table th {
        background-color: #27272a !important;
        color: #ffffff !important;
        border-color: #3f3f46 !important;
    }
    .schedule-table td {
        border-color: #27272a !important;
        vertical-align: middle;
        min-width: 140px;
    }
    .slot-time {
        font-weight: 700;
        background-color: #1e1b15 !important;
        color: #eab308 !important;
        min-width: 90px !important;
    }
    .booking-card {
        border-radius: 0.5rem;
        padding: 0.5rem;
        font-size: 0.85rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-align: left;
    }
    .booking-pending {
        background-color: rgba(245, 158, 11, 0.15) !important;
        border-left: 4px solid #f59e0b !important;
        color: #f59e0b !important;
    }
    .booking-approved {
        background-color: rgba(6, 182, 212, 0.15) !important;
        border-left: 4px solid #06b6d4 !important;
        color: #06b6d4 !important;
    }
    .booking-completed {
        background-color: rgba(16, 185, 129, 0.15) !important;
        border-left: 4px solid #10b981 !important;
        color: #10b981 !important;
    }
    .btn-xs {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
    }
    .card-filter {
        background: linear-gradient(135deg, #18181b 0%, #1e1b15 100%) !important;
        border: 1px solid #27272a !important;
    }
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-premium p-4 border-0 position-relative overflow-hidden" style="background: linear-gradient(135deg, #18181b 0%, #1e1b15 100%) !important;">
            <div style="position: relative; z-index: 2;">
                <h4 class="fw-bold text-white mb-2"><i class="bi bi-calendar2-check-fill text-primary me-2"></i>Jadwal Konsultasi Mahasiswa</h4>
                <p class="text-muted mb-0">Halaman ini digunakan untuk melihat ketersediaan jadwal bimbingan dan melakukan booking konsultasi dengan Staff Kemahasiswaan atau Wakil Dekan 3 (WD3).</p>
            </div>
            <div class="position-absolute end-0 top-0 bottom-0 d-none d-md-flex align-items-center pe-5" style="opacity: 0.1; font-size: 6rem; color: #eab308;">
                <i class="bi bi-calendar-range-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Selection & Info -->
<div class="row mb-4">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-filter p-3 rounded-4 mb-3 mb-md-0">
            <form action="{{ route('consultation.index') }}" method="GET" id="filterForm">
                <label for="penerima_id" class="form-label text-white fw-bold mb-2">Pilih Penerima Konsultasi</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-primary"><i class="bi bi-person-fill"></i></span>
                    <select name="penerima_id" id="penerima_id" class="form-select border-secondary" onchange="document.getElementById('filterForm').submit();">
                        @foreach($recipients as $recipient)
                            @php
                                $roleLabel = $recipient->hasRole('wd3') ? 'WD3' : 'Staff';
                            @endphp
                            <option value="{{ $recipient->id }}" {{ $selectedPenerimaId == $recipient->id ? 'selected' : '' }}>
                                {{ $recipient->name }} ({{ $roleLabel }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-8 d-flex align-items-center">
        <div class="card card-premium w-100 p-3 border-0">
            <div class="row g-2 text-center text-md-start">
                <div class="col-6 col-sm-3">
                    <span class="badge bg-secondary w-100 py-2"><i class="bi bi-circle-fill text-success me-1"></i> Tersedia / Kosong</span>
                </div>
                <div class="col-6 col-sm-3">
                    <span class="badge bg-warning-subtle text-warning w-100 py-2"><i class="bi bi-clock-history me-1"></i> Menunggu</span>
                </div>
                <div class="col-6 col-sm-3">
                    <span class="badge bg-info-subtle text-info w-100 py-2"><i class="bi bi-calendar-check me-1"></i> Disetujui</span>
                </div>
                <div class="col-6 col-sm-3">
                    <span class="badge bg-success-subtle text-success w-100 py-2"><i class="bi bi-check-circle-fill me-1"></i> Selesai</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Calendar Grid -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-premium p-4 border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white m-0">
                    <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Ketersediaan Jadwal: {{ $selectedPenerima ? $selectedPenerima->name : 'N/A' }}
                </h5>
                <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Waktu dalam format WIB</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered schedule-table align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th class="slot-time align-middle">Waktu</th>
                            @foreach($dates as $date)
                                <th class="py-3">
                                    <div class="text-white fw-bold">{{ $date->isoFormat('dddd') }}</div>
                                    <div class="small text-muted">{{ $date->format('d M') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slots as $slot)
                            <tr>
                                <td class="slot-time py-3">{{ $slot }}</td>
                                @foreach($dates as $date)
                                    @php
                                        $slotDateTime = $date->format('Y-m-d') . ' ' . $slot;
                                        // Cari konsultasi untuk slot ini yang berstatus aktif
                                        $booking = isset($consultations[$slotDateTime]) ? $consultations[$slotDateTime]->filter(function($c) {
                                            return in_array($c->status, ['menunggu', 'disetujui', 'selesai']);
                                        })->first() : null;
                                    @endphp
                                    <td class="p-2">
                                        @if($booking)
                                            @if($booking->status === 'menunggu')
                                                <div class="booking-card booking-pending">
                                                    <div class="fw-bold text-white text-truncate">{{ $booking->mahasiswa->name }}</div>
                                                    <div class="small mb-1 text-capitalize text-muted"><i class="bi bi-geo-alt"></i> {{ str_replace('_', ' ', $booking->jenis) }}</div>
                                                    @if($booking->keterangan)
                                                        <div class="small text-muted text-truncate" title="{{ $booking->keterangan }}">{{ $booking->keterangan }}</div>
                                                    @endif
                                                    
                                                    @if(Auth::id() == $selectedPenerimaId || Auth::user()->hasRole('admin'))
                                                        <div class="d-flex gap-1 mt-2">
                                                            <form action="{{ route('consultation.updateStatus', $booking->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="disetujui">
                                                                <button type="submit" class="btn btn-xs btn-success text-white px-2" onclick="return confirm('Setujui permintaan bimbingan ini?')">
                                                                    <i class="bi bi-check-lg"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('consultation.updateStatus', $booking->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="ditolak">
                                                                <button type="submit" class="btn btn-xs btn-danger text-white px-2" onclick="return confirm('Tolak permintaan bimbingan ini?')">
                                                                    <i class="bi bi-x-lg"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-warning text-dark mt-1" style="font-size: 0.65rem;">Menunggu Konfirmasi</span>
                                                    @endif
                                                </div>
                                            @elseif($booking->status === 'disetujui')
                                                <div class="booking-card booking-approved">
                                                    <div class="fw-bold text-white text-truncate">{{ $booking->mahasiswa->name }}</div>
                                                    <div class="small mb-1 text-capitalize text-muted"><i class="bi bi-geo-alt"></i> {{ str_replace('_', ' ', $booking->jenis) }}</div>
                                                    @if($booking->keterangan)
                                                        <div class="small text-muted text-truncate" title="{{ $booking->keterangan }}">{{ $booking->keterangan }}</div>
                                                    @endif

                                                    @if(Auth::id() == $selectedPenerimaId || Auth::user()->hasRole('admin'))
                                                        <div class="mt-2">
                                                            <form action="{{ route('consultation.updateStatus', $booking->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="selesai">
                                                                <button type="submit" class="btn btn-xs btn-info text-dark w-100 fw-bold" onclick="return confirm('Selesaikan sesi bimbingan ini?')">
                                                                    <i class="bi bi-check2-circle"></i> Selesai
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-info text-dark mt-1" style="font-size: 0.65rem;">Telah Di-booking</span>
                                                    @endif
                                                </div>
                                            @elseif($booking->status === 'selesai')
                                                <div class="booking-card booking-completed">
                                                    <div class="fw-bold text-muted text-truncate">{{ $booking->mahasiswa->name }}</div>
                                                    <div class="small text-muted text-truncate"><i class="bi bi-check-lg text-success"></i> Selesai</div>
                                                </div>
                                            @endif
                                        @else
                                            @if(Auth::user()->hasRole('mahasiswa'))
                                                <button class="btn btn-outline-success btn-sm w-100 rounded-pill py-2" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#bookingModal"
                                                        data-date="{{ $date->format('Y-m-d') }}"
                                                        data-date-formatted="{{ $date->isoFormat('dddd, d M Y') }}"
                                                        data-time="{{ $slot }}">
                                                    <i class="bi bi-plus-circle me-1"></i> Pilih
                                                </button>
                                            @else
                                                <span class="text-success small fw-semibold"><i class="bi bi-check2"></i> Tersedia</span>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- History Log / Summary -->
<div class="row">
    <div class="col-12">
        <div class="card card-premium p-4 border-0">
            <h5 class="fw-bold text-white mb-4"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pengajuan Konsultasi</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Nama Pemohon</th>
                            <th scope="col">Penerima Bimbingan</th>
                            <th scope="col">Waktu Jadwal</th>
                            <th scope="col">Tipe</th>
                            <th scope="col">Tujuan / Deskripsi</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myConsultations as $c)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $c->mahasiswa->name }}</div>
                                    <span class="text-muted small">{{ $c->mahasiswa->email }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-white">{{ $c->penerima->name }}</div>
                                    <span class="badge bg-secondary badge-premium text-capitalize">{{ $c->tujuan }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-white">{{ $c->jadwal->isoFormat('dddd, d M Y') }}</div>
                                    <div class="small text-primary">{{ $c->jadwal->format('H:i') }} WIB</div>
                                </td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $c->jenis) }}</td>
                                <td class="text-wrap" style="max-width: 250px;">{{ $c->keterangan ?? '-' }}</td>
                                <td>
                                    @if($c->status === 'menunggu')
                                        <span class="badge bg-warning-subtle text-warning badge-premium">Menunggu</span>
                                    @elseif($c->status === 'disetujui')
                                        <span class="badge bg-info-subtle text-info badge-premium">Disetujui</span>
                                    @elseif($c->status === 'ditolak')
                                        <span class="badge bg-danger-subtle text-danger badge-premium">Ditolak</span>
                                    @elseif($c->status === 'selesai')
                                        <span class="badge bg-success-subtle text-success badge-premium">Selesai</span>
                                    @else
                                        <span class="badge bg-secondary badge-premium">{{ $c->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada riwayat pengajuan bimbingan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $myConsultations->appends(request()->except('history_page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Booking Modal (Mahasiswa Only) -->
@if(Auth::user()->hasRole('mahasiswa'))
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white fw-bold" id="bookingModalLabel"><i class="bi bi-bookmark-plus text-primary me-2"></i>Ajukan Booking Konsultasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('consultation.store') }}" method="POST">
                @csrf
                <input type="hidden" name="penerima_id" value="{{ $selectedPenerimaId }}">
                <input type="hidden" name="jadwal_date" id="modal_jadwal_date">
                <input type="hidden" name="jadwal_time" id="modal_jadwal_time">

                <div class="modal-body">
                    <div class="mb-3 p-3 bg-dark rounded-3 border border-secondary">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Penerima:</small>
                                <span class="fw-bold text-white">{{ $selectedPenerima ? $selectedPenerima->name : 'N/A' }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Jadwal:</small>
                                <span class="fw-bold text-primary" id="modal_jadwal_formatted"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jenis" class="form-label text-white fw-bold">Jenis Konsultasi</label>
                        <select name="jenis" id="jenis" class="form-select border-secondary" required>
                            <option value="tatap_muka">Tatap Muka (Offline)</option>
                            <option value="online">Online (Live Chat / Meet)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label text-white fw-bold">Tujuan / Keterangan Konsultasi</label>
                        <textarea name="keterangan" id="keterangan" rows="4" class="form-control border-secondary" placeholder="Tuliskan materi atau alasan konsultasi secara singkat..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send-fill me-1"></i> Ajukan Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const bookingModal = document.getElementById('bookingModal');
        if (bookingModal) {
            bookingModal.addEventListener('show.bs.modal', function (event) {
                // Tombol pemicu modal
                const button = event.relatedTarget;
                
                // Ambil data-attributes
                const date = button.getAttribute('data-date');
                const dateFormatted = button.getAttribute('data-date-formatted');
                const time = button.getAttribute('data-time');

                // Update input tersembunyi
                document.getElementById('modal_jadwal_date').value = date;
                document.getElementById('modal_jadwal_time').value = time;

                // Tampilkan info teks terformat
                document.getElementById('modal_jadwal_formatted').textContent = `${dateFormatted} pukul ${time} WIB`;
            });
        }
    });
</script>
@endsection
