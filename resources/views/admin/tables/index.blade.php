@extends('admin.layouts.app')

@section('title', 'Kelola Meja')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-table me-2"></i>Kelola Meja
        </h4>
        <small class="text-muted">Manajemen meja rumah makan</small>
    </div>
    <div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-2"></i>Tambah Meja
        </button>
    </div>
</div>

<!-- Statistik Meja - 3 Card di Tengah -->
<div class="row mb-4 justify-content-center">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-table text-primary" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $tables->count() }}</h3>
                <p class="text-muted mb-0">Total Meja</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $tables->where('status', 'tersedia')->count() }}</h3>
                <p class="text-muted mb-0">Tersedia</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-bookmark text-warning" style="font-size: 48px;"></i>
                <h3 class="mt-3">{{ $tables->where('status', 'reserved')->count() }}</h3>
                <p class="text-muted mb-0">Reserved</p>
            </div>
        </div>
    </div>
</div>

<!-- Grid Meja -->
<div class="table-container">
    <div class="row">
        @forelse($tables as $table)
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100 
                {{ $table->status == 'tersedia' ? 'border-success' : ($table->status == 'terisi' ? 'border-danger' : 'border-warning') }}" 
                style="border-width: 3px;">
                <div class="card-body text-center">
                    <i class="bi bi-table" style="font-size: 48px; color: 
                        {{ $table->status == 'tersedia' ? '#28a745' : ($table->status == 'terisi' ? '#dc3545' : '#ffc107') }};">
                    </i>
                    <h4 class="mt-3">Meja {{ $table->nomor_meja }}</h4>
                    <p class="mb-2">
                        <i class="bi bi-people me-1"></i>Kapasitas: {{ $table->kapasitas }} orang
                    </p>
                    <span class="badge 
                        {{ $table->status == 'tersedia' ? 'bg-success' : ($table->status == 'terisi' ? 'bg-danger' : 'bg-warning') }}">
                        {{ ucfirst($table->status) }}
                    </span>
                    @if($table->keterangan)
                    <p class="mt-2 mb-0 text-muted small">
                        <i class="bi bi-info-circle me-1"></i>{{ $table->keterangan }}
                    </p>
                    @endif

                    <hr>

                    <div class="d-flex gap-2 justify-content-center flex-wrap">

                        {{-- Tombol Edit (hanya jika tidak terisi) --}}
                        @if($table->status != 'terisi')
                        <button class="btn btn-sm btn-warning" 
                                onclick="editTable({{ $table->id }}, '{{ $table->nomor_meja }}', {{ $table->kapasitas }}, '{{ addslashes($table->keterangan) }}')"
                                title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif

                        {{-- Tombol Toggle Status (tersedia ↔ reserved, hanya jika tidak terisi) --}}
                        @if($table->status != 'terisi')
                        <button class="btn btn-sm btn-info"
                                onclick="confirmStatusChange({{ $table->id }}, '{{ $table->status }}', '{{ addslashes($table->nomor_meja) }}')"
                                title="Ubah Status">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                        @endif

                        {{-- Tombol Selesai / Pelanggan Pulang (hanya jika terisi) --}}
                        @if($table->status == 'terisi')
                        <button class="btn btn-sm btn-success"
                                onclick="confirmFinish({{ $table->id }}, '{{ addslashes($table->nomor_meja) }}')"
                                title="Tandai Selesai">
                            <i class="bi bi-check-circle me-1"></i> Selesai
                        </button>
                        @endif

                        {{-- Tombol Reservasi (hanya jika tersedia) --}}
                        @if($table->status == 'tersedia')
                        <button class="btn btn-sm btn-primary"
                                onclick="confirmReserve({{ $table->id }}, '{{ addslashes($table->nomor_meja) }}')"
                                title="Reservasi Meja">
                            <i class="bi bi-calendar-check me-1"></i> Reservasi
                        </button>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 64px; color: #ccc;"></i>
                <p class="mt-3 text-muted">Belum ada data meja</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Meja -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #FF8C42; color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Meja Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahMeja" action="{{ route('admin.tables.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Meja <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nomor_meja" id="tambah_nomor" required placeholder="Contoh: 1, A1, VIP-1">
                        <div class="invalid-feedback">Nomor meja harus diisi</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="kapasitas" id="tambah_kapasitas" required min="1" placeholder="Jumlah orang">
                        <div class="invalid-feedback">Kapasitas harus diisi minimal 1 orang</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="tambah_keterangan" rows="3" placeholder="Contoh: Meja dekat jendela"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-2"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Meja -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #FF8C42; color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Meja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Meja <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nomor_meja" id="edit_nomor" required>
                        <div class="invalid-feedback">Nomor meja harus diisi</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="kapasitas" id="edit_kapasitas" required min="1">
                        <div class="invalid-feedback">Kapasitas harus diisi minimal 1 orang</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="edit_keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-2"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Status Change Tersembunyi -->
<form id="statusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

<!-- Form Selesai Tersembunyi -->
<form id="finishForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

<!-- Form Reservasi Tersembunyi -->
<form id="reserveForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

@endsection

