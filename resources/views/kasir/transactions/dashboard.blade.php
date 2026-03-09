@extends('kasir.layouts.app')

@section('title', 'Transaksi Baru')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-cart me-2"></i>Transaksi Baru
        </h4>
        <small class="text-muted">Pilih produk untuk memulai transaksi</small>
    </div>
    <div>
        <button class="btn btn-primary-custom" onclick="resetCart()">
            <i class="bi bi-arrow-clockwise me-2"></i>Reset Keranjang
        </button>
    </div>
</div>

<div class="row">
    <!-- Daftar Produk (Kiri) -->
    <div class="col-md-8">
        <!-- Filter Kategori -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-success active" onclick="filterCategory('all')">
                    Semua
                </button>
                @foreach($categories as $category)
                <button type="button" class="btn btn-outline-success" onclick="filterCategory('{{ $category->id }}')">
                    {{ $category->nama_kategori }} ({{ $category->products_count }})
                </button>
                @endforeach
            </div>
        </div>

        <!-- Grid Produk -->
        <div class="row" id="productGrid">
            @forelse($products as $product)
            <div class="col-md-4 col-lg-3 mb-3 product-item" data-category="{{ $product->kategori_id }}">
                <div class="product-card" onclick="addToCart({{ json_encode($product) }})">
                    @if($product->foto)
                        <img src="{{ asset('uploads/products/' . $product->foto) }}" class="product-img" alt="{{ $product->nama_produk }}">
                    @else
                        <div class="product-img" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-image" style="font-size: 48px; color: #ccc;"></i>
                        </div>
                    @endif
                    
                    <div class="product-name">{{ $product->nama_produk }}</div>
                    <div class="product-price">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                    <div class="product-stock">
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
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 64px; color: #ccc;"></i>
                    <p class="mt-3 text-muted">Tidak ada produk tersedia</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Keranjang (Kanan) -->
    <div class="col-md-4">
        <div class="cart-container">
            <h5 class="mb-3">
                <i class="bi bi-cart3 me-2"></i>Keranjang Belanja
            </h5>

            <div id="cartItems" class="mb-3">
                <div class="text-center text-muted py-4">
                    <i class="bi bi-cart-x" style="font-size: 48px;"></i>
                    <p class="mt-2">Keranjang masih kosong</p>
                </div>
            </div>

            <div class="cart-total">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Total Item:</strong>
                    <span id="totalItems">0</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total Harga:</strong>
                    <strong class="text-success" id="totalPrice">Rp 0</strong>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control" id="namaPelanggan" placeholder="Masukkan nama pelanggan">
                </div>

                <div class="mb-3">
                    <label class="form-label">Uang Bayar</label>
                    <input type="number" class="form-control" id="uangBayar" placeholder="0" oninput="calculateChange()">
                </div>

                <div class="mb-3">
                    <label class="form-label">Uang Kembali</label>
                    <input type="text" class="form-control" id="uangKembali" readonly style="background: #f8f9fa; font-weight: 600;">
                </div>

                <button class="btn btn-primary-custom w-100" onclick="processTransaction()">
                    <i class="bi bi-check-circle me-2"></i>Proses Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Transaksi Berhasil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 64px;"></i>
                <h4 class="mt-3">Pembayaran Berhasil!</h4>
                <p class="mb-0">Nomor Transaksi: <strong id="modalNomorUnik"></strong></p>
                <hr>
                <div class="text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Harga:</span>
                        <strong id="modalTotalHarga">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Uang Bayar:</span>
                        <strong id="modalUangBayar">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Uang Kembali:</span>
                        <strong class="text-success" id="modalUangKembali">Rp 0</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary-custom" onclick="printReceipt()">
                    <i class="bi bi-printer me-2"></i>Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];
    let lastTransactionNumber = '';

    // Filter kategori
    function filterCategory(categoryId) {
        const buttons = document.querySelectorAll('.btn-group button');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        const products = document.querySelectorAll('.product-item');
        products.forEach(product => {
            if (categoryId === 'all' || product.dataset.category == categoryId) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    }

    // Tambah ke keranjang
    function addToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);
        
        if (existingItem) {
            if (existingItem.jumlah < product.stok) {
                existingItem.jumlah++;
            } else {
                alert(`Stok ${product.nama_produk} tidak mencukupi!`);
                return;
            }
        } else {
            cart.push({
                id: product.id,
                nama: product.nama_produk,
                harga: product.harga_produk,
                stok: product.stok,
                jumlah: 1
            });
        }
        
        updateCart();
    }

    // Update tampilan keranjang
    function updateCart() {
        const cartItemsDiv = document.getElementById('cartItems');
        
        if (cart.length === 0) {
            cartItemsDiv.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-cart-x" style="font-size: 48px;"></i>
                    <p class="mt-2">Keranjang masih kosong</p>
                </div>
            `;
        } else {
            let html = '';
            cart.forEach((item, index) => {
                html += `
                    <div class="cart-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>${item.nama}</strong>
                            <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQty(${index})">-</button>
                                <span class="mx-2">${item.jumlah}</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="increaseQty(${index})">+</button>
                            </div>
                            <strong class="text-success">Rp ${formatNumber(item.harga * item.jumlah)}</strong>
                        </div>
                    </div>
                `;
            });
            cartItemsDiv.innerHTML = html;
        }
        
        updateTotal();
    }

    // Update total
    function updateTotal() {
        const totalItems = cart.reduce((sum, item) => sum + item.jumlah, 0);
        const totalPrice = cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        
        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalPrice').textContent = 'Rp ' + formatNumber(totalPrice);
        
        calculateChange();
    }

    // Hitung kembalian
    function calculateChange() {
        const totalPrice = cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        const uangBayar = parseInt(document.getElementById('uangBayar').value) || 0;
        const kembalian = uangBayar - totalPrice;
        
        document.getElementById('uangKembali').value = kembalian >= 0 ? 'Rp ' + formatNumber(kembalian) : 'Rp 0';
    }

    // Kurangi jumlah
    function decreaseQty(index) {
        if (cart[index].jumlah > 1) {
            cart[index].jumlah--;
            updateCart();
        }
    }

    // Tambah jumlah
    function increaseQty(index) {
        if (cart[index].jumlah < cart[index].stok) {
            cart[index].jumlah++;
            updateCart();
        } else {
            alert(`Stok ${cart[index].nama} tidak mencukupi!`);
        }
    }

    // Hapus dari keranjang
    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCart();
    }

    // Reset keranjang
    function resetCart() {
        if (confirm('Yakin ingin reset keranjang?')) {
            cart = [];
            document.getElementById('namaPelanggan').value = '';
            document.getElementById('uangBayar').value = '';
            document.getElementById('uangKembali').value = '';
            updateCart();
        }
    }

    // Proses transaksi
    function processTransaction() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }
        
        const namaPelanggan = document.getElementById('namaPelanggan').value;
        if (!namaPelanggan) {
            alert('Nama pelanggan harus diisi!');
            return;
        }
        
        const uangBayar = parseInt(document.getElementById('uangBayar').value) || 0;
        const totalPrice = cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        
        if (uangBayar < totalPrice) {
            alert('Uang bayar kurang dari total harga!');
            return;
        }
        
        // Kirim ke server
        const items = cart.map(item => ({
            id_produk: item.id,
            jumlah: item.jumlah
        }));
        
        fetch('{{ route("kasir.transactions.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                items: items,
                nama_pelanggan: namaPelanggan,
                uang_bayar: uangBayar
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                lastTransactionNumber = data.data.nomor_unik;
                
                // Tampilkan modal sukses
                document.getElementById('modalNomorUnik').textContent = data.data.nomor_unik;
                document.getElementById('modalTotalHarga').textContent = 'Rp ' + formatNumber(data.data.total_harga);
                document.getElementById('modalUangBayar').textContent = 'Rp ' + formatNumber(data.data.uang_bayar);
                document.getElementById('modalUangKembali').textContent = 'Rp ' + formatNumber(data.data.uang_kembali);
                
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
                
                // Reset keranjang
                cart = [];
                document.getElementById('namaPelanggan').value = '';
                document.getElementById('uangBayar').value = '';
                document.getElementById('uangKembali').value = '';
                updateCart();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }

    // Cetak struk
    function printReceipt() {
        window.open(`/kasir/transactions/print/${lastTransactionNumber}`, '_blank');
    }

    // Format angka
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
@endpush