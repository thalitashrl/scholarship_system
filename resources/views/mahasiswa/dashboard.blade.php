@extends('layouts.admin')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<!-- Welcome Premium Hero -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-premium p-4 border-0 position-relative overflow-hidden" style="background: linear-gradient(135deg, #18181b 0%, #1e1b15 100%) !important;">
            <div style="position: relative; z-index: 2;">
                <h4 class="fw-bold text-white mb-2">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="text-muted mb-0">Portal ini mempermudah Anda untuk memantau beasiswa aktif, mengajukan surat keterangan akademis, dan melakukan konsultasi online langsung dengan staf kemahasiswaan.</p>
            </div>
            <div class="position-absolute end-0 top-0 bottom-0 d-none d-md-flex align-items-center pe-5" style="opacity: 0.1; font-size: 8rem; color: #eab308;">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Widgets -->
<div class="row g-4 mb-4">
    <!-- Stat 1: Pengajuan Surat -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-premium card-stat p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Pengajuan Surat</span>
                    <h2 class="fw-extrabold text-navy-blue mt-2 mb-0">{{ $totalSurat }}</h2>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3">
                    <i class="bi bi-file-earmark-text-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Obrolan Aktif -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-premium card-stat p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Chat Aktif</span>
                    <h2 class="fw-extrabold text-navy-blue mt-2 mb-0">{{ $activeChats }}</h2>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-3">
                    <i class="bi bi-chat-left-text-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Notifikasi Baru -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-premium card-stat p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Notifikasi Baru</span>
                    <h2 class="fw-extrabold text-navy-blue mt-2 mb-0">{{ $unreadNotifications }}</h2>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-3">
                    <i class="bi bi-bell-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Beasiswa Tersedia -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-premium card-stat p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Beasiswa Aktif</span>
                    <h2 class="fw-extrabold text-navy-blue mt-2 mb-0">{{ $beasiswaAktifCount }}</h2>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-3">
                    <i class="bi bi-award-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Available Scholarships -->
    <div class="col-12 col-lg-7">
        <div class="card card-premium border-0 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-navy-blue m-0">
                    <i class="bi bi-award me-2 text-primary"></i>Daftar Beasiswa Aktif
                </h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="border-0">Beasiswa</th>
                            <th scope="col" class="border-0">Jenis</th>
                            <th scope="col" class="border-0">Batas Waktu</th>
                            <th scope="col" class="border-0 text-center">Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beasiswaList as $b)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white">{{ $b->judul }}</div>
                                    <span class="text-muted small d-inline-block text-truncate" style="max-width: 280px;">{{ $b->deskripsi }}</span>
                                </td>
                                <td>
                                    @if($b->jenis === 'internal')
                                        <span class="badge bg-primary-subtle text-primary badge-premium">Internal</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info badge-premium">Eksternal</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $b->deadline->format('d M Y') }}</td>
                                <td class="text-center"><span class="badge bg-secondary rounded-pill px-2 py-1">{{ $b->kuota }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada program beasiswa yang aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Letters -->
    <div class="col-12 col-lg-5">
        <div class="card card-premium border-0 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-navy-blue m-0">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Pengajuan Surat Terakhir
                </h5>
                <a href="{{ route('mahasiswa.surat.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Lihat Semua</a>
            </div>

            <div class="list-group list-group-flush bg-transparent">
                @forelse($suratTerbaru as $s)
                    <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-dark text-primary p-2 rounded-3 me-3">
                                <i class="bi bi-file-earmark-check-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-1">{{ $s->jenis_surat }}</h6>
                                <small class="text-muted">{{ $s->tanggal_pengajuan->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div>
                            @if($s->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning badge-premium">Pending</span>
                            @elseif($s->status === 'diproses')
                                <span class="badge bg-info-subtle text-info badge-premium">Diproses</span>
                            @elseif($s->status === 'selesai')
                                <span class="badge bg-success-subtle text-success badge-premium">Selesai</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger badge-premium">Ditolak</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-envelope-open fs-2 d-block mb-2"></i> Anda belum memiliki riwayat pengajuan surat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
