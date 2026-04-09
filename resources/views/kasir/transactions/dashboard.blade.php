@extends('kasir.layouts.app')

@section('title', 'Transaksi Baru')

@section('content')

<div class="page-header">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-cart me-2"></i>Transaksi Baru</h4>
        <small class="text-muted">Pilih produk untuk memulai transaksi</small>
    </div>
    <button class="btn-primary-custom" onclick="resetCart()">
        <i class="bi bi-arrow-clockwise"></i>Reset
    </button>
</div>

<div class="transaksi-wrapper">

    {{-- ════════════════════════════════════════
         PANEL KIRI: Daftar Produk
    ════════════════════════════════════════ --}}
    <div class="produk-panel">

        {{-- Filter Kategori --}}
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-2">
                <button type="button"
                        class="btn btn-sm btn-success active"
                        id="catBtn_all"
                        onclick="filterCategory('all', this)">
                    Semua
                </button>
                @foreach($categories as $category)
                <button type="button"
                        class="btn btn-sm btn-outline-success"
                        id="catBtn_{{ $category->id }}"
                        onclick="filterCategory('{{ $category->id }}', this)">
                    {{ $category->nama_kategori }} ({{ $category->products_count }})
                </button>
                @endforeach
            </div>
        </div>

        {{-- Grid Produk --}}
        <div class="row g-3" id="productGrid">
            @forelse($products as $product)
            <div class="col-6 col-md-4 col-xl-3 product-item"
                 data-category="{{ $product->kategori_id }}">
                <div class="product-card" onclick="addToCart({{ json_encode($product) }})">
                    @if($product->foto)
                        <img src="{{ asset('uploads/products/' . $product->foto) }}"
                             class="product-img" alt="{{ $product->nama_produk }}">
                    @else
                        <div class="product-img d-flex align-items-center justify-content-center"
                             style="background:#f0f0f0;">
                            <i class="bi bi-image" style="font-size:40px;color:#ccc;"></i>
                        </div>
                    @endif
                    <div class="product-info">
                        <div class="product-name">{{ $product->nama_produk }}</div>
                        <div class="product-price">
                            Rp {{ number_format($product->harga_produk, 0, ',', '.') }}
                        </div>
                        <div>
                            <span class="badge badge-custom {{ $product->stok < 10 ? 'bg-danger' : 'bg-success' }}">
                                Stok: {{ $product->stok }}
                            </span>
                        </div>
                        @if($product->category)
                        <div class="mt-1">
                            <small class="badge bg-info">{{ $product->category->nama_kategori }}</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox" style="font-size:64px;color:#ccc;"></i>
                <p class="mt-3 text-muted">Tidak ada produk tersedia</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ════════════════════════════════════════
         PANEL KANAN: Keranjang
    ════════════════════════════════════════ --}}
    <div class="keranjang-panel">
        <div class="cart-container">

            {{-- Mode Tabs --}}
            <div class="mode-tabs">
                <div class="mode-tab active" id="tabBiasa" onclick="setMode('biasa')">
                    <i class="bi bi-cart3 me-1"></i>Transaksi Biasa
                </div>
                <div class="mode-tab" id="tabBooking" onclick="setMode('booking')">
                    <i class="bi bi-calendar-check me-1"></i>Dari Booking
                    @if($bookings->count() > 0)
                        <span class="badge bg-danger ms-1">{{ $bookings->count() }}</span>
                    @endif
                </div>
            </div>

            {{-- ══════ PANEL BIASA ══════ --}}
            <div id="panelBiasa">
                <div id="cartItems" class="mb-2">
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-cart-x" style="font-size:48px;"></i>
                        <p class="mt-2 mb-0">Keranjang masih kosong</p>
                    </div>
                </div>

                <div class="cart-total">
                    <div class="d-flex justify-content-between mb-1">
                        <strong>Total Item:</strong>
                        <span id="totalItems">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Total Harga:</strong>
                        <strong class="text-success" id="totalPrice">Rp 0</strong>
                    </div>

                    <div class="mb-2">
                        <label class="form-label form-label-sm">Jenis Pemesanan <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="jenisPemesanan" onchange="toggleMejaField()">
                            <option value="dine_in">Dine In (Makan di Tempat)</option>
                            <option value="take_away">Take Away (Bawa Pulang)</option>
                        </select>
                    </div>

                    <div class="mb-2" id="mejaField">
                        <label class="form-label form-label-sm">Pilih Meja <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="idMeja">
                            <option value="">-- Pilih Meja --</option>
                            @foreach($tables as $table)
                                @if($table->status === 'tersedia')
                                <option value="{{ $table->id }}">
                                    Meja {{ $table->nomor_meja }} ({{ $table->kapasitas }} orang)
                                </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted">Hanya meja tersedia</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label form-label-sm">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="namaPelanggan"
                               placeholder="Masukkan nama pelanggan">
                    </div>

                    {{-- Metode Bayar: Transaksi Biasa --}}
                    <div class="mb-2">
                        <label class="form-label form-label-sm">Metode Pembayaran <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="metodeBayarBiasa"
                                       id="metodeCashBiasa" value="cash" checked
                                       onchange="toggleMetodeBiasa()">
                                <label class="btn btn-outline-success btn-sm w-100" for="metodeCashBiasa">
                                    <i class="bi bi-cash me-1"></i>Cash
                                </label>
                            </div>
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="metodeBayarBiasa"
                                       id="metodeTransferBiasa" value="transfer"
                                       onchange="toggleMetodeBiasa()">
                                <label class="btn btn-outline-primary btn-sm w-100" for="metodeTransferBiasa">
                                    <i class="bi bi-bank me-1"></i>Transfer
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Field Cash Biasa --}}
                    <div id="fieldCashBiasa">
                        <div class="mb-2">
                            <label class="form-label form-label-sm">Uang Bayar <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" id="uangBayar"
                                   placeholder="0" oninput="calculateChange()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-sm">Uang Kembali</label>
                            <input type="text" class="form-control form-control-sm" id="uangKembali"
                                   readonly style="background:#f8f9fa;font-weight:600;">
                        </div>
                    </div>

                    {{-- Field Transfer Biasa --}}
                    <div id="fieldTransferBiasa" style="display:none;">
                        <div class="mb-2">
                            <label class="form-label form-label-sm">No. Referensi Transfer <small class="text-muted">(opsional)</small></label>
                            <input type="text" class="form-control form-control-sm" id="refTransferBiasa"
                                   placeholder="Contoh: TRF-20240101-001">
                        </div>
                        <div class="mb-3">
                            <div class="alert alert-info py-2 mb-0" style="font-size:12px;">
                                <i class="bi bi-info-circle me-1"></i>
                                Pastikan transfer sudah diterima sebelum proses transaksi.
                                Uang kembali = Rp 0 untuk pembayaran transfer.
                            </div>
                        </div>
                    </div>

                    <button class="btn-primary-custom w-100 justify-content-center"
                            onclick="processTransaction()">
                        <i class="bi bi-check-circle"></i>Proses Transaksi
                    </button>
                </div>
            </div>

            {{-- ══════ PANEL BOOKING ══════ --}}
            <div id="panelBooking" style="display:none;">

                @if($bookings->count() > 0)
                <div class="mb-2">
                    <small class="text-muted fw-bold">Pilih booking pelanggan:</small>
                    <div class="mt-2" style="max-height:200px;overflow-y:auto;">
                        @foreach($bookings as $booking)
                        <div class="booking-card"
                             data-id="{{ $booking->id }}"
                             data-nama="{{ $booking->nama_pelanggan }}"
                             data-dp="{{ $booking->jumlah_dp }}"
                             data-meja-nomor="{{ $booking->meja->nomor_meja ?? '-' }}"
                             data-meja-id="{{ $booking->id_meja ?? '' }}"
                             data-catatan="{{ $booking->catatan_pesanan ?? '' }}"
                             data-tanggal="{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d/m/Y') }}"
                             data-jam="{{ \Carbon\Carbon::parse($booking->jam_kedatangan)->format('H:i') }}"
                             data-bukti="{{ $booking->bukti_dp ? asset('uploads/bukti_dp/' . $booking->bukti_dp) : '' }}"
                             data-dp-verified="{{ $booking->dp_verified ? '1' : '0' }}"
                             onclick="pilihBookingFromCard(this)">
                            <div class="booking-name">{{ $booking->nama_pelanggan }}</div>
                            <div class="booking-detail">
                                <i class="bi bi-table me-1"></i>Meja {{ $booking->meja->nomor_meja ?? '-' }}
                                &nbsp;|&nbsp;
                                <i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d/m/Y') }}
                                &nbsp;|&nbsp;
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($booking->jam_kedatangan)->format('H:i') }}
                            </div>
                            <div class="mt-1 d-flex flex-wrap gap-1">
                                <span class="badge bg-success" style="font-size:11px;">
                                    DP: Rp {{ number_format($booking->jumlah_dp, 0, ',', '.') }}
                                </span>
                                @if($booking->dp_verified)
                                    <span class="badge bg-info" style="font-size:11px;">
                                        <i class="bi bi-check-circle me-1"></i>DP Terverifikasi
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark" style="font-size:11px;">
                                        DP Belum Verifikasi
                                    </span>
                                @endif
                                @if($booking->catatan_pesanan)
                                    <span class="badge bg-secondary" style="font-size:11px;">Ada catatan</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x" style="font-size:48px;"></i>
                    <p class="mt-2 mb-0">Tidak ada booking terkonfirmasi</p>
                    <small>Booking perlu dikonfirmasi admin dulu</small>
                </div>
                @endif

                {{-- Detail setelah booking dipilih --}}
                <div id="bookingDetail" style="display:none;">

                    {{-- Info DP yang sudah dibayar --}}
                    <div class="dp-info-box">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="dp-label"><i class="bi bi-check-circle me-1"></i>DP sudah dibayar</div>
                                <div class="dp-value" id="dpNominal">Rp 0</div>
                            </div>
                            <div class="text-end">
                                <i class="bi bi-cash-coin text-success" style="font-size:26px;"></i>
                                <div id="dpVerifiedBadge" style="display:none;">
                                    <span class="badge bg-info mt-1" style="font-size:10px;">
                                        <i class="bi bi-check-circle me-1"></i>Terverifikasi
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- Tombol lihat bukti transfer DP --}}
                        <div id="buktiDpBox" class="mt-2" style="display:none;">
                            <button type="button" class="btn btn-sm btn-outline-info py-0 px-2"
                                    onclick="lihatBuktDpKasir()">
                                <i class="bi bi-image me-1"></i>
                                <small>Lihat Bukti Transfer DP</small>
                            </button>
                        </div>
                    </div>

                    {{-- Catatan pesanan dari booking --}}
                    <div id="catatanBox" class="catatan-box" style="display:none;">
                        <small class="fw-bold text-warning">
                            <i class="bi bi-sticky me-1"></i>Catatan pesanan dari booking:
                        </small>
                        <p class="mb-0 mt-1" id="catatanText" style="font-size:13px;"></p>
                    </div>

                    {{-- Keranjang booking --}}
                    <div id="cartItemsBooking" class="mb-2">
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-cart-x" style="font-size:36px;"></i>
                            <p class="mt-1 mb-0" style="font-size:13px;">Pilih produk dari menu</p>
                        </div>
                    </div>

                    <div class="cart-total">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:13px;">Total Pesanan:</span>
                            <strong id="bTotalPesanan">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:13px;">DP Sudah Bayar:</span>
                            <span class="text-success" id="bDpSudah">- Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2"
                             style="border-top:1px solid #FF8C42;padding-top:8px;margin-top:4px;">
                            <strong>Sisa Tagihan:</strong>
                            <strong class="text-danger" id="bSisaTagihan">Rp 0</strong>
                        </div>

                        {{-- Metode Bayar Sisa: Booking --}}
                        <div class="mb-2">
                            <label class="form-label form-label-sm">
                                Metode Bayar Sisa <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-2">
                                <div class="flex-fill">
                                    <input type="radio" class="btn-check" name="metodeBayarBooking"
                                           id="metodeCashBooking" value="cash" checked
                                           onchange="toggleMetodeBooking()">
                                    <label class="btn btn-outline-success btn-sm w-100" for="metodeCashBooking">
                                        <i class="bi bi-cash me-1"></i>Cash
                                    </label>
                                </div>
                                <div class="flex-fill">
                                    <input type="radio" class="btn-check" name="metodeBayarBooking"
                                           id="metodeTransferBooking" value="transfer"
                                           onchange="toggleMetodeBooking()">
                                    <label class="btn btn-outline-primary btn-sm w-100" for="metodeTransferBooking">
                                        <i class="bi bi-bank me-1"></i>Transfer
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Field Cash Booking --}}
                        <div id="fieldCashBooking">
                            <div class="mb-2">
                                <label class="form-label form-label-sm">
                                    Uang Bayar (sisa) <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control form-control-sm"
                                       id="bUangBayar" placeholder="0"
                                       oninput="hitungKembaliBooking()">
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-sm">Uang Kembali</label>
                                <input type="text" class="form-control form-control-sm"
                                       id="bUangKembali" readonly
                                       style="background:#f8f9fa;font-weight:600;">
                            </div>
                        </div>

                        {{-- Field Transfer Booking --}}
                        <div id="fieldTransferBooking" style="display:none;">
                            <div class="mb-2">
                                <label class="form-label form-label-sm">
                                    No. Referensi Transfer <small class="text-muted">(opsional)</small>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="bRefTransfer"
                                       placeholder="Contoh: TRF-20240101-001">
                            </div>
                            <div class="mb-3">
                                <div class="alert alert-info py-2 mb-0" style="font-size:12px;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Pelanggan membayar sisa via transfer.<br>
                                    Pastikan transfer sudah diterima sebelum proses.
                                </div>
                            </div>
                        </div>

                        <button class="btn-primary-custom w-100 justify-content-center"
                                onclick="processBookingTransaction()">
                            <i class="bi bi-check-circle"></i>Selesaikan Transaksi Booking
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     Modal Lihat Bukti DP (Kasir View)
════════════════════════════════════════ --}}
<div class="modal fade" id="modalBuktiDpKasir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#0dcaf0;color:white;">
                <h5 class="modal-title">
                    <i class="bi bi-image me-2"></i>Bukti Transfer DP
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="kasirBuktiImg" src="" alt="Bukti Transfer DP" class="img-fluid rounded shadow-sm">
                <p class="mt-2 mb-0 text-muted" style="font-size:13px;" id="kasirBuktiNama"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     Modal Sukses
