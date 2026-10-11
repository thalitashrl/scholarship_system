@extends('layouts.admin')

@section('title', 'Daftar Pengajuan Surat')

@section('content')
<div class="card card-premium border-0 p-4 mb-4">
    <div class="mb-4">
        <h5 class="fw-bold text-navy-blue m-0"><i class="bi bi-file-earmark-text-fill me-2 text-primary"></i>Pengajuan Surat Mahasiswa</h5>
        <p class="text-muted small mt-1">Kelola dan proses persetujuan berkas persuratan akademik mahasiswa aktif.</p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="border-0">Mahasiswa</th>
                    <th scope="col" class="border-0">Detail Pengajuan</th>
                    <th scope="col" class="border-0">Dokumen Lampiran</th>
                    <th scope="col" class="border-0">Waktu</th>
                    <th scope="col" class="border-0">Status</th>
                    <th scope="col" class="border-0 text-end">Tindakan Cepat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surat as $s)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $s->user->name }}</div>
                            <span class="text-muted small">{{ $s->user->email }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">{{ $s->jenis_surat }}</div>
                            <span class="text-muted small text-wrap d-block" style="max-width: 250px;">{{ $s->keperluan }}</span>
                        </td>
                        <td>
                            @php
                                $mhsDocs = $s->dokumen->where('tipe', 'pengajuan');
                                $adminDocs = $s->dokumen->where('tipe', 'balasan');
                            @endphp

                            <div class="fw-semibold text-muted small mb-1">Lampiran Mahasiswa:</div>
                            @forelse($mhsDocs as $doc)
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-light btn-sm text-primary border rounded-3 p-2 d-inline-flex align-items-center mb-1">
                                    <i class="bi {{ $doc->format === 'PDF' ? 'bi-file-pdf' : 'bi-file-image' }} me-2 text-danger fs-5"></i>
                                    <div class="text-start">
                                        <div class="small fw-semibold text-dark" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $doc->nama_file }}
                                        </div>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ round($doc->ukuran / 1024, 1) }} KB</span>
                                    </div>
                                </a>
                            @empty
                                <span class="text-muted small d-block mb-2"><i class="bi bi-file-x-fill me-1"></i> Tidak ada lampiran</span>
                            @endforelse

                            <div class="fw-semibold text-muted small mt-2 mb-1">Hasil Surat (Staf/Admin):</div>
                            @forelse($adminDocs as $doc)
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-success-subtle btn-sm text-success border-success-subtle rounded-3 p-2 d-inline-flex align-items-center mb-1">
                                    <i class="bi {{ $doc->format === 'PDF' ? 'bi-file-pdf' : 'bi-file-image' }} me-2 text-success fs-5"></i>
                                    <div class="text-start">
                                        <div class="small fw-semibold text-success" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $doc->nama_file }}
                                        </div>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ round($doc->ukuran / 1024, 1) }} KB</span>
                                    </div>
                                </a>
                            @empty
                                <span class="text-muted small d-block"><i class="bi bi-file-earmark-x me-1"></i> Belum diunggah</span>
                            @endforelse
                        </td>
                        <td>
                            <div class="small"><strong>Diajukan:</strong> {{ $s->tanggal_pengajuan->format('d M Y H:i') }}</div>
                            @if($s->tanggal_selesai)
                                <div class="small text-success"><strong>Selesai:</strong> {{ $s->tanggal_selesai->format('d M Y H:i') }}</div>
                            @endif
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
                        <td class="text-end">
                            <form action="{{ route('admin.surat.updateStatus', $s->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('PUT')
                                <div class="input-group input-group-sm mb-1">
                                    <select name="status" class="form-select rounded-start-pill border-secondary-subtle" onchange="this.form.submit()">
                                        <option value="pending" {{ $s->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="diproses" {{ $s->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="selesai" {{ $s->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="ditolak" {{ $s->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary rounded-end-pill px-3 border border-start-0 border-secondary-subtle">Ubah</button>
                                </div>
                            </form>

                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 mt-1 d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#uploadModal-{{ $s->id }}">
                                <i class="bi bi-file-earmark-arrow-up me-1"></i> Unggah Hasil
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-envelope-x fs-1 d-block mb-2 text-secondary"></i> Belum ada pengajuan surat masuk dari mahasiswa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $surat->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection

@section('modals')
@foreach($surat as $s)
    <!-- Modal Upload File untuk Pengajuan ID {{ $s->id }} -->
    <div class="modal" id="uploadModal-{{ $s->id }}" tabindex="-1" aria-labelledby="uploadModalLabel-{{ $s->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-start">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold text-navy-blue" id="uploadModalLabel-{{ $s->id }}">
                        <i class="bi bi-file-earmark-arrow-up-fill text-primary me-2"></i>Unggah Surat Hasil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.surat.upload', $s->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-3">
                        <p class="text-muted small">Silakan pilih file surat yang telah diproses untuk dikirim ke <strong>{{ $s->user->name }}</strong>.</p>
                        
                        <div class="mb-3">
                            <label for="file-{{ $s->id }}" class="form-label fw-semibold text-muted">Berkas Surat (PDF/JPG/PNG, Max 2MB)</label>
                            <input class="form-control" type="file" id="file-{{ $s->id }}" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="status_selesai-{{ $s->id }}" name="status_selesai" value="1" checked>
                            <label class="form-check-label fw-medium text-dark" for="status_selesai-{{ $s->id }}">Tandai pengajuan sebagai "Selesai"</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Unggah & Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
