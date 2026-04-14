@extends('owner.layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-clock-history me-2" style="color: #6f42c1;"></i>Log Aktivitas
        </h4>
        <small class="text-muted">Monitor aktivitas Admin dan Kasir</small>
    </div>
    <div>
        <button type="button" class="btn btn-danger" onclick="printPdf()" id="btnDownloadLogPdf">
            <i class="bi bi-file-pdf me-2"></i>Download PDF
        </button>
    </div>
</div>

<!-- Statistik Card (dipindah ke atas) -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-person-badge text-primary" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $totalAdmin }}</h3>
                <p class="text-muted mb-0">Aktivitas Admin</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-person-check text-success" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $totalKasir }}</h3>
                <p class="text-muted mb-0">Aktivitas Kasir</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-event text-info" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $totalHariIni }}</h3>
                <p class="text-muted mb-0">Aktivitas Hari Ini</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.logs.index') }}">
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
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role" id="role">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kasir" {{ request('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">User</label>
                    <select class="form-select" name="user" id="user_filter">
                        <option value="">Semua User</option>
                        @foreach($userList as $user)
                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                {{ $user->nama }} ({{ ucfirst($user->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-funnel me-2"></i>Filter
                </button>
                <a href="{{ route('owner.logs.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Log -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Waktu</th>
                    <th width="18%">User</th>
                    <th width="10%">Role</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                <tr>
                    <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}</td>
                    <td>
                        <strong>{{ $log->created_at->format('d/m/Y') }}</strong><br>
                        <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                                 style="width: 35px; height: 35px; flex-shrink: 0;">
                                <strong style="color: #6f42c1;">{{ strtoupper(substr($log->user->nama, 0, 1)) }}</strong>
                            </div>
                            <div>
                                <strong>{{ $log->user->nama }}</strong><br>
                                <small class="text-muted">{{ $log->user->username }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-custom {{ $log->user->role == 'admin' ? 'bg-primary' : 'bg-success' }}">
                            <i class="bi bi-{{ $log->user->role == 'admin' ? 'shield-check' : 'cash-register' }} me-1"></i>
                            {{ ucfirst($log->user->role) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-start">
                            <i class="bi bi-dot text-primary" style="font-size: 24px; margin-top: -5px;"></i>
                            <span>{{ $log->activity }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 64px; color: #ccc;"></i>
                        <p class="mt-3 text-muted">Tidak ada log aktivitas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3 px-1">
        <small class="text-muted">
            Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}
            dari {{ $logs->total() }} data
        </small>
        <div>
            {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="alert alert-info mt-4" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Informasi:</strong> Log aktivitas menampilkan semua kegiatan yang dilakukan oleh Admin dan Kasir dalam sistem.
    Gunakan filter untuk mempermudah pencarian log spesifik.
</div>
@endsection

@push('scripts')
<script>
function printPdf() {
    const tanggalMulai   = document.getElementById('tanggal_mulai').value;
    const tanggalSelesai = document.getElementById('tanggal_selesai').value;
    const role           = document.getElementById('role').value;
    const user           = document.getElementById('user_filter').value;

    let url = '{{ route("owner.logs.pdf") }}';
    const params = new URLSearchParams();

    if (tanggalMulai)   params.append('tanggal_mulai',   tanggalMulai);
    if (tanggalSelesai) params.append('tanggal_selesai', tanggalSelesai);
    if (role)           params.append('role', role);
    if (user)           params.append('user', user);

    if (params.toString()) url += '?' + params.toString();

    const btn = document.getElementById('btnDownloadLogPdf');
    const originalHtml = btn.innerHTML;
    btn.disabled  = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            : ''
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Gagal mengunduh PDF');
        return response.blob();
    })
    .then(blob => {
        const blobUrl = window.URL.createObjectURL(blob);
        const a       = document.createElement('a');
        a.href        = blobUrl;
        a.download    = 'log-aktivitas.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(blobUrl);
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
        btn.disabled  = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
@endpush