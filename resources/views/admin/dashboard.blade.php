@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@push('styles')
<style>
    /* ── Alert stok rendah ─────────────────────────── */
    .alert-stok-rendah {
        background: #FFF7ED;
        border: 1px solid #FED7AA;
        color: #C2410C;
        border-radius: 8px;
        font-size: 0.88rem;
        padding: 10px 16px;
        margin-bottom: 20px;
    }

    /* ── Card ringkasan transaksi ──────────────────── */
    .transaksi-stat-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 14px 10px;
        text-align: center;
        border: 1px solid #e9ecef;
    }
    .transaksi-stat-card i  { font-size: 26px; display: block; margin-bottom: 6px; }
    .transaksi-stat-card h5,
    .transaksi-stat-card h6 { margin: 0 0 2px; font-weight: 700; }
    .transaksi-stat-card h6 { font-size: 13px; }
    .transaksi-stat-card small { color: #6c757d; font-size: 12px; }

    /* ── Pagination warna brand ────────────────────── */
    .page-link {
        color: #FF8C42;
        border-color: #dee2e6;
    }
    .page-link:hover    { background: #FFF5F0; color: #FF6B35; border-color: #FF8C42; }
    .page-item.active .page-link {
        background: #FF8C42;
        border-color: #FF8C42;
        color: white;
    }
    .page-item.disabled .page-link { color: #adb5bd; }

    /* ── Tabel ─────────────────────────────────────── */
    .table thead th {
        background: #FFE8DC;
        color: #333;
        font-weight: 600;
        border: none;
        padding: 12px 15px;
    }
    .table tbody td { padding: 12px 15px; vertical-align: middle; }

    /* ── Footer pagination bar ─────────────────────── */
    .pagination-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 12px 16px;
        border-top: 1px solid #e9ecef;
    }
</style>
@endpush

@section('content')

{{-- Page Title --}}
<div class="content-header">
    <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
</div>

{{-- Welcome Alert --}}
<div class="welcome-alert">
    Selamat Datang Admin!
</div>

{{-- =========================================================
     STATISTIK CARDS
     ========================================================= --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card-1">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-number">{{ $totalProduk }}</div>
            <div class="stat-label">Total Produk</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-2">
            <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
            <div class="stat-number">{{ $totalTransaksi }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-3">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-number">{{ $totalUser }}</div>
            <div class="stat-label">Total User</div>
        </div>
    </div>
</div>

{{-- Stok Rendah --}}
@if($produkStokRendah > 0)
<div class="alert-stok-rendah">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Terdapat <strong>{{ $produkStokRendah }}</strong> produk dengan stok rendah (stok &lt; 10).
</div>
@endif

{{-- =========================================================
     LOG AKTIVITAS — ADMIN ONLY
     ========================================================= --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru
            <span class="badge bg-primary ms-2" style="font-size:11px;">Admin</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $loop->iteration + ($logPage - 1) * $logPerPage }}</td>
                        <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                        <td>{{ $log->user->nama }}</td>
                        <td><span class="badge bg-primary">Admin</span></td>
                        <td>{{ $log->activity }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i>Belum ada aktivitas admin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Log --}}
        @if($totalLogPages > 1)
        <div class="pagination-bar">
            <small class="text-muted">
                Menampilkan {{ ($logPage - 1) * $logPerPage + 1 }}–{{ min($logPage * $logPerPage, $totalLogs) }}
                dari {{ $totalLogs }} aktivitas
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item {{ $logPage == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?log_page={{ $logPage - 1 }}&trx_page={{ $trxPage }}">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    @if($logPage > 3)
                        <li class="page-item"><a class="page-link" href="?log_page=1&trx_page={{ $trxPage }}">1</a></li>
                        @if($logPage > 4)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                    @endif
                    @for($i = max(1, $logPage - 2); $i <= min($totalLogPages, $logPage + 2); $i++)
                        <li class="page-item {{ $i == $logPage ? 'active' : '' }}">
                            <a class="page-link" href="?log_page={{ $i }}&trx_page={{ $trxPage }}">{{ $i }}</a>
                        </li>
                    @endfor
                    @if($logPage < $totalLogPages - 2)
                        @if($logPage < $totalLogPages - 3)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                        <li class="page-item"><a class="page-link" href="?log_page={{ $totalLogPages }}&trx_page={{ $trxPage }}">{{ $totalLogPages }}</a></li>
                    @endif
                    <li class="page-item {{ $logPage == $totalLogPages ? 'disabled' : '' }}">
                        <a class="page-link" href="?log_page={{ $logPage + 1 }}&trx_page={{ $trxPage }}">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

{{-- =========================================================
     RIWAYAT TRANSAKSI KASIR
     ========================================================= --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-receipt me-2"></i>Riwayat Transaksi Kasir
        </h5>
    </div>

    {{-- Statistik ringkasan --}}
    <div class="card-body pb-2 pt-3">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="transaksi-stat-card">
                    <i class="bi bi-receipt text-success"></i>
                    <h5>{{ $totalTransaksi }}</h5>
                    <small>Total Transaksi</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="transaksi-stat-card">
                    <i class="bi bi-cash-stack text-primary"></i>
                    <h6>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h6>
                    <small>Total Pendapatan</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="transaksi-stat-card">
                    <i class="bi bi-shop text-success"></i>
                    <h5>{{ $totalDineIn }}</h5>
                    <small>Dine In</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="transaksi-stat-card">
                    <i class="bi bi-bag text-warning"></i>
                    <h5>{{ $totalTakeAway }}</h5>
                    <small>Take Away</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                    @forelse($semuaTransaksi as $nomorUnik => $items)
                    @php
                        $first      = $items->first();
                        $grandTotal = $items->sum('total_harga');
                        $totalItem  = $items->sum('jumlah');
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration + ($trxPage - 1) * $trxPerPage }}</td>

                        <td>
                            <strong class="text-primary" style="font-size:11px;">{{ $nomorUnik }}</strong>
                            @if($first->dp_dibayar > 0)
                                <span class="badge bg-info text-dark d-block mt-1" style="font-size:10px;">Dari Booking</span>
                            @endif
                        </td>

                        <td>
                            <div>{{ $first->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $first->created_at->format('H:i') }}</small>
                        </td>

                        <td><strong>{{ $first->nama_pelanggan }}</strong></td>

                        <td>
                            <span class="badge {{ $first->jenis_pemesanan === 'dine_in' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $first->jenis_pemesanan === 'dine_in' ? 'Dine In' : 'Take Away' }}
                            </span>
                        </td>

                        <td>
                            @if($first->table)
                                <span class="badge bg-primary">Meja {{ $first->table->nomor_meja }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td style="min-width:200px;">
                            @foreach($items as $item)
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <span class="badge bg-secondary" style="font-size:11px;">{{ $item->jumlah }}x</span>
                                <span style="font-size:13px;">{{ $item->product->nama_produk ?? '-' }}</span>
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
                                <small class="text-muted d-block">DP: Rp {{ number_format($first->dp_dibayar, 0, ',', '.') }}</small>
                                <small class="text-muted d-block">Sisa: Rp {{ number_format($first->sisa_pembayaran, 0, ',', '.') }}</small>
                                <small class="text-muted d-block">Bayar: Rp {{ number_format($first->uang_bayar, 0, ',', '.') }}</small>
                            @else
                                <small class="text-muted d-block">Bayar: Rp {{ number_format($first->uang_bayar, 0, ',', '.') }}</small>
                                <small class="text-muted d-block">Kembali: Rp {{ number_format($first->uang_kembali, 0, ',', '.') }}</small>
                            @endif
                            <span class="badge {{ $first->status_pembayaran === 'lunas' ? 'bg-success' : 'bg-danger' }} mt-1" style="font-size:10px;">
                                {{ $first->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size:48px;color:#ccc;"></i>
                            <p class="mt-3 text-muted">Belum ada transaksi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Transaksi --}}
        @if($totalTrxPages > 1)
        <div class="pagination-bar">
            <small class="text-muted">
                Menampilkan {{ ($trxPage - 1) * $trxPerPage + 1 }}–{{ min($trxPage * $trxPerPage, $totalUnikTransaksi) }}
                dari {{ $totalUnikTransaksi }} transaksi
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item {{ $trxPage == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?trx_page={{ $trxPage - 1 }}&log_page={{ $logPage }}">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    @if($trxPage > 3)
                        <li class="page-item"><a class="page-link" href="?trx_page=1&log_page={{ $logPage }}">1</a></li>
                        @if($trxPage > 4)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                    @endif
                    @for($i = max(1, $trxPage - 2); $i <= min($totalTrxPages, $trxPage + 2); $i++)
                        <li class="page-item {{ $i == $trxPage ? 'active' : '' }}">
                            <a class="page-link" href="?trx_page={{ $i }}&log_page={{ $logPage }}">{{ $i }}</a>
                        </li>
                    @endfor
                    @if($trxPage < $totalTrxPages - 2)
                        @if($trxPage < $totalTrxPages - 3)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                        <li class="page-item"><a class="page-link" href="?trx_page={{ $totalTrxPages }}&log_page={{ $logPage }}">{{ $totalTrxPages }}</a></li>
                    @endif
                    <li class="page-item {{ $trxPage == $totalTrxPages ? 'disabled' : '' }}">
                        <a class="page-link" href="?trx_page={{ $trxPage + 1 }}&log_page={{ $logPage }}">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

@endsection