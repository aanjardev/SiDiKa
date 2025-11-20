@extends('layouts.admin')

@section('title', 'Transaksi Pembelian')

@push('page-actions')
    {{-- Halaman form tidak perlu tombol aksi di header --}}
@endpush

@section('content')

{{-- Tampilkan Error Validasi (dari Backend) --}}
@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <h5 class="alert-heading">Ada Kesalahan Input!</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ======================================================= --}}
{{-- PERBAIKAN 1: Tambahkan id="formPembelian" --}}
{{-- ======================================================= --}}
<form action="{{ isset($pembelian) ? route('admin.purchases.update', $pembelian->id) : route('admin.purchases.store') }}" method="POST" id="formPembelian">
    @csrf
    @if(isset($pembelian))
        @method('PUT') {{-- Spoofing method PUT untuk update --}}
    @endif

    <input type="hidden" name="user_id" value="{{ Auth::id() ?? 1 }}">
    <input type="hidden" id="pembelian_id_hidden" name="pembelian_id" value="{{ $pembelian->id ?? '' }}">

    <div class="row">
        {{-- KOLOM KIRI (70%): Form Utama --}}
        <div class="col-lg-8">

            {{-- CARD 1: INFORMASI TRANSAKSI (Customer & Cabang) --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Informasi Transaksi</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="" selected disabled>Pilih customer...</option>
                                @foreach($semua_customer as $customer)
                                <option value="{{ $customer->id }}"
                                    {{-- Logika: Pilih yang dari old() atau yang tersimpan di $pembelian --}}
                                    {{ (old('customer_id') == $customer->id) || (isset($pembelian) && $pembelian->customer_id == $customer->id) ? 'selected' : '' }}
                                >
                                    {{ $customer->nama }} ({{ $customer->no_telp }})
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">
                                Jika customer baru, <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer">klik di sini untuk menambahkannya</a>.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="perusahaan_cabang_id" class="form-label">Lokasi Transaksi (Cabang)</label>
                            <select class="form-select @error('perusahaan_cabang_id') is-invalid @enderror" id="perusahaan_cabang_id" name="perusahaan_cabang_id" required>
                                @foreach($semua_cabang as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{-- Logika: Pilih yang tersimpan di $pembelian, atau default untuk mode Create --}}
                                    @if(isset($pembelian))
                                        {{ $pembelian->perusahaan_cabang_id == $cabang->id ? 'selected' : '' }}
                                    @else
                                        {{ (Auth::user()->cabang_id_default ?? 1) == $cabang->id ? 'selected' : '' }}
                                    @endif
                                >
                                    {{ $cabang->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('perusahaan_cabang_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: DAFTAR ITEM (1-N Produk Draft) --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-bold mb-0">Item yang Dibeli</h5>
                        <button type="button" class="btn btn-primary" id="btnBukaModalItem">
                            <i class="fas fa-plus fa-fw me-1"></i> Tambah Item
                        </button>
                    </div>

                    @error('items')
                        <div class="alert alert-danger small p-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> {{ $message }}
                        </div>
                    @enderror

                    <div class="table-responsive">
                        <table class="table align-middle table-product mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Kategori</th>
                                    <th>Serial Number</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="item-list-wrapper">
                                {{-- Item yang ditambah (via JS) akan muncul di sini --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

 {{-- KOLOM KANAN (30%): Aksi & Harga --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">Status & Harga</h5>

                    {{-- INPUT 1: TAWARAN CUSTOMER --}}
            <div class="mb-3">
                <label for="display_harga_tawaran_customer" class="form-label">Tawaran Customer</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    {{-- Input TAMPILAN (User mengetik di sini) --}}
                    <input type="text" class="form-control rupiah-mask"
                        id="display_harga_tawaran_customer" style="height: 40px"
                        placeholder="0"
                        value="{{ old('harga_tawaran_customer', $pembelian->harga_tawaran_customer ?? '') }}">

                    {{-- Input ASLI (Dikirim ke Database) --}}
                    <input type="hidden"
                        name="harga_tawaran_customer"
                        id="harga_tawaran_customer"
                        value="{{ old('harga_tawaran_customer', $pembelian->harga_tawaran_customer ?? '') }}">
                </div>
            </div>

            {{-- INPUT 2: TAWARAN TOKO --}}
            <div class="mb-3">
                <label for="display_harga_tawaran_toko" class="form-label">Tawaran Toko</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    {{-- Input TAMPILAN --}}
                    <input type="text" class="form-control rupiah-mask"
                        id="display_harga_tawaran_toko"
                        placeholder="0" style="height: 40px"
                        value="{{ old('harga_tawaran_toko', $pembelian->harga_tawaran_toko ?? '') }}">

                    {{-- Input ASLI --}}
                    <input type="hidden"
                        name="harga_tawaran_toko"
                        id="harga_tawaran_toko"
                        value="{{ old('harga_tawaran_toko', $pembelian->harga_tawaran_toko ?? '') }}">
                </div>
            </div>

            {{-- INPUT 3: HARGA DEAL --}}
            <div class="mb-3">
                <label for="display_harga_deal" class="form-label fw-bold text-success">Harga Deal (Final)</label>
                <div class="input-group">
                    <span class="input-group-text fw-bold text-success">Rp</span>
                    {{-- Input TAMPILAN --}}
                    <input type="text" class="form-control fw-bold text-success rupiah-mask"
                        id="display_harga_deal"
                        placeholder="0"
                        value="{{ old('harga_deal', $pembelian->harga_deal ?? '') }}">

                    {{-- Input ASLI --}}
                    <input type="hidden"
                        name="harga_deal"
                        id="harga_deal"
                        value="{{ old('harga_deal', $pembelian->harga_deal ?? '') }}">
                </div>
                <div class="form-text small">Isi jika sudah sepakat.</div>
            </div>

                    <hr class="my-4">

                    {{-- TOMBOL AKSI (Sejajar Satu Baris) --}}
                    <div class="d-flex gap-2">
                        {{-- Tombol Draft (Saya ubah jadi outline agar tidak terlalu dominan, tapi tetap sejajar) --}}
                        <button type="submit" name="status_pembelian" value="draft" class="btn btn-outline-primary w-100" title="Simpan sebagai Draft">
                            <i class="fas fa-save d-block d-md-none"></i> {{-- Icon only di layar kecil --}}
                            <span class="d-none d-md-inline"><i class="fas fa-save me-1"></i> Draft</span>
                        </button>

                        {{-- Tombol Tidak Deal --}}
                        <button type="submit" name="status_pembelian" value="tidak_deal" class="btn btn-danger w-100" title="Batalkan Transaksi">
                            <i class="fas fa-times d-block d-md-none"></i>
                            <span class="d-none d-md-inline"><i class="fas fa-times me-1"></i> No Deal</span>
                        </button>

                        {{-- Tombol Deal --}}
                        <button type="submit" name="status_pembelian" value="deal" class="btn btn-success w-100" title="Sepakat / Deal">
                            <i class="fas fa-check d-block d-md-none"></i>
                            <span class="d-none d-md-inline"><i class="fas fa-check me-1"></i> Deal</span>
                        </button>
                    </div>

                    <p class="text-muted small text-center mt-2 mb-0" style="font-size: 0.75rem;">
                        Pastikan harga sudah sesuai sebelum klik Deal.
                    </p>

                </div>
            </div>

            {{-- Card Salin Link (Tetap dimunculkan jika mode edit) --}}
            @if(isset($pembelian))
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-2">Bagikan Tinjauan</h6>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" id="shareable-link" value="{{ route('admin.purchases.show', $pembelian->id) }}" readonly>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyToClipboard()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</form>


{{-- ======================================================= --}}
{{-- MODAL TAMBAH CUSTOMER (DENGAN ATRIBUT 'name') --}}
{{-- ======================================================= --}}
<div class="modal fade" id="modalTambahCustomer" tabindex="-1" aria-labelledby="modalTambahCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahCustomerLabel">Tambah Customer Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- PERBAIKAN: Semua input di sini HARUS punya atribut 'name' --}}
                <form id="formTambahCustomer">
                    <div class="alert alert-info small">
                        Customer yang ditambahkan di sini akan otomatis tersimpan di database.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="customer_nama_modal">Nama Customer*</label>
                            <input type="text" class="form-control" id="customer_nama_modal" name="nama" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="customer_no_telp_modal">Nomor Telepon*</label>
                            <input type="text" class="form-control" id="customer_no_telp_modal" name="no_telp" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="customer_identitas_modal">NIK (Identitas)</label>
                            <input type="text" class="form-control" id="customer_identitas_modal" name="identitas">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="customer_jenis_kelamin_modal">Jenis Kelamin</label>
                            <select class="form-select" id="customer_jenis_kelamin_modal" name="jenis_kelamin" style="height: calc(2.5rem + 9px);">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="customer_alamat_modal">Alamat</label>
                        <textarea class="form-control" rows="2" id="customer_alamat_modal" name="alamat"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="customer_referensi_modal">Referensi (Opsional)</label>
                        <input type="text" class="form-control" id="customer_referensi_modal" name="referensi" placeholder="Mis: Info dari Instagram, Teman, dll.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="customer_keterangan_modal">Keterangan (Opsional)</label>
                        <textarea class="form-control" rows="2" id="customer_keterangan_modal" name="keterangan" placeholder="Mis: Pelanggan lama, sering jual/beli..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanCustomer">
                    Simpan Customer
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ======================================================= --}}
{{-- MODAL UNTUK TAMBAH ITEM (Dengan Accordion) --}}
{{-- ======================================================= --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-labelledby="modalTambahItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahItemLabel">Tambah Item Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="formTambahItem">
                    <h6 class="fw-bold text-primary">1. Informasi Dasar Item</h6>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nama Item*</label>
                            <input type="text" class="form-control" id="item_nama_item" placeholder="Misal: Canon 60D Body Only">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kategori*</label>
                            <select class="form-select" id="item_kategori_id">
                                <option value="" selected disabled>Pilih Kategori...</option>
                                @foreach($semua_kategori as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Number (Body/Unit)</label>
                            <input type="text" class="form-control" id="item_serial_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Number (Lensa)</label>
                            <input type="text" class="form-control" id="item_serial_lens">
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-bold text-primary">2. Pengecekan Kondisi</h6>
                    <div class="accordion" id="accordionKondisi">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFisik">
                                    Kondisi Fisik Unit
                                </button>
                            </h2>
                            <div id="collapseFisik" class="accordion-collapse collapse" data-bs-parent="#accordionKondisi">
                                <div class="accordion-body row">
                                    <div class="col-md-6 mb-3"><label class="form-label">Kondisi Fisik (Keseluruhan)</label><input type="text" class="form-control" id="item_kondisi_fisik" placeholder="Mis: 90% (bekas pemakaian)"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Kondisi Baut</label><input type="text" class="form-control" id="item_kondisi_baut" placeholder="Mis: Utuh, ada bekas obeng"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Tutup USB/Port</label><input type="text" class="form-control" id="item_kondisi_tutup_usb" placeholder="Mis: Ada, kencang"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Karet Grip</label><input type="text" class="form-control" id="item_kondisi_grip" placeholder="Mis: Rapat, sedikit melar"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Mounting Lensa/Body</label><input type="text" class="form-control" id="item_kondisi_mounting" placeholder="Mis: Bersih, ada goresan"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">LCD</label><input type="text" class="form-control" id="item_kondisi_lcd" placeholder="Mis: Bening, no vignette"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Tombol-tombol</label><input type="text" class="form-control" id="item_kondisi_tombol" placeholder="Mis: Berfungsi semua, empuk"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Slot Memori</label><input type="text" class="form-control" id="item_kondisi_slot_memori" placeholder="Mis: Normal"></div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLensa">
                                    Kondisi Lensa & Sensor
                                </button>
                            </h2>
                            <div id="collapseLensa" class="accordion-collapse collapse" data-bs-parent="#accordionKondisi">
                                <div class="accordion-body row">
                                    <div class="col-md-6 mb-3"><label class="form-label">Jamur Lensa</label><input type="text" class="form-control" id="item_kondisi_jamur_lensa" placeholder="Mis: Tidak ada / Jamur tipis"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">View Finder (Jendela Bidik)</label><input type="text" class="form-control" id="item_kondisi_view_finder" placeholder="Mis: Bersih / Ada debu"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Jamur Sensor</label><input type="text" class="form-control" id="item_kondisi_jamur_sensor" placeholder="Mis: Tidak ada / Ada 1 titik"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Zoom Lensa (In/Out)</label><input type="text" class="form-control" id="item_kondisi_zoom_lensa" placeholder="Mis: Lancar, tidak seret"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Autofokus (AF) Lensa</label><input type="text" class="form-control" id="item_kondisi_af_lensa" placeholder="Mis: Cepat, normal"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Diafragma (Aperture) Lensa</label><input type="text" class="form-control" id="item_kondisi_diafragma_lensa" placeholder="Mis: Normal, tidak error"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Kalibrasi Fokus</label><input type="text" class="form-control" id="item_kondisi_kalibrasi_fokus" placeholder="Mis: Normal, tidak front/back"></div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLain">
                                    Kondisi Fungsi Lain & Kelengkapan
                                </button>
                            </h2>
                            <div id="collapseLain" class="accordion-collapse collapse" data-bs-parent="#accordionKondisi">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label">Flash Internal/Eksternal</label><input type="text" class="form-control" id="item_kondisi_flash" placeholder="Mis: Normal, hotshoe berfungsi"></div>
                                        <div class="col-md-6 mb-3"><label class="form-label">Sound/Mic</label><input type="text" class="form-control" id="item_kondisi_sound_mic" placeholder="Mis: Normal"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kondisi Lain-lain (Opsional)</label>
                                        <input type="text" class="form-control" id="item_kondisi_lain_lain" placeholder="Mis: WiFi normal, Bluetooth normal">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kelengkapan Awal</label>
                                        <textarea class="form-control" rows="2" id="item_kelengkapan" placeholder="Misal: Box, Baterai Ori, Charger Ori, Strap, Tas..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanItem">
                    Simpan Item
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-icon {
        background: transparent !important; border: none !important;
        padding: 0 !important; color: #dc3545 !important;
        cursor: pointer !important; font-size: 16px !important;
    }
    .btn-icon:hover { color: #bb2d3b !important; }
    .accordion-button:not(.collapsed) {
        color: var(--bs-primary);
        background-color: var(--bs-primary-bg-subtle);
    }
    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(78, 107, 255, 0.25);
    }
    .accordion-body {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>

    let currentPembelianId = '{{ $pembelian->id ?? '' }}';
    let initialItems = @json(old('items') ?? ($pembelian->item_pembelian_draft ?? []));

    // Array ini sekarang hanya untuk TAMPILAN di tabel
    let itemsPembelian = [];
    let itemCounter = 0;

    if (initialItems.length > 0) {
        itemsPembelian = initialItems.map(item => {
            // Ambil nama kategori jika sudah ada (dari relasi di model)
            if (item.kategori && item.kategori.nama_kategori) {
                item.kategori_nama = item.kategori.nama_kategori;
            }
            // Jika tidak ada (misal dari old() atau bug), gunakan fallback display
            else {
                item.kategori_nama = item.kategori_nama || 'Kategori (Reloaded)';
            }

            // Pastikan ID ada (ID ini adalah ID DB item)
            if(typeof item.id === 'undefined') {
                item.id = itemCounter++;
            }

            return item;
        });
    }

    document.addEventListener("DOMContentLoaded", function() {

        const mainForm = document.getElementById('formPembelian');
        if (!mainForm) {
            console.error('Form utama #formPembelian tidak ditemukan!');
            return;
        }

        const btnBukaModalItem = document.getElementById('btnBukaModalItem');
        const btnSimpanItem = document.getElementById('btnSimpanItem');
        const itemListWrapper = document.getElementById('item-list-wrapper');
        const modalTambahItem = new bootstrap.Modal(document.getElementById('modalTambahItem'));
        const hiddenPembelianIdInput = document.getElementById('pembelian_id_hidden');
        const customerSelect = document.getElementById('customer_id');
        const cabangSelect = document.getElementById('perusahaan_cabang_id');

        if (currentPembelianId) {
            hiddenPembelianIdInput.value = currentPembelianId;
        }
        // Render tabel (awalnya akan kosong)
        renderItemList();

        // TAMBAHKAN EVENT LISTENER INI
        btnBukaModalItem.addEventListener('click', function() {
            const customerId = customerSelect.value;
            const cabangId = cabangSelect.value;

            if (!customerId || !cabangId) {
                alert('Harap pilih Customer dan Lokasi Transaksi (Cabang) terlebih dahulu sebelum menambah item.');
                return; // Hentikan aksi
            }

            // Jika customer & cabang sudah dipilih, baru buka modalnya
            modalTambahItem.show();
        });



        // =======================================================
        // PERUBAHAN UTAMA 1: Simpan Item (AJAX)
        // =======================================================
        btnSimpanItem.addEventListener('click', function() {
            const namaItem = document.getElementById('item_nama_item').value;
            const kategoriSelect = document.getElementById('item_kategori_id');
            const kategoriId = kategoriSelect.value;

            if (!namaItem || !kategoriId) {
                alert('Nama Item dan Kategori wajib diisi.');
                return;
            }

            // Kumpulkan semua data item dari modal
            const newItemData = {
                // Data parent (dibutuhkan HANYA untuk item pertama)
                pembelian_id: currentPembelianId,
                customer_id: document.getElementById('customer_id').value,
                perusahaan_cabang_id: document.getElementById('perusahaan_cabang_id').value,
                user_id: mainForm.querySelector('input[name="user_id"]').value,

                // Data item
                nama_item: namaItem,
                kategori_id: kategoriId,
                serial_number: document.getElementById('item_serial_number').value,
                serial_lens: document.getElementById('item_serial_lens').value,
                kondisi_fisik: document.getElementById('item_kondisi_fisik').value,
                kondisi_baut: document.getElementById('item_kondisi_baut').value,
                kondisi_tutup_usb: document.getElementById('item_kondisi_tutup_usb').value,
                kondisi_grip: document.getElementById('item_kondisi_grip').value,
                kondisi_jamur_lensa: document.getElementById('item_kondisi_jamur_lensa').value,
                kondisi_view_finder: document.getElementById('item_kondisi_view_finder').value,
                kondisi_mounting: document.getElementById('item_kondisi_mounting').value,
                kondisi_slot_memori: document.getElementById('item_kondisi_slot_memori').value,
                kondisi_jamur_sensor: document.getElementById('item_kondisi_jamur_sensor').value,
                kondisi_lcd: document.getElementById('item_kondisi_lcd').value,
                kondisi_tombol: document.getElementById('item_kondisi_tombol').value,
                kondisi_zoom_lensa: document.getElementById('item_kondisi_zoom_lensa').value,
                kondisi_af_lensa: document.getElementById('item_kondisi_af_lensa').value,
                kondisi_diafragma_lensa: document.getElementById('item_kondisi_diafragma_lensa').value,
                kondisi_kalibrasi_fokus: document.getElementById('item_kondisi_kalibrasi_fokus').value,
                kondisi_flash: document.getElementById('item_kondisi_flash').value,
                kondisi_sound_mic: document.getElementById('item_kondisi_sound_mic').value,
                kondisi_lain_lain: document.getElementById('item_kondisi_lain_lain').value,
                kelengkapan_awal: document.getElementById('item_kelengkapan').value
            };

            // Tampilkan loading
            btnSimpanItem.disabled = true;
            btnSimpanItem.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

            // Kirim data ke Controller via AJAX (Fetch)
            fetch("{{ route('admin.purchases.ajaxStoreItemDraft') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(newItemData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; }); // Tangani error validasi
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    // 1. Simpan ID Pembelian (Induk)
                    currentPembelianId = result.pembelian_id;
                    hiddenPembelianIdInput.value = result.pembelian_id;

                    // 2. Tambahkan item baru (dari server) ke array JS
                    itemsPembelian.push(result.item);

                    // 3. Render ulang tabel
                    renderItemList();

                    // 4. Reset & tutup modal
                    document.getElementById('formTambahItem').querySelectorAll('input, textarea, select').forEach(input => {
                        if(input.type === 'select-one') input.selectedIndex = 0;
                        else input.value = '';
                    });
                    document.querySelectorAll('#accordionKondisi .accordion-collapse').forEach(collapse => {
                        new bootstrap.Collapse(collapse, { toggle: false }).hide();
                    });
                    modalTambahItem.hide();
                } else {
                    alert('Gagal: ' + (result.message || 'Error tidak diketahui.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMsg = 'Gagal menyimpan data. Cek console (F12) untuk detail.';
                if (error.errors) {
                    // Tampilkan error validasi pertama
                    errorMsg = Object.values(error.errors)[0][0];
                } else if (error.message) {
                    errorMsg = error.message;
                }
                alert(errorMsg);
            })
            .finally(() => {
                btnSimpanItem.disabled = false;
                btnSimpanItem.innerHTML = 'Simpan Item';
            });
        });

        // =======================================================
        // PERUBAHAN UTAMA 2: Hapus Item (AJAX)
        // =======================================================
        // 'id' di sini adalah ID dari database (item.id)
        window.hapusItem = function(id) {
            if (confirm('Yakin ingin menghapus item ini dari database?')) {

                fetch(`/admin/purchases/delete-item-draft/${id}`, { // Gunakan route baru
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Hapus item dari array JS
                        itemsPembelian = itemsPembelian.filter(item => item.id !== id);
                        // Render ulang tabel
                        renderItemList();
                    } else {
                        alert('Gagal menghapus: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal menghapus item.');
                });
            }
        }

        // =======================================================
        // PERUBAHAN UTAMA 3: Render Tabel
        // =======================================================
        function renderItemList() {
            if (!itemListWrapper || !mainForm) return;

            itemListWrapper.innerHTML = '';

            // HAPUS SEMUA INPUT HIDDEN YANG LAMA
            // (Kita tidak membutuhkannya lagi, karena data sudah di DB)
            mainForm.querySelectorAll('input[name^="items["]').forEach(input => input.remove());
            mainForm.querySelectorAll('textarea[name^="items["]').forEach(input => input.remove());

            if (itemsPembelian.length === 0) {
                itemListWrapper.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada item yang ditambahkan. <br>
                            Klik tombol "Tambah Item" untuk memulai.
                        </td>
                    </tr>
                `;
            } else {
                itemsPembelian.forEach((item, index) => {
                    let tr = document.createElement('tr');
                    let shortKondisi = item.kondisi_fisik || 'N/A';
                    if(shortKondisi && shortKondisi.length > 30) shortKondisi = shortKondisi.substring(0, 30) + '...';

                    // Ambil nama kategori dari data relasi (hasil load 'kategori' di controller)
                    let kategoriNama = (item.kategori && item.kategori.nama_kategori) ? item.kategori.nama_kategori : 'N/A';

                    tr.innerHTML = `
                        <td>
                            <h6 class="mb-0">${item.nama_item}</h6>
                            <small class="text-muted">Kondisi: ${shortKondisi}</small>
                        </td>
                        <td>${kategoriNama}</td>
                        <td>${item.serial_number || 'N/A'}</td>
                        <td class="text-end">
                            <button type="button" class="btn-icon text-danger" title="Hapus" onclick="hapusItem(${item.id})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    `;
                    itemListWrapper.appendChild(tr);


                });
            }
        }

        // --- (SCRIPT MODAL CUSTOMER ANDA - TETAP SAMA) ---
        const btnSimpanCustomer = document.getElementById('btnSimpanCustomer');
        const modalTambahCustomer = new bootstrap.Modal(document.getElementById('modalTambahCustomer'));
        const formTambahCustomer = document.getElementById('formTambahCustomer');

        btnSimpanCustomer.addEventListener('click', function() {
            // ... (Logika simpan customer Anda tidak perlu diubah) ...
            const nama = document.getElementById('customer_nama_modal').value;
            const no_telp = document.getElementById('customer_no_telp_modal').value;
            if(!nama || !no_telp) {
                alert('Nama dan No. Telepon customer wajib diisi.');
                return;
            }
            const formData = new FormData(formTambahCustomer);
            const data = Object.fromEntries(formData.entries());
            btnSimpanCustomer.disabled = true;
            btnSimpanCustomer.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            fetch("{{ route('admin.customers.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) { return response.json().then(err => { throw err; }); }
                return response.json();
            })
            .then(result => {
                if(result.success) {
                    const customerSelect = document.getElementById('customer_id');
                    const newOption = new Option(`${result.customer.nama} (${result.customer.no_telp})`, result.customer.id, true, true);
                    customerSelect.appendChild(newOption);
                    formTambahCustomer.reset();
                    modalTambahCustomer.hide();
                } else {
                    alert('Gagal menyimpan customer: ' + (result.message || 'Error tidak diketahui.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMsg = 'Gagal menyimpan data. Cek console (F12) untuk detail.';
                if(error.errors) { errorMsg = Object.values(error.errors)[0][0]; }
                alert(errorMsg);
            })
            .finally(() => {
                btnSimpanCustomer.disabled = false;
                btnSimpanCustomer.innerHTML = 'Simpan Customer';
            });
        });

        // --- (Script copy-paste link Anda - TETAP SAMA) ---
        window.copyToClipboard = function() {
            const linkInput = document.getElementById('shareable-link');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            alert('Link tinjauan telah disalin ke clipboard!');
        }

        const rupiahInputs = document.querySelectorAll('.rupiah-mask');

    rupiahInputs.forEach(input => {
        // 1. Format awal saat halaman diload (jika ada value dari old/database)
        if (input.value) {
            const cleanValue = input.value.replace(/\D/g, ''); // Hapus karakter non-angka
            input.value = formatRupiah(cleanValue); // Tampilkan format
        }

        // 2. Event listener saat mengetik
        input.addEventListener('keyup', function(e) {
            // Ambil value tanpa karakter non-angka
            let cleanValue = this.value.replace(/\D/g, '');

            // Update tampilan ke format Rupiah
            this.value = formatRupiah(cleanValue);

            // Update input HIDDEN yang berteman dengan input ini
            // Kita cari input hidden yang ID-nya mirip (tanpa prefix 'display_')
            const hiddenInputId = this.id.replace('display_', '');
            const hiddenInput = document.getElementById(hiddenInputId);

            if(hiddenInput) {
                hiddenInput.value = cleanValue;
            }
        });
    });

    function formatRupiah(angka) {
        if (!angka) return '';

        // Menggunakan fungsi bawaan Intl untuk format Indonesia
        return new Intl.NumberFormat('id-ID').format(angka);

        // ATAU jika ingin manual regex titik:
        // return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    });
</script>
@endpush
