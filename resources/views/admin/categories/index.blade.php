@extends('admin.layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-grid me-2"></i>Daftar Kategori
        </h4>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
    </button>
</div>

<!-- Tabel Kategori -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Produk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $category->nama_kategori }}</strong></td>
                    <td>{{ $category->deskripsi }}</td>
                    <td>
                        <span class="badge badge-custom bg-info">
                            {{ $category->products->count() }} Produk
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-custom {{ $category->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($category->status) }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <button class="btn btn-warning btn-action" onclick="editCategory({{ json_encode($category) }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <!-- Tombol Hapus (Permanen) dengan Konfirmasi SweetAlert -->
                        <button class="btn btn-danger btn-action btn-delete" onclick="confirmDelete({{ $category->id }}, '{{ addslashes($category->nama_kategori) }}')" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                        <!-- Tombol Status (Aktif/Nonaktif) dengan Konfirmasi -->
                        <button class="btn btn-{{ $category->status == 'aktif' ? 'secondary' : 'success' }} btn-action"
                                onclick="confirmStatusChange({{ $category->id }}, '{{ $category->status }}', '{{ addslashes($category->nama_kategori) }}')"
                                title="{{ $category->status == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="bi bi-{{ $category->status == 'aktif' ? 'x-circle' : 'check-circle' }}"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-2 text-muted">Belum ada kategori</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #FF8C42; color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahKategori" method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kategori" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #FF8C42; color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Kategori
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kategori" id="edit_nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Delete Tersembunyi untuk Submit via JS -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Form Status Change Tersembunyi -->
<form id="statusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

@endsection

@push('scripts')
<script>
    // Fungsi Edit Kategori
    function editCategory(category) {
        document.getElementById('formEdit').action = `/admin/categories/${category.id}`;
        document.getElementById('edit_nama').value = category.nama_kategori;
        document.getElementById('edit_deskripsi').value = category.deskripsi || '';
        
        var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
    }

    // Konfirmasi Hapus dengan SweetAlert2
    function confirmDelete(categoryId, categoryName) {
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Apakah Anda yakin ingin menghapus kategori <strong>${categoryName}</strong>?<br><span style="color: red;">Perhatian: Produk dengan kategori ini akan kehilangan kategori!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form delete
                const form = document.getElementById('deleteForm');
                form.action = `/admin/categories/${categoryId}`;
                form.submit();
                
                // Tampilkan loading alert
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Kategori sedang dihapus',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }
    
    // Konfirmasi Ubah Status (Aktif/Nonaktif)
    function confirmStatusChange(categoryId, currentStatus, categoryName) {
        let actionText = currentStatus === 'aktif' ? 'menonaktifkan' : 'mengaktifkan';
        
        Swal.fire({
            title: `Konfirmasi ${actionText}`,
            html: `Apakah Anda yakin ingin ${actionText} kategori <strong>${categoryName}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FF8C42',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Ya, ${actionText}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form untuk toggle status
                const form = document.getElementById('statusForm');
                form.action = `/admin/categories/toggle/${categoryId}`;
                form.submit();
                
                Swal.fire({
                    title: 'Memproses...',
                    text: `Sedang ${actionText} kategori`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }
    
    // Intercept form submit untuk Tambah dan Edit
    document.addEventListener('DOMContentLoaded', function() {
        // Tangkap form tambah kategori
        const tambahForm = document.getElementById('formTambahKategori');
        if (tambahForm) {
            tambahForm.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Simpan Kategori?',
                    text: "Pastikan data kategori sudah benar sebelum disimpan.",
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
                            text: 'Sedang menambahkan kategori',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        tambahForm.submit();
                    }
                });
            });
        }
        
        // Tangkap form edit kategori
        const editForm = document.getElementById('formEdit');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Update Kategori?',
                    text: "Apakah Anda yakin ingin mengubah data kategori ini?",
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
                            text: 'Sedang memperbarui kategori',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        editForm.submit();
                    }
                });
            });
        }
    });
    
    // Menampilkan notifikasi sukses/error jika ada session flash
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
</script>
@endpush