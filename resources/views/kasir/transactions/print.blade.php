<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $firstTransaction->nomor_unik }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .receipt {
            border: 1px dashed #000;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .info {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .info-row span:last-child {
            text-align: right;
            max-width: 160px;
        }
        
        .items {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .item {
            margin: 5px 0;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .total {
            margin-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 13px;
        }
        
        .total-row.grand {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .total-row.dp-row {
            color: #0d6efd;
            font-size: 12px;
        }

        .total-row.sisa-row {
            color: #dc3545;
            font-size: 12px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">

        
        <div class="header">
            <h2>RUMAH MAKAN FOODESIA</h2>
            <p>Jl. Contoh No. 123, Bandung</p>
            <p>Telp: (022) 1234567</p>
        </div>
        
        
        @php
            $metode = $firstTransaction->metode_pembayaran ?? 'cash';
            $jenis  = $firstTransaction->jenis_pemesanan;
        @endphp

        <div class="info">
            <div class="info-row">
                <span>No. Transaksi</span>
                <strong>{{ substr($firstTransaction->nomor_unik, 0, 12) }}...</strong>
            </div>
            <div class="info-row">
                <span>Tanggal</span>
                <span>{{ $firstTransaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span>Kasir</span>
                <span>{{ $firstTransaction->user->nama }}</span>
            </div>
            <div class="info-row">
                <span>Pelanggan</span>
                <span>{{ $firstTransaction->nama_pelanggan }}</span>
            </div>
            <div class="info-row">
                <span>Jenis</span>
                <span>
                    {{ $jenis === 'dine_in' ? 'Dine In' : 'Take Away' }}
                    @if($jenis === 'dine_in' && $firstTransaction->table)
                        - Meja {{ $firstTransaction->table->nomor_meja }}
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span>Pembayaran</span>
                <span>{{ $metode === 'transfer' ? 'Transfer Bank' : 'Tunai / Cash' }}</span>
            </div>
        </div>
        
       
        <div class="items">
            @php $grandTotal = 0; @endphp
            
            @foreach($transactions as $transaction)
                @php
                    $subtotal    = $transaction->total_harga;
                    $grandTotal += $subtotal;
                @endphp
                <div class="item">
                    <div class="item-name">{{ $transaction->product->nama_produk }}</div>
                    <div class="item-detail">
                        <span>{{ $transaction->jumlah }} x Rp {{ number_format($transaction->product->harga_produk, 0, ',', '.') }}</span>
                        <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @endforeach
        </div>
        
       
        <div class="total">
            <div class="total-row grand">
                <span>TOTAL</span>
                <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>

            @if($firstTransaction->dp_dibayar > 0)
            <div class="total-row dp-row">
                <span>DP Booking</span>
                <span>Rp {{ number_format($firstTransaction->dp_dibayar, 0, ',', '.') }}</span>
            </div>
            <div class="total-row sisa-row">
                <span>Sisa Bayar</span>
                <span>Rp {{ number_format($firstTransaction->sisa_pembayaran, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="total-row">
                <span>Bayar</span>
                <span>Rp {{ number_format($firstTransaction->uang_bayar, 0, ',', '.') }}</span>
            </div>

            @if($firstTransaction->uang_kembali > 0)
            <div class="total-row">
                <span>Kembali</span>
                <span>Rp {{ number_format($firstTransaction->uang_kembali, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
        
       
        <div class="footer">
            <p>*** TERIMA KASIH ***</p>
            <p>Selamat datang kembali</p>
            @if($jenis === 'dine_in' && $firstTransaction->table)
            <p style="margin-top: 10px;">Selamat menikmati di Meja {{ $firstTransaction->table->nomor_meja }}</p>
            @else
            <p style="margin-top: 10px;">Selamat menikmati!</p>
            @endif
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cetak Struk
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>
</body>
</html>