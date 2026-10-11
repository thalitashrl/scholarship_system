@extends('layouts.admin')

@section('title', 'Master Jenis Beasiswa')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-premium border-0 p-4 shadow-lg">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-bold text-white m-0">
                    <i class="bi bi-tags text-primary me-2"></i>Master Jenis Beasiswa
                </h5>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Jenis Beasiswa
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="border-0" style="width: 80px;">ID</th>
                            <th scope="col" class="border-0" style="width: 250px;">Nama Jenis Beasiswa</th>
                            <th scope="col" class="border-0">Deskripsi</th>
                            <th scope="col" class="border-0 text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisBeasiswa as $jb)
                            <tr>
                                <td class="fw-bold text-muted">#{{ $jb->id }}</td>
                                <td class="fw-bold text-white">{{ $jb->nama }}</td>
                                <td class="text-wrap">{{ $jb->deskripsi ?? 'Tidak ada deskripsi.' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Edit Trigger -->
                                        <button type="button" class="btn btn-sm btn-light rounded-2 px-2.5" data-bs-toggle="modal" data-bs-target="#editModal{{ $jb->id }}">
                                            <i class="bi bi-pencil-square text-warning me-1"></i> Edit
                                        </button>

                                        <!-- Delete Trigger -->
                                        <form action="{{ route('admin.jenis-beasiswa.destroy', $jb->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis beasiswa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light rounded-2 px-2.5">
                                                <i class="bi bi-trash-fill text-danger me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada data jenis beasiswa master.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $jenisBeasiswa->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
            <!-- Edit Modals (Di Luar Tabel agar z-index backdrop tidak menutup modal) -->
            @foreach($jenisBeasiswa as $jb)
                <div class="modal fade" id="editModal{{ $jb->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $jb->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('admin.jenis-beasiswa.update', $jb->id) }}" method="POST" class="modal-content">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-bottom border-dark">
                                <h5 class="modal-title text-white fw-bold" id="editModalLabel{{ $jb->id }}">
                                    <i class="bi bi-pencil-square text-warning me-2"></i>Edit Jenis Beasiswa
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama{{ $jb->id }}" class="form-label text-muted fw-semibold">Nama Jenis Beasiswa</label>
                                    <input type="text" class="form-control" id="nama{{ $jb->id }}" name="nama" value="{{ old('nama', $jb->nama) }}" required>
                                </div>
                                <div class="mb-0">
                                    <label for="deskripsi{{ $jb->id }}" class="form-label text-muted fw-semibold">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi{{ $jb->id }}" name="deskripsi" rows="4">{{ old('deskripsi', $jb->deskripsi) }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top border-dark">
                                <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-2 px-4">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('admin.jenis-beasiswa.store') }}" method="POST" class="modal-content">
                        @csrf
                        <div class="modal-header border-bottom border-dark">
                            <h5 class="modal-title text-white fw-bold" id="addModalLabel">
                                <i class="bi bi-tags text-primary me-2"></i>Tambah Jenis Beasiswa Baru
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama" class="form-label text-muted fw-semibold">Nama Jenis Beasiswa</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Beasiswa PPA" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-0">
                                <label for="deskripsi" class="form-label text-muted fw-semibold">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" placeholder="Tulis deskripsi atau kriteria singkat jenis beasiswa tersebut...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-top border-dark">
                            <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-2 px-4">Tambah Data</button>
                        </div>
                    </form>
                </div>
            </div>
@endsection