════════════════════════════════════════ --}}
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#FF8C42;">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Transaksi Berhasil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-check-circle-fill text-success" style="font-size:64px;"></i>
                <h4 class="mt-3">Pembayaran Berhasil!</h4>
                <p class="mb-0">No. Transaksi: <strong id="modalNomorUnik"></strong></p>
                <hr>
                <div class="text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Pesanan:</span>
                        <strong id="modalTotalHarga">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2" id="modalDpRow">
                        <span>DP Sudah Bayar:</span>
                        <strong class="text-success" id="modalDp">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2" id="modalSisaRow">
                        <span>Sisa Tagihan:</span>
                        <strong id="modalSisa">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Metode Bayar:</span>
                        <strong id="modalMetode">-</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Uang Bayar:</span>
                        <strong id="modalUangBayar">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between" id="modalKembaliRow">
                        <span>Uang Kembali:</span>
                        <strong class="text-success" id="modalUangKembali">Rp 0</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn-primary-custom" onclick="printReceipt()">
                    <i class="bi bi-printer"></i>Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let cart            = [];
let cartBooking     = [];
let currentMode     = 'biasa';
let selectedBooking = null;
let lastTransactionNumber = '';

// ── MODE ─────────────────────────────────────────────────────
function setMode(mode) {
    currentMode = mode;
    document.getElementById('tabBiasa').classList.toggle('active', mode === 'biasa');
    document.getElementById('tabBooking').classList.toggle('active', mode === 'booking');
    document.getElementById('panelBiasa').style.display   = mode === 'biasa'   ? 'block' : 'none';
    document.getElementById('panelBooking').style.display = mode === 'booking' ? 'block' : 'none';
}

// ── FILTER KATEGORI ───────────────────────────────────────────
function filterCategory(categoryId, btn) {
    document.querySelectorAll('[id^="catBtn_"]').forEach(b => {
        b.classList.remove('btn-success','active');
        b.classList.add('btn-outline-success');
    });
    btn.classList.remove('btn-outline-success');
    btn.classList.add('btn-success','active');
    document.querySelectorAll('.product-item').forEach(p => {
        p.style.display =
            (categoryId === 'all' || p.dataset.category == categoryId) ? '' : 'none';
    });
}

// ── TOGGLE METODE BAYAR ───────────────────────────────────────
function toggleMetodeBiasa() {
    const isTransfer = document.getElementById('metodeTransferBiasa').checked;
    document.getElementById('fieldCashBiasa').style.display     = isTransfer ? 'none'  : 'block';
    document.getElementById('fieldTransferBiasa').style.display = isTransfer ? 'block' : 'none';
    if (!isTransfer) calculateChange();
}

function toggleMetodeBooking() {
    const isTransfer = document.getElementById('metodeTransferBooking').checked;
    document.getElementById('fieldCashBooking').style.display     = isTransfer ? 'none'  : 'block';
    document.getElementById('fieldTransferBooking').style.display = isTransfer ? 'block' : 'none';
}

