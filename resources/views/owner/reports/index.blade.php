@extends('owner.layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-graph-up me-2" style="color: #6f42c1;"></i>Laporan Transaksi
        </h4>
        <small class="text-muted">Filter dan lihat laporan transaksi</small>
    </div>
    <div>
        <button type="button" class="btn btn-danger" onclick="printPdf()">
            <i class="bi bi-file-pdf me-2"></i>Download PDF
        </button>
    </div>
</div>

<!-- Filter Form -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.reports.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai"
                           value="{{ request('tanggal_mulai') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai"
                           value="{{ request('tanggal_selesai') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Kasir</label>
                    <select class="form-select" name="kasir" id="kasir">
                        <option value="">Semua Kasir</option>
                        @foreach($kasirList as $kasir)
                            <option value="{{ $kasir->id }}" {{ request('kasir') == $kasir->id ? 'selected' : '' }}>
                                {{ $kasir->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Produk</label>
                    <select class="form-select" name="produk" id="produk">
                        <option value="">Semua Produk</option>
                        @foreach($produkList as $produk)
                            <option value="{{ $produk->id }}" {{ request('produk') == $produk->id ? 'selected' : '' }}>
                                {{ $produk->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-funnel me-2"></i>Filter
                </button>
                <a href="{{ route('owner.reports.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-receipt text-primary" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $totalTransaksi }}</h3>
                <p class="text-muted mb-0">Total Transaksi</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-cash-stack text-success" style="font-size: 48px;"></i>
                <h3 class="mt-3">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                <p class="text-muted mb-0">Total Pendapatan</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-box-seam text-warning" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $totalItem }}</h3>
                <p class="text-muted mb-0">Total Item Terjual</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Transaksi -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Transaksi</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = ($transactions->currentPage() - 1) * $transactions->perPage() + 1;
                @endphp
                
                @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>
                        <small class="text-primary"><strong>{{ $transaction->nomor_unik }}</strong></small>
                    </td>
                    <td>
                        <small>{{ $transaction->created_at->format('d/m/Y') }}</small><br>
                        <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                    </td>
                    <td>{{ $transaction->user->nama }}</td>
                    <td>{{ $transaction->nama_pelanggan }}</td>
                    <td>{{ $transaction->product->nama_produk }}</td>
                    <td>
                        <span class="badge bg-info">{{ $transaction->jumlah }}x</span>
                    </td>
                    <td><strong class="text-success">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-2 text-muted">Tidak ada data transaksi</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-3">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function printPdf() {
    // Ambil parameter filter dari form
    const tanggalMulai = document.getElementById('tanggal_mulai').value;
    const tanggalSelesai = document.getElementById('tanggal_selesai').value;
    const kasir = document.getElementById('kasir').value;
    const produk = document.getElementById('produk').value;
    
    // Buat URL dengan parameter
    let url = '{{ route("owner.reports.pdf") }}';
    const params = new URLSearchParams();
    
    if (tanggalMulai) params.append('tanggal_mulai', tanggalMulai);
    if (tanggalSelesai) params.append('tanggal_selesai', tanggalSelesai);
    if (kasir) params.append('kasir', kasir);
    if (produk) params.append('produk', produk);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    // Buka di tab baru
    window.open(url, '_blank');
}
</script>
@endpush