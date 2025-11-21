@extends('layouts.admin')

@section('title', 'Buat Transaksi Pembelian')

@section('content')

{{-- Custom CSS untuk Halaman Ini --}}
@push('styles')
<style>
    /* Modern Card Style */
    .card-modern {
        border: 1px solid #f0f0f0;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
    }
    
    .card-header-modern {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px;
        border-radius: 16px 16px 0 0 !important;
    }

    /* Input Group Styling */
    .input-group-modern .input-group-text {
        background-color: #fff;
        border-right: none;
        color: #6c757d;
        border-color: #dee2e6;
        border-radius: 10px 0 0 10px;
    }

    .input-group-modern .form-control, 
    .input-group-modern .form-select {
        border-left: none;
        border-color: #dee2e6;
        border-radius: 0 10px 10px 0;
        padding: 10px 15px;
    }

    .input-group-modern .form-control:focus,
    .input-group-modern .form-select:focus {
        box-shadow: none;
        border-color: #86b7fe; 
        border-left: 1px solid #86b7fe; /* Kembalikan border saat fokus */
    }

    /* Labels */
    .form-label-modern {
        font-size: 0.85rem;
        font-weight: 600;
        color: #344767;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Action Buttons in Table */
    .btn-action-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    
    .btn-action-icon:hover {
        background-color: #fee2e2;
        color: #dc2626;
        border-color: #fecaca;
    }

    /* Accordion Styling */
    .accordion-modern .accordion-button {
        background-color: #f8f9fa;
        border-radius: 8px !important;
        color: #495057;
        font-weight: 600;
        box-shadow: none;
    }
    
    .accordion-modern .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0d6efd;
    }
    
    .accordion-modern .accordion-item {
        border: 1px solid #eee;
        border-radius: 8px !important;
        margin-bottom: 10px;
        overflow: hidden;
    }
</style>
@endpush

{{-- Tampilkan Error Validasi --}}
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

<form action="{{ route('admin.purchases.store') }}" method="POST" id="formPembelian">
    @csrf
    <input type="hidden" name="user_id" value="{{ Auth::id() ?? 1 }}">
    <input type="hidden" id="pembelian_id_hidden" name="pembelian_id" value="">

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
                                <select class="form-select @error('customer_id') is-invalid @enderror"
                                    id="customer_id" name="customer_id" required>
                                    <option value="" selected disabled>-- Pilih Customer --</option>
                                    @foreach($semua_customer as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->nama }} ({{ $customer->no_telp }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @error('customer_id')
                                <div class="text-danger small mb-1">{{ $message }}</div>
                            @enderror

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
                                <select class="form-select @error('perusahaan_cabang_id') is-invalid @enderror"
                                    id="perusahaan_cabang_id" name="perusahaan_cabang_id" required>
                                    @foreach($semua_cabang as $cabang)
                                    <option value="{{ $cabang->id }}"
                                        {{ (Auth::user()->cabang_id_default ?? 1) == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('perusahaan_cabang_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
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
                    @error('items')
                    <div class="alert alert-danger small m-3 border-0 bg-danger bg-opacity-10 text-danger rounded-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> {{ $message }}
                    </div>
                    @enderror

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

        {{-- KOLOM KANAN: Sticky Sidebar --}}
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
                        <button type="submit" name="status_pembelian" value="draft" class="btn btn-light border text-secondary py-2 fw-medium rounded-3 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i class="fas fa-save"></i> Simpan Sebagai Draft
                        </button>
                    </div>

                    <hr class="border-dashed my-4">

                    {{-- Inputs Harga --}}
                    <div class="mb-3">
                        <label for="harga_tawaran_customer" class="form-label-modern">Tawaran Customer</label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            <input type="number" class="form-control fw-bold" id="harga_tawaran_customer" name="harga_tawaran_customer" placeholder="0" value="{{ old('harga_tawaran_customer') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="harga_tawaran_toko" class="form-label-modern">Tawaran Toko</label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            <input type="number" class="form-control fw-bold" id="harga_tawaran_toko" name="harga_tawaran_toko" placeholder="0" value="{{ old('harga_tawaran_toko') }}">
                        </div>
                    </div>

                    {{-- FINAL DEAL --}}
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-4 border border-primary border-opacity-25">
                        <label for="harga_deal" class="form-label-modern mb-1">HARGA DEAL (FINAL)</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-primary text-white border-0 fw-bold px-3">Rp</span>
                            <input type="number" class="form-control border-0 fw-bold text-success fs-5" id="harga_deal" name="harga_deal" placeholder="0" value="{{ old('harga_deal') }}" style="height: 50px;">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <button type="submit" name="status_pembelian" value="tidak_deal" class="btn btn-light border w-100 py-2 rounded-3 fw-medium">
                                <i class="fas fa-times me-1"></i> Batal
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="status_pembelian" value="deal" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm">
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

{{-- ======================================================= --}}
{{-- MODAL TAMBAH CUSTOMER --}}
{{-- ======================================================= --}}
<div class="modal fade" id="modalTambahCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark">
                    <span class="bg-primary bg-opacity-10 p-2 rounded-2 text-primary me-2"><i class="fa-solid fa-user-plus"></i></span>
                    Tambah Customer Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formTambahCustomer">
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-3 small mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info text-primary"></i>
                        <span class="text-primary fw-medium">Data otomatis tersimpan setelah tombol simpan ditekan.</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-modern">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" class="form-control" id="customer_nama_modal" name="nama" required placeholder="Nama sesuai KTP">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">WhatsApp / Telepon <span class="text-danger">*</span></label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" class="form-control" id="customer_no_telp_modal" name="no_telp" required placeholder="0812xxxx">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">NIK (Identitas)</label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                                <input type="text" class="form-control" id="customer_identitas_modal" name="identitas" placeholder="Opsional">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Jenis Kelamin</label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-venus-mars"></i></span>
                                <select class="form-select" id="customer_jenis_kelamin_modal" name="jenis_kelamin">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label-modern">Alamat Lengkap</label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-map-location-dot"></i></span>
                                <textarea class="form-control" rows="2" id="customer_alamat_modal" name="alamat" placeholder="Masukkan alamat domisili..."></textarea>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <label class="form-label-modern">Referensi</label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-bullhorn"></i></span>
                                <input type="text" class="form-control" id="customer_referensi_modal" name="referensi" placeholder="Contoh: Instagram, Teman">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Catatan</label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-note-sticky"></i></span>
                                <input type="text" class="form-control" id="customer_keterangan_modal" name="keterangan" placeholder="Catatan khusus...">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border rounded-3 px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-3 px-4 fw-medium shadow-sm" id="btnSimpanCustomer">
                    <i class="fa-solid fa-save me-1"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- MODAL TAMBAH ITEM --}}
{{-- ======================================================= --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-white border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark">
                    <span class="bg-warning bg-opacity-10 p-2 rounded-2 text-warning me-2"><i class="fa-solid fa-box-open"></i></span>
                    Tambah Item Pembelian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="formTambahItem">

                    {{-- Section 1: Identitas Barang --}}
                    <div class="bg-light p-4 rounded-4 mb-4 border border-dashed">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wide text-muted border-bottom pb-2">
                            <i class="fa-solid fa-tag me-2 text-warning"></i>1. Identitas Barang
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label-modern">Nama Item <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg fs-6" id="item_nama_item" placeholder="Contoh: Canon EOS 60D Body Only">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-modern">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg fs-6" id="item_kategori_id">
                                    <option value="" selected disabled>Pilih...</option>
                                    @foreach($semua_kategori as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">Serial Number (Body)</label>
                                <input type="text" class="form-control font-monospace" id="item_serial_number" placeholder="SN Body">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">Serial Number (Lensa)</label>
                                <input type="text" class="form-control font-monospace" id="item_serial_lens" placeholder="SN Lensa (Jika ada)">
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Pengecekan (Accordion) --}}
                    <h6 class="fw-bold text-dark mb-3 px-1 small text-uppercase tracking-wide text-muted">
                        <i class="fa-solid fa-clipboard-check me-2 text-success"></i>2. Detail Kondisi & Kelengkapan
                    </h6>

                    <div class="accordion accordion-modern" id="accordionKondisi">
                        {{-- Accordion Item: Fisik --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFisik">
                                    <i class="fa-solid fa-camera me-2 text-secondary"></i> Kondisi Fisik Unit
                                </button>
                            </h2>
                            <div id="collapseFisik" class="accordion-collapse collapse" data-bs-parent="#accordionKondisi">
                                <div class="accordion-body bg-white">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Fisik Overall</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_fisik" placeholder="Contoh: 95% Mulus">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Kondisi Baut</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_baut" placeholder="Utuh/Segel/Lecet">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Karet Grip</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_grip" placeholder="Rapat/Melar/Putih">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Tutup USB/Mic</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_tutup_usb" placeholder="Ada/Putus/Hilang">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">LCD</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_lcd" placeholder="Vignette/Dead Pixel">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Tombol</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_tombol" placeholder="Normal/Keras/Macet">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Mounting</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_mounting" placeholder="Bersih/Kotor">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Slot Memori</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_slot_memori" placeholder="Normal/Error">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Accordion Item: Lensa --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLensa">
                                    <i class="fa-solid fa-bullseye me-2 text-secondary"></i> Kondisi Lensa & Optik
                                </button>
                            </h2>
                            <div id="collapseLensa" class="accordion-collapse collapse" data-bs-parent="#accordionKondisi">
                                <div class="accordion-body bg-white">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Jamur Lensa</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_jamur_lensa" placeholder="Clean/Bibit/Parah">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Jamur Sensor</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_jamur_sensor" placeholder="Clean/Debu Mikro">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">View Finder</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_view_finder" placeholder="Bersih/Kotor">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Zooming</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_zoom_lensa" placeholder="Lancar/Seret">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Auto Fokus (AF)</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_af_lensa" placeholder="Cepat/Mati/Berisik">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Aperture</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_diafragma_lensa" placeholder="Normal/Err01">
                                        </div>
                                        <div class="col-12">
                                            <label class="small fw-bold text-muted mb-1">Kalibrasi Fokus</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_kalibrasi_fokus" placeholder="Akurat/Miss Fokus">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Accordion Item: Lainnya --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLain">
                                    <i class="fa-solid fa-list-check me-2 text-secondary"></i> Fungsi Lain & Kelengkapan
                                </button>
                            </h2>
                            <div id="collapseLain" class="accordion-collapse collapse" data-bs-parent="#accordionKondisi">
                                <div class="accordion-body bg-white">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Flash</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_flash" placeholder="Nyala/Mati">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1">Mic/Sound</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_sound_mic" placeholder="Jelas/Kresek">
                                        </div>
                                        <div class="col-12">
                                            <label class="small fw-bold text-muted mb-1">Lain-lain</label>
                                            <input type="text" class="form-control form-control-sm" id="item_kondisi_lain_lain" placeholder="Wifi, Touchscreen, dll">
                                        </div>
                                    </div>
                                    <div class="border-top pt-3">
                                        <label class="form-label-modern"><i class="fa-solid fa-box-archive me-1"></i> Kelengkapan Awal</label>
                                        <textarea class="form-control" rows="2" id="item_kelengkapan" placeholder="Contoh: Box, Charger, Baterai, Strap..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border rounded-3 px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-3 px-4 fw-medium shadow-sm" id="btnSimpanItem">
                    <i class="fa-solid fa-save me-1"></i> Simpan Item
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Array ini sekarang hanya untuk TAMPILAN di tabel
    let itemsPembelian = [];
    // ID pembelian (induk) yang sedang aktif
    let currentPembelianId = null;

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

        // Render tabel (awalnya akan kosong)
        renderItemList();

        // Event Buka Modal Item
        btnBukaModalItem.addEventListener('click', function() {
            const customerId = customerSelect.value;
            const cabangId = cabangSelect.value;

            if (!customerId || !cabangId) {
                alert('Harap pilih Customer dan Lokasi Transaksi (Cabang) terlebih dahulu.');
                return;
            }
            modalTambahItem.show();
        });

        // Event Simpan Item (AJAX)
        btnSimpanItem.addEventListener('click', function() {
            const namaItem = document.getElementById('item_nama_item').value;
            const kategoriSelect = document.getElementById('item_kategori_id');
            const kategoriId = kategoriSelect.value;

            if (!namaItem || !kategoriId) {
                alert('Nama Item dan Kategori wajib diisi.');
                return;
            }

            const newItemData = {
                pembelian_id: currentPembelianId,
                customer_id: document.getElementById('customer_id').value,
                perusahaan_cabang_id: document.getElementById('perusahaan_cabang_id').value,
                user_id: mainForm.querySelector('input[name="user_id"]').value,
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

            btnSimpanItem.disabled = true;
            btnSimpanItem.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

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
                    if (!response.ok) return response.json().then(err => {
                        throw err;
                    });
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        currentPembelianId = result.pembelian_id;
                        hiddenPembelianIdInput.value = result.pembelian_id;
                        itemsPembelian.push(result.item);
                        renderItemList();

                        // Reset form & accordion
                        document.getElementById('formTambahItem').querySelectorAll('input, textarea, select').forEach(input => {
                            if (input.type === 'select-one') input.selectedIndex = 0;
                            else input.value = '';
                        });
                        document.querySelectorAll('#accordionKondisi .accordion-collapse').forEach(collapse => {
                            new bootstrap.Collapse(collapse, {
                                toggle: false
                            }).hide();
                        });
                        modalTambahItem.hide();
                    } else {
                        alert('Gagal: ' + (result.message || 'Error tidak diketahui.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMsg = 'Gagal menyimpan data.';
                    if (error.errors) errorMsg = Object.values(error.errors)[0][0];
                    else if (error.message) errorMsg = error.message;
                    alert(errorMsg);
                })
                .finally(() => {
                    btnSimpanItem.disabled = false;
                    btnSimpanItem.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Item';
                });
        });

        // Event Hapus Item (AJAX)
        window.hapusItem = function(id) {
            if (confirm('Yakin ingin menghapus item ini?')) {
                fetch(`/admin/purchases/delete-item-draft/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            itemsPembelian = itemsPembelian.filter(item => item.id !== id);
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

        // Render Tabel
        function renderItemList() {
            if (!itemListWrapper || !mainForm) return;
            itemListWrapper.innerHTML = '';

            // Hapus input hidden lama agar tidak duplikat saat submit final
            mainForm.querySelectorAll('input[name^="items["]').forEach(input => input.remove());
            mainForm.querySelectorAll('textarea[name^="items["]').forEach(input => input.remove());

            if (itemsPembelian.length === 0) {
                itemListWrapper.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <div class="bg-light rounded-circle p-3 mb-3 text-secondary">
                                    <i class="fa-solid fa-box-open fa-2x"></i>
                                </div>
                                <h6 class="text-secondary fw-bold mb-1">Keranjang Kosong</h6>
                                <p class="small text-muted mb-0">Belum ada item ditambahkan ke transaksi ini.</p>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                itemsPembelian.forEach((item) => {
                    let tr = document.createElement('tr');
                    let shortKondisi = item.kondisi_fisik || '-';
                    if (shortKondisi.length > 30) shortKondisi = shortKondisi.substring(0, 30) + '...';
                    let kategoriNama = (item.kategori && item.kategori.nama_kategori) ? item.kategori.nama_kategori : '-';

                    // Template String Updated for New Look
                    tr.innerHTML = `
                        <td class="ps-4 py-3">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">${item.nama_item}</span>
                                <span class="small text-muted"><i class="fa-solid fa-circle-info me-1 text-info" style="font-size: 0.7rem;"></i>${shortKondisi}</span>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge rounded-pill bg-white text-dark border border-secondary-subtle fw-normal px-3 py-2">
                                ${kategoriNama}
                            </span>
                        </td>
                        <td class="py-3 font-monospace text-secondary small">${item.serial_number || '-'}</td>
                        <td class="text-center py-3">
                            <button type="button" class="btn-action-icon" title="Hapus Item" onclick="hapusItem(${item.id})">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    `;
                    itemListWrapper.appendChild(tr);
                });
            }
        }

        // Script Simpan Customer (Modal)
        const btnSimpanCustomer = document.getElementById('btnSimpanCustomer');
        const modalTambahCustomer = new bootstrap.Modal(document.getElementById('modalTambahCustomer'));
        const formTambahCustomer = document.getElementById('formTambahCustomer');

        btnSimpanCustomer.addEventListener('click', function() {
            const nama = document.getElementById('customer_nama_modal').value;
            const no_telp = document.getElementById('customer_no_telp_modal').value;
            if (!nama || !no_telp) {
                alert('Nama dan No. Telepon customer wajib diisi.');
                return;
            }
            const formData = new FormData(formTambahCustomer);
            const data = Object.fromEntries(formData.entries());

            btnSimpanCustomer.disabled = true;
            btnSimpanCustomer.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

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
                    if (!response.ok) return response.json().then(err => {
                        throw err;
                    });
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        const customerSelect = document.getElementById('customer_id');
                        const newOption = new Option(`${result.customer.nama} (${result.customer.no_telp})`, result.customer.id, true, true);
                        customerSelect.appendChild(newOption);
                        formTambahCustomer.reset();
                        modalTambahCustomer.hide();
                    } else {
                        alert('Gagal menyimpan customer: ' + (result.message || 'Error.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMsg = 'Gagal menyimpan data.';
                    if (error.errors) errorMsg = Object.values(error.errors)[0][0];
                    alert(errorMsg);
                })
                .finally(() => {
                    btnSimpanCustomer.disabled = false;
                    btnSimpanCustomer.innerHTML = 'Simpan Data';
                });
        });

        // Script Copy Link
        window.copyToClipboard = function() {
            const linkInput = document.getElementById('shareable-link');
            if (linkInput) {
                linkInput.select();
                linkInput.setSelectionRange(0, 99999);
                document.execCommand('copy');
                alert('Link tersalin!');
            }
        }
    });
</script>
@endpush