@push('scripts')
<script>
    // Fungsi validasi form tambah meja
    function validateTambahForm() {
        let isValid = true;
        const nomor = document.getElementById('tambah_nomor');
        const kapasitas = document.getElementById('tambah_kapasitas');
        
        nomor.classList.remove('is-invalid');
        kapasitas.classList.remove('is-invalid');
        
        if (!nomor.value.trim()) {
            nomor.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!kapasitas.value || parseInt(kapasitas.value) < 1) {
            kapasitas.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Fungsi validasi form edit meja
    function validateEditForm() {
        let isValid = true;
        const nomor = document.getElementById('edit_nomor');
        const kapasitas = document.getElementById('edit_kapasitas');
        
        nomor.classList.remove('is-invalid');
        kapasitas.classList.remove('is-invalid');
        
        if (!nomor.value.trim()) {
            nomor.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!kapasitas.value || parseInt(kapasitas.value) < 1) {
            kapasitas.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Fungsi Edit Meja
    function editTable(id, nomorMeja, kapasitas, keterangan) {
        document.getElementById('formEdit').action = `/admin/tables/${id}`;
        document.getElementById('edit_nomor').value = nomorMeja;
        document.getElementById('edit_kapasitas').value = kapasitas;
        document.getElementById('edit_keterangan').value = keterangan || '';
        
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }
    
    // Konfirmasi Ubah Status (tersedia ↔ reserved)
    function confirmStatusChange(tableId, currentStatus, tableName) {
        let actionText = currentStatus === 'tersedia' ? 'reservasi' : 'jadikan tersedia';
        
        Swal.fire({
            title: `Konfirmasi ${actionText}`,
            html: `Apakah Anda yakin ingin ${actionText} <strong>Meja ${tableName}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FF8C42',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Ya, ${actionText}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('statusForm');
                form.action = `/admin/tables/${tableId}/status`;
                form.submit();
                
                Swal.fire({
                    title: 'Memproses...',
                    text: `Sedang ${actionText} meja`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }
    
    // Konfirmasi Reservasi (dari tersedia ke reserved)
    function confirmReserve(tableId, tableName) {
        Swal.fire({
            title: 'Reservasi Meja',
            html: `
                <div style="text-align: left">
                    <p>Apakah Anda yakin ingin melakukan reservasi untuk <strong>Meja ${tableName}</strong>?</p>
                    <label class="form-label mt-2">Nama Pelanggan (Opsional)</label>
                    <input type="text" id="customerName" class="form-control" placeholder="Masukkan nama pelanggan">
                    <label class="form-label mt-2">Keterangan Reservasi (Opsional)</label>
                    <textarea id="reserveNote" class="form-control" rows="2" placeholder="Catatan reservasi"></textarea>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#FF8C42',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reservasi!',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const customerName = document.getElementById('customerName').value;
                const reserveNote = document.getElementById('reserveNote').value;
                return { customerName: customerName, reserveNote: reserveNote };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('reserveForm');
                form.action = `/admin/tables/${tableId}/reserve`;
                
                let customerInput = document.createElement('input');
                customerInput.type = 'hidden';
                customerInput.name = 'customer_name';
                customerInput.value = result.value.customerName;
                form.appendChild(customerInput);
                
                let noteInput = document.createElement('input');
                noteInput.type = 'hidden';
                noteInput.name = 'reserve_note';
                noteInput.value = result.value.reserveNote;
                form.appendChild(noteInput);
                
                form.submit();
                
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang melakukan reservasi meja',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }
    
    // Konfirmasi Selesai (pelanggan pulang)
    function confirmFinish(tableId, tableName) {
        Swal.fire({
            title: 'Tandai Selesai?',
            html: `Apakah pelanggan di <strong>Meja ${tableName}</strong> sudah selesai?<br>Meja akan dikosongkan dan status menjadi tersedia.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('finishForm');
                form.action = `/admin/tables/${tableId}/tersedia`;
                form.submit();
                
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengosongkan meja',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }
    
    // Intercept form submit untuk Tambah dan Edit
    document.addEventListener('DOMContentLoaded', function() {
        // Tangkap form tambah meja
        const tambahForm = document.getElementById('formTambahMeja');
        if (tambahForm) {
            tambahForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!validateTambahForm()) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        html: 'Mohon lengkapi field yang wajib diisi:<br>• Nomor Meja<br>• Kapasitas',
                        icon: 'warning',
                        confirmButtonColor: '#FF8C42',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Simpan Meja?',
                    text: "Pastikan data meja sudah benar sebelum disimpan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF8C42',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Sedang menambahkan meja',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        tambahForm.submit();
                    }
                });
            });
        }
        
        // Tangkap form edit meja
        const editForm = document.getElementById('formEdit');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!validateEditForm()) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        html: 'Mohon lengkapi field yang wajib diisi:<br>• Nomor Meja<br>• Kapasitas',
                        icon: 'warning',
                        confirmButtonColor: '#FF8C42',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Update Meja?',
                    text: "Apakah Anda yakin ingin mengubah data meja ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF8C42',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Update!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mengupdate...',
                            text: 'Sedang memperbarui meja',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        editForm.submit();
                    }
                });
            });
        }
    });
    
    // Menampilkan notifikasi sukses/error jika ada session flash
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#FF8C42',
            timer: 3000,
            showConfirmButton: true
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Gagal!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#FF8C42'
        });
    @endif
</script>
@endpush