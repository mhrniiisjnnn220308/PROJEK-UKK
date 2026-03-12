@extends('owner.layouts.app')

@section('title', 'Data Produk')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-box-seam me-2" style="color: #6f42c1;"></i>Data Produk
        </h4>
        <small class="text-muted">Daftar semua produk dalam sistem</small>
    </div>
    <div>
        <a href="{{ route('owner.reports.products.pdf') }}" target="_blank" class="btn btn-danger">
            <i class="bi bi-file-pdf me-2"></i>Download PDF
        </a>
    </div>
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
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Terakhir Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                <tr>
                    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}</td>
                    <td>
                        @if($product->foto)
                            <img src="{{ asset('uploads/products/' . $product->foto) }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" 
                                 alt="{{ $product->nama_produk }}">
                        @else
                            <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="color: #ccc;"></i>
                            </div>
                        @endif
                    </td>
                    <td><strong>{{ $product->nama_produk }}</strong></td>
                    <td>
                        <span class="badge badge-custom bg-info">
                            {{ $product->category->nama_kategori ?? '-' }}
                        </span>
                    </td>
                    <td><strong>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</strong></td>
                    <td>
                        <span class="badge badge-custom {{ $product->stok < 10 ? 'bg-danger' : 'bg-success' }}">
                            {{ $product->stok }} unit
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-custom {{ $product->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td>
                        <small>{{ $product->updated_at->format('d/m/Y H:i') }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-2 text-muted">Tidak ada data produk</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-3">
        {{ $products->links() }}
    </div>
</div>

<!-- Statistik Produk -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-box-seam text-primary" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $products->total() }}</h3>
                <p class="text-muted mb-0">Total Produk</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ \App\Models\Product::where('status', 'aktif')->count() }}</h3>
                <p class="text-muted mb-0">Produk Aktif</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ \App\Models\Product::where('stok', '<', 10)->where('status', 'aktif')->count() }}</h3>
                <p class="text-muted mb-0">Stok Menipis</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-x-circle text-danger" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ \App\Models\Product::where('status', 'nonaktif')->count() }}</h3>
                <p class="text-muted mb-0">Produk Nonaktif</p>
            </div>
        </div>
    </div>
</div>
@endsection