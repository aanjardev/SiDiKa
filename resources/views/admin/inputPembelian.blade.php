@extends('layouts.admin')

@section('title', 'Transaksi Pembelian')

@push('page-actions')
    @php
        $backRoute = route('admin.purchases.index');
        if(isset($pembelian)) {
            $backRoute = route('admin.purchases.show', $pembelian->id);
        }
    @endphp

    <a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnKembali">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
@endpush

@section('content')

{{-- Custom CSS (Dari HEAD) --}}
@push('styles')
<style>
    /* Modern Card Style */
    .card-modern { border: 1px solid #f0f0f0; border-radius: 16px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04); transition: all 0.3s ease; }
    .card-header-modern { background-color: #fff; border-bottom: 1px solid #f0f0f0; padding: 20px 24px; border-radius: 16px 16px 0 0 !important; }
    
    /* Input Group Styling */
    .input-group-modern .input-group-text { background-color: #fff; border-right: none; color: #6c757d; border-color: #dee2e6; border-radius: 10px 0 0 10px; }
    .input-group-modern .form-control, .input-group-modern .form-select { border-left: none; border-color: #dee2e6; border-radius: 0 10px 10px 0; padding: 10px 15px; }
    .input-group-modern .form-control:focus, .input-group-modern .form-select:focus { box-shadow: none; border-color: #86b7fe; border-left: 1px solid #86b7fe; }
    
    /* Labels */
    .form-label-modern { font-size: 0.85rem; font-weight: 600; color: #344767; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    /* Action Buttons in Table */
    .btn-action-icon { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; border: 1px solid transparent; background: transparent; color: #dc3545; }
    .btn-action-icon:hover { background-color: #fee2e2; color: #dc2626; border-color: #fecaca; }
    
    /* Accordion Styling */
    .accordion-modern .accordion-button { background-color: #f8f9fa; border-radius: 8px !important; color: #495057; font-weight: 600; box-shadow: none; }
    .accordion-modern .accordion-button:not(.collapsed) { background-color: #e7f1ff; color: #0d6efd; }
    .accordion-modern .accordion-item { border: 1px solid #eee; border-radius: 8px !important; margin-bottom: 10px; overflow: hidden; }
</style>
@endpush

{{-- Tampilkan Error Validasi (Dari Main) --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm" role="alert" style="background-color: #fff5f5; border-left: 5px solid #dc3545 !important;">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-danger bg-opacity-10 p-2 rounded-circle text-danger">
            <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
        </div>
        <div>
            <h6 class="fw-bold text-danger mb-1">Terjadi Kesalahan Input</h6>
            <ul class="mb-0 small text-secondary ps-3">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Form Wrapper (Logic Main + ID HEAD) --}}
<form action="{{ isset($pembelian) ? route('admin.purchases.update', $pembelian->id) : route('admin.purchases.store') }}" method="POST" id="formPembelian">
    @csrf
    @if(isset($pembelian))
        @method('PUT')
    @endif
    
    <input type="hidden" name="user_id" value="{{ Auth::id() ?? 1 }}">
    <input type="hidden" id="pembelian_id_hidden" name="pembelian_id" value="{{ $pembelian->id ?? '' }}">

    <div class="row g-4">

        {{-- KOLOM KIRI: Form Utama --}}
        <div class="col-lg-8">

            {{-- CARD 1: Informasi Transaksi --}}
            <div class="card card-modern mb-4">
                <div class="card-header-modern d-flex align-items-center gap-3">
                    <i class="fa-solid fa-file-invoice fa-lg text-primary"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Informasi Transaksi</h6>
                        <p class="text-muted small mb-0">Data pelanggan dan lokasi transaksi</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Customer --}}
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label-modern">Customer</label>
                            <div class="input-group input-group-modern mb-2">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                    <option value="" selected disabled>-- Pilih Customer --</option>
                                    @foreach($semua_customer as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ (old('customer_id') == $customer->id) || (isset($pembelian) && $pembelian->customer_id == $customer->id) ? 'selected' : '' }}>
                                        {{ $customer->nama }} ({{ $customer->no_telp }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer"
                                   class="small text-decoration-none fw-bold text-primary d-inline-flex align-items-center gap-1 ms-2">
                                    <i class="fa-solid fa-plus-circle"></i> Customer Baru
                                </a>
                            </div>
                        </div>

                        {{-- Lokasi Cabang --}}
                        <div class="col-md-6">
                            <label for="perusahaan_cabang_id" class="form-label-modern">Lokasi Transaksi</label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-store"></i></span>
                                <select class="form-select @error('perusahaan_cabang_id') is-invalid @enderror" id="perusahaan_cabang_id" name="perusahaan_cabang_id" required>
                                    @foreach($semua_cabang as $cabang)
                                    <option value="{{ $cabang->id }}"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: Daftar Item --}}
            <div class="card card-modern mb-4">
                <div class="card-header-modern d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-box-open fa-lg text-primary"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Keranjang Barang</h6>
                            <p class="text-muted small mb-0">Daftar unit yang akan dibeli</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-medium shadow-sm" id="btnBukaModalItem">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Item
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead class="bg-light text-secondary small uppercase">
                                <tr>
                                    <th class="ps-4 py-3 fw-bold border-0" style="width: 45%;">NAMA ITEM / KONDISI</th>
                                    <th class="py-3 fw-bold border-0">KATEGORI</th>
                                    <th class="py-3 fw-bold border-0">SN (SERIAL)</th>
                                    <th class="text-center py-3 fw-bold border-0" style="width: 80px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="item-list-wrapper" class="border-top-0">
                                {{-- Render JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Sticky Sidebar (Visual HEAD, Input Logic Main) --}}
        <div class="col-lg-4">
            <div class="card card-modern mb-4 position-sticky" style="top: 20px; z-index: 10;">
                <div class="card-header-modern bg-primary bg-opacity-10 border-primary border-opacity-10">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calculator"></i> Status & Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4">

                    {{-- Draft Button --}}
                    <div class="d-grid mb-4">
                        <button type="submit" name="status_pembelian" value="draft" id="btnDraft" class="btn btn-light border text-secondary py-2 fw-medium rounded-3 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i class="fas fa-save"></i> Simpan Sebagai Draft
                        </button>
                    </div>

                    <hr class="border-dashed my-4">

                    {{-- Inputs Harga: Menggunakan Logic Dual Input (Hidden + Display) --}}
                    
                    {{-- Tawaran Customer --}}
                    <div class="mb-3">
                        <label for="display_harga_tawaran_customer" class="form-label-modern">Tawaran Customer</label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            <input type="text" class="form-control fw-bold rupiah-mask" 
                                   id="display_harga_tawaran_customer" placeholder="0" 
                                   value="{{ old('harga_tawaran_customer', $pembelian->harga_tawaran_customer ?? '') }}">
                            <input type="hidden" name="harga_tawaran_customer" id="harga_tawaran_customer" 
                                   value="{{ old('harga_tawaran_customer', $pembelian->harga_tawaran_customer ?? '') }}">
                        </div>
                    </div>

                    {{-- Tawaran Toko --}}
                    <div class="mb-4">
                        <label for="display_harga_tawaran_toko" class="form-label-modern">Tawaran Toko</label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            <input type="text" class="form-control fw-bold rupiah-mask" 
                                   id="display_harga_tawaran_toko" placeholder="0" 
                                   value="{{ old('harga_tawaran_toko', $pembelian->harga_tawaran_toko ?? '') }}">
                            <input type="hidden" name="harga_tawaran_toko" id="harga_tawaran_toko" 
                                   value="{{ old('harga_tawaran_toko', $pembelian->harga_tawaran_toko ?? '') }}">
                        </div>
                    </div>

                    {{-- FINAL DEAL --}}
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-4 border border-primary border-opacity-25">
                        <label for="display_harga_deal" class="form-label-modern mb-1 text-primary">HARGA DEAL (FINAL)</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-primary text-white border-0 fw-bold px-3">Rp</span>
                            <input type="text" class="form-control border-0 fw-bold text-success fs-5 rupiah-mask" 
                                   id="display_harga_deal" placeholder="0" style="height: 50px;"
                                   value="{{ old('harga_deal', $pembelian->harga_deal ?? '') }}">
                            <input type="hidden" name="harga_deal" id="harga_deal" 
                                   value="{{ old('harga_deal', $pembelian->harga_deal ?? '') }}">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <button type="submit" name="status_pembelian" value="tidak_deal" id="btnNoDeal" class="btn btn-light border w-100 py-2 rounded-3 fw-medium">
                                <i class="fas fa-times me-1"></i> Batal
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="status_pembelian" value="deal" id="btnDeal" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm">
                                <i class="fas fa-check me-1"></i> DEAL
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Share Link (Mode Edit) --}}
            @if(isset($pembelian))
            <div class="card card-modern mb-4 border-dashed bg-light">
                <div class="card-body p-3">
                    <label class="form-label-modern small mb-2 text-muted"><i class="fa-solid fa-share-nodes me-1"></i> Link Transaksi</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm bg-white" id="shareable-link" value="{{ route('admin.purchases.show', $pembelian->id) }}" readonly>
                        <button class="btn btn-primary btn-sm" type="button" onclick="copyToClipboard()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>

{{-- MODAL TAMBAH ITEM (Visual HEAD Lengkap - FINAL FLOATING ACCORDION VERSION) --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            {{-- Header --}}
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-box-open text-primary me-3 fs-4"></i>
                    Tambah Item Pembelian
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div id="formTambahItem">
                    {{-- =========================================
                         SECTION 1: IDENTITAS BARANG
                         ========================================= --}}
                    <div class="mb-5">
                        <h6 class="fw-bold text-secondary mb-4 pb-2 border-bottom">
                            <span class="me-2 text-primary fw-black">1.</span> Identitas Barang
                        </h6>

                        <div class="row g-3 px-2">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-secondary small">Nama Item <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg fs-6 shadow-none" id="item_nama_item" placeholder="Contoh: Canon EOS 60D Body Only">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg fs-6 shadow-none" id="item_kategori_id">
                                    <option value="" selected disabled>Pilih...</option>
                                    @foreach($semua_kategori as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Serial Number (Body)</label>
                                <input type="text" class="form-control font-monospace shadow-none" id="item_serial_number" placeholder="SN Body">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Serial Number (Lensa)</label>
                                <input type="text" class="form-control font-monospace shadow-none" id="item_serial_lens" placeholder="SN Lensa (Jika ada)">
                            </div>
                        </div>
                    </div>


                    {{-- =========================================
                         SECTION 2: DETAIL KONDISI (Floating Cards Style)
                         ========================================= --}}
                    <div class="mb-3">
                        <h6 class="fw-bold text-secondary mb-4 pb-2 border-bottom">
                            <span class="me-2 text-primary fw-black">2.</span> Detail Kondisi & Kelengkapan
                        </h6>

                        {{-- PERUBAHAN DI SINI: --}}
                        {{-- 1. Hapus class 'border', 'rounded', 'overflow-hidden' dari wrapper induk --}}
                        <div class="accordion" id="accordionKondisi">

                            {{-- ITEM 1: Fisik --}}
                            {{-- 2. Tambahkan style card (border, rounded, mb-3, shadow-sm) di sini --}}
                            <div class="accordion-item bg-white mb-3 border rounded-3 overflow-hidden shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button bg-white shadow-none collapsed fw-semibold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFisik">
                                        <i class="fa-solid fa-camera me-3 text-primary opacity-75"></i> Kondisi Fisik Unit
                                    </button>
                                </h2>
                                <div id="collapseFisik" class="accordion-collapse collapse" data-bs-parent="#accordionKondisi">
                                    <div class="accordion-body bg-white pt-0 pb-4 px-4">
                                        <hr class="mt-0 mb-4 text-muted opacity-25">
                                        <div class="row g-3">
                                            <div class="col-md-6"><label class="form-label fw-semibold text-secondary small mb-1">Fisik Overall</label><input type="text" class="form-control form-control-sm shadow-none" id="item_kondisi_fisik" placeholder="Contoh: 95% Mulus"></div>
                                            <div class="col-md-6"><label class="form-label fw-semibold text-secondary small mb-1">Kondisi Baut</label><input type="text" class="form-control form-control-sm shadow-none" id="item_kondisi_baut" placeholder="Utuh/Segel/Lecet"></div>
                                            <div class="col-md-6"><label class="form-label fw-semibold text-secondary small mb-1">Karet Grip</label><input type="text" class="form-control form-control-sm shadow-none" id="item_kondisi_grip" placeholder="Rapat/Melar/Putih"></div>
                                            <div class="col-md-6"><label class="form-label fw-semibold text-secondary small mb-1">Tutup USB/Mic</label><input type="text" class="form-control form-control-sm shadow-none" id="item_kondisi_tutup_usb" placeholder="Ada/Putus/Hilang"></div>
                                            <div class="col-md-6"><label class="form-label fw-semibold text-secondary small mb-1">LCD</label><input type="text" class="form-control form-control-sm shadow-none" id="item_kondisi_lcd" placeholder="Vignette/Dead Pixel"></div>
                                            <div class="col-md-6"><label class="form-label fw-semibold text-secondary small mb-1">Tombol</label><input type="text" class="form-control form-control-sm shadow-none" id="item_kondisi_tombol" placeholder="Normal/Keras/Macet"></div>
                                            <div class="col-md-6"><label class="form-label fw-semibold text-secondary small mb-1">Mounting</label><input type="text" class="form-control form-control-sm shadow-none" id="item_kondisi_mounting" placeholder="Bersih/Kotor"></div>
                                            <div class="col-md-6"><label class="form-label fw-semibold text-secondary small mb-1">Slot Memori</label><input type="text" class="form-control form-control-sm shadow-none" id="item_kondisi_slot_memori" placeholder="Normal/Error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ITEM 2: Lainnya --}}
                            {{-- 3. Hapus 'border-top' manual, ganti dengan style card yang sama --}}
                            <div class="accordion-item bg-white mb-3 border rounded-3 overflow-hidden shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button bg-white shadow-none collapsed fw-semibold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLain">
                                        <i class="fa-solid fa-list-check me-3 text-primary opacity-75"></i> Fungsi Lain & Kelengkapan
                                    </button>
                                </h2>
                                <div id="collapseLain" class="accordion-collapse collapse" data-bs-parent="#accordionKondisi">
                                    <div class="accordion-body bg-white pt-0 pb-4 px-4">
                                         <hr class="mt-0 mb-4 text-muted opacity-25">
                                        <div>
                                            <label class="form-label fw-semibold text-secondary small"><i class="fa-solid fa-box-archive me-2 text-primary opacity-50"></i> Kelengkapan Awal</label>
                                            <textarea class="form-control shadow-none" rows="3" id="item_kelengkapan" placeholder="Contoh: Box, Charger, Baterai, Strap..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                <button type="button" class="btn btn-light bg-white border rounded-3 px-4 fw-semibold text-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-3 px-4 fw-semibold shadow-sm" id="btnSimpanItem">
                    <i class="fa-solid fa-save me-2"></i> Simpan Item
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.accordion-button:not(.collapsed) {
    color: var(--bs-dark); 
    background-color: #fff; /* Background putih saat aktif */
    box-shadow: inset 0 -1px 0 rgba(0,0,0,.125); /* Border bawah halus */
}
.accordion-button::after {
    /* Optional: Mengubah warna panah accordion jadi primary biar seragam */
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230d6efd'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}
</style>
@endpush

@endsection

@push('scripts')
<script>
    // 1. Mapping Kategori (Logic Main)
    let kategoriMap = {};
    @foreach($semua_kategori as $kat)
        kategoriMap[{{ $kat->id }}] = '{{ $kat->nama_kategori }}';
    @endforeach

    let currentPembelianId = '{{ $pembelian->id ?? '' }}';
    let initialItems = @json($pembelian->item_pembelian_draft ?? []);
    let itemsPembelian = [];
    
    if (initialItems.length > 0) {
        itemsPembelian = initialItems;
    }

    // Variables
    const form = document.getElementById('formPembelian');
    const btnKembali = document.getElementById('btnKembali');
    let isFormDirty = false;
    let hiddenHargaDeal, btnDraft, btnNoDeal, btnDeal;

    function markFormAsDirty() { if (!isFormDirty) isFormDirty = true; }

    document.addEventListener("DOMContentLoaded", function() {
        const mainForm = document.getElementById('formPembelian');
        if (!mainForm) return;

        const btnBukaModalItem = document.getElementById('btnBukaModalItem');
        const btnSimpanItem = document.getElementById('btnSimpanItem');
        const itemListWrapper = document.getElementById('item-list-wrapper');
        const modalTambahItem = new bootstrap.Modal(document.getElementById('modalTambahItem'));
        const hiddenPembelianIdInput = document.getElementById('pembelian_id_hidden');
        const customerSelect = document.getElementById('customer_id');
        const cabangSelect = document.getElementById('perusahaan_cabang_id');

        // Init Tombol Aksi
        hiddenHargaDeal = document.getElementById('harga_deal');
        btnDraft = document.getElementById('btnDraft');
        btnNoDeal = document.getElementById('btnNoDeal');
        btnDeal = document.getElementById('btnDeal');

        if (currentPembelianId) hiddenPembelianIdInput.value = currentPembelianId;

        renderItemList();

        // Event Buka Modal Item
        btnBukaModalItem.addEventListener('click', function() {
            if (!customerSelect.value || !cabangSelect.value) {
                alert('Harap pilih Customer dan Lokasi Transaksi terlebih dahulu.');
                return;
            }
            modalTambahItem.show();
        });

        // ********** FUNGSI KONTROL TOMBOL AKSI **********
        function checkDealButtonStatus() {
            const hasItems = itemsPembelian.length > 0;
            if (!hasItems || !btnDeal || !hiddenHargaDeal) return;
            const dealValue = parseInt(hiddenHargaDeal.value) || 0;
            btnDeal.disabled = (dealValue <= 0);
        }

        function controlActionButtons() {
            const hasItems = itemsPembelian.length > 0;
            const isDisabled = !hasItems;
            [btnDraft, btnNoDeal].forEach(btn => { if(btn) btn.disabled = isDisabled; });
            if (btnDeal) {
                if (isDisabled) btnDeal.disabled = true;
                else checkDealButtonStatus();
            }
        }

        // ********** SIMPAN ITEM (AJAX) - MERGED LOGIC **********
        btnSimpanItem.addEventListener('click', function() {
            const namaItem = document.getElementById('item_nama_item').value;
            const kategoriId = document.getElementById('item_kategori_id').value;

            if (!namaItem || !kategoriId) { alert('Nama Item dan Kategori wajib diisi.'); return; }

            const newItemData = {
                pembelian_id: currentPembelianId,
                customer_id: customerSelect.value,
                perusahaan_cabang_id: cabangSelect.value,
                user_id: mainForm.querySelector('input[name="user_id"]').value,
                // Data Utama
                nama_item: namaItem,
                kategori_id: kategoriId,
                // Data Tambahan dari HEAD (Detail Kondisi)
                serial_number: document.getElementById('item_serial_number').value,
                serial_lens: document.getElementById('item_serial_lens').value,
                kondisi_fisik: document.getElementById('item_kondisi_fisik').value,
                kondisi_baut: document.getElementById('item_kondisi_baut').value,
                kondisi_tutup_usb: document.getElementById('item_kondisi_tutup_usb').value,
                kondisi_grip: document.getElementById('item_kondisi_grip').value,
                kondisi_lcd: document.getElementById('item_kondisi_lcd').value,
                kondisi_tombol: document.getElementById('item_kondisi_tombol').value,
                kondisi_mounting: document.getElementById('item_kondisi_mounting').value,
                kondisi_slot_memori: document.getElementById('item_kondisi_slot_memori').value,
                kelengkapan_awal: document.getElementById('item_kelengkapan').value
            };

            btnSimpanItem.disabled = true;
            btnSimpanItem.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            fetch("{{ route('admin.purchases.ajaxStoreItemDraft') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(newItemData)
            })
            .then(response => { if (!response.ok) throw new Error('Gagal'); return response.json(); })
            .then(result => {
                if (result.success) {
                    currentPembelianId = result.pembelian_id;
                    hiddenPembelianIdInput.value = result.pembelian_id;
                    itemsPembelian.push(result.item);
                    renderItemList();
                    modalTambahItem.hide();
                    // Reset Form Item
                    document.querySelectorAll('#formTambahItem input').forEach(i => i.value = '');
                } else {
                    alert('Gagal: ' + result.message);
                }
            })
            .catch(err => alert('Gagal menyimpan data.'))
            .finally(() => {
                btnSimpanItem.disabled = false;
                btnSimpanItem.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Item';
            });
        });

        // ********** HAPUS ITEM **********
        window.hapusItem = function(id) {
            if (confirm('Yakin ingin menghapus item ini?')) {
                fetch(`/admin/purchases/delete-item-draft/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(result => {
                    if(result.success) {
                        itemsPembelian = itemsPembelian.filter(item => item.id !== id);
                        renderItemList();
                    } else alert('Gagal menghapus');
                });
            }
        }

        // ********** RENDER TABEL (Visual HEAD) **********
        function renderItemList() {
            itemListWrapper.innerHTML = '';
            if (itemsPembelian.length === 0) {
                itemListWrapper.innerHTML = `<tr><td colspan="4" class="text-center py-5"><div class="d-flex flex-column align-items-center opacity-50"><div class="bg-light rounded-circle p-3 mb-3 text-secondary"><i class="fa-solid fa-box-open fa-2x"></i></div><h6 class="text-secondary fw-bold mb-1">Keranjang Kosong</h6><p class="small text-muted mb-0">Belum ada item ditambahkan.</p></div></td></tr>`;
            } else {
                itemsPembelian.forEach((item) => {
                    let tr = document.createElement('tr');
                    let shortKondisi = item.kondisi_fisik || '-';
                    let kategoriNama = (item.kategori && item.kategori.nama_kategori) ? item.kategori.nama_kategori : (kategoriMap[item.kategori_id] || '-');
                    
                    tr.innerHTML = `
                        <td class="ps-4 py-3">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">${item.nama_item}</span>
                                <span class="small text-muted"><i class="fa-solid fa-circle-info me-1 text-info" style="font-size: 0.7rem;"></i>${shortKondisi}</span>
                            </div>
                        </td>
                        <td class="py-3"><span class="badge rounded-pill bg-white text-dark border border-secondary-subtle fw-normal px-3 py-2">${kategoriNama}</span></td>
                        <td class="py-3 font-monospace text-secondary small">${item.serial_number || '-'}</td>
                        <td class="text-center py-3">
                            <button type="button" class="btn-action-icon" title="Hapus Item" onclick="hapusItem(${item.id})"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    `;
                    itemListWrapper.appendChild(tr);
                });
            }
            controlActionButtons();
        }

        // ********** RUPIAH FORMATTING **********
        const rupiahInputs = document.querySelectorAll('.rupiah-mask');
        rupiahInputs.forEach(input => {
            const isDealInput = input.id === 'display_harga_deal';
            if (input.value) {
                const cleanValue = input.value.replace(/\D/g, '');
                input.value = new Intl.NumberFormat('id-ID').format(cleanValue);
            }
            input.addEventListener('keyup', function() {
                let cleanValue = this.value.replace(/\D/g, '');
                this.value = cleanValue ? new Intl.NumberFormat('id-ID').format(cleanValue) : '';
                const hiddenInput = document.getElementById(this.id.replace('display_', ''));
                if(hiddenInput) hiddenInput.value = cleanValue;
                if(isDealInput) checkDealButtonStatus();
                markFormAsDirty();
            });
        });

        // ********** MODAL CUSTOMER (Logic Main) **********
        const btnSimpanCustomer = document.getElementById('btnSimpanCustomer');
        const modalTambahCustomer = new bootstrap.Modal(document.getElementById('modalTambahCustomer'));
        const formTambahCustomer = document.getElementById('formTambahCustomer');

        btnSimpanCustomer.addEventListener('click', function() {
            const nama = document.getElementById('customer_nama_modal').value;
            const no_telp = document.getElementById('customer_no_telp_modal').value;
            if(!nama || !no_telp) { alert('Nama dan No. Telepon wajib diisi.'); return; }
            
            const formData = new FormData(formTambahCustomer);
            const data = Object.fromEntries(formData.entries());
            
            btnSimpanCustomer.disabled = true;
            fetch("{{ route('admin.customers.store') }}", {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(result => {
                if(result.success) {
                    const newOption = new Option(`${result.customer.nama} (${result.customer.no_telp})`, result.customer.id, true, true);
                    customerSelect.appendChild(newOption);
                    formTambahCustomer.reset();
                    modalTambahCustomer.hide();
                } else alert('Gagal menyimpan customer.');
            })
            .finally(() => btnSimpanCustomer.disabled = false);
        });

        // ********** COPY LINK **********
        window.copyToClipboard = function() {
            const linkInput = document.getElementById('shareable-link');
            linkInput.select();
            document.execCommand('copy');
        }
    });
</script>
@endpush