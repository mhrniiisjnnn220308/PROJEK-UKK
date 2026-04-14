<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 12px;
            background: #f0f0f0;
            border: 1px solid #ddd;
        }
        .stat-box h3 {
            margin: 5px 0;
            color: #6f42c1;
            font-size: 18px;
        }
        .stat-box p {
            margin: 0;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #6f42c1;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>RUMAH MAKAN FOODESIA</h2>
        <h3>DATA PRODUK</h3>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistik -->
    <div class="stats">
        <div class="stat-box">
            <p>Total Produk</p>
            <h3>{{ $totalProduk }}</h3>
        </div>
        <div class="stat-box">
            <p>Produk Aktif</p>
            <h3>{{ $totalAktif }}</h3>
        </div>
        <div class="stat-box">
            <p>Stok Menipis</p>
            <h3>{{ $stokMenipis }}</h3>
        </div>
        <div class="stat-box">
            <p>Produk Nonaktif</p>
            <h3>{{ $totalNonaktif }}</h3>
        </div>
    </div>

    <!-- Tabel -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Produk</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 15%;" class="text-right">Harga</th>
                <th style="width: 10%;" class="text-center">Stok</th>
                <th style="width: 10%;" class="text-center">Status</th>
                <th style="width: 20%;">Terakhir Update</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $product->nama_produk }}</strong></td>
                <td>
                    <span class="badge badge-info">
                        {{ $product->category->nama_kategori ?? '-' }}
                    </span>
                </td>
                <td class="text-right">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="badge {{ $product->stok < 10 ? 'badge-danger' : 'badge-success' }}">
                        {{ $product->stok }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge {{ $product->status == 'aktif' ? 'badge-success' : 'badge-secondary' }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </td>
                <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary -->
    <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
        <strong>Ringkasan:</strong><br>
        Total Produk: {{ $totalProduk }} |
        Aktif: {{ $totalAktif }} |
        Nonaktif: {{ $totalNonaktif }} |
        Stok Menipis (&lt; 10): {{ $stokMenipis }}
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            Laporan ini dicetak oleh: {{ Auth::user()->nama }} (Owner)<br>
            &copy; {{ date('Y') }} Rumah Makan Foodesia - Sistem Laporan &amp; Monitoring
        </p>
    </div>
</body>
</html>