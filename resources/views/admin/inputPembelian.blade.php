@extends('layouts.admin')

@section('title', 'Buat Transaksi Pembelian')

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
<form action="{{ route('admin.purchases.store') }}" method="POST" id="formPembelian">
    @csrf

    <input type="hidden" name="user_id" value="{{ Auth::id() ?? 1 }}">

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
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                                <option value="{{ $cabang->id }}" {{ (Auth::user()->cabang_id_default ?? 1) == $cabang->id ? 'selected' : '' }}>
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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahItem">
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
                    <h5 class="card-title fw-bold mb-3">Aksi & Status</h5>

                    <div class="d-grid mb-2">
                        <button type="submit" name="status_pembelian" value="draft" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-1"></i> Simpan Draft & Tinjau
                        </button>
                    </div>

                    <p class="text-muted small text-center">Simpan untuk mendapatkan link "Read-Only" yang bisa dibagikan ke atasan.</p>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label for="harga_tawaran_customer" class="form-label">Tawaran Customer (Total)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('harga_tawaran_customer') is-invalid @enderror" id="harga_tawaran_customer" name="harga_tawaran_customer" placeholder="Mis: 5000000" value="{{ old('harga_tawaran_customer') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="harga_tawaran_toko" class="form-label">Tawaran Toko (Total)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('harga_tawaran_toko') is-invalid @enderror" id="harga_tawaran_toko" name="harga_tawaran_toko" placeholder="Mis: 4500000" value="{{ old('harga_tawaran_toko') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="harga_deal" class="form-label fw-bold text-success">Harga Deal (Final)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold text-success">Rp</span>
                            <input type="number" class="form-control @error('harga_deal') is-invalid @enderror" id="harga_deal" name="harga_deal" placeholder="0" value="{{ old('harga_deal') }}">
                        </div>
                        <div class="form-text">Isi ini jika sudah ada kesepakatan harga.</div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" name="status_pembelian" value="deal" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i> Deal
                        </button>
                        <button type="submit" name="status_pembelian" value="tidak_deal" class="btn btn-danger w-100">
                            <i class="fas fa-times me-1"></i> Tidak Deal
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card Salin Link (Contoh, muncul saat halaman 'edit') --}}
            @if(isset($pembelian))
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-2">Bagikan Tinjauan</h6>
                    <p class="text-muted small mt-0 mb-2">Gunakan link ini untuk diskusi dengan atasan.</p>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" id="shareable-link" value="{{ route('admin.purchases.show', $pembelian->id) }}" readonly>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyToClipboard()">
                            <i class="fas fa-copy me-1"></i> Salin
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
                            <select class="form-select" id="customer_jenis_kelamin_modal" name="jenis_kelamin">
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
    // =======================================================
    // PERBAIKAN 1: Ambil data 'old' dari Laravel jika validasi gagal
    // =======================================================
    let initialItems = @json(old('items') ?? []);
    let itemsPembelian = [];
    let itemCounter = 0;

    // Jika ada data 'old', kita rebuild array JS kita
    if (initialItems.length > 0) {
        itemsPembelian = initialItems.map(item => {
            // (Kita perlu juga 'kategori_nama' yang tidak ada di 'old')
            // (Untuk sementara, kita biarkan kosong saat reload)
            item.id = itemCounter++; // Beri ID unik baru (client-side)
            item.kategori_nama = item.kategori_nama || 'Kategori (Reloaded)'; // fallback
            return item;
        });
    }


    document.addEventListener("DOMContentLoaded", function() {

        // =======================================================
        // PERBAIKAN 2: Ganti selector ke ID form #formPembelian
        // =======================================================
        const mainForm = document.getElementById('formPembelian');
        if (!mainForm) {
            console.error('Form utama #formPembelian tidak ditemukan!');
            return; // Hentikan script jika form utama tidak ada
        }

        const btnSimpanItem = document.getElementById('btnSimpanItem');
        const itemListWrapper = document.getElementById('item-list-wrapper');
        const modalTambahItem = new bootstrap.Modal(document.getElementById('modalTambahItem'));

        // Panggil renderItemList() saat halaman dimuat
        // Ini akan otomatis menampilkan pesan 'kosong' ATAU data 'old'
        renderItemList();

        // Saat tombol "Simpan Item" di Modal diklik
        btnSimpanItem.addEventListener('click', function() {
            const namaItem = document.getElementById('item_nama_item').value;
            const kategoriSelect = document.getElementById('item_kategori_id');
            const kategoriId = kategoriSelect.value;
            const kategoriNama = kategoriId ? kategoriSelect.options[kategoriSelect.selectedIndex].text : 'N/A';

            if (!namaItem || !kategoriId) {
                alert('Nama Item dan Kategori wajib diisi.');
                return;
            }

            const newItem = {
                id: itemCounter++,
                nama_item: namaItem,
                kategori_id: kategoriId,
                kategori_nama: kategoriNama, // Kita simpan nama kategori di JS
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
            itemsPembelian.push(newItem);
            renderItemList();
            document.getElementById('formTambahItem').querySelectorAll('input, textarea, select').forEach(input => {
                if(input.type === 'select-one') input.selectedIndex = 0;
                else input.value = '';
            });
            document.querySelectorAll('#accordionKondisi .accordion-collapse').forEach(collapse => {
                new bootstrap.Collapse(collapse, { toggle: false }).hide();
            });
            modalTambahItem.hide();
        });

        // Fungsi untuk menghapus item
        window.hapusItem = function(id) {
            if (confirm('Yakin ingin menghapus item ini?')) {
                itemsPembelian = itemsPembelian.filter(item => item.id !== id);
                renderItemList();
            }
        }

        // Fungsi untuk menggambar ulang tabel item
        function renderItemList() {
            if (!itemListWrapper || !mainForm) return; // Penjaga error

            itemListWrapper.innerHTML = '';

            // Hapus semua input 'items[]' yang lama
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

                    // PERBAIKAN: Pastikan kategori_nama ada (ambil dari 'old' atau item baru)
                    let kategoriNama = item.kategori_nama;
                    if (!kategoriNama) {
                        // Jika ini dari 'old()' dan kita tidak menyimpan nama, cari di dropdown
                        const katSelect = document.getElementById('item_kategori_id');
                        const opt = katSelect.querySelector(`option[value="${item.kategori_id}"]`);
                        if(opt) kategoriNama = opt.text;
                    }

                    tr.innerHTML = `
                        <td>
                            <h6 class="mb-0">${item.nama_item}</h6>
                            <small class="text-muted">Kondisi: ${shortKondisi}</small>
                        </td>
                        <td>${kategoriNama || 'N/A'}</td>
                        <td>${item.serial_number || 'N/A'}</td>
                        <td class="text-end">
                            <button type="button" class="btn-icon text-danger" title="Hapus" onclick="hapusItem(${item.id})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    `;
                    itemListWrapper.appendChild(tr);

                    // PENTING: Tambahkan semua data item sebagai Input Tersembunyi (Hidden)
                    Object.keys(item).forEach(key => {
                        // Kita tidak perlu submit 'id'
                        if(key === 'id') return;

                        const inputName = `items[${index}][${key}]`;
                        const inputValue = item[key] || ''; // Pastikan tidak 'null'

                        // Gunakan textarea untuk semua agar aman dari karakter aneh
                        const textarea = document.createElement('textarea');
                        textarea.name = inputName;
                        textarea.value = inputValue;
                        textarea.style.display = 'none';
                        mainForm.appendChild(textarea);
                    });
                });
            }
        }

        // --- (SCRIPT UNTUK MODAL CUSTOMER) ---
        const btnSimpanCustomer = document.getElementById('btnSimpanCustomer');
        const modalTambahCustomer = new bootstrap.Modal(document.getElementById('modalTambahCustomer'));
        const formTambahCustomer = document.getElementById('formTambahCustomer');

        btnSimpanCustomer.addEventListener('click', function() {
            const nama = document.getElementById('customer_nama_modal').value;
            const no_telp = document.getElementById('customer_no_telp_modal').value;

            if(!nama || !no_telp) {
                alert('Nama dan No. Telepon customer wajib diisi.');
                return; // Stop di sini
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
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
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
                if(error.errors) {
                    errorMsg = Object.values(error.errors)[0][0];
                }
                alert(errorMsg);
            })
            .finally(() => {
                btnSimpanCustomer.disabled = false;
                btnSimpanCustomer.innerHTML = 'Simpan Customer';
            });
        });

        window.copyToClipboard = function() {
            const linkInput = document.getElementById('shareable-link');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            alert('Link tinjauan telah disalin ke clipboard!');
        }
    });
</script>
@endpush