// ── ADD TO CART (deteksi mode) ────────────────────────────────
function addToCart(product) {
    if (currentMode === 'booking') {
        if (!selectedBooking) { showToast('warning','Pilih booking terlebih dahulu!'); return; }
        addToCartBooking(product);
    } else {
        addToCartBiasa(product);
    }
}

// ── CART BIASA ────────────────────────────────────────────────
function addToCartBiasa(product) {
    const ex = cart.find(i => i.id === product.id);
    if (ex) {
        if (ex.jumlah < product.stok) ex.jumlah++;
        else { showToast('danger','Stok tidak mencukupi!'); return; }
    } else {
        if (product.stok < 1) { showToast('danger','Stok habis!'); return; }
        cart.push({ id: product.id, nama: product.nama_produk,
                    harga: product.harga_produk, stok: product.stok, jumlah: 1 });
    }
    updateCart();
}

function updateCart() {
    const div = document.getElementById('cartItems');
    if (!cart.length) {
        div.innerHTML = `<div class="text-center text-muted py-4">
            <i class="bi bi-cart-x" style="font-size:48px;"></i>
            <p class="mt-2 mb-0">Keranjang masih kosong</p></div>`;
    } else {
        div.innerHTML = cart.map((item, i) => `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong style="font-size:13px;">${item.nama}</strong>
                    <button class="btn btn-sm btn-danger" style="width:26px;height:26px;padding:0;line-height:1;"
                            onclick="removeFromCart(${i})">
                        <i class="bi bi-trash" style="font-size:11px;"></i></button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary"
                                style="width:26px;height:26px;padding:0;line-height:1;"
                                onclick="decreaseQty(${i})">−</button>
                        <span class="mx-1" style="min-width:20px;text-align:center;">${item.jumlah}</span>
                        <button class="btn btn-sm btn-outline-secondary"
                                style="width:26px;height:26px;padding:0;line-height:1;"
                                onclick="increaseQty(${i})">+</button>
                    </div>
                    <strong class="text-success" style="font-size:13px;">Rp ${fmt(item.harga * item.jumlah)}</strong>
                </div>
            </div>`).join('');
    }
    updateTotal();
}

