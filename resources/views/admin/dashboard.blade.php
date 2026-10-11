@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Card 1: Total Mahasiswa -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-premium card-stat p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Total Mahasiswa</span>
                    <h2 class="fw-extrabold text-navy-blue mt-2 mb-0">{{ $totalMahasiswa }}</h2>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 2: Beasiswa Aktif -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-premium card-stat p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Beasiswa Aktif</span>
                    <h2 class="fw-extrabold text-navy-blue mt-2 mb-0">{{ $beasiswaAktif }}</h2>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-3">
                    <i class="bi bi-award-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 3: Surat Pending -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-premium card-stat p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Surat Pending</span>
                    <h2 class="fw-extrabold text-navy-blue mt-2 mb-0">{{ $suratPending }}</h2>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-3">
                    <i class="bi bi-envelope-exclamation-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 4: Konsultasi Terjadwal -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-premium card-stat p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Konsultasi Aktif</span>
                    <h2 class="fw-extrabold text-navy-blue mt-2 mb-0">{{ $konsultasiTerjadwal }}</h2>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-3">
                    <i class="bi bi-calendar2-check-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS SECTION -->
<div class="row g-4 mb-4">
    <!-- Prodi Chart -->
    <div class="col-12 col-lg-7">
        <div class="card card-premium border-0 p-4 h-100">
            <h6 class="fw-bold text-navy-blue mb-3"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Distribusi Mahasiswa Per Program Studi</h6>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="prodiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Surat Status Chart -->
    <div class="col-12 col-lg-5">
        <div class="card card-premium border-0 p-4 h-100">
            <h6 class="fw-bold text-navy-blue mb-3"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Status Pengajuan Surat</h6>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="suratChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Letters Section -->
    <div class="col-12">
        <div class="card card-premium border-0 p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-bold text-navy-blue m-0"><i class="bi bi-clock-history me-2 text-primary"></i>Pengajuan Surat Terbaru</h5>
                <a href="{{ route('admin.surat.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Lihat Semua</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="border-0">Nama Mahasiswa</th>
                            <th scope="col" class="border-0">Jenis Surat</th>
                            <th scope="col" class="border-0">Keperluan</th>
                            <th scope="col" class="border-0">Tanggal Diajukan</th>
                            <th scope="col" class="border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratTerbaru as $s)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $s->user->name }}</div>
                                    <span class="text-muted small">{{ $s->user->email }}</span>
                                </td>
                                <td>{{ $s->jenis_surat }}</td>
                                <td class="text-wrap" style="max-width: 250px;">{{ $s->keperluan }}</td>
                                <td>{{ $s->tanggal_pengajuan->format('d M Y H:i') }}</td>
                                <td>
                                    @if($s->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning badge-premium">Pending</span>
                                    @elseif($s->status === 'diproses')
                                        <span class="badge bg-info-subtle text-info badge-premium">Diproses</span>
                                    @elseif($s->status === 'selesai')
                                        <span class="badge bg-success-subtle text-success badge-premium">Selesai</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger badge-premium">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada pengajuan surat masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Konfigurasi global Chart.js untuk pembacaan di tema gelap
        Chart.defaults.color = '#cbd5e1';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

        // 1. Prodi Chart (Bar Chart)
        const prodiCtx = document.getElementById('prodiChart').getContext('2d');
        const prodiLabels = {!! json_encode($prodiDistribution->pluck('prodi')) !!};
        const prodiData = {!! json_encode($prodiDistribution->pluck('total')) !!};

        new Chart(prodiCtx, {
            type: 'bar',
            data: {
                labels: prodiLabels,
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: prodiData,
                    backgroundColor: 'rgba(234, 179, 8, 0.85)',
                    borderColor: 'rgb(234, 179, 8)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // 2. Surat Status Chart (Doughnut Chart)
        const suratCtx = document.getElementById('suratChart').getContext('2d');
        const rawSuratLabels = {!! json_encode($suratDistribution->pluck('status')) !!};
        const suratData = {!! json_encode($suratDistribution->pluck('total')) !!};
        
        // Peta warna dan teks readable
        const colorMap = {
            'pending': '#f59e0b',
            'diproses': '#06b6d4',
            'selesai': '#10b981',
            'ditolak': '#ef4444'
        };

        const readableLabels = rawSuratLabels.map(status => status.charAt(0).toUpperCase() + status.slice(1));
        const bgColors = rawSuratLabels.map(status => colorMap[status] || '#cbd5e1');

        new Chart(suratCtx, {
            type: 'doughnut',
            data: {
                labels: readableLabels,
                datasets: [{
                    data: suratData,
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endsection

