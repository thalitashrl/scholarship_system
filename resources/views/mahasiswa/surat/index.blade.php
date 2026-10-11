@extends('layouts.admin')

@section('title', 'Pengajuan Surat Keterangan')

@section('content')
<div class="row g-4">
    <!-- Left Column: Form Pengajuan Baru -->
    <div class="col-12 col-lg-4">
        <div class="card card-premium border-0 p-4">
            <h5 class="fw-bold text-white mb-3">
                <i class="bi bi-file-earmark-plus-fill text-primary me-2"></i>Pengajuan Surat Baru
            </h5>
            <hr class="border-secondary mb-4">

            <form action="{{ route('mahasiswa.surat.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Pilihan Jenis Surat -->
                <div class="mb-3">
                    <label for="jenis_surat" class="form-label text-muted fw-semibold">Pilih Jenis Surat</label>
                    <select class="form-select @error('jenis_surat') is-invalid @enderror" id="jenis_surat" name="jenis_surat" required>
                        <option value="" disabled selected>-- Pilih Jenis Surat --</option>
                        @foreach($jenisSurat as $js)
                            <option value="{{ $js->nama }}" {{ old('jenis_surat') == $js->nama ? 'selected' : '' }}>
                                {{ $js->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_surat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Keperluan -->
                <div class="mb-3">
                    <label for="keperluan" class="form-label text-muted fw-semibold">Keperluan / Alasan</label>
                    <textarea class="form-control @error('keperluan') is-invalid @enderror" id="keperluan" name="keperluan" rows="4" placeholder="Jelaskan secara rinci untuk apa surat ini diajukan..." required>{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Lampiran Dokumen -->
                <div class="mb-4">
                    <label for="file" class="form-label text-muted fw-semibold">Dokumen Pendukung <span class="small">(Opsional, Max 2MB)</span></label>
                    <input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="form-text text-muted small mt-1">Format didukung: PDF, JPG, JPEG, PNG.</div>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-send-fill me-2"></i> Kirim Pengajuan
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Riwayat Pengajuan -->
    <div class="col-12 col-lg-8">
        <div class="card card-premium border-0 p-4">
            <h5 class="fw-bold text-white mb-3">
                <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pengajuan Surat Anda
            </h5>
            <hr class="border-secondary mb-4">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="border-0">Detail Pengajuan</th>
                            <th scope="col" class="border-0">Tanggal Pengajuan</th>
                            <th scope="col" class="border-0">Status</th>
                            <th scope="col" class="border-0 text-center">Lampiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratList as $s)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white mb-1">{{ $s->jenis_surat }}</div>
                                    <p class="text-muted small mb-0 text-wrap" style="max-width: 320px;">
                                        <strong>Keperluan:</strong> {{ $s->keperluan }}
                                    </p>
                                </td>
                                <td>
                                    <div>{{ $s->tanggal_pengajuan->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $s->tanggal_pengajuan->format('H:i') }} WIB</small>
                                </td>
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
                                <td class="text-start">
                                    @php
                                        $mhsDocs = $s->dokumen->where('tipe', 'pengajuan');
                                        $adminDocs = $s->dokumen->where('tipe', 'balasan');
                                    @endphp

                                    @if($mhsDocs->isNotEmpty())
                                        <div class="small fw-semibold text-muted mb-1">Lampiran Anda:</div>
                                        @foreach($mhsDocs as $dok)
                                            <a href="{{ asset('storage/' . $dok->file_path) }}" target="_blank" class="btn btn-sm btn-light py-1 px-2 rounded-2 mb-2 d-inline-flex align-items-center text-truncate" style="max-width: 180px;" title="{{ $dok->nama_file }}">
                                                <i class="bi bi-file-earmark-text text-primary me-1"></i> Lihat Dokumen
                                            </a>
                                        @endforeach
                                    @endif

                                    @if($adminDocs->isNotEmpty())
                                        <div class="small fw-semibold text-success mt-2 mb-1">Hasil Surat:</div>
                                        @foreach($adminDocs as $dok)
                                            <a href="{{ asset('storage/' . $dok->file_path) }}" target="_blank" class="btn btn-sm btn-success py-1.5 px-3 rounded-pill mb-1 d-inline-flex align-items-center" title="{{ $dok->nama_file }}">
                                                <i class="bi bi-cloud-arrow-down-fill me-1.5"></i> Unduh Hasil Surat
                                            </a>
                                        @endforeach
                                    @else
                                        @if($s->status === 'selesai' && $adminDocs->isEmpty())
                                            <span class="text-muted small d-block">Menunggu berkas diunggah</span>
                                        @elseif($mhsDocs->isEmpty())
                                            <span class="text-muted small">-</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada riwayat pengajuan surat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $suratList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
