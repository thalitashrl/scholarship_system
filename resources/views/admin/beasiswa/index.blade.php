@extends('layouts.admin')

@section('title', 'Kelola Beasiswa')

@section('content')
<div class="card card-premium border-0 p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h5 class="fw-bold text-navy-blue m-0"><i class="bi bi-award-fill me-2 text-primary"></i>Kelola Beasiswa</h5>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Beasiswa
        </button>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 bg-danger-subtle text-danger-emphasis p-3 rounded-3 small mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="border-0">Judul Beasiswa</th>
                    <th scope="col" class="border-0">Jenis</th>
                    <th scope="col" class="border-0">Kuota</th>
                    <th scope="col" class="border-0">Deadline</th>
                    <th scope="col" class="border-0">Status</th>
                    <th scope="col" class="border-0 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($beasiswa as $b)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $b->judul }}</div>
                            <span class="text-muted small text-wrap d-block" style="max-width: 350px;">{{ Str::limit($b->deskripsi, 80) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $b->jenis === 'internal' ? 'bg-indigo text-white' : 'bg-secondary-subtle text-secondary-emphasis' }} badge-premium">
                                {{ ucfirst($b->jenis) }}
                            </span>
                        </td>
                        <td>{{ $b->kuota }} Kursi</td>
                        <td>{{ $b->deadline->format('d M Y') }}</td>
                        <td>
                            @if($b->status === 'aktif')
                                <span class="badge bg-success-subtle text-success badge-premium">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger badge-premium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <!-- Edit Button -->
                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $b->id }}"
                                        data-judul="{{ $b->judul }}"
                                        data-jenis="{{ $b->jenis }}"
                                        data-deskripsi="{{ $b->deskripsi }}"
                                        data-kuota="{{ $b->kuota }}"
                                        data-deadline="{{ $b->deadline->format('Y-m-d') }}"
                                        data-status="{{ $b->status }}">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <!-- Delete Button -->
                                <form action="{{ route('admin.beasiswa.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus beasiswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i> Belum ada program beasiswa yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $beasiswa->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@section('modals')
<!-- ==============================================
     CREATE MODAL
     ============================================== -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.beasiswa.store') }}" method="POST" class="modal-content card-premium border-0 p-3 shadow-lg">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-navy-blue" id="createModalLabel"><i class="bi bi-award-fill text-primary me-2"></i>Tambah Beasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-3">
                    <label for="judul" class="form-label small fw-semibold">Judul Beasiswa</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Beasiswa Prestasi Kemahasiswaan" required>
                </div>
                <div class="mb-3">
                    <label for="jenis" class="form-label small fw-semibold">Jenis Beasiswa</label>
                    <select name="jenis" class="form-select" required>
                        <option value="internal">Internal (Fakultas / Universitas)</option>
                        <option value="eksternal">Eksternal (Lembaga / Bank luar)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label small fw-semibold">Deskripsi Singkat</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan deskripsi dan syarat beasiswa..." required></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label for="kuota" class="form-label small fw-semibold">Kuota (Kursi)</label>
                        <input type="number" name="kuota" class="form-control" min="1" placeholder="Contoh: 10" required>
                    </div>
                    <div class="col-6">
                        <label for="status" class="form-label small fw-semibold">Status Awal</label>
                        <select name="status" class="form-select" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label for="deadline" class="form-label small fw-semibold">Batas Waktu Pendaftaran (Deadline)</label>
                    <input type="date" name="deadline" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Program</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     EDIT MODAL
     ============================================== -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editForm" method="POST" class="modal-content card-premium border-0 p-3 shadow-lg">
            @csrf
            @method('PUT')
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-navy-blue" id="editModalLabel"><i class="bi bi-pencil-square text-primary me-2"></i>Perbarui Beasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-3">
                    <label for="editJudul" class="form-label small fw-semibold">Judul Beasiswa</label>
                    <input type="text" name="judul" id="editJudul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="editJenis" class="form-label small fw-semibold">Jenis Beasiswa</label>
                    <select name="jenis" id="editJenis" class="form-select" required>
                        <option value="internal">Internal (Fakultas / Universitas)</option>
                        <option value="eksternal">Eksternal (Lembaga / Bank luar)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editDeskripsi" class="form-label small fw-semibold">Deskripsi Singkat</label>
                    <textarea name="deskripsi" id="editDeskripsi" class="form-control" rows="3" required></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label for="editKuota" class="form-label small fw-semibold">Kuota (Kursi)</label>
                        <input type="number" name="kuota" id="editKuota" class="form-control" min="1" required>
                    </div>
                    <div class="col-6">
                        <label for="editStatus" class="form-label small fw-semibold">Status Program</label>
                        <select name="status" id="editStatus" class="form-select" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label for="editDeadline" class="form-label small fw-semibold">Batas Waktu Pendaftaran (Deadline)</label>
                    <input type="date" name="deadline" id="editDeadline" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Perbarui Program</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Logika pengisian otomatis Modal Edit
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const judul = button.getAttribute('data-judul');
            const jenis = button.getAttribute('data-jenis');
            const deskripsi = button.getAttribute('data-deskripsi');
            const kuota = button.getAttribute('data-kuota');
            const deadline = button.getAttribute('data-deadline');
            const status = button.getAttribute('data-status');

            const form = editModal.querySelector('#editForm');
            form.action = `/admin/beasiswa/${id}`;

            editModal.querySelector('#editJudul').value = judul;
            editModal.querySelector('#editJenis').value = jenis;
            editModal.querySelector('#editDeskripsi').value = deskripsi;
            editModal.querySelector('#editKuota').value = kuota;
            editModal.querySelector('#editDeadline').value = deadline;
            editModal.querySelector('#editStatus').value = status;
        });
    }
</script>
@endsection
