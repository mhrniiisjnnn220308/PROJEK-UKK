@extends('kasir.layouts.app')

@section('title', 'Log Aktivitas')

@section('content')

<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-journal-text me-2"></i>Log Aktivitas
        </h4>
        <small class="text-muted">Riwayat aktivitas yang kamu lakukan</small>
    </div>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th style="width:160px;">Waktu</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                <tr>
                    <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $i + 1 }}</td>
                    <td>
                        <div style="font-size:13px;">{{ $log->created_at->format('d/m/Y') }}</div>
                        <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                    </td>
                    <td>
                        @php
                            $activity = $log->activity;
                            $isBooking = str_contains($activity, 'Booking');
                        @endphp
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge {{ $isBooking ? 'bg-info text-dark' : 'bg-success' }} mt-1" style="font-size:10px;white-space:nowrap;">
                                {{ $isBooking ? 'Booking' : 'Transaksi' }}
                            </span>
                            <span style="font-size:13px;">{{ $activity }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-5">
                        <i class="bi bi-journal-x" style="font-size:48px;color:#ccc;"></i>
                        <p class="mt-3 text-muted">Belum ada log aktivitas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection