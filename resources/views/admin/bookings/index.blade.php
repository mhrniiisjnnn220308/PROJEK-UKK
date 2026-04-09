@extends('admin.layouts.app')

@section('title', 'Kelola Booking')

@section('content')

<div class="page-header">
    <div>
        <h4 class="page-title mb-0">
            <i class="bi bi-calendar-check me-2"></i>Kelola Booking
        </h4>
        <small class="text-muted">Manajemen reservasi meja pelanggan</small>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-2"></i>Tambah Booking
    </button>
</div>

@php
    $totalBooking = $bookings->count();
    $pending      = $bookings->where('status', 'pending')->count();
    $selesai      = $bookings->where('status', 'selesai')->count();
    $totalDp      = $bookings->whereIn('status', ['pending','konfirmasi','selesai'])->sum('jumlah_dp');
@endphp

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm text-center p-3">
            <i class="bi bi-calendar2-week text-primary" style="font-size:40px;"></i>
            <h3 class="mt-2">{{ $totalBooking }}</h3>
            <p class="text-muted mb-0">Total Booking</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center p-3">
            <i class="bi bi-hourglass-split text-warning" style="font-size:40px;"></i>
            <h3 class="mt-2">{{ $pending }}</h3>
            <p class="text-muted mb-0">Menunggu Konfirmasi</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center p-3">
            <i class="bi bi-patch-check text-success" style="font-size:40px;"></i>
            <h3 class="mt-2">{{ $selesai }}</h3>
            <p class="text-muted mb-0">Total Lunas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center p-3">
            <i class="bi bi-cash-stack text-info" style="font-size:40px;"></i>
            <h3 class="mt-2">Rp {{ number_format($totalDp, 0, ',', '.') }}</h3>
            <p class="text-muted mb-0">Total DP Masuk</p>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Catatan Pesanan</th>
                    <th>DP & Bukti</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $i => $booking)
                <tr>
                    <td>{{ $i + 1 }}</td>

                    <td>
                        <strong>{{ $booking->nama_pelanggan }}</strong>
                        <small class="text-muted d-block">{{ $booking->no_hp }}</small>
                    </td>

                    <td>
                        <span class="badge bg-primary">
                            {{ $booking->meja->nomor_meja ?? '-' }}
                        </span>
                        <small class="text-muted d-block">
                            {{ $booking->meja->kapasitas ?? '-' }} orang
                        </small>
                    </td>

                    <td>{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->jam_kedatangan)->format('H:i') }}</td>
                    <td><small>{{ $booking->catatan_pesanan ?? '-' }}</small></td>

                    {{-- Kolom DP & Bukti Transfer --}}
                    <td>
                        <strong class="text-success">
                            Rp {{ number_format($booking->jumlah_dp, 0, ',', '.') }}
                        </strong>
                        <small class="text-muted d-block">DP awal</small>

                        {{-- Badge status DP --}}
                        @if($booking->status === 'selesai')
                            <span class="badge bg-success mt-1">Lunas</span>
                        @elseif($booking->status === 'batal')
                            <span class="badge bg-secondary mt-1">Batal</span>
                        @elseif($booking->dp_verified)
                            <span class="badge bg-info mt-1">
                                <i class="bi bi-check-circle me-1"></i>DP Terverifikasi
                            </span>
                        @else
                            <span class="badge bg-warning text-dark mt-1">Belum Verifikasi</span>
                        @endif

                        {{-- Tombol lihat/upload bukti --}}
                        @if($booking->bukti_dp)
                            <div class="mt-1">
                                <button class="btn btn-sm btn-outline-info py-0 px-2"
                                        onclick="lihatBukti('{{ asset('uploads/bukti_dp/' . $booking->bukti_dp) }}', '{{ $booking->nama_pelanggan }}')"
                                        title="Lihat Foto Bukti Transfer">
                                    <i class="bi bi-image me-1"></i><small>Lihat Bukti</small>
                                </button>
                                @if(!$booking->dp_verified && $booking->status !== 'batal')
                                    <button class="btn btn-sm btn-outline-success py-0 px-2 mt-1"
                                            onclick="verifikasiDp({{ $booking->id }}, '{{ addslashes($booking->nama_pelanggan) }}')"
                                            title="Verifikasi DP">
                                        <i class="bi bi-check-lg me-1"></i><small>Verifikasi</small>
                                    </button>
                                @endif
                            </div>
                        @elseif(!in_array($booking->status, ['selesai','batal']))
                            <div class="mt-1">
                                <button class="btn btn-sm btn-outline-warning py-0 px-2"
                                        onclick="openUploadBukti({{ $booking->id }}, '{{ addslashes($booking->nama_pelanggan) }}')"
                                        title="Upload Bukti Transfer">
                                    <i class="bi bi-upload me-1"></i><small>Upload Bukti</small>
                                </button>
                            </div>
                        @else
                            <small class="text-muted d-block mt-1">Tidak ada bukti</small>
                        @endif
                    </td>

                    <td>
                        @php
                            $badgeClass = match($booking->status) {
                                'pending'    => 'bg-warning text-dark',
                                'konfirmasi' => 'bg-success',
                                'selesai'    => 'bg-secondary',
                                'batal'      => 'bg-danger',
                                default      => 'bg-secondary',
                            };
                            $statusLabel = match($booking->status) {
                                'pending'    => 'Menunggu',
                                'konfirmasi' => 'Terkonfirmasi',
                                'selesai'    => 'Selesai',
                                'batal'      => 'Dibatalkan',
                                default      => $booking->status,
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>

                    <td class="text-nowrap">

                        {{-- Edit: hanya pending / konfirmasi --}}
                        @if(in_array($booking->status, ['pending', 'konfirmasi']))
                        <button class="btn btn-warning btn-action"
                                data-id="{{ $booking->id }}"
                                data-nama="{{ $booking->nama_pelanggan }}"
                                data-nohp="{{ $booking->no_hp }}"
                                data-meja="{{ $booking->id_meja }}"
                                data-dp="{{ $booking->jumlah_dp }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('Y-m-d') }}"
                                data-jam="{{ \Carbon\Carbon::parse($booking->jam_kedatangan)->format('H:i') }}"
                                data-catatan="{{ $booking->catatan_pesanan }}"
                                onclick="editBooking(this)"
                                title="Edit Booking">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif

                        {{-- Konfirmasi: hanya pending dan dp sudah terverifikasi --}}
                        @if($booking->status === 'pending' && $booking->dp_verified)
                        <button class="btn btn-success btn-action"
                                onclick="confirmKonfirmasi({{ $booking->id }}, '{{ addslashes($booking->nama_pelanggan) }}')"
                                title="Konfirmasi booking ini">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        @elseif($booking->status === 'pending' && !$booking->dp_verified)
                        {{-- Konfirmasi langsung meski tanpa bukti (opsional, misal bayar tunai DP) --}}
                        <button class="btn btn-outline-success btn-action"
                                onclick="confirmKonfirmasiTanpaBukti({{ $booking->id }}, '{{ addslashes($booking->nama_pelanggan) }}')"
                                title="Konfirmasi tanpa verifikasi bukti">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        @endif

                        {{-- Batal: hanya pending / konfirmasi --}}
                        @if(in_array($booking->status, ['pending','konfirmasi']))
                        <button class="btn btn-danger btn-action"
                                onclick="confirmBatal({{ $booking->id }}, '{{ addslashes($booking->nama_pelanggan) }}')"
                                title="Batalkan booking ini">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif

                        {{-- Selesai: tampilkan struk jika ada transaksi --}}
                        @if($booking->status === 'selesai')
                            @if($booking->transaksi)
                                <a href="{{ route('kasir.transactions.print', $booking->transaksi->nomor_unik) }}"
                                   target="_blank"
                                   class="btn btn-info btn-action"
                                   title="Lihat Bukti Pembayaran / Struk">
                                    <i class="bi bi-receipt"></i>
                                </a>
                            @else
                                <button class="btn btn-secondary btn-action"
                                        onclick="confirmHapusBooking({{ $booking->id }}, '{{ addslashes($booking->nama_pelanggan) }}')"
                                        title="Hapus Permanen">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        @endif

                        {{-- Batal: boleh hapus permanen --}}
                        @if($booking->status === 'batal')
                        <button class="btn btn-secondary btn-action"
                                onclick="confirmHapusBooking({{ $booking->id }}, '{{ addslashes($booking->nama_pelanggan) }}')"
                                title="Hapus Permanen">
                                <i class="bi bi-trash"></i>
                        </button>
                        @endif

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-calendar-x" style="font-size:48px;color:#ccc;"></i>
                        <p class="mt-3 text-muted">Belum ada data booking</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 px-1">
    <small class="text-muted">
        <i class="bi bi-pencil text-warning"></i> Edit &nbsp;|&nbsp;
        <i class="bi bi-check-lg text-success"></i> Konfirmasi &nbsp;|&nbsp;
        <i class="bi bi-x-circle text-danger"></i> Batalkan &nbsp;|&nbsp;
        <i class="bi bi-receipt text-info"></i> Lihat Struk &nbsp;|&nbsp;
        <i class="bi bi-trash text-secondary"></i> Hapus permanen &nbsp;|&nbsp;
        <i class="bi bi-image text-info"></i> Lihat Bukti Transfer
    </small>
</div>

{{-- ================================================================== --}}
{{-- Modal Tambah Booking                                                 --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#FF8C42;color:white;">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-plus me-2"></i>Tambah Booking Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bookings.store') }}" method="POST"
                  id="formTambah" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_pelanggan"
                                   id="tambah_nama" placeholder="Nama lengkap pelanggan" required>
                            <div class="invalid-feedback">Nama pelanggan harus diisi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_hp"
                                   id="tambah_nohp" placeholder="08xxxxxxxxxx" required>
                            <div class="invalid-feedback">No HP harus diisi</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Meja <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_meja" id="tambah_meja" required>
                                <option value="">-- Pilih Meja --</option>
                                @foreach($tables as $table)
                                <option value="{{ $table->id }}">
                                    {{ $table->nomor_meja }}
                                    ({{ $table->kapasitas }} orang)
                                    — {{ ucfirst($table->status) }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Meja harus dipilih</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah DP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="jumlah_dp"
                                       id="tambah_dp" placeholder="0" min="0" required>
                            </div>
                            <div class="invalid-feedback">Jumlah DP harus diisi</div>
                            <small class="text-muted">DP adalah uang muka tanda jadi booking.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Kedatangan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_booking"
                                   id="tambah_tanggal" min="{{ date('Y-m-d') }}" required>
                            <div class="invalid-feedback">Tanggal kedatangan harus diisi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Kedatangan <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="jam_kedatangan"
                                   id="tambah_jam" required>
                            <div class="invalid-feedback">Jam kedatangan harus diisi</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Pesanan</label>
                        <textarea class="form-control" name="catatan_pesanan" id="tambah_catatan" rows="3"
                                  placeholder="Contoh: 2 Nasi Goreng, 1 Ayam Goreng, tidak suka pedas..."></textarea>
                        <small class="text-muted">Opsional — isi jika pelanggan sudah memesan sebelumnya</small>
                    </div>

                    {{-- Upload Bukti Transfer DP --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Foto Bukti Transfer DP
                            <span class="badge bg-secondary ms-1">Opsional</span>
                        </label>
                        <input type="file" class="form-control" name="bukti_dp"
                               id="tambah_bukti_dp" accept="image/*"
                               onchange="previewBukti(this, 'tambah_preview_bukti')">
                        <small class="text-muted">Format: JPG, PNG, JPEG. Maks 2MB. Bisa diupload belakangan.</small>
                        <div class="mt-2" id="tambah_preview_bukti" style="display:none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-height:160px;">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Simpan Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- Modal Edit Booking                                                   --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#FF8C42;color:white;">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_pelanggan"
                                   id="edit_nama_pelanggan" placeholder="Nama lengkap pelanggan" required>
                            <div class="invalid-feedback">Nama pelanggan harus diisi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_hp"
                                   id="edit_no_hp" placeholder="08xxxxxxxxxx" required>
                            <div class="invalid-feedback">No HP harus diisi</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Meja <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_meja" id="edit_id_meja" required>
                                <option value="">-- Pilih Meja --</option>
                                @foreach($tables as $table)
                                <option value="{{ $table->id }}">
                                    {{ $table->nomor_meja }}
                                    ({{ $table->kapasitas }} orang)
                                    — {{ ucfirst($table->status) }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Meja harus dipilih</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah DP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="jumlah_dp"
                                       id="edit_jumlah_dp" placeholder="0" min="0" required>
                            </div>
                            <div class="invalid-feedback">Jumlah DP harus diisi</div>
                            <small class="text-muted">DP adalah uang muka tanda jadi booking.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Kedatangan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_booking"
                                   id="edit_tanggal_booking" required>
                            <div class="invalid-feedback">Tanggal kedatangan harus diisi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Kedatangan <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="jam_kedatangan"
                                   id="edit_jam_kedatangan" required>
                            <div class="invalid-feedback">Jam kedatangan harus diisi</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Pesanan</label>
                        <textarea class="form-control" name="catatan_pesanan"
                                  id="edit_catatan_pesanan" rows="3"
                                  placeholder="Contoh: 2 Nasi Goreng, 1 Ayam Goreng, tidak suka pedas..."></textarea>
                        <small class="text-muted">Opsional — isi jika pelanggan sudah memesan sebelumnya</small>
                    </div>

                    {{-- Upload / Ganti Bukti Transfer DP --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Foto Bukti Transfer DP
                            <span class="badge bg-secondary ms-1">Opsional</span>
                        </label>
                        <div id="edit_existing_bukti" class="mb-2" style="display:none;">
                            <small class="text-muted">Bukti saat ini:</small><br>
                            <img id="edit_existing_img" src="" alt="Bukti DP" class="img-thumbnail mt-1" style="max-height:120px;">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="hapus_bukti_dp"
                                       id="edit_hapus_bukti" value="1">
                                <label class="form-check-label text-danger" for="edit_hapus_bukti">
                                    Hapus foto bukti ini
                                </label>
                            </div>
                        </div>
                        <input type="file" class="form-control" name="bukti_dp"
                               id="edit_bukti_dp" accept="image/*"
                               onchange="previewBukti(this, 'edit_preview_bukti')">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti foto. Format: JPG, PNG. Maks 2MB.</small>
                        <div class="mt-2" id="edit_preview_bukti" style="display:none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-height:160px;">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Update Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- Modal Upload Bukti DP (standalone, bisa diakses dari tabel)          --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalUploadBukti" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#0dcaf0;color:white;">
                <h5 class="modal-title">
                    <i class="bi bi-upload me-2"></i>Upload Bukti Transfer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUploadBukti" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p class="mb-2">
                        Booking: <strong id="uploadBuktiNama"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto Bukti <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="bukti_dp"
                               id="upload_bukti_file" accept="image/*" required
                               onchange="previewBukti(this, 'upload_preview')">
                        <small class="text-muted">JPG, PNG, JPEG. Maks 2MB.</small>
                    </div>
                    <div id="upload_preview" style="display:none;">
                        <img src="" alt="Preview" class="img-thumbnail w-100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-upload me-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- Modal Lihat Foto Bukti Transfer                                      --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalLihatBukti" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#0dcaf0;color:white;">
                <h5 class="modal-title">
                    <i class="bi bi-image me-2"></i>Bukti Transfer DP — <span id="lihatBuktiNama"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="lihatBuktiImg" src="" alt="Bukti Transfer" class="img-fluid rounded shadow-sm">
            </div>
            <div class="modal-footer">
                <a id="lihatBuktiDownload" href="" download target="_blank"
                   class="btn btn-outline-info">
                    <i class="bi bi-download me-1"></i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Forms untuk aksi status --}}
<form id="form-konfirmasi" method="POST" style="display:none;">
    @csrf
    @method('PUT')
</form>
<form id="form-batal" method="POST" style="display:none;">
    @csrf
    @method('PUT')
</form>
<form id="form-hapus" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
<form id="form-verifikasi-dp" method="POST" style="display:none;">
    @csrf
    @method('PUT')
</form>

@endsection

@push('scripts')
<script>
    // ----------------------------------------------------------------
    // Preview foto sebelum upload
    // ----------------------------------------------------------------
    function previewBukti(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const file = input.files[0];
            // Validasi ukuran (2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({ title: 'File terlalu besar!', text: 'Maksimal ukuran file 2MB.', icon: 'warning', confirmButtonColor: '#FF8C42' });
                input.value = '';
                preview.style.display = 'none';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    // ----------------------------------------------------------------
    // Lihat foto bukti transfer
    // ----------------------------------------------------------------
    function lihatBukti(url, nama) {
        document.getElementById('lihatBuktiImg').src = url;
        document.getElementById('lihatBuktiNama').textContent = nama;
        document.getElementById('lihatBuktiDownload').href = url;
        new bootstrap.Modal(document.getElementById('modalLihatBukti')).show();
    }

    // ----------------------------------------------------------------
    // Buka modal upload bukti (standalone)
    // ----------------------------------------------------------------
    function openUploadBukti(id, nama) {
        document.getElementById('uploadBuktiNama').textContent = nama;
        document.getElementById('formUploadBukti').action = '/admin/bookings/' + id + '/upload-bukti';
        document.getElementById('upload_bukti_file').value = '';
        document.getElementById('upload_preview').style.display = 'none';
        new bootstrap.Modal(document.getElementById('modalUploadBukti')).show();
    }

    // ----------------------------------------------------------------
    // Verifikasi DP (tandai DP sudah dicek admin)
    // ----------------------------------------------------------------
    function verifikasiDp(id, nama) {
        Swal.fire({
            title: 'Verifikasi DP?',
            html: `Konfirmasi bahwa DP dari <strong>${nama}</strong> sudah diterima dan bukti transfer valid?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Verifikasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                var form = document.getElementById('form-verifikasi-dp');
                form.action = '/admin/bookings/' + id + '/verifikasi-dp';
                form.submit();
            }
        });
    }

    // ----------------------------------------------------------------
    // Validasi form Tambah
    // ----------------------------------------------------------------
    function validateTambahForm() {
        let isValid = true;
        const fields = [
            { el: document.getElementById('tambah_nama'),    check: v => v.trim() !== '' },
            { el: document.getElementById('tambah_nohp'),    check: v => v.trim() !== '' },
            { el: document.getElementById('tambah_meja'),    check: v => v !== '' },
            { el: document.getElementById('tambah_dp'),      check: v => v !== '' && parseFloat(v) >= 0 },
            { el: document.getElementById('tambah_tanggal'), check: v => v !== '' },
            { el: document.getElementById('tambah_jam'),     check: v => v !== '' },
        ];
        fields.forEach(f => { if (f.el) f.el.classList.remove('is-invalid'); });
        fields.forEach(f => {
            if (f.el && !f.check(f.el.value)) {
                f.el.classList.add('is-invalid');
                isValid = false;
            }
        });
        return isValid;
    }

    // ----------------------------------------------------------------
    // Validasi form Edit
    // ----------------------------------------------------------------
    function validateEditForm() {
        let isValid = true;
        const fields = [
            { el: document.getElementById('edit_nama_pelanggan'),  check: v => v.trim() !== '' },
            { el: document.getElementById('edit_no_hp'),           check: v => v.trim() !== '' },
            { el: document.getElementById('edit_id_meja'),         check: v => v !== '' },
            { el: document.getElementById('edit_jumlah_dp'),       check: v => v !== '' && parseFloat(v) >= 0 },
            { el: document.getElementById('edit_tanggal_booking'), check: v => v !== '' },
            { el: document.getElementById('edit_jam_kedatangan'),  check: v => v !== '' },
        ];
        fields.forEach(f => { if (f.el) f.el.classList.remove('is-invalid'); });
        fields.forEach(f => {
            if (f.el && !f.check(f.el.value)) {
                f.el.classList.add('is-invalid');
                isValid = false;
            }
        });
        return isValid;
    }

    // ----------------------------------------------------------------
    // Buka modal edit & isi data
    // ----------------------------------------------------------------
    function editBooking(btn) {
        var id      = btn.getAttribute('data-id');
        var nama    = btn.getAttribute('data-nama');
        var nohp    = btn.getAttribute('data-nohp');
        var meja    = btn.getAttribute('data-meja');
        var dp      = btn.getAttribute('data-dp');
        var tanggal = btn.getAttribute('data-tanggal');
        var jam     = btn.getAttribute('data-jam');
        var catatan = btn.getAttribute('data-catatan');
        var bukti   = btn.getAttribute('data-bukti');

        document.getElementById('formEdit').action = '/admin/bookings/' + id;
        document.getElementById('edit_nama_pelanggan').value  = nama;
        document.getElementById('edit_no_hp').value           = nohp;
        document.getElementById('edit_id_meja').value         = meja;
        document.getElementById('edit_jumlah_dp').value       = dp;
        document.getElementById('edit_tanggal_booking').value = tanggal;
        document.getElementById('edit_jam_kedatangan').value  = jam;
        document.getElementById('edit_catatan_pesanan').value = (catatan && catatan !== 'null') ? catatan : '';

        // Tampilkan bukti existing jika ada
        const existingBox = document.getElementById('edit_existing_bukti');
        const existingImg = document.getElementById('edit_existing_img');
        const hapusCb     = document.getElementById('edit_hapus_bukti');
        if (bukti && bukti !== 'null' && bukti !== '') {
            existingImg.src = '/uploads/bukti_dp/' + bukti;
            existingBox.style.display = 'block';
            hapusCb.checked = false;
        } else {
            existingBox.style.display = 'none';
        }

        // Reset preview
        document.getElementById('edit_bukti_dp').value = '';
        document.getElementById('edit_preview_bukti').style.display = 'none';
        document.querySelectorAll('#formEdit .is-invalid').forEach(el => el.classList.remove('is-invalid'));

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    // ----------------------------------------------------------------
    // DOMContentLoaded
    // ----------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {

        // Tambahkan data-bukti pada tombol edit (perlu ditambahkan di blade juga)
        // Sudah dihandle via data-bukti attribute di tombol edit

        // Submit TAMBAH
        const formTambah = document.getElementById('formTambah');
        if (formTambah) {
            formTambah.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!validateTambahForm()) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        html: 'Mohon lengkapi <strong>semua field</strong> yang wajib diisi.',
                        icon: 'warning',
                        confirmButtonColor: '#FF8C42',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                var nama = document.getElementById('tambah_nama').value.trim();
                Swal.fire({
                    title: 'Simpan Booking?',
                    html: `Pastikan data booking untuk <strong>${nama}</strong> sudah benar.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF8C42',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        formTambah.submit();
                    }
                });
            });
        }

        // Submit EDIT
        const formEdit = document.getElementById('formEdit');
        if (formEdit) {
            formEdit.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!validateEditForm()) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        html: 'Mohon lengkapi <strong>semua field</strong> yang wajib diisi.',
                        icon: 'warning',
                        confirmButtonColor: '#FF8C42',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                var nama = document.getElementById('edit_nama_pelanggan').value.trim();
                Swal.fire({
                    title: 'Update Booking?',
                    html: `Simpan perubahan data booking <strong>${nama}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF8C42',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Update!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Mengupdate...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        formEdit.submit();
                    }
                });
            });
        }

        // Flash message
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

    });

    // ----------------------------------------------------------------
    // Konfirmasi booking (DP sudah terverifikasi)
    // ----------------------------------------------------------------
    function confirmKonfirmasi(id, nama) {
        Swal.fire({
            title: 'Konfirmasi Booking?',
            html: `Konfirmasi booking untuk <strong>${nama}</strong>?<br>
                   <span class="text-success"><i class="bi bi-check-circle"></i> DP sudah terverifikasi.</span><br>
                   Meja akan di-reserve.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                var form = document.getElementById('form-konfirmasi');
                form.action = '/admin/bookings/' + id + '/konfirmasi';
                form.submit();
            }
        });
    }

    // ----------------------------------------------------------------
    // Konfirmasi booking tanpa bukti (admin input manual / DP tunai)
    // ----------------------------------------------------------------
    function confirmKonfirmasiTanpaBukti(id, nama) {
        Swal.fire({
            title: 'Konfirmasi Tanpa Bukti?',
            html: `Booking <strong>${nama}</strong> belum ada bukti transfer terverifikasi.<br>
                   Lanjutkan konfirmasi? (misal: DP dibayar tunai / sudah dicek manual)`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fd7e14',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tetap Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                var form = document.getElementById('form-konfirmasi');
                form.action = '/admin/bookings/' + id + '/konfirmasi';
                form.submit();
            }
        });
    }

    // ----------------------------------------------------------------
    // Batalkan booking
    // ----------------------------------------------------------------
    function confirmBatal(id, nama) {
        Swal.fire({
            title: 'Batalkan Booking?',
            html: `Batalkan booking <strong>${nama}</strong>?<br><span style="color:#dc3545;">Meja akan kembali tersedia.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                var form = document.getElementById('form-batal');
                form.action = '/admin/bookings/' + id + '/batal';
                form.submit();
            }
        });
    }

    // ----------------------------------------------------------------
    // Hapus permanen
    // ----------------------------------------------------------------
    function confirmHapusBooking(id, nama) {
        Swal.fire({
            title: 'Hapus Booking?',
            html: `Hapus permanen booking <strong>${nama}</strong>?<br><span style="color:red;">Tidak dapat dibatalkan!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                var form = document.getElementById('form-hapus');
                form.action = '/admin/bookings/' + id;
                form.submit();
            }
        });
    }
</script>
@endpush