function updateTotal() {
    const totalItems = cart.reduce((s, i) => s + i.jumlah, 0);
    const totalPrice = cart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalPrice').textContent = 'Rp ' + fmt(totalPrice);
    calculateChange();
}

function calculateChange() {
    const total = cart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const bayar = parseInt(document.getElementById('uangBayar').value) || 0;
    document.getElementById('uangKembali').value =
        bayar >= total ? 'Rp ' + fmt(bayar - total) : 'Rp 0';
}

function decreaseQty(i) { if (cart[i].jumlah > 1) { cart[i].jumlah--; updateCart(); } }
function increaseQty(i) {
    if (cart[i].jumlah < cart[i].stok) { cart[i].jumlah++; updateCart(); }
    else showToast('danger','Stok tidak mencukupi!');
}
function removeFromCart(i) { cart.splice(i, 1); updateCart(); }

// ── PILIH BOOKING ─────────────────────────────────────────────
function pilihBookingFromCard(el) {
    document.querySelectorAll('.booking-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    selectedBooking = {
        id         : el.dataset.id,
        nama       : el.dataset.nama,
        dp         : parseInt(el.dataset.dp) || 0,
        mejaId     : el.dataset.mejaId,
        mejaNo     : el.dataset.mejaNomor,
        buktiUrl   : el.dataset.bukti || '',
        dpVerified : el.dataset.dpVerified === '1',
    };
    cartBooking = [];

    document.getElementById('dpNominal').textContent = 'Rp ' + fmt(selectedBooking.dp);
    document.getElementById('bDpSudah').textContent  = '- Rp ' + fmt(selectedBooking.dp);

    // Tampilkan badge terverifikasi
    const verifiedBadge = document.getElementById('dpVerifiedBadge');
    verifiedBadge.style.display = selectedBooking.dpVerified ? 'block' : 'none';

    // Tampilkan tombol lihat bukti jika ada
    const buktiBox = document.getElementById('buktiDpBox');
    buktiBox.style.display = selectedBooking.buktiUrl ? 'block' : 'none';

    const catatan = el.dataset.catatan;
    if (catatan && catatan.trim()) {
        document.getElementById('catatanText').textContent = catatan;
        document.getElementById('catatanBox').style.display = 'block';
    } else {
        document.getElementById('catatanBox').style.display = 'none';
    }

    document.getElementById('bookingDetail').style.display = 'block';

    // Reset metode bayar ke cash
    document.getElementById('metodeCashBooking').checked = true;
    toggleMetodeBooking();

    updateCartBooking();
}

// ── LIHAT BUKTI DP (dari kasir) ───────────────────────────────
function lihatBuktDpKasir() {
    if (!selectedBooking || !selectedBooking.buktiUrl) return;
    document.getElementById('kasirBuktiImg').src  = selectedBooking.buktiUrl;
    document.getElementById('kasirBuktiNama').textContent =
        'Bukti DP ' + selectedBooking.nama + ' — Rp ' + fmt(selectedBooking.dp);
    new bootstrap.Modal(document.getElementById('modalBuktiDpKasir')).show();
}

// ── CART BOOKING ──────────────────────────────────────────────
function addToCartBooking(product) {
    const ex = cartBooking.find(i => i.id === product.id);
    if (ex) {
        if (ex.jumlah < product.stok) ex.jumlah++;
        else { showToast('danger','Stok tidak mencukupi!'); return; }
    } else {
        if (product.stok < 1) { showToast('danger','Stok habis!'); return; }
        cartBooking.push({ id: product.id, nama: product.nama_produk,
                           harga: product.harga_produk, stok: product.stok, jumlah: 1 });
    }
    updateCartBooking();
}

function updateCartBooking() {
    const div = document.getElementById('cartItemsBooking');
    if (!cartBooking.length) {
        div.innerHTML = `<div class="text-center text-muted py-3">
            <i class="bi bi-cart-x" style="font-size:36px;"></i>
            <p class="mt-1 mb-0" style="font-size:13px;">Pilih produk dari menu</p></div>`;
    } else {
        div.innerHTML = cartBooking.map((item, i) => `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong style="font-size:13px;">${item.nama}</strong>
                    <button class="btn btn-sm btn-danger" style="width:26px;height:26px;padding:0;line-height:1;"
                            onclick="removeFromCartBooking(${i})">
                        <i class="bi bi-trash" style="font-size:11px;"></i></button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary"
                                style="width:26px;height:26px;padding:0;line-height:1;"
                                onclick="decreaseQtyBooking(${i})">−</button>
                        <span class="mx-1" style="min-width:20px;text-align:center;">${item.jumlah}</span>
                        <button class="btn btn-sm btn-outline-secondary"
                                style="width:26px;height:26px;padding:0;line-height:1;"
                                onclick="increaseQtyBooking(${i})">+</button>
                    </div>
                    <strong class="text-success" style="font-size:13px;">Rp ${fmt(item.harga * item.jumlah)}</strong>
                </div>
            </div>`).join('');
    }
    hitungSisaBooking();
}

function hitungSisaBooking() {
    const total = cartBooking.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const dp    = selectedBooking ? selectedBooking.dp : 0;
    const sisa  = Math.max(0, total - dp);
    document.getElementById('bTotalPesanan').textContent = 'Rp ' + fmt(total);
    document.getElementById('bSisaTagihan').textContent  = 'Rp ' + fmt(sisa);
    document.getElementById('bUangBayar').value = sisa;
    hitungKembaliBooking();
}

function hitungKembaliBooking() {
    const total = cartBooking.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const dp    = selectedBooking ? selectedBooking.dp : 0;
    const sisa  = Math.max(0, total - dp);
    const bayar = parseInt(document.getElementById('bUangBayar').value) || 0;
    document.getElementById('bUangKembali').value =
        bayar >= sisa ? 'Rp ' + fmt(bayar - sisa) : 'Rp 0';
}

function decreaseQtyBooking(i) {
    if (cartBooking[i].jumlah > 1) { cartBooking[i].jumlah--; updateCartBooking(); }
}
function increaseQtyBooking(i) {
    if (cartBooking[i].jumlah < cartBooking[i].stok) { cartBooking[i].jumlah++; updateCartBooking(); }
    else showToast('danger','Stok tidak mencukupi!');
}
function removeFromCartBooking(i) { cartBooking.splice(i, 1); updateCartBooking(); }

// ── PROSES TRANSAKSI BIASA ────────────────────────────────────
function processTransaction() {
    if (!cart.length) { showToast('warning','Keranjang masih kosong!'); return; }

    const nama    = document.getElementById('namaPelanggan').value.trim();
    const jenis   = document.getElementById('jenisPemesanan').value;
    const meja    = document.getElementById('idMeja').value;
    const total   = cart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const isTransfer = document.getElementById('metodeTransferBiasa').checked;
    const metode  = isTransfer ? 'transfer' : 'cash';
    const bayar   = isTransfer ? total : (parseInt(document.getElementById('uangBayar').value) || 0);
    const refNo   = isTransfer ? (document.getElementById('refTransferBiasa').value.trim() || null) : null;

    if (!nama)                            { showToast('warning','Nama pelanggan harus diisi!'); return; }
    if (jenis === 'dine_in' && !meja)     { showToast('warning','Pilih meja untuk Dine In!'); return; }
    if (!isTransfer && bayar < total)     { showToast('warning','Uang bayar kurang!'); return; }

    fetch('{{ route("kasir.transactions.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            items           : cart.map(i => ({ id_produk: i.id, jumlah: i.jumlah })),
            nama_pelanggan  : nama,
            jenis_pemesanan : jenis,
            id_meja         : meja || null,
            uang_bayar      : bayar,
            metode_bayar    : metode,
            no_referensi    : refNo,
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            lastTransactionNumber = data.data.nomor_unik;
            tampilModal(
                data.data.total_harga, null, null,
                data.data.uang_bayar, data.data.uang_kembali,
                data.data.nomor_unik, metode
            );
            cart = [];
            document.getElementById('namaPelanggan').value  = '';
            document.getElementById('uangBayar').value      = '';
            document.getElementById('uangKembali').value    = '';
            document.getElementById('jenisPemesanan').value = 'dine_in';
            document.getElementById('idMeja').value         = '';
            document.getElementById('metodeCashBiasa').checked = true;
            toggleMejaField();
            toggleMetodeBiasa();
            updateCart();
            setTimeout(() => location.reload(), 2500);
        } else {
            showToast('danger', data.message || 'Transaksi gagal!');
        }
    })
    .catch(() => showToast('danger','Terjadi kesalahan jaringan!'));
}

