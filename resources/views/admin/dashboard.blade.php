@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

{{-- Page Title --}}
<div class="content-header">
    <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
</div>

{{-- Welcome Alert --}}
<div class="welcome-alert">
    Selamat Datang Admin !
</div>

{{-- Statistik Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-number">{{ $totalProduk }}</div>
            <div class="stat-label">Total Produk</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
            <div class="stat-number">{{ $totalTransaksi }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-number">{{ $totalUser }}</div>
            <div class="stat-label">Total User</div>
        </div>
    </div>
</div>

{{-- Stok Rendah Info --}}
@if($produkStokRendah > 0)
<div class="alert" style="background:#FFF7ED; border:1px solid #FED7AA; color:#C2410C; border-radius:8px; font-size:0.88rem; padding:10px 16px; margin-bottom:20px;">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Terdapat <strong>{{ $produkStokRendah }}</strong> produk dengan stok rendah (stok &lt; 10).
</div>
@endif

{{-- Log Aktivitas Terbaru --}}
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>
                            <small>{{ $log->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>{{ $log->user->nama }}</td>
                        <td>
                            <span class="badge bg-{{ $log->user->role == 'admin' ? 'primary' : ($log->user->role == 'kasir' ? 'success' : 'warning') }}">
                                {{ ucfirst($log->user->role) }}
                            </span>
                        </td>
                        <td>{{ $log->activity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection