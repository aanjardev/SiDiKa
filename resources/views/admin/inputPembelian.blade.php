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

    .select2-container .select2-selection--single {
        height: 44px !important; /* Tinggi Select2 agar sesuai dengan input-group-modern */
        border-radius: 0 10px 10px 0 !important;
        padding: 7px 15px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    /* Autocomplete suggestions dropdown for customer search */
    #customer_suggestions {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        z-index: 2050;
        display: none;
        max-height: 280px;
        overflow: auto;
    }
</style>
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- Tema Select2 untuk Bootstrap 5 (Link ini mungkin perlu Anda ubah ke file lokal jika Anda memodifikasi tema) --}}
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

{{-- Tampilkan Error Validasi (Dari Main)
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
@endif --}}

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
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label-modern">Customer</label>
                            <div class="input-group input-group-modern mb-2" style="position: relative;">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>

                                {{-- Visible search input + hidden customer_id (single input area) --}}
                                <input type="text" class="form-control" id="customer_search" placeholder="Cari Nama atau No. Telp Customer..." aria-label="Cari Nama atau No. Telp Customer" value="{{ isset($pembelian) && $pembelian->customer ? $pembelian->customer->nama . ' (' . $pembelian->customer->no_telp . ')' : '' }}">
                                <input type="hidden" id="customer_id" name="customer_id" value="{{ isset($pembelian) && $pembelian->customer ? $pembelian->customer->id : '' }}">
                                {{-- Container for autocomplete suggestions --}}
                                <div id="customer_suggestions" class="dropdown-menu" style="width:100%;"></div>

                            </div>
                            <div class="invalid-feedback" id="customer_search_error" style="display:none;">
                                Customer wajib dipilih sebelum menambah item.
                            </div>
                            <div class="d-flex justify-content-between">
                                {{-- Tombol Modal Customer Baru --}}
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer"
                                   class="small text-decoration-none fw-bold text-primary d-inline-flex align-items-center gap-1 ms-2">
                                    <i class="fa-solid fa-plus-circle"></i> Customer Baru
                                </a>
                                <span id="customer_info_display" class="small text-muted me-2">
                                    {{-- Ini tidak lagi digunakan dengan Select2, tapi biarkan saja untuk berjaga-jaga --}}
                                </span>
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

                    {{-- Inputs Harga: Menggunakan Logic Dual Input (Hidden + Display) --}}

                    {{-- Tawaran Customer --}}
                    <div class="mb-3">
                        <label for="display_harga_tawaran_customer" class="form-label-modern">Tawaran Customer</label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            {{-- Tambahkan class is-invalid pada input yang terlihat --}}
                            <input type="text" class="form-control fw-bold rupiah-mask @error('harga_tawaran_customer') is-invalid @enderror"
                                id="display_harga_tawaran_customer" placeholder="0"
                                value="{{ old('harga_tawaran_customer', $pembelian->harga_tawaran_customer ?? '') }}">
                            <input type="hidden" name="harga_tawaran_customer" id="harga_tawaran_customer"
                                value="{{ old('harga_tawaran_customer', $pembelian->harga_tawaran_customer ?? '') }}">
                        </div>
                        {{-- Tampilkan pesan error di bawah input group --}}
                        @error('harga_tawaran_customer')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Tawaran Toko --}}
                    <div class="mb-4">
                        <label for="display_harga_tawaran_toko" class="form-label-modern">Tawaran Toko</label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            {{-- Tambahkan class is-invalid pada input yang terlihat --}}
                            <input type="text" class="form-control fw-bold rupiah-mask @error('harga_tawaran_toko') is-invalid @enderror"
                                id="display_harga_tawaran_toko" placeholder="0"
                                value="{{ old('harga_tawaran_toko', $pembelian->harga_tawaran_toko ?? '') }}">
                            <input type="hidden" name="harga_tawaran_toko" id="harga_tawaran_toko"
                                value="{{ old('harga_tawaran_toko', $pembelian->harga_tawaran_toko ?? '') }}">
                        </div>
                        {{-- Tampilkan pesan error di bawah input group --}}
                        @error('harga_tawaran_toko')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- FINAL DEAL --}}
                    {{-- Pembungkus utama untuk Harga Deal --}}
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-4 border border-primary border-opacity-25">
                        <label for="display_harga_deal" class="form-label-modern mb-1 text-primary">HARGA DEAL</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-primary text-white border-0 fw-bold px-3">Rp</span>
                            {{-- Tambahkan class is-invalid pada input yang terlihat --}}
                            <input type="text" class="form-control border-0 fw-bold text-success fs-5 rupiah-mask @error('harga_deal') is-invalid @enderror"
                                id="display_harga_deal" placeholder="0" style="height: 50px;"
                                value="{{ old('harga_deal', $pembelian->harga_deal ?? '') }}">
                            <input type="hidden" name="harga_deal" id="harga_deal"
                                value="{{ old('harga_deal', $pembelian->harga_deal ?? '') }}">
                        </div>
                        {{-- Tampilkan pesan error di bawah input group --}}
                        @error('harga_deal')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Action Buttons (Revisi: 3 Kolom) --}}
                    <div class="row g-2">
                        <div class="col-4">
                            <button type="submit" name="status_pembelian" value="draft" id="btnDraft" class="btn btn-light border w-100 py-2 rounded-3 fw-medium">
                                <i class="fas fa-save"></i> Draft
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="submit" name="status_pembelian" value="tidak_deal" id="btnNoDeal" class="btn btn-danger w-100 py-2 rounded-3 fw-medium">
                                <i class="fas fa-times"></i> No-Deal
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="submit" name="status_pembelian" value="deal" id="btnDeal" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm">
                                <i class="fas fa-check"></i> DEAL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- MODAL TAMBAH ITEM (Visual HEAD Lengkap - FINAL FLOATING ACCORDION VERSION) --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            {{-- Header --}}
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="modalTambahItemTitle">
                    <i class="fa-solid fa-box-open text-primary me-3 fs-4"></i>
                    <span class="modal-title-text">Tambah Item Pembelian</span>
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
                                <select class="form-select form-select-lg fs-6 shadow-none" id="item_kategori_id" style="height: calc(2.5rem + 10px);">
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
                        SECTION 2: DETAIL KONDISI (Struktur Baru)
                        ========================================= --}}
                    <div class="mb-3">
                        <h6 class="fw-bold text-secondary mb-4 pb-2 border-bottom">
                            <span class="me-2 text-primary fw-black">2.</span> Detail Kondisi & Kelengkapan
                        </h6>

                        <div class="accordion" id="qcConditionAccordion">

                            {{-- Accordion 1: Fisik --}}
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        <i class="fa-solid fa-camera me-2 text-secondary"></i> Kondisi Fisik Unit
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                                    <div class="accordion-body bg-light">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Kondisi Fisik Overall</label>
                                                <input type="text" id="item_kondisi_fisik" class="form-control form-control-sm" placeholder="Contoh: 95% Mulus">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Kondisi Baut</label>
                                                <input type="text" id="item_kondisi_baut" class="form-control form-control-sm" placeholder="Utuh/Segel/Lecet">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Tutup USB/Mic</label>
                                                <input type="text" id="item_kondisi_tutup_usb" class="form-control form-control-sm" placeholder="Ada/Putus/Hilang">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Karet Grip</label>
                                                <input type="text" id="item_kondisi_grip" class="form-control form-control-sm" placeholder="Rapat/Melar/Putih">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Accordion 2: Lensa & Sensor --}}
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        <i class="fa-solid fa-bullseye me-2 text-secondary"></i> Kondisi Lensa & Sensor
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                                    <div class="accordion-body bg-light">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Jamur Lensa</label>
                                                <input type="text" id="item_kondisi_jamur_lensa" class="form-control form-control-sm" placeholder="Tidak ada / Ada Tipis / Tebal">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Jamur Sensor</label>
                                                <input type="text" id="item_kondisi_jamur_sensor" class="form-control form-control-sm" placeholder="Tidak ada / Ada">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Auto Fokus (AF)</label>
                                                <input type="text" id="item_kondisi_af_lensa" class="form-control form-control-sm" placeholder="Normal / Error / Lambat">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Diafragma (Aperture)</label>
                                                <input type="text" id="item_kondisi_diafragma_lensa" class="form-control form-control-sm" placeholder="Normal / Blade error / Sticking">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Zooming</label>
                                                <input type="text" id="item_kondisi_zoom_lensa" class="form-control form-control-sm" placeholder="Normal / Seret / Macet">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Kalibrasi Fokus</label>
                                                <input type="text" id="item_kondisi_kalibrasi_fokus" class="form-control form-control-sm" placeholder="Normal / Front / Back">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Accordion 3: Fungsi Lain & Kelengkapan --}}
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                        <i class="fa-solid fa-list-check me-2 text-secondary"></i> Fungsi Lain & Kelengkapan
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                                    <div class="accordion-body bg-light">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Mounting</label>
                                                <input type="text" id="item_kondisi_mounting" class="form-control form-control-sm" placeholder="Bersih/Kotor">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Slot Memori</label>
                                                <input type="text" id="item_kondisi_slot_memori" class="form-control form-control-sm" placeholder="Normal/Error">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">LCD</label>
                                                <input type="text" id="item_kondisi_lcd" class="form-control form-control-sm" placeholder="Vignette/Dead Pixel">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Tombol</label>
                                                <input type="text" id="item_kondisi_tombol" class="form-control form-control-sm" placeholder="Normal/Keras/Macet">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Flash</label>
                                                <input type="text" id="item_kondisi_flash" class="form-control form-control-sm" placeholder="Normal / Mati / Berfungsi sebagian">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Sound / Mic</label>
                                                <input type="text" id="item_kondisi_sound_mic" class="form-control form-control-sm" placeholder="Normal / Kresek / Mati">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">View Finder</label>
                                            <input type="text" id="item_kondisi_view_finder" class="form-control form-control-sm" placeholder="Bersih / Kotor / Berjamur">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Kondisi Lain-lain</label>
                                            <input type="text" id="item_kondisi_lain_lain" class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted fw-bold">Kelengkapan</label>
                                            <input type="text" id="item_kelengkapan" class="form-control form-control-sm" placeholder="Box, Charger, Baterai, Strap...">
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

{{-- MODAL TAMBAH CUSTOMER (Revisi Form Sesuai Migrasi) --}}
<div class="modal fade" id="modalTambahCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-user-plus text-primary me-3 fs-4"></i>
                    Tambah Customer Baru
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahCustomer">
                <div class="modal-body p-4">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Nama Customer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control required-field" id="customer_nama_modal" name="nama" required>
                            <div class="invalid-feedback">
                                Nama customer wajib diisi.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control required-field" id="customer_no_telp_modal" name="no_telp" required>
                            <div class="invalid-feedback">
                                Nomor telepon wajib diisi.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select required-field" id="customer_jenis_kelamin_modal" name="jenis_kelamin" required style="height:calc(2.5rem + 10px);">
                                <option value="" selected disabled>Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <div class="invalid-feedback">
                                Jenis kelamin wajib dipilih.
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Alamat</label>
                            <input type="text" class="form-control" name="alamat">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">No. Identitas (KTP/SIM)</label>
                            <input type="text" class="form-control" name="identitas">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Referensi</label>
                            <input type="text" class="form-control" name="referensi">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light bg-white border rounded-3 px-4 fw-semibold text-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold shadow-sm" id="btnSimpanCustomer">
                        <i class="fa-solid fa-save me-2"></i> Simpan
                    </button>
                </div>
            </form>
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

#customer_search::placeholder {
    font-size: 0.9rem;
    color: #6c757d;
}

#modalTambahCustomer .required-field.is-invalid {
    border-color: #dc3545;
}

#modalTambahCustomer .required-field.is-invalid:focus {
    box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.25);
}

#modalTambahCustomer .invalid-feedback {
    display: none;
    font-size: 0.85rem;
    color: #dc3545;
    margin-top: 0.25rem;
}

#modalTambahCustomer .required-field.is-invalid + .invalid-feedback,
#modalTambahCustomer .form-select.is-invalid + .invalid-feedback,
#modalTambahCustomer .form-control.is-invalid + .invalid-feedback {
    display: block;
}
</style>
@endpush

@endsection

@push('scripts')
{{-- Langkah 1: Memuat JQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Langkah 2: Memuat Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // 1. Mapping Kategori (Logic Main) - TETAP
    let kategoriMap = {};
    @foreach($semua_kategori as $kat)
    kategoriMap[{{ $kat->id }}] = '{{ $kat->nama_kategori }}';
    @endforeach

    let currentPembelianId = '{{ $pembelian->id ?? '' }}';
    let initialItems = @json($pembelian->item_pembelian_draft ?? []);
    let itemsPembelian = [];
    let editingItemId = null;

    if (initialItems.length > 0) {
        itemsPembelian = initialItems;
    }

    // Variables
    const form = document.getElementById('formPembelian');
    const btnKembali = document.getElementById('btnKembali');
    let isFormDirty = false;
    let hiddenHargaDeal, btnDraft, btnNoDeal, btnDeal;

    function markFormAsDirty() { if (!isFormDirty) isFormDirty = true; }

    // ********** LANGKAH PENTING: Menggunakan jQuery Ready Function **********
    $(function() {
        const mainForm = document.getElementById('formPembelian');
        if (!mainForm) return;

        const btnBukaModalItem = document.getElementById('btnBukaModalItem');
        const btnSimpanItem = document.getElementById('btnSimpanItem');
        const itemListWrapper = document.getElementById('item-list-wrapper');
        const modalTambahItemEl = document.getElementById('modalTambahItem');
        const modalTambahItem = modalTambahItemEl ? new bootstrap.Modal(modalTambahItemEl) : null;
        const hiddenPembelianIdInput = document.getElementById('pembelian_id_hidden');

        // Elemen yang diperlukan
        const customerSearch = $('#customer_search'); // visible text input
        const customerSearchInput = document.getElementById('customer_search');
        const customerSearchError = document.getElementById('customer_search_error');
        const customerIdInput = document.getElementById('customer_id'); // hidden id input (pure JS)
        const customerSuggestions = $('#customer_suggestions');
        const cabangSelect = document.getElementById('perusahaan_cabang_id');
        const itemNamaInput = document.getElementById('item_nama_item');
        const modalTambahItemTitle = document.getElementById('modalTambahItemTitle');
        const modalTambahItemTitleText = modalTambahItemTitle ? modalTambahItemTitle.querySelector('.modal-title-text') : null;
        const userIdInput = mainForm.querySelector('input[name="user_id"]');

        const itemFieldMap = {
            nama_item: 'item_nama_item',
            kategori_id: 'item_kategori_id',
            serial_number: 'item_serial_number',
            serial_lens: 'item_serial_lens',
            kondisi_fisik: 'item_kondisi_fisik',
            kondisi_baut: 'item_kondisi_baut',
            kondisi_tutup_usb: 'item_kondisi_tutup_usb',
            kondisi_grip: 'item_kondisi_grip',
            kondisi_jamur_lensa: 'item_kondisi_jamur_lensa',
            kondisi_jamur_sensor: 'item_kondisi_jamur_sensor',
            kondisi_af_lensa: 'item_kondisi_af_lensa',
            kondisi_diafragma_lensa: 'item_kondisi_diafragma_lensa',
            kondisi_zoom_lensa: 'item_kondisi_zoom_lensa',
            kondisi_kalibrasi_fokus: 'item_kondisi_kalibrasi_fokus',
            kondisi_mounting: 'item_kondisi_mounting',
            kondisi_slot_memori: 'item_kondisi_slot_memori',
            kondisi_lcd: 'item_kondisi_lcd',
            kondisi_tombol: 'item_kondisi_tombol',
            kondisi_flash: 'item_kondisi_flash',
            kondisi_sound_mic: 'item_kondisi_sound_mic',
            kondisi_view_finder: 'item_kondisi_view_finder',
            kondisi_lain_lain: 'item_kondisi_lain_lain',
            kelengkapan: 'item_kelengkapan'
        };

        const getItemFieldElement = (key) => {
            const id = itemFieldMap[key];
            return id ? document.getElementById(id) : null;
        };

        const collectItemFormData = () => {
            const data = {};
            Object.keys(itemFieldMap).forEach(key => {
                const el = getItemFieldElement(key);
                data[key] = el ? el.value : '';
            });
            return data;
        };

        const populateItemForm = (data = {}) => {
            Object.keys(itemFieldMap).forEach(key => {
                const el = getItemFieldElement(key);
                if (!el) return;
                el.value = data[key] ?? '';
            });
        };

        const clearItemFormFields = () => {
            Object.keys(itemFieldMap).forEach(key => {
                const el = getItemFieldElement(key);
                if (!el) return;
                if (el.tagName === 'SELECT') {
                    el.value = '';
                } else {
                    el.value = '';
                }
            });
        };

        const setItemModalMode = (mode = 'add') => {
            const isEditMode = mode === 'edit';
            if (modalTambahItemTitleText) {
                modalTambahItemTitleText.textContent = isEditMode ? 'Edit Item Pembelian' : 'Tambah Item Pembelian';
            }
            if (btnSimpanItem) {
                btnSimpanItem.innerHTML = isEditMode
                    ? '<i class="fas fa-save me-1"></i> Update Item'
                    : '<i class="fas fa-save me-1"></i> Simpan Item';
            }
        };

        const prepareNewItemForm = () => {
            editingItemId = null;
            clearItemFormFields();
            setItemModalMode('add');
        };

        setItemModalMode('add');

        if (modalTambahItemEl) {
            modalTambahItemEl.addEventListener('shown.bs.modal', () => {
                if (itemNamaInput) itemNamaInput.focus();
            });
            modalTambahItemEl.addEventListener('hidden.bs.modal', () => {
                prepareNewItemForm();
            });
        }
        if (cabangSelect) {
            cabangSelect.addEventListener('change', () => cabangSelect.classList.remove('is-invalid'));
        }

        // ********** SIMPLE AUTOCOMPLETE FOR CUSTOMER (AJAX) **********
        function debounce(fn, delay){
            let t;
            return function(){
                const args = arguments;
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), delay);
            };
        }

        function normalizeItem(item){
            if (!item) return null;
            if (typeof item.text !== 'undefined') return { id: item.id, text: item.text };
            const name = item.nama || item.name || item.full_name || '';
            const phone = item.no_telp || item.phone || item.telepon || '';
            let text = name || phone || '';
            if (name && phone) text = name + ' (' + phone + ')';
            return { id: item.id, text: text };
        }

        function renderSuggestions(items){
            customerSuggestions.empty();
            if (!items || items.length === 0) {
                customerSuggestions.hide();
                return;
            }
            items.forEach(i => {
                const el = $('<a href="#" class="dropdown-item">').text(i.text).data('item', i);
                customerSuggestions.append(el);
            });
            customerSuggestions.show();
        }

        const fetchCustomers = debounce(function(query){
            if (!query || query.length < 3) { customerSuggestions.hide(); return; }
            fetch(`{{ route('admin.customers.search') }}?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    const mapped = (Array.isArray(data) ? data : []).map(normalizeItem).filter(x => x);
                    renderSuggestions(mapped);
                })
                .catch(err => { console.error(err); customerSuggestions.hide(); });
        }, 220);

        function showCustomerSelectionError(message) {
            if (!customerSearchInput) return;
            customerSearchInput.classList.add('is-invalid');
            if (customerSearchError) {
                customerSearchError.textContent = message || 'Customer wajib dipilih sebelum menambah item.';
                customerSearchError.style.display = 'block';
            }
        }

        function clearCustomerSelectionError() {
            if (!customerSearchInput) return;
            customerSearchInput.classList.remove('is-invalid');
            if (customerSearchError) {
                customerSearchError.style.display = 'none';
            }
        }

        // Autofocus behaviour: if new pembelian, focus the search input
        if (!currentPembelianId || currentPembelianId === ''){
            setTimeout(() => { customerSearch.focus(); }, 120);
        }

        // Input handlers
        customerSearch.on('input', function(){
            const v = $(this).val();
            // clear selected id when typing
            customerIdInput.value = '';
            fetchCustomers(v);
            markFormAsDirty();
            clearCustomerSelectionError();
        });

        // Click on suggestion
        customerSuggestions.on('click', '.dropdown-item', function(e){
            e.preventDefault();
            const item = $(this).data('item');
            if (!item) return;
            customerSearch.val(item.text);
            customerIdInput.value = item.id;
            customerSuggestions.hide();
            markFormAsDirty();
            clearCustomerSelectionError();
        });

        // Hide suggestions on outside click
        $(document).on('click', function(e){
            if (!$(e.target).closest('#customer_search').length && !$(e.target).closest('#customer_suggestions').length) {
                customerSuggestions.hide();
            }
        });

        // Event listener untuk menandai form kotor (handled by our input handlers)

        // Init Tombol Aksi
        hiddenHargaDeal = document.getElementById('harga_deal');
        btnDraft = document.getElementById('btnDraft');
        btnNoDeal = document.getElementById('btnNoDeal');
        btnDeal = document.getElementById('btnDeal');

        if (currentPembelianId) hiddenPembelianIdInput.value = currentPembelianId;

        renderItemList();

        // Event Buka Modal Item
        btnBukaModalItem.addEventListener('click', function() {
            let hasError = false;
            if (!customerIdInput.value) {
                showCustomerSelectionError();
                if (customerSearchInput) customerSearchInput.focus();
                hasError = true;
            }
            if (!cabangSelect?.value) {
                cabangSelect?.classList.add('is-invalid');
                if (!hasError && cabangSelect) cabangSelect.focus();
                hasError = true;
            }
            if (hasError || !modalTambahItem) return;
            prepareNewItemForm();
            modalTambahItem.show();
        });

        // ********** FUNGSI KONTROL TOMBOL AKSI ********** (TETAP)
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

        // ********** SIMPAN ITEM (AJAX) - TETAP **********
        btnSimpanItem.addEventListener('click', function() {
            const formValues = collectItemFormData();
            const namaItem = (formValues.nama_item || '').trim();
            const kategoriId = formValues.kategori_id;

            if (!namaItem || !kategoriId) {
                alert('Nama Item dan Kategori wajib diisi.');
                return;
            }

            formValues.nama_item = namaItem;

            const isEditing = Boolean(editingItemId);
            const payload = isEditing
                ? formValues
                : {
                    pembelian_id: currentPembelianId,
                    customer_id: customerIdInput.value,
                    perusahaan_cabang_id: cabangSelect.value,
                    user_id: userIdInput?.value || '',
                    ...formValues,
                    kelengkapan_awal: formValues.kelengkapan
                };

            btnSimpanItem.disabled = true;
            btnSimpanItem.innerHTML = isEditing
                ? '<i class="fas fa-spinner fa-spin"></i> Mengupdate...'
                : '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            const requestPromise = isEditing
                ? fetch(`/admin/purchases/update-item-draft/${editingItemId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                })
                : fetch("{{ route('admin.purchases.ajaxStoreItemDraft') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });

            requestPromise
                .then(response => { if (!response.ok) throw new Error('Gagal'); return response.json(); })
                .then(result => {
                    if (!result.success) {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan.'));
                        return;
                    }

                    if (isEditing) {
                        itemsPembelian = itemsPembelian.map(item => item.id === editingItemId ? result.item : item);
                        renderItemList();
                        markFormAsDirty();
                        modalTambahItem?.hide();
                    } else {
                        currentPembelianId = result.pembelian_id;
                        hiddenPembelianIdInput.value = result.pembelian_id;
                        itemsPembelian.push(result.item);
                        renderItemList();
                        markFormAsDirty();
                        modalTambahItem?.hide();
                    }
                })
                .catch(() => alert('Gagal menyimpan data.'))
                .finally(() => {
                    btnSimpanItem.disabled = false;
                    setItemModalMode(isEditing ? 'edit' : 'add');
                });
        });

        // ********** FUNGSI MODAL CUSTOMER BARU **********
        const btnSimpanCustomer = document.getElementById('btnSimpanCustomer');
        const modalTambahCustomerEl = document.getElementById('modalTambahCustomer');
        const modalTambahCustomer = new bootstrap.Modal(modalTambahCustomerEl);
        const formTambahCustomer = document.getElementById('formTambahCustomer');
        const customerNamaInput = document.getElementById('customer_nama_modal');
        const customerNoTelpInput = document.getElementById('customer_no_telp_modal');
        const customerGenderSelect = document.getElementById('customer_jenis_kelamin_modal');
        const customerRequiredFields = [customerNamaInput, customerNoTelpInput, customerGenderSelect].filter(Boolean);

        const getCustomerFieldValue = (field) => {
            if (!field) return '';
            return field.tagName === 'SELECT' ? field.value : field.value.trim();
        };

        const validateCustomerForm = () => {
            let isValid = true;
            customerRequiredFields.forEach(field => {
                if (!getCustomerFieldValue(field)) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            return isValid;
        };

        const resetCustomerValidation = () => {
            customerRequiredFields.forEach(field => field.classList.remove('is-invalid'));
        };

        customerRequiredFields.forEach(field => {
            const eventType = field.tagName === 'SELECT' ? 'change' : 'input';
            field.addEventListener(eventType, () => {
                if (getCustomerFieldValue(field)) {
                    field.classList.remove('is-invalid');
                }
            });
        });

        if (modalTambahCustomerEl && customerNamaInput) {
            modalTambahCustomerEl.addEventListener('shown.bs.modal', () => {
                customerNamaInput.focus();
            });
        }

        btnSimpanCustomer.addEventListener('click', function(e) {
            e.preventDefault();
            if (!validateCustomerForm()) {
                const firstInvalid = customerRequiredFields.find(field => field.classList.contains('is-invalid'));
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            const formData = new FormData(formTambahCustomer);
            const data = Object.fromEntries(formData.entries());

            btnSimpanCustomer.disabled = true;
            btnSimpanCustomer.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            fetch("{{ route('admin.customers.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' }, // Menggunakan jQuery untuk CSRF jika perlu
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(result => {
                    if(result.success) {
                        // Set hidden id and visible search input to the created customer
                        customerIdInput.value = result.customer.id;
                        customerSearch.val(result.customer.nama + ' (' + result.customer.no_telp + ')');
                        clearCustomerSelectionError();

                        formTambahCustomer.reset();
                        resetCustomerValidation();
                        modalTambahCustomer.hide();
                    } else {
                    alert('Gagal menyimpan customer: ' + (result.message || 'Terjadi kesalahan.'));
                }
            })
            .catch(err => alert('Gagal menyimpan customer. Silakan coba lagi.'))
            .finally(() => {
                btnSimpanCustomer.disabled = false;
                btnSimpanCustomer.innerHTML = '<i class="fa-solid fa-save me-2"></i> Simpan';
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

        window.editItem = function(id) {
            const targetItem = itemsPembelian.find(item => item.id === id);
            if (!targetItem || !modalTambahItem) return;
            editingItemId = id;
            populateItemForm(targetItem);
            setItemModalMode('edit');
            modalTambahItem.show();
        }

        // ********** RENDER TABEL (Visual HEAD) **********
        function renderItemList() {
            itemListWrapper.innerHTML = '';
            if (itemsPembelian.length === 0) {
                itemListWrapper.innerHTML = `<tr><td colspan="4" class="text-center py-5"><div class="d-flex flex-column align-items-center opacity-50"><div class="bg-light rounded-circle p-3 mb-3 text-secondary"><i class="fa-solid fa-box-open fa-2x"></i></div><h6 class="text-secondary fw-bold mb-1">Keranjang Kosong</h6><p class="small text-muted mb-0">Belum ada item ditambahkan.</p></div></td></tr>`;
            } else {
                itemsPembelian.forEach((item) => {
                    let tr = document.createElement('tr');
                    let summaryText = item.kondisi_fisik || item.serial_number || '-';
                    if (item.kondisi_fisik && item.serial_number) {
                        summaryText = `${item.kondisi_fisik} (SN: ${item.serial_number})`;
                    } else if (item.kondisi_fisik) {
                        summaryText = item.kondisi_fisik;
                    } else if (item.serial_number) {
                        summaryText = `SN: ${item.serial_number}`;
                    } else {
                        summaryText = '-';
                    }

                    let kategoriNama = (item.kategori && item.kategori.nama_kategori) ? item.kategori.nama_kategori : (kategoriMap[item.kategori_id] || '-');

                    tr.innerHTML = `
                        <td class="ps-4 py-3">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">${item.nama_item}</span>
                                <span class="small text-muted"><i class="fa-solid fa-circle-info me-1 text-info" style="font-size: 0.7rem;"></i>${summaryText}</span>
                            </div>
                        </td>
                        <td class="py-3"><span class="badge rounded-pill bg-white text-dark border border-secondary-subtle fw-normal px-3 py-2">${kategoriNama}</span></td>
                        <td class="py-3 font-monospace text-secondary small">${item.serial_number || '-'}</td>
                        <td class="text-center py-3">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn-action-icon" title="Edit Item" onclick="editItem(${item.id})">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn-action-icon" title="Hapus Item" onclick="hapusItem(${item.id})">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
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

    });
</script>
@endpush
