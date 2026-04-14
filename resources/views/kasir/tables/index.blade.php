@extends('kasir.layouts.app')

@section('title', 'Status Meja')

@section('content')

<div class="page-header">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-table me-2"></i>Status Meja</h4>
        <small class="text-muted">Kelola ketersediaan meja restoran</small>
    </div>
    
    <div class="d-flex gap-2 flex-wrap">
        <span class="badge bg-success px-3 py-2" style="font-size:13px;">
            <i class="bi bi-check-circle me-1"></i>
            Tersedia: {{ $tables->where('status','tersedia')->count() }}
        </span>
        <span class="badge bg-danger px-3 py-2" style="font-size:13px;">
            <i class="bi bi-x-circle me-1"></i>
            Terpakai: {{ $tables->where('status','terisi')->count() }}
        </span>
        <span class="badge bg-warning text-dark px-3 py-2" style="font-size:13px;">
            <i class="bi bi-calendar-check me-1"></i>
            Booking: {{ $tables->where('status','booking')->count() }}
        </span>
    </div>
</div>


<form id="form-bebaskan" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>
<form id="form-selesai" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<div class="row g-3">
    @forelse($tables as $table)
    @php
        $status     = $table->status;
        $badgeColor = match($status) {
            'tersedia' => 'success',
            'terisi'   => 'danger',
            'booking'  => 'warning',
            default    => 'secondary',
        };
        $cardBorder = match($status) {
            'tersedia' => '#28a745',
            'terisi'   => '#dc3545',
            'booking'  => '#ffc107',
            default    => '#ccc',
        };
        $cardBg = match($status) {
            'tersedia' => '#f0fff4',
            'terisi'   => '#fff5f5',
            'booking'  => '#fffbf0',
            default    => '#fafafa',
        };

        $bookingAktif = $table->bookings()
            ->where('status', 'konfirmasi')
            ->first();
    @endphp

    <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100 shadow-sm"
             style="border: 2px solid {{ $cardBorder }}; background: {{ $cardBg }}; border-radius: 12px;">
            <div class="card-body text-center py-3 px-2">

               
                <div style="font-size: 40px; color: {{ $cardBorder }};">
                    <i class="bi bi-{{ $status === 'tersedia' ? 'table' : ($status === 'booking' ? 'calendar-check' : 'person-fill') }}"></i>
                </div>
                <h5 class="fw-bold mt-2 mb-1">Meja {{ $table->nomor_meja }}</h5>
                <p class="text-muted mb-2" style="font-size:13px;">
                    <i class="bi bi-people me-1"></i>{{ $table->kapasitas }} orang
                </p>

               
                <span class="badge bg-{{ $badgeColor }} px-3 py-1" style="font-size:12px;">
                    {{ ucfirst($status) }}
                </span>

                
                @if($bookingAktif)
                <div class="mt-2 p-2 rounded" style="background:rgba(0,0,0,0.05);font-size:12px;text-align:left;">
                    <div><i class="bi bi-person me-1"></i><strong>{{ $bookingAktif->nama_pelanggan }}</strong></div>
                    <div><i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse($bookingAktif->tanggal_booking)->format('d/m/Y') }}</div>
                    <div><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($bookingAktif->jam_kedatangan)->format('H:i') }}</div>
                    <div><i class="bi bi-cash me-1"></i>DP: Rp {{ number_format($bookingAktif->jumlah_dp, 0, ',', '.') }}</div>
                </div>
                @endif

                
                <div class="mt-3 d-flex flex-column gap-2">

                    @if($status === 'tersedia')
                        <span class="text-success fw-bold" style="font-size:13px;">
                            <i class="bi bi-check-circle me-1"></i>Siap digunakan
                        </span>

                    @elseif($status === 'terisi')
                        <button type="button"
                                class="btn btn-outline-success w-100 btn-sm"
                                onclick="confirmBebaskan({{ $table->id }}, '{{ $table->nomor_meja }}')">
                            <i class="bi bi-unlock me-1"></i>Bebaskan Meja
                        </button>

                    @elseif($status === 'booking')
                        @if($bookingAktif)
                            <button type="button"
                                    class="btn btn-success w-100 fw-bold btn-sm"
                                    onclick="confirmSelesai({{ $table->id }}, '{{ $table->nomor_meja }}')">
                                <i class="bi bi-check2-circle me-1"></i>Selesai & Bebaskan
                            </button>
                        @else
                            <span class="text-warning fw-bold" style="font-size:12px;">
                                <i class="bi bi-hourglass-split me-1"></i>Menunggu kedatangan
                            </span>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-inbox" style="font-size:64px;color:#ccc;"></i>
        <p class="mt-3 text-muted">Belum ada data meja.</p>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
    function confirmBebaskan(id, nomor) {
        showConfirm({
            icon     : '<i class="bi bi-unlock"></i>',
            iconType : 'success',
            title    : 'Bebaskan Meja ' + nomor + '?',
            desc     : 'Meja akan kembali tersedia dan bisa digunakan pelanggan lain.',
            btnType  : 'success',
            btnLabel : 'Ya, Bebaskan!',
            onYes    : function() {
                showToast('success', 'Memproses...');
                var form = document.getElementById('form-bebaskan');
                form.action = '/kasir/tables/' + id + '/bebaskan';
                form.submit();
            }
        });
    }

    function confirmSelesai(id, nomor) {
        showConfirm({
            icon     : '<i class="bi bi-check2-circle"></i>',
            iconType : 'warning',
            title    : 'Selesaikan Meja ' + nomor + '?',
            desc     : 'Booking akan ditutup dan meja dibebaskan kembali.',
            btnType  : 'warning',
            btnLabel : 'Ya, Selesaikan!',
            onYes    : function() {
                showToast('success', 'Memproses...');
                var form = document.getElementById('form-selesai');
                form.action = '/kasir/tables/' + id + '/selesai';
                form.submit();
            }
        });
    }
</script>
@endpush