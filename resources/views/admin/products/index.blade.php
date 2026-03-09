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

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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
                        @if($product->foto)
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
                        <a href="{{ route('admin.products.toggle', $product->id) }}" 
                           class="btn btn-{{ $product->status == 'aktif' ? 'secondary' : 'success' }} btn-action"
                           onclick="return confirm('Yakin ingin mengubah status produk ini?')"
                           title="{{ $product->status == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="bi bi-{{ $product->status == 'aktif' ? 'x-circle' : 'check-circle' }}"></i>
                        </a>
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
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="kategori_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Produk <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga_produk" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stok" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Foto Produk</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
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
            <form method="POST" id="formEdit" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" id="edit_nama" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="kategori_id" id="edit_kategori" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Produk <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga_produk" id="edit_harga" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stok" id="edit_stok" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Foto Produk</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                        <div id="current_foto" class="mt-2"></div>
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
@endsection

@push('scripts')
<script>
    function editProduct(product) {
        document.getElementById('formEdit').action = `/admin/products/${product.id}`;
        document.getElementById('edit_nama').value = product.nama_produk;
        document.getElementById('edit_kategori').value = product.kategori_id;
        document.getElementById('edit_harga').value = product.harga_produk;
        document.getElementById('edit_stok').value = product.stok;
        document.getElementById('edit_deskripsi').value = product.deskripsi || '';
        
        // Tampilkan foto saat ini
        const fotoDiv = document.getElementById('current_foto');
        if (product.foto) {
            fotoDiv.innerHTML = `
                <div class="alert alert-info d-flex align-items-center">
                    <img src="/uploads/products/${product.foto}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                    <div>
                        <strong>Foto saat ini</strong><br>
                        <small>${product.foto}</small>
                    </div>
                </div>
            `;
        } else {
            fotoDiv.innerHTML = '<small class="text-muted">Belum ada foto</small>';
        }
        
        var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
    }
</script>
@endpush