// ── PROSES TRANSAKSI BOOKING ──────────────────────────────────
function processBookingTransaction() {
    if (!selectedBooking)    { showToast('warning','Pilih booking dulu!'); return; }
    if (!cartBooking.length) { showToast('warning','Tambahkan produk ke keranjang!'); return; }

    const total      = cartBooking.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const sisa       = Math.max(0, total - selectedBooking.dp);
    const isTransfer = document.getElementById('metodeTransferBooking').checked;
    const metode     = isTransfer ? 'transfer' : 'cash';
    const bayar      = isTransfer ? sisa : (parseInt(document.getElementById('bUangBayar').value) || 0);
    const refNo      = isTransfer ? (document.getElementById('bRefTransfer').value.trim() || null) : null;

    if (!isTransfer && bayar < sisa) { showToast('warning','Uang bayar kurang dari sisa tagihan!'); return; }

    fetch('{{ route("kasir.transactions.storeFromBooking") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            booking_id   : selectedBooking.id,
            items        : cartBooking.map(i => ({ id_produk: i.id, jumlah: i.jumlah })),
            uang_bayar   : bayar,
            metode_bayar : metode,
            no_referensi : refNo,
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            lastTransactionNumber = data.data.nomor_unik;
            tampilModal(
                data.data.total_harga, data.data.jumlah_dp,
                data.data.sisa_tagihan, data.data.uang_bayar,
                data.data.uang_kembali, data.data.nomor_unik, metode
            );
            cartBooking     = [];
            selectedBooking = null;
            document.getElementById('bookingDetail').style.display = 'none';
            document.querySelectorAll('.booking-card').forEach(c => c.classList.remove('selected'));
            setTimeout(() => location.reload(), 2500);
        } else {
            showToast('danger', data.message || 'Transaksi gagal!');
        }
    })
    .catch(() => showToast('danger','Terjadi kesalahan jaringan!'));
}

// ── MODAL SUKSES ──────────────────────────────────────────────
function tampilModal(total, dp, sisa, bayar, kembali, nomor, metode) {
    document.getElementById('modalNomorUnik').textContent   = nomor;
    document.getElementById('modalTotalHarga').textContent  = 'Rp ' + fmt(total);
    document.getElementById('modalUangBayar').textContent   = 'Rp ' + fmt(bayar);
    document.getElementById('modalUangKembali').textContent = 'Rp ' + fmt(kembali);
    document.getElementById('modalMetode').textContent      =
        metode === 'transfer' ? '🏦 Transfer Bank' : '💵 Cash';

    // Sembunyikan baris kembali jika transfer
    document.getElementById('modalKembaliRow').style.display =
        metode === 'transfer' ? 'none' : 'flex';

    if (dp !== null) {
        document.getElementById('modalDp').textContent   = 'Rp ' + fmt(dp);
        document.getElementById('modalSisa').textContent = 'Rp ' + fmt(sisa);
        document.getElementById('modalDpRow').style.display   = 'flex';
        document.getElementById('modalSisaRow').style.display = 'flex';
    } else {
        document.getElementById('modalDpRow').style.display   = 'none';
        document.getElementById('modalSisaRow').style.display = 'none';
    }
    new bootstrap.Modal(document.getElementById('successModal')).show();
}

// ── HELPERS ───────────────────────────────────────────────────
function toggleMejaField() {
    const show = document.getElementById('jenisPemesanan').value === 'dine_in';
    document.getElementById('mejaField').style.display = show ? 'block' : 'none';
    if (!show) document.getElementById('idMeja').value = '';
}

function resetCart() {
    showConfirm({
        icon: '<i class="bi bi-arrow-clockwise"></i>',
        iconType: 'warning',
        title: 'Reset Keranjang',
        desc: 'Yakin ingin mereset semua isi keranjang?',
        btnType: 'warning',
        btnLabel: 'Ya, Reset',
        onYes: () => {
            cart = []; cartBooking = []; selectedBooking = null;
            document.getElementById('namaPelanggan').value      = '';
            document.getElementById('uangBayar').value          = '';
            document.getElementById('uangKembali').value        = '';
            document.getElementById('jenisPemesanan').value     = 'dine_in';
            document.getElementById('idMeja').value             = '';
            document.getElementById('metodeCashBiasa').checked  = true;
            document.getElementById('metodeCashBooking').checked = true;
            toggleMejaField();
            toggleMetodeBiasa();
            toggleMetodeBooking();
            updateCart();
            document.getElementById('bookingDetail').style.display = 'none';
            document.querySelectorAll('.booking-card').forEach(c => c.classList.remove('selected'));
            showToast('success', 'Keranjang berhasil direset!');
        }
    });
}

function printReceipt() {
    window.open(`/kasir/transactions/print/${lastTransactionNumber}`, '_blank');
}

function fmt(num) {
    return (num || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

document.addEventListener('DOMContentLoaded', function() {
    toggleMejaField();
    toggleMetodeBiasa();
});
</script>
@endpush