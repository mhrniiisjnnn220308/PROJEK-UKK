@extends('kasir.layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')

<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-clock-history me-2"></i>Riwayat Transaksi
        </h4>
        <small class="text-muted">Daftar semua transaksi yang telah dilakukan</small>
    </div>
</div>

{{-- Statistik --}}
<div class="row mb-4">
    <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center py-3">
                <i class="bi bi-receipt text-success" style="font-size:36px;"></i>
                <h3 class="mt-2 mb-0">{{ $totalTransaksi }}</h3>
                <p class="text-muted mb-0 small">Total Transaksi</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center py-3">
                <i class="bi bi-cash-stack text-primary" style="font-size:36px;"></i>
                <h4 class="mt-2 mb-0" style="font-size:16px;">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </h4>
                <p class="text-muted mb-0 small">Total Pendapatan</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center py-3">
                <i class="bi bi-shop text-success" style="font-size:36px;"></i>
                <h3 class="mt-2 mb-0">{{ $totalDineIn }}</h3>
                <p class="text-muted mb-0 small">Dine In</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center py-3">
                <i class="bi bi-bag text-warning" style="font-size:36px;"></i>
                <h3 class="mt-2 mb-0">{{ $totalTakeAway }}</h3>
                <p class="text-muted mb-0 small">Take Away</p>
            </div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Jenis</th>
                    <th>Meja</th>
                    <th>Pesanan</th>
                    <th>Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = ($paginator->currentPage() - 1) * $paginator->perPage() + 1;
                @endphp

                @forelse($paginator->items() as $nomorUnik => $items)
                @php
                    $first      = $items->first();
                    $grandTotal = $items->sum('total_harga');
                    $totalItem  = $items->sum('jumlah');
                @endphp
                <tr>
                    
                    <td>{{ $no++ }}</td>

                    
                    <td>
                        <strong class="text-primary" style="font-size:11px;">
                            {{ $nomorUnik }}
                        </strong>
                        @if($first->dp_dibayar > 0)
                        <span class="badge bg-info text-dark d-block mt-1" style="font-size:10px;">
                            Dari Booking
                        </span>
                        @endif
                    </td>

                    
                    <td>
                        <div>{{ $first->created_at->format('d/m/Y') }}</div>
                        <small class="text-muted">{{ $first->created_at->format('H:i') }}</small>
                    </td>

                   
                    <td>
                        <strong>{{ $first->nama_pelanggan }}</strong>
                    </td>

                   
                    <td>
                        <span class="badge {{ $first->jenis_pemesanan === 'dine_in' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $first->jenis_pemesanan === 'dine_in' ? 'Dine In' : 'Take Away' }}
                        </span>
                    </td>

                    
                    <td>
                        @if($first->table)
                            <span class="badge bg-primary">
                                Meja {{ $first->table->nomor_meja }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    
                    <td style="min-width:200px;">
                        @foreach($items as $item)
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <span class="badge bg-secondary" style="font-size:11px;">
                                {{ $item->jumlah }}x
                            </span>
                            <span style="font-size:13px;">
                                {{ $item->product->nama_produk ?? '-' }}
                            </span>
                            <span class="text-muted" style="font-size:11px;margin-left:auto;">
                                Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                        <div class="text-muted" style="font-size:11px;border-top:1px dashed #ddd;padding-top:3px;margin-top:3px;">
                            {{ $totalItem }} item
                        </div>
                    </td>

                    
                    <td style="min-width:160px;">
                        <strong class="text-success d-block">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </strong>

                        @if($first->dp_dibayar > 0)
                            <small class="text-muted d-block">
                                DP: Rp {{ number_format($first->dp_dibayar, 0, ',', '.') }}
                            </small>
                            <small class="text-muted d-block">
                                Sisa: Rp {{ number_format($first->sisa_pembayaran, 0, ',', '.') }}
                            </small>
                            <small class="text-muted d-block">
                                Bayar: Rp {{ number_format($first->uang_bayar, 0, ',', '.') }}
                            </small>
                            @if($first->uang_kembali > 0)
                            <small class="text-muted d-block">
                                Kembali: Rp {{ number_format($first->uang_kembali, 0, ',', '.') }}
                            </small>
                            @endif
                        @else
                            <small class="text-muted d-block">
                                Bayar: Rp {{ number_format($first->uang_bayar, 0, ',', '.') }}
                            </small>
                            @if($first->uang_kembali > 0)
                            <small class="text-muted d-block">
                                Kembali: Rp {{ number_format($first->uang_kembali, 0, ',', '.') }}
                            </small>
                            @endif
                        @endif

                        <span class="badge bg-success mt-1" style="font-size:10px;">
                            {{ $first->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size:48px;color:#ccc;"></i>
                        <p class="mt-3 text-muted">Belum ada transaksi</p>
                        <a href="{{ route('kasir.transactions.dashboard') }}"
                           class="btn btn-primary-custom">
                            <i class="bi bi-plus-circle me-2"></i>Buat Transaksi Baru
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    
    @if($paginator->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $paginator->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection