@extends('owner.layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
<div class="page-header">
    <h4 class="page-title mb-0">
        <i class="bi bi-speedometer2 me-2" style="color: #6f42c1;"></i>Dashboard Overview
    </h4>
    <small class="text-muted">Ringkasan sistem secara keseluruhan</small>
</div>

<!-- Statistik Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card stat-card-1">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Total Produk</h6>
                    <h2 class="mb-0 mt-2">{{ $totalProduk }}</h2>
                    <small>Produk Aktif</small>
                </div>
                <i class="bi bi-box-seam" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card stat-card-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Total Transaksi</h6>
                    <h2 class="mb-0 mt-2">{{ $totalTransaksi }}</h2>
                    <small>Semua Waktu</small>
                </div>
                <i class="bi bi-receipt" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card stat-card-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Total Pendapatan</h6>
                    <h2 class="mb-0 mt-2" style="font-size: 1.5rem;">{{ number_format($totalPendapatan / 1000000, 1) }}jt</h2>
                    <small>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</small>
                </div>
                <i class="bi bi-cash-stack" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card stat-card-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Total User</h6>
                    <h2 class="mb-0 mt-2">{{ $totalUser }}</h2>
                    <small>User Aktif</small>
                </div>
                <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Transaksi Hari Ini -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check text-success" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $transaksiHariIni }}</h3>
                <p class="text-muted mb-0">Transaksi Hari Ini</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-wallet2 text-primary" style="font-size: 48px;"></i>
                <h3 class="mt-3">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                <p class="text-muted mb-0">Pendapatan Hari Ini</p>
            </div>
        </div>
    </div>
</div>

<!-- Produk Terlaris -->
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-trophy text-warning me-2"></i>Produk Terlaris
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produkTerlaris as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->nama_produk }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $item->product->category->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $item->total_terjual }} unit</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaksi Terbaru -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-receipt-cutoff text-success me-2"></i>Transaksi Terbaru
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Kasir</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiTerbaru as $nomor => $items)
                                @php
                                    $first = $items->first();
                                    $total = $items->sum('total_harga');
                                @endphp
                                <tr>
                                    <td>
                                        <small class="text-primary"><strong>{{ substr($nomor, 0, 12) }}...</strong></small><br>
                                        <small class="text-muted">{{ $first->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>{{ $first->user->nama }}</td>
                                    <td><strong class="text-success">Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Aktivitas Terbaru -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-clock-history text-info me-2"></i>Log Aktivitas Terbaru
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logTerbaru as $log)
                    <tr>
                        <td>
                            <small>{{ $log->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>{{ $log->user->nama }}</td>
                        <td>
                            <span class="badge bg-{{ $log->user->role == 'admin' ? 'primary' : 'success' }}">
                                {{ ucfirst($log->user->role) }}
                            </span>
                        </td>
                        <td>{{ $log->activity }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada log aktivitas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('owner.logs.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i>Lihat Semua Log
            </a>
        </div>
    </div>
</div>
@endsection