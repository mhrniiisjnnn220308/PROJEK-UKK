@extends('kasir.layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-clock-history me-2"></i>Riwayat Transaksi
        </h4>
        <small class="text-muted">Daftar transaksi yang telah dilakukan</small>
    </div>
</div>

<!-- Tabel Transaksi -->
<div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead style="background: #E8F5E9;">
                <tr>
                    <th>No</th>
                    <th>Nomor Unik</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedTransactions = $transactions->groupBy('nomor_unik');
                    $no = ($transactions->currentPage() - 1) * $transactions->perPage() + 1;
                @endphp
                
                @forelse($groupedTransactions as $nomorUnik => $items)
                    @php
                        $firstItem = $items->first();
                        $grandTotal = $items->sum('total_harga');
                    @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td><strong class="text-primary">{{ $nomorUnik }}</strong></td>
                        <td>
                            <small>{{ $firstItem->created_at->format('d/m/Y') }}</small><br>
                            <small class="text-muted">{{ $firstItem->created_at->format('H:i') }}</small>
                        </td>
                        <td>{{ $firstItem->nama_pelanggan }}</td>
                        <td>
                            @foreach($items as $item)
                                <div class="mb-1">
                                    <span class="badge bg-info">{{ $item->jumlah }}x</span>
                                    {{ $item->product->nama_produk }}
                                </div>
                            @endforeach
                        </td>
                        <td>
                            <strong class="text-success" style="font-size: 16px;">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            <a href="{{ route('kasir.transactions.print', $nomorUnik) }}" 
                               class="btn btn-sm btn-primary-custom" 
                               target="_blank"
                               title="Cetak Struk">
                                <i class="bi bi-printer me-1"></i>Cetak
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                            <p class="mt-3 text-muted">Belum ada transaksi</p>
                            <a href="{{ route('kasir.dashboard') }}" class="btn btn-primary-custom">
                                <i class="bi bi-plus-circle me-2"></i>Buat Transaksi Baru
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($groupedTransactions->count() > 0)
    <div class="mt-3 d-flex justify-content-center">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

<!-- Statistik Transaksi -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-receipt text-success" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $groupedTransactions->count() }}</h3>
                <p class="text-muted mb-0">Total Transaksi</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-cash-stack text-primary" style="font-size: 48px;"></i>
                <h3 class="mt-3">Rp {{ number_format($transactions->sum('total_harga'), 0, ',', '.') }}</h3>
                <p class="text-muted mb-0">Total Pendapatan</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-box-seam text-warning" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $transactions->sum('jumlah') }}</h3>
                <p class="text-muted mb-0">Total Item Terjual</p>
            </div>
        </div>
    </div>
</div>
@endsection