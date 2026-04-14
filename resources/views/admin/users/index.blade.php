@extends('admin.layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="content-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-people me-2"></i>Kelola User
        </h4>
        <small class="text-muted">Tambah, Update, dan Kelola User Sistem</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-2"></i>Tambah User
    </button>
</div>

<!-- Tabel User -->
<div class="card shadow-sm mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <i class="bi bi-person-circle me-2"></i>
                            {{ $user->username }}
                        </td>
                        <td>{{ $user->nama }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : ($user->role == 'kasir' ? 'success' : 'warning') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->status == 'aktif' ? 'success' : 'secondary' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $user->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td class="text-nowrap">
                            @if($user->role === 'owner')
                                <button class="btn btn-sm btn-secondary"
                                        onclick="blockedOwnerAction('mengedit')"
                                        title="Tidak dapat mengedit akun Owner">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @else
                                <button class="btn btn-sm btn-warning"
                                        onclick="editUser({{ json_encode($user) }})"
                                        title="Edit User">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @endif

                            {{-- Tombol Toggle Status --}}
                            @if($user->id != Auth::id())
                                @if($user->role === 'owner')
                                    <button class="btn btn-sm btn-secondary"
                                            onclick="blockedOwnerAction('menonaktifkan')"
                                            title="Tidak dapat menonaktifkan Owner">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-{{ $user->status == 'aktif' ? 'secondary' : 'success' }}"
                                            onclick="confirmToggleStatus({{ $user->id }}, '{{ $user->status }}', '{{ addslashes($user->nama) }}')"
                                            title="{{ $user->status == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }} user ini">
                                        <i class="bi bi-{{ $user->status == 'aktif' ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<form id="form-toggle" method="POST" style="display:none;">
    @csrf
    @method('GET')
</form>


<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#FF8C42; color:white;">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Tambah User Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" id="formTambah">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" id="tambah_username" required>
                        <div class="invalid-feedback">Username harus diisi</div>
                        <small class="text-muted">Username harus unik</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" id="tambah_password" required>
                        <div class="invalid-feedback">Password harus diisi</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" id="tambah_nama" required>
                        <div class="invalid-feedback">Nama lengkap harus diisi</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" name="role" id="tambah_role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                            <option value="owner">Owner</option>
                        </select>
                        <div class="invalid-feedback">Role harus dipilih</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#FF8C42; color:white;">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" id="edit_username" required>
                        <div class="invalid-feedback">Username harus diisi</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="edit_password">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" id="edit_nama" required>
                        <div class="invalid-feedback">Nama lengkap harus diisi</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" name="role" id="edit_role" required>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                            <option value="owner">Owner</option>
                        </select>
                        <div class="invalid-feedback">Role harus dipilih</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    
    function validateTambahForm() {
        let isValid = true;
        const fields = [
            { el: document.getElementById('tambah_username'), check: v => v.trim() !== '' },
            { el: document.getElementById('tambah_password'), check: v => v.trim() !== '' },
            { el: document.getElementById('tambah_nama'),     check: v => v.trim() !== '' },
            { el: document.getElementById('tambah_role'),     check: v => v !== '' },
        ];
        fields.forEach(f => { if (f.el) f.el.classList.remove('is-invalid'); });
        fields.forEach(f => {
            if (f.el && !f.check(f.el.value)) {
                f.el.classList.add('is-invalid');
                isValid = false;
            }
        });
        return isValid;
    }

    
    function validateEditForm() {
        let isValid = true;
        const fields = [
            { el: document.getElementById('edit_username'), check: v => v.trim() !== '' },
            { el: document.getElementById('edit_nama'),     check: v => v.trim() !== '' },
            { el: document.getElementById('edit_role'),     check: v => v !== '' },
        ];
        fields.forEach(f => { if (f.el) f.el.classList.remove('is-invalid'); });
        fields.forEach(f => {
            if (f.el && !f.check(f.el.value)) {
                f.el.classList.add('is-invalid');
                isValid = false;
            }
        });
        return isValid;
    }

    
    function editUser(user) {
        document.getElementById('formEdit').action = `/admin/users/${user.id}`;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_nama').value     = user.nama;
        document.getElementById('edit_role').value     = user.role;
        document.getElementById('edit_password').value = '';

        document.querySelectorAll('#formEdit .is-invalid').forEach(el => el.classList.remove('is-invalid'));

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    
    function blockedOwnerAction(aksi) {
        Swal.fire({
            title: 'Aksi Ditolak!',
            html: `Anda tidak dapat <strong>${aksi}</strong> akun dengan role <strong>Owner</strong>.`,
            icon: 'error',
            confirmButtonColor: '#FF8C42',
            confirmButtonText: 'Mengerti'
        });
    }

    
    function confirmToggleStatus(id, currentStatus, nama) {
        var actionText = currentStatus === 'aktif' ? 'menonaktifkan' : 'mengaktifkan';
        var btnColor   = currentStatus === 'aktif' ? '#6c757d' : '#198754';

        Swal.fire({
            title: 'Ubah Status User?',
            html: `Apakah Anda yakin ingin <strong>${actionText}</strong> user <strong>${nama}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Ya, ${actionText}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: `Sedang ${actionText} user`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                window.location.href = `/admin/users/${id}/toggle`;
            }
        });
    }

    
    document.addEventListener('DOMContentLoaded', function () {

        
        const formTambah = document.getElementById('formTambah');
        if (formTambah) {
            formTambah.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!validateTambahForm()) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        html: 'Mohon lengkapi <strong>semua field</strong> yang wajib diisi.',
                        icon: 'warning',
                        confirmButtonColor: '#FF8C42',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                var nama = document.getElementById('tambah_nama').value.trim();
                Swal.fire({
                    title: 'Simpan User?',
                    html: `Pastikan data untuk <strong>${nama}</strong> sudah benar.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF8C42',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        formTambah.submit();
                    }
                });
            });
        }

        
        const formEdit = document.getElementById('formEdit');
        if (formEdit) {
            formEdit.addEventListener('submit', function (e) {
                e.preventDefault();

               
                var roleValue = document.getElementById('edit_role').value;
                var namaValue = document.getElementById('edit_nama').value.trim();

                if (!validateEditForm()) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        html: 'Mohon lengkapi <strong>semua field</strong> yang wajib diisi.',
                        icon: 'warning',
                        confirmButtonColor: '#FF8C42',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Update User?',
                    html: `Simpan perubahan data user <strong>${namaValue}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF8C42',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Update!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mengupdate...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        formEdit.submit();
                    }
                });
            });
        }

        
        @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#FF8C42',
            timer: 3000,
            showConfirmButton: true
        });
        @endif

        
        @if(session('error'))
        Swal.fire({
            title: 'Gagal!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#FF8C42'
        });
        @endif

    });
</script>
@endpush