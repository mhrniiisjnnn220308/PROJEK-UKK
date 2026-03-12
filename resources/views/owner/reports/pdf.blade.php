<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #6f42c1;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            color: #6f42c1;
        }
        .filter-info {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 15px;
            background: #f0f0f0;
            border: 1px solid #ddd;
        }
        .stat-box h3 {
            margin: 5px 0;
            color: #6f42c1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #6f42c1;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>RUMAH MAKAN FOODESIA</h2>
        <h3>LAPORAN TRANSAKSI</h3>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
    
    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Filter Laporan:</strong><br>
        Periode: 
        @if($filters['tanggal_mulai'] && $filters['tanggal_selesai'])
            {{ date('d/m/Y', strtotime($filters['tanggal_mulai'])) }} s/d {{ date('d/m/Y', strtotime($filters['tanggal_selesai'])) }}
        @elseif($filters['tanggal_mulai'])
            Dari {{ date('d/m/Y', strtotime($filters['tanggal_mulai'])) }}
        @elseif($filters['tanggal_selesai'])
            Sampai {{ date('d/m/Y', strtotime($filters['tanggal_selesai'])) }}
        @else
            Semua Periode
        @endif
        <br>
        Kasir: {{ $filters['kasir'] }}<br>
        Produk: {{ $filters['produk'] }}
    </div>
    
    <!-- Statistik -->
    <div class="stats">
        <div class="stat-box">
            <p>Total Transaksi</p>
            <h3>{{ $totalTransaksi }}</h3>
        </div>
        <div class="stat-box">
            <p>Total Pendapatan</p>
            <h3>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>
        <div class="stat-box">
            <p>Total Item Terjual</p>
            <h3>{{ $totalItem }}</h3>
        </div>
    </div>
    
    <!-- Tabel -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Transaksi</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Pelanggan</th>
                <th>Produk</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaction->nomor_unik }}</td>
                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $transaction->user->nama }}</td>
                <td>{{ $transaction->nama_pelanggan }}</td>
                <td>{{ $transaction->product->nama_produk }}</td>
                <td class="text-center">{{ $transaction->jumlah }}</td>
                <td class="text-right">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #6f42c1; color: white; font-weight: bold;">
                <td colspan="6" class="text-right">GRAND TOTAL:</td>
                <td class="text-center">{{ $totalItem }}</td>
                <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    
    <!-- Footer -->
    <div class="footer">
        <p>
            Laporan ini dicetak oleh: {{ Auth::user()->nama }} (Owner)<br>
            © {{ date('Y') }} Rumah Makan Foodesia - Sistem Laporan & Monitoring
        </p>
    </div>
</body>
</html>