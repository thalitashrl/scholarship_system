@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-premium border-0 p-4 shadow-lg">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-bold text-white m-0">
                    <i class="bi bi-people-fill text-primary me-2"></i>Kelola Pengguna
                </h5>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="border-0">Nama Pengguna</th>
                            <th scope="col" class="border-0">Email</th>
                            <th scope="col" class="border-0">Role / Hak Akses</th>
                            <th scope="col" class="border-0">Detail Akademik (Mahasiswa)</th>
                            <th scope="col" class="border-0 text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            @php
                                $role = $u->roles->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-white mb-0">{{ $u->name }}</div>
                                    <small class="text-muted">Terdaftar: {{ $u->created_at->format('d M Y') }}</small>
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if($role)
                                        @if($role->name === 'admin')
                                            <span class="badge bg-danger-subtle text-danger badge-premium text-uppercase">Admin</span>
                                        @elseif($role->name === 'staff')
                                            <span class="badge bg-primary-subtle text-primary badge-premium text-uppercase">Staff</span>
                                        @elseif($role->name === 'wd3')
                                            <span class="badge bg-success-subtle text-success badge-premium text-uppercase">WD3</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning badge-premium text-uppercase">Mahasiswa</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary badge-premium">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($u->mahasiswa)
                                        <div class="small text-white">
                                            <strong>NIM:</strong> {{ $u->mahasiswa->nim }} | 
                                            <strong>IPK:</strong> {{ number_format($u->mahasiswa->ipk, 2) }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $u->mahasiswa->prodi }} - {{ $u->mahasiswa->angkatan }}
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Edit Trigger -->
                                        <button type="button" class="btn btn-sm btn-light rounded-2 px-2.5" data-bs-toggle="modal" data-bs-target="#editModal{{ $u->id }}">
                                            <i class="bi bi-pencil-square text-warning me-1"></i> Edit
                                        </button>

                                        <!-- Delete Trigger (Admin cannot delete themselves) -->
                                        @if($u->id !== Auth::id())
                                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini beserta seluruh profilnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light rounded-2 px-2.5">
                                                    <i class="bi bi-trash-fill text-danger me-1"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada data pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
            <!-- Edit Modals (Di Luar Tabel untuk Valid HTML & Mencegah Masalah Backdrop Bootstrap) -->
            @foreach($users as $u)
                @php
                    $role = $u->roles->first();
                @endphp
                <div class="modal fade" id="editModal{{ $u->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $u->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <form action="{{ route('admin.users.update', $u->id) }}" method="POST" class="modal-content">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="user_id" value="{{ $u->id }}">
                            <div class="modal-header border-bottom border-dark">
                                <h5 class="modal-title text-white fw-bold" id="editModalLabel{{ $u->id }}">
                                    <i class="bi bi-pencil-square text-warning me-2"></i>Edit Pengguna
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                <!-- Nama -->
                                <div class="mb-3">
                                    <label for="name{{ $u->id }}" class="form-label text-muted fw-semibold">Nama Pengguna</label>
                                    <input type="text" class="form-control" id="name{{ $u->id }}" name="name" value="{{ (old('user_id') == $u->id) ? old('name', $u->name) : $u->name }}" required>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email{{ $u->id }}" class="form-label text-muted fw-semibold">Email</label>
                                    <input type="email" class="form-control" id="email{{ $u->id }}" name="email" value="{{ (old('user_id') == $u->id) ? old('email', $u->email) : $u->email }}" required>
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password{{ $u->id }}" class="form-label text-muted fw-semibold">Password <span class="small text-muted">(Kosongkan jika tidak ingin diubah)</span></label>
                                    <input type="password" class="form-control" id="password{{ $u->id }}" name="password" placeholder="Minimal 8 karakter">
                                </div>

                                <!-- Role Select -->
                                <div class="mb-3">
                                    <label for="edit-role-select-{{ $u->id }}" class="form-label text-muted fw-semibold">Pilih Role</label>
                                    <select class="form-select" id="edit-role-select-{{ $u->id }}" name="role_id" required>
                                        @php
                                            $selectedRoleId = (old('user_id') == $u->id) ? old('role_id', $role?->id) : $role?->id;
                                        @endphp
                                        @foreach($roles as $r)
                                            <option value="{{ $r->id }}" data-role-name="{{ $r->name }}" {{ $selectedRoleId == $r->id ? 'selected' : '' }}>
                                                {{ strtoupper($r->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Bagian Field Akademik Mahasiswa (Dinamis) -->
                                <div id="edit-mahasiswa-fields-{{ $u->id }}" class="{{ ($role && $role->name === 'mahasiswa') ? '' : 'd-none' }} border border-dark rounded-3 p-3 bg-dark mb-2">
                                    <h6 class="text-white fw-bold mb-3"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Data Profil Mahasiswa</h6>
                                    
                                    <div class="mb-3">
                                        <label for="nim{{ $u->id }}" class="form-label text-muted fw-semibold">NIM (Nomor Induk Mahasiswa)</label>
                                        <input type="text" class="form-control" id="nim{{ $u->id }}" name="nim" value="{{ (old('user_id') == $u->id) ? old('nim', $u->mahasiswa->nim ?? '') : ($u->mahasiswa->nim ?? '') }}">
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <label for="prodi{{ $u->id }}" class="form-label text-muted fw-semibold">Program Studi</label>
                                            <input type="text" class="form-control" id="prodi{{ $u->id }}" name="prodi" value="{{ (old('user_id') == $u->id) ? old('prodi', $u->mahasiswa->prodi ?? '') : ($u->mahasiswa?->prodi ?? '') }}">
                                        </div>
                                        <div class="col-6">
                                            <label for="fakultas{{ $u->id }}" class="form-label text-muted fw-semibold">Fakultas</label>
                                            <input type="text" class="form-control" id="fakultas{{ $u->id }}" name="fakultas" value="{{ (old('user_id') == $u->id) ? old('fakultas', $u->mahasiswa->fakultas ?? '') : ($u->mahasiswa?->fakultas ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <label for="angkatan{{ $u->id }}" class="form-label text-muted fw-semibold">Angkatan</label>
                                            <input type="number" class="form-control" id="angkatan{{ $u->id }}" name="angkatan" value="{{ (old('user_id') == $u->id) ? old('angkatan', $u->mahasiswa->angkatan ?? '') : ($u->mahasiswa?->angkatan ?? '') }}">
                                        </div>
                                        <div class="col-6">
                                            <label for="ipk{{ $u->id }}" class="form-label text-muted fw-semibold">IPK</label>
                                            <input type="number" step="0.01" class="form-control" id="ipk{{ $u->id }}" name="ipk" value="{{ (old('user_id') == $u->id) ? old('ipk', $u->mahasiswa->ipk ?? '') : ($u->mahasiswa?->ipk ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top border-dark">
                                <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-2 px-4">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const select = document.getElementById('edit-role-select-{{ $u->id }}');
                        const fields = document.getElementById('edit-mahasiswa-fields-{{ $u->id }}');

                        function toggleFields() {
                            const selectedOpt = select.options[select.selectedIndex];
                            if (selectedOpt) {
                                const roleName = selectedOpt.getAttribute('data-role-name');
                                if (roleName === 'mahasiswa') {
                                    fields.classList.remove('d-none');
                                } else {
                                    fields.classList.add('d-none');
                                }
                            }
                        }
                        select.addEventListener('change', toggleFields);
                        toggleFields();
                    });
                </script>
            @endforeach

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content">
                        @csrf
                        <div class="modal-header border-bottom border-dark">
                            <h5 class="modal-title text-white fw-bold" id="addModalLabel">
                                <i class="bi bi-person-plus text-primary me-2"></i>Tambah Pengguna Baru
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start">
                            <!-- Nama -->
                            <div class="mb-3">
                                <label for="name" class="form-label text-muted fw-semibold">Nama Pengguna</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Ahmad Subardjo" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted fw-semibold">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Contoh: ahmad@test.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label text-muted fw-semibold">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 8 karakter" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role Select -->
                            <div class="mb-3">
                                <label for="add-role-select" class="form-label text-muted fw-semibold">Pilih Role</label>
                                <select class="form-select @error('role_id') is-invalid @enderror" id="add-role-select" name="role_id" required>
                                    <option value="" disabled selected>-- Pilih Role --</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}" data-role-name="{{ $r->name }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                                            {{ strtoupper($r->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bagian Field Akademik Mahasiswa (Dinamis) -->
                            <div id="add-mahasiswa-fields" class="d-none border border-dark rounded-3 p-3 bg-dark mb-2">
                                <h6 class="text-white fw-bold mb-3"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Data Profil Mahasiswa</h6>
                                
                                <div class="mb-3">
                                    <label for="nim" class="form-label text-muted fw-semibold">NIM (Nomor Induk Mahasiswa)</label>
                                    <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim" name="nim" value="{{ old('nim') }}" placeholder="Contoh: 20260005">
                                    @error('nim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label for="prodi" class="form-label text-muted fw-semibold">Program Studi</label>
                                        <input type="text" class="form-control @error('prodi') is-invalid @enderror" id="prodi" name="prodi" value="{{ old('prodi') }}" placeholder="Informatika">
                                        @error('prodi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="fakultas" class="form-label text-muted fw-semibold">Fakultas</label>
                                        <input type="text" class="form-control @error('fakultas') is-invalid @enderror" id="fakultas" name="fakultas" value="{{ old('fakultas') }}" placeholder="Fakultas Ilmu Komputer">
                                        @error('fakultas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <label for="angkatan" class="form-label text-muted fw-semibold">Angkatan</label>
                                        <input type="number" class="form-control @error('angkatan') is-invalid @enderror" id="angkatan" name="angkatan" value="{{ old('angkatan', date('Y')) }}">
                                        @error('angkatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="ipk" class="form-label text-muted fw-semibold">IPK</label>
                                        <input type="number" step="0.01" class="form-control @error('ipk') is-invalid @enderror" id="ipk" name="ipk" value="{{ old('ipk') }}" placeholder="3.50">
                                        @error('ipk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-dark">
                            <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-2 px-4">Tambah Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const select = document.getElementById('add-role-select');
        const fields = document.getElementById('add-mahasiswa-fields');

        function toggleFields() {
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt) {
                const roleName = selectedOpt.getAttribute('data-role-name');
                if (roleName === 'mahasiswa') {
                    fields.classList.remove('d-none');
                } else {
                    fields.classList.add('d-none');
                }
            }
        }
        select.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection
