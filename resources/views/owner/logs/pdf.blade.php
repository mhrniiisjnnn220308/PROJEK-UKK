<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Aktivitas - FOODESIA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            background: #fff;
            padding: 30px 40px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 6px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #6f42c1;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .header h2 {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .divider {
            border: none;
            border-top: 2px solid #6f42c1;
            margin: 12px 0;
        }

        /* ── Filter Info ── */
        .filter-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 10px;
            color: #444;
            line-height: 1.7;
        }

        .filter-box strong {
            font-size: 10px;
            color: #333;
        }

        /* ── Stat Cards ── */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #ddd;
        }

        .stats-table td {
            width: 33.33%;
            text-align: center;
            padding: 12px 8px;
            border-right: 1px solid #ddd;
        }

        .stats-table td:last-child {
            border-right: none;
        }

        .stats-table .stat-label {
            font-size: 10px;
            color: #555;
            margin-bottom: 4px;
        }

        .stats-table .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: #6f42c1;
        }

        /* ── Data Table ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        table.data-table thead tr {
            background: #6f42c1;
            color: white;
        }

        table.data-table thead th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f9f6ff;
        }

        table.data-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        table.data-table tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #ede8f5;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            color: white;
        }

        .badge-admin { background: #0d6efd; }
        .badge-kasir { background: #198754; }

        /* ── Footer ── */
        .footer {
            margin-top: 16px;
            text-align: center;
            font-size: 9px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>RUMAH MAKAN FOODESIA</h1>
        <h2>LOG AKTIVITAS</h2>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <hr class="divider">

    <!-- Filter Info -->
    <div class="filter-box">
        <strong>Filter Laporan:</strong><br>
        Periode:
        @if($filters['tanggal_mulai'] && $filters['tanggal_selesai'])
            {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') }}
            s/d
            {{ \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d/m/Y') }}
        @elseif($filters['tanggal_mulai'])
            Dari {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') }}
        @elseif($filters['tanggal_selesai'])
            Sampai {{ \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d/m/Y') }}
        @else
            Semua Periode
        @endif
        <br>
        Role: {{ $filters['role'] }}<br>
        User: {{ $filters['user'] }}
    </div>

    <!-- Statistik -->
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-label">Aktivitas Admin</div>
                <div class="stat-number">{{ $totalAdmin }}</div>
            </td>
            <td>
                <div class="stat-label">Aktivitas Kasir</div>
                <div class="stat-number">{{ $totalKasir }}</div>
            </td>
            <td>
                <div class="stat-label">Total Dalam Filter</div>
                <div class="stat-number">{{ $logs->count() }}</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">Waktu</th>
                <th width="18%">User</th>
                <th width="9%">Role</th>
                <th>Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    {{ $log->created_at->format('d/m/Y') }}<br>
                    <span style="color:#888;">{{ $log->created_at->format('H:i:s') }}</span>
                </td>
                <td>
                    <strong>{{ $log->user->nama }}</strong><br>
                    <span style="color:#888;">{{ $log->user->username }}</span>
                </td>
                <td>
                    <span class="badge {{ $log->user->role === 'admin' ? 'badge-admin' : 'badge-kasir' }}">
                        {{ ucfirst($log->user->role) }}
                    </span>
                </td>
                <td>{{ $log->activity }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#aaa;">
                    Tidak ada data log aktivitas
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        FOODESIA &mdash; Owner Panel &bull; Dokumen ini digenerate otomatis oleh sistem &bull; {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>