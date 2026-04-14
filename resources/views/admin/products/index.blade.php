@extends('admin.layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-box-seam me-2"></i>Kelola Produk
        </h4>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-2"></i>Tambah Produk
    </button>
</div>

<!-- Tabel Produk -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($product->foto && file_exists(public_path('storage/uploads/products/' . $product->foto)))
                            <img src="{{ asset('storage/uploads/products/' . $product->foto) }}" class="product-img" alt="{{ $product->nama_produk }}">
                        @elseif($product->foto && file_exists(public_path('uploads/products/' . $product->foto)))
                            <img src="{{ asset('uploads/products/' . $product->foto) }}" class="product-img" alt="{{ $product->nama_produk }}">
                        @else
                            <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="font-size: 24px; color: #ccc;"></i>
                            </div>
                        @endif
                    </td>
                    <td><strong>{{ $product->nama_produk }}</strong></td>
                    <td>
                        <span class="badge badge-custom" style="background: #E3F2FD; color: #1976D2;">
                            {{ $product->category ? $product->category->nama_kategori : 'Tidak ada' }}
                        </span>
                    </td>
                    <td>
                        <small>{{ Str::limit($product->deskripsi, 50) }}</small>
                    </td>
                    <td><strong>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</strong></td>
                    <td>
                        <span class="badge badge-custom {{ $product->stok < 10 ? 'bg-danger' : 'bg-success' }}">
                            {{ $product->stok }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <button class="btn btn-warning btn-action" onclick="editProduct({{ json_encode($product) }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <!-- Tombol Hapus (Permanen) dengan Konfirmasi SweetAlert -->
                        <button class="btn btn-danger btn-action btn-delete" onclick="confirmDelete({{ $product->id }}, '{{ addslashes($product->nama_produk) }}')" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                        <!-- Tombol Status (Aktif/Nonaktif) dengan Konfirmasi -->
                        <button class="btn btn-{{ $product->status == 'aktif' ? 'secondary' : 'success' }} btn-action"
                                onclick="confirmStatusChange({{ $product->id }}, '{{ $product->status }}', '{{ addslashes($product->nama_produk) }}')"
                                title="{{ $product->status == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="bi bi-{{ $product->status == 'aktif' ? 'x-circle' : 'check-circle' }}"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-2 text-muted">Belum ada produk</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #FF8C42; color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahProduk" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" id="tambah_nama" required>
                            <div class="invalid-feedback">Nama produk harus diisi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="kategori_id" id="tambah_kategori" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Kategori harus dipilih</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Produk <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga_produk" id="tambah_harga" required min="0" step="1000">
                            <div class="invalid-feedback">Harga produk harus diisi dengan angka yang valid (minimal 0)</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stok" id="tambah_stok" required min="0">
                            <div class="invalid-feedback">Stok harus diisi dengan angka yang valid (minimal 0)</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Foto Produk <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="foto" id="tambah_foto" accept="image/*" required>
                        <div class="invalid-feedback">Foto produk harus diisi</div>
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB (Wajib diisi)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" id="tambah_deskripsi" rows="3" required></textarea>
                        <div class="invalid-feedback">Deskripsi produk harus diisi</div>
                        <small class="text-muted">Deskripsi produk (Wajib diisi)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom" id="btnSimpan">
                        <i class="bi bi-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #FF8C42; color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Produk
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" id="edit_nama" required>
                            <div class="invalid-feedback">Nama produk harus diisi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="kategori_id" id="edit_kategori" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Kategori harus dipilih</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Produk <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga_produk" id="edit_harga" required min="0" step="1000">
                            <div class="invalid-feedback">Harga produk harus diisi dengan angka yang valid (minimal 0)</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stok" id="edit_stok" required min="0">
                            <div class="invalid-feedback">Stok harus diisi dengan angka yang valid (minimal 0)</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Foto Produk <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="foto" id="edit_foto" accept="image/*">
                        <div class="invalid-feedback">Foto produk harus diisi</div>
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB (Kosongkan jika tidak ingin mengubah foto)</small>
                        <div id="current_foto" class="mt-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3" required></textarea>
                        <div class="invalid-feedback">Deskripsi produk harus diisi</div>
                        <small class="text-muted">Deskripsi produk (Wajib diisi)</small>
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


<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>


<form id="statusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

@endsection

@push('scripts')
<script>
    
    function validateTambahForm() {
        let isValid = true;
        const nama = document.getElementById('tambah_nama');
        const kategori = document.getElementById('tambah_kategori');
        const harga = document.getElementById('tambah_harga');
        const stok = document.getElementById('tambah_stok');
        const foto = document.getElementById('tambah_foto');
        const deskripsi = document.getElementById('tambah_deskripsi');
        
        
        [nama, kategori, harga, stok, foto, deskripsi].forEach(field => {
            if (field) field.classList.remove('is-invalid');
        });
        
       
        if (!nama.value.trim()) {
            nama.classList.add('is-invalid');
            isValid = false;
        }
        
        
        if (!kategori.value) {
            kategori.classList.add('is-invalid');
            isValid = false;
        }
        
        
        if (!harga.value || parseFloat(harga.value) < 0) {
            harga.classList.add('is-invalid');
            isValid = false;
        }
        
        
        if (!stok.value || parseInt(stok.value) < 0) {
            stok.classList.add('is-invalid');
            isValid = false;
        }
        
       
        if (!foto.files || foto.files.length === 0) {
            foto.classList.add('is-invalid');
            isValid = false;
        }
        
        
        if (!deskripsi.value.trim()) {
            deskripsi.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    
    function validateEditForm() {
        let isValid = true;
        const nama = document.getElementById('edit_nama');
        const kategori = document.getElementById('edit_kategori');
        const harga = document.getElementById('edit_harga');
        const stok = document.getElementById('edit_stok');
        const foto = document.getElementById('edit_foto');
        const deskripsi = document.getElementById('edit_deskripsi');
        
        
        [nama, kategori, harga, stok, deskripsi].forEach(field => {
            if (field) field.classList.remove('is-invalid');
        });
        if (foto) foto.classList.remove('is-invalid');
        
        
        if (!nama.value.trim()) {
            nama.classList.add('is-invalid');
            isValid = false;
        }
        
        
        if (!kategori.value) {
            kategori.classList.add('is-invalid');
            isValid = false;
        }
        
       
        if (!harga.value || parseFloat(harga.value) < 0) {
            harga.classList.add('is-invalid');
            isValid = false;
        }
        
        
        if (!stok.value || parseInt(stok.value) < 0) {
            stok.classList.add('is-invalid');
            isValid = false;
        }
        
        
        if (!deskripsi.value.trim()) {
            deskripsi.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    
    function getFotoUrl(foto) {
        if (!foto) return null;
       
        return "{{ asset('storage/uploads/products') }}/" + foto;
    }
    
    
    function editProduct(product) {
        document.getElementById('formEdit').action = `/admin/products/${product.id}`;
        document.getElementById('edit_nama').value = product.nama_produk;
        document.getElementById('edit_kategori').value = product.kategori_id;
        document.getElementById('edit_harga').value = product.harga_produk;
        document.getElementById('edit_stok').value = product.stok;
        document.getElementById('edit_deskripsi').value = product.deskripsi || '';
        
        const fotoDiv = document.getElementById('current_foto');
        if (product.foto) {
            
            let fotoUrl = "{{ asset('storage/uploads/products') }}/" + product.foto;
            
            fotoDiv.innerHTML = `
                <div class="alert alert-info d-flex align-items-center">
                    <img src="${fotoUrl}" 
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px;"
                         onerror="this.onerror=null; this.src='{{ asset('uploads/products') }}/${product.foto}';">
                    <div>
                        <strong>Foto saat ini</strong><br>
                        <small>${product.foto}</small>
                        <br><small class="text-warning">* Jika ingin mengganti, upload foto baru</small>
                    </div>
                </div>
            `;
            
            document.getElementById('edit_foto').removeAttribute('required');
        } else {
            fotoDiv.innerHTML = '<small class="text-danger">Belum ada foto! Wajib upload foto.</small>';
            document.getElementById('edit_foto').setAttribute('required', 'required');
        }
        
        var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
    }

    
    function confirmDelete(productId, productName) {
        Swal.fire({
            title: 'Hapus Produk?',
            html: `Apakah Anda yakin ingin menghapus produk <strong>${productName}</strong>?<br><span style="color: red;">Tindakan ini tidak dapat dibatalkan!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                
                const form = document.getElementById('deleteForm');
                form.action = `/admin/products/${productId}`;
                form.submit();
                
                
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Produk sedang dihapus',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }
    
    
    function confirmStatusChange(productId, currentStatus, productName) {
        let actionText = currentStatus === 'aktif' ? 'menonaktifkan' : 'mengaktifkan';
        
        Swal.fire({
            title: `Konfirmasi ${actionText}`,
            html: `Apakah Anda yakin ingin ${actionText} produk <strong>${productName}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FF8C42',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Ya, ${actionText}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                
                const form = document.getElementById('statusForm');
                form.action = `/admin/products/toggle/${productId}`;
                form.submit();
                
                Swal.fire({
                    title: 'Memproses...',
                    text: `Sedang ${actionText} produk`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }
    
    
    document.addEventListener('DOMContentLoaded', function() {
       
        const tambahForm = document.getElementById('formTambahProduk');
        if (tambahForm) {
            tambahForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                
                if (!validateTambahForm()) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        html: 'Mohon lengkapi <strong>SEMUA FIELD</strong> yang wajib diisi:<br>' +
                              '• Nama Produk<br>' +
                              '• Kategori<br>' +
                              '• Harga Produk<br>' +
                              '• Stok<br>' +
                              '• Foto Produk<br>' +
                              '• Deskripsi',
                        icon: 'warning',
                        confirmButtonColor: '#FF8C42',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Simpan Produk?',
                    text: "Pastikan semua data produk sudah lengkap dan benar sebelum disimpan.",
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
                            text: 'Sedang menambahkan produk',
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
        
        
        const editForm = document.getElementById('formEdit');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
               
                if (!validateEditForm()) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        html: 'Mohon lengkapi <strong>SEMUA FIELD</strong> yang wajib diisi:<br>' +
                              '• Nama Produk<br>' +
                              '• Kategori<br>' +
                              '• Harga Produk<br>' +
                              '• Stok<br>' +
                              '• Deskripsi',
                        icon: 'warning',
                        confirmButtonColor: '#FF8C42',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Update Produk?',
                    text: "Apakah Anda yakin ingin mengubah data produk ini?",
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
                            text: 'Sedang memperbarui produk',
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