@extends('layouts.admin')

@section('title', 'Proses QC - ' . ($item->nama_item ?? 'Item'))

@push('page-actions')
    <a href="{{ route('admin.quality-control.index') }}" class="btn btn-light border btn-sm d-flex align-items-center gap-2 text-secondary fw-medium">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
@endpush

@section('content')

{{-- Error Alert (Style HEAD) --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert" style="border-left: 5px solid #dc3545 !important;">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-danger"></i>
            <strong class="text-danger">Ada Kesalahan Input!</strong>
        </div>
        <ul class="mb-0 mt-1 small text-secondary">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.quality-control.update', $item->id) }}" method="POST" id="qcForm">
    @csrf
    @method('PUT')

    <div class="row">

        {{-- KOLOM KIRI: Detail & Kondisi (Style HEAD) --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-box-open me-2 text-primary"></i>Detail Item & Fisik
                    </h6>
                </div>

                <div class="card-body p-4">

                    {{-- Identitas Item --}}
                    <div class="mb-3">
                        {{-- Logic Main: Added asterisk and required-field class --}}
                        <label class="form-label fw-medium text-secondary small">Nama Item <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-tag"></i></span>
                            <input type="text" name="nama_item" class="form-control border-start-0 ps-2 required-field" value="{{ old('nama_item', $item->nama_item) }}">
                        </div>
                        {{-- Logic Main: Error Message --}}
                        <div class="invalid-feedback d-block-none" id="nama_item_error">
                            Nama Item wajib diisi
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Kategori <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-layer-group"></i></span>
                                {{-- Logic Main: Added required-field class --}}
                                <select name="kategori_id" class="form-select border-start-0 ps-2 required-field">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($semua_kategori as $kat)
                                        <option value="{{ $kat->id }}" {{ (old('kategori_id', $item->kategori_id) == $kat->id) ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Logic Main: Error Message --}}
                            <div class="invalid-feedback d-block-none" id="kategori_id_error">
                                Kategori wajib dipilih
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Kode SKU <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-barcode"></i></span>
                                {{-- Logic Main: Added required-field class --}}
                                <input type="text" name="kode_sku" class="form-control border-start-0 ps-2 required-field" value="{{ old('kode_sku', $item->kode_sku) }}">
                            </div>
                            {{-- Logic Main: Error Message --}}
                            <div class="invalid-feedback d-block-none" id="kode_sku_error">
                                Kode SKU wajib diisi
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Serial Number (Body)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-fingerprint"></i></span>
                                <input type="text" name="serial_number" class="form-control border-start-0 ps-2 font-monospace" value="{{ old('serial_number', $item->serial_number) }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Serial Number (Lensa)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-fingerprint"></i></span>
                                <input type="text" name="serial_lens" class="form-control border-start-0 ps-2 font-monospace" value="{{ old('serial_lens', $item->serial_lens) }}">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-clipboard-check me-2 text-success"></i>Pengecekan Kondisi
                    </h6>

                    <div class="accordion shadow-sm" id="qcConditionAccordion" style="border-radius: 8px; overflow: hidden;">

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
                                            <label class="form-label small text-muted">Kondisi Fisik</label>
                                            <input type="text" name="kondisi_fisik" class="form-control form-control-sm" value="{{ old('kondisi_fisik', $item->kondisi_fisik) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Kondisi Baut</label>
                                            <input type="text" name="kondisi_baut" class="form-control form-control-sm" value="{{ old('kondisi_baut', $item->kondisi_baut) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Tutup USB</label>
                                            <input type="text" name="kondisi_tutup_usb" class="form-control form-control-sm" value="{{ old('kondisi_tutup_usb', $item->kondisi_tutup_usb) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Karet Grip</label>
                                            <input type="text" name="kondisi_grip" class="form-control form-control-sm" value="{{ old('kondisi_grip', $item->kondisi_grip) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Accordion 2: Lensa --}}
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
                                            <input type="text" name="kondisi_jamur_lensa" class="form-control form-control-sm" value="{{ old('kondisi_jamur_lensa', $item->kondisi_jamur_lensa) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Jamur Sensor</label>
                                            <input type="text" name="kondisi_jamur_sensor" class="form-control form-control-sm" value="{{ old('kondisi_jamur_sensor', $item->kondisi_jamur_sensor) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Auto Fokus (AF)</label>
                                            <input type="text" name="kondisi_af_lensa" class="form-control form-control-sm" value="{{ old('kondisi_af_lensa', $item->kondisi_af_lensa) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Diafragma (Aperture)</label>
                                            <input type="text" name="kondisi_diafragma_lensa" class="form-control form-control-sm" value="{{ old('kondisi_diafragma_lensa', $item->kondisi_diafragma_lensa) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Zooming</label>
                                            <input type="text" name="kondisi_zoom_lensa" class="form-control form-control-sm" value="{{ old('kondisi_zoom_lensa', $item->kondisi_zoom_lensa) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">View Finder</label>
                                            <input type="text" name="kondisi_view_finder" class="form-control form-control-sm" value="{{ old('kondisi_view_finder', $item->kondisi_view_finder) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Accordion 3: Lainnya --}}
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
                                            <input type="text" name="kondisi_mounting" class="form-control form-control-sm" value="{{ old('kondisi_mounting', $item->kondisi_mounting) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Slot Memori</label>
                                            <input type="text" name="kondisi_slot_memori" class="form-control form-control-sm" value="{{ old('kondisi_slot_memori', $item->kondisi_slot_memori) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">LCD</label>
                                            <input type="text" name="kondisi_lcd" class="form-control form-control-sm" value="{{ old('kondisi_lcd', $item->kondisi_lcd) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Tombol</label>
                                            <input type="text" name="kondisi_tombol" class="form-control form-control-sm" value="{{ old('kondisi_tombol', $item->kondisi_tombol) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Flash</label>
                                            <input type="text" name="kondisi_flash" class="form-control form-control-sm" value="{{ old('kondisi_flash', $item->kondisi_flash) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-muted">Sound / Mic</label>
                                            <input type="text" name="kondisi_sound_mic" class="form-control form-control-sm" value="{{ old('kondisi_sound_mic', $item->kondisi_sound_mic) }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Kondisi Lain-lain</label>
                                        <input type="text" name="kondisi_lain_lain" class="form-control form-control-sm" value="{{ old('kondisi_lain_lain', $item->kondisi_lain_lain) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted fw-bold">Kelengkapan</label>
                                        <input type="text" name="kelengkapan" class="form-control form-control-sm" value="{{ old('kelengkapan', $item->kelengkapan) }}" placeholder="Box, Charger, dll">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0 mt-4">
                        <label class="form-label fw-medium text-secondary small">Deskripsi Produk (Final) <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_produk" class="form-control required-field" rows="4">{{ old('deskripsi_produk', $item->deskripsi_produk) }}</textarea>
                         {{-- Logic Main: Error Message --}}
                        <div class="invalid-feedback" id="deskripsi_produk_error">
                            Deskripsi Produk wajib diisi
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Harga & Status (Style HEAD) --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-calculator me-2 text-warning"></i>Harga & Status
                    </h6>
                </div>

                <div class="card-body p-4">
                    {{-- Harga Modal --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Harga Modal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="text" name="harga_beli" id="harga_beli" class="form-control border-start-0 ps-2 rupiah-mask" value="{{ old('harga_beli', $item->harga_beli) }}">
                        </div>
                    </div>

                    {{-- Harga Servis --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Estimasi Biaya Servis</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="text" name="harga_servis" id="harga_servis" class="form-control border-start-0 ps-2 rupiah-mask" value="{{ old('harga_servis', $item->harga_servis) }}">
                        </div>
                    </div>

                    {{-- Harga Jual --}}
                    <div class="mb-3">
                        {{-- Logic Main: Added asterisk and required-field class --}}
                        <label class="form-label fw-medium text-secondary small">Rencana Harga Jual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="text" name="harga_jual" id="harga_jual" class="form-control border-start-0 ps-2 rupiah-mask required-field" value="{{ old('harga_jual', $item->harga_jual) }}">
                        </div>
                        {{-- Logic Main: Error Message --}}
                        <div class="invalid-feedback" id="harga_jual_error">
                            Harga Jual wajib diisi dan harus berupa angka lebih dari 0
                        </div>
                    </div>

                    {{-- Qty --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Qty</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-cubes"></i></span>
                            <input type="number" name="qty" class="form-control border-start-0 ps-2" value="{{ old('qty', $item->qty) }}">
                        </div>
                    </div>

                    {{-- Grade & Status --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Grade</label>
                            <select name="grade" class="form-select">
                                <option value="Unggulan" {{ (old('grade', $item->grade) == 'Unggulan') ? 'selected' : '' }}>Unggulan</option>
                                <option value="Standar" {{ (old('grade', $item->grade) == 'Standar') ? 'selected' : '' }}>Standar</option>
                                <option value="Minus" {{ (old('grade', $item->grade) == 'Minus') ? 'selected' : '' }}>Minus</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Status Barang</label>
                            <select name="status" class="form-select">
                                <option value="Second" {{ (old('status', $item->status) == 'Second') ? 'selected' : '' }}>Second</option>
                                <option value="Baru" {{ (old('status', $item->status) == 'Baru') ? 'selected' : '' }}>Baru</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Status QC --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">STATUS AKHIR QC</label>
                        <select name="status_qc" class="form-select form-select-lg fw-bold {{ $item->status_qc == 'lolos_qc' ? 'text-success border-success' : 'text-secondary' }}">
                            <option value="menunggu_qc" {{ (old('status_qc', $item->status_qc) == 'menunggu_qc') ? 'selected' : '' }}>Menunggu QC</option>
                            <option value="lolos_qc" {{ (old('status_qc', $item->status_qc) == 'lolos_qc') ? 'selected' : '' }}>Lolos QC</option>
                            <option value="gagal_qc" {{ (old('status_qc', $item->status_qc) == 'gagal_qc') ? 'selected' : '' }}>Gagal QC</option>
                            <option value="diarsipkan" {{ (old('status_qc', $item->status_qc) == 'diarsipkan') ? 'selected' : '' }}>Diarsipkan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary small">Catatan QC Internal</label>
                        <textarea name="catatan_qc" class="form-control" rows="2" placeholder="Catatan untuk tim internal...">{{ old('catatan_qc', $item->catatan_qc) }}</textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="save" class="btn btn-primary fw-medium py-2">
                            <i class="fa-solid fa-save me-2"></i> Simpan Perubahan
                        </button>
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="submit" name="action" value="draft" class="btn btn-light border w-100 fw-medium text-secondary">
                                    Draft
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="action" value="archive" class="btn btn-outline-danger w-100 fw-medium">
                                    Arsipkan
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</form>

@endsection

{{-- Styles dari Main --}}
@push('styles')
<style>
    /* Tambahan dari Main untuk validasi */
    .required-field:invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: none;
        font-size: 0.875em;
        color: #dc3545;
        margin-top: 0.25rem;
    }

    /* Custom rule agar invalid feedback muncul saat sibling input invalid,
       bahkan jika di dalam input-group */
    .required-field.is-invalid ~ .invalid-feedback,
    .required-field.is-invalid ~ .invalid-feedback,
    .input-group:has(.required-field.is-invalid) ~ .invalid-feedback, /* Modern Browsers */
    .is-invalid + .invalid-feedback {
        display: block;
    }

    /* Fallback visual untuk input group jika is-invalid */
    .input-group > .form-control.is-invalid {
        border-color: #dc3545;
        z-index: 4; /* Ensure border is visible */
    }
    .input-group > .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

    .text-danger {
        color: #dc3545 !important;
    }
</style>
@endpush

{{-- Scripts dari Main --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('qcForm');
    const btnKembali = document.querySelector('a[href*="quality-control"]');
    let isFormDirty = false;

    function markFormAsDirty() {
        if (!isFormDirty) {
            isFormDirty = true;
        }
    }

    // Deteksi perubahan pada semua input
    if (form) {
        form.querySelectorAll('input, select, textarea').forEach(element => {
            // Skip hidden inputs dan submit buttons
            if (element.type === 'hidden' || element.type === 'submit') return;

            element.addEventListener('input', markFormAsDirty);
            element.addEventListener('change', markFormAsDirty);
        });

        // Reset status dirty saat form berhasil disubmit
        form.addEventListener('submit', function() {
            isFormDirty = false;
        });
    }

    // Konfirmasi saat klik tombol kembali
    if (btnKembali) {
        btnKembali.addEventListener('click', function(e) {
            if (isFormDirty) {
                e.preventDefault();
                window.confirmAction("Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?", "Konfirmasi", "Ya, tinggalkan", "Batal")
                    .then((result) => {
                        if (result.isConfirmed) {
                            isFormDirty = false; // Reset flag sebelum redirect
                            window.location.href = btnKembali.href;
                        }
                    });
            }
        });
    }

    // Konfirmasi saat pindah route (back button atau menu lain)
    window.addEventListener('beforeunload', function(e) {
        if (isFormDirty) {
            e.preventDefault();
            e.returnValue = 'Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?';
            return e.returnValue;
        }
    });

    // Intercept link di sidebar/menu (hanya yang ada di sidebar)
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.querySelectorAll('a[href]').forEach(link => {
            // Skip link yang ada di dalam form
            if (link.closest('form')) return;

            link.addEventListener('click', function(e) {
                if (isFormDirty) {
                    e.preventDefault();
                    window.confirmAction("Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?", "Konfirmasi", "Ya, tinggalkan", "Batal")
                        .then((result) => {
                            if (result.isConfirmed) {
                                isFormDirty = false; // Reset flag sebelum redirect
                                window.location.href = link.href;
                            }
                        });
                }
            });
        });
    }
                        window.location.href = link.href;
                    }
                }
            });
        });
    }

    const rupiahInputs = document.querySelectorAll('.rupiah-mask');

    function formatRupiah(angka) {
        if (!angka) return '';
        // remove non-digits
        let number_string = String(angka).replace(/[^0-9]/g, '');
        if (number_string === '') return '';
        return number_string.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    rupiahInputs.forEach(function(input){
        // initial format
        input.value = formatRupiah(input.value);

        input.addEventListener('input', function(e){
            const pos = this.selectionStart;
            this.value = formatRupiah(this.value);
            // best-effort: attempt to keep cursor at end
            this.selectionStart = this.selectionEnd = this.value.length;
        });
        input.addEventListener('blur', function(){
            this.value = formatRupiah(this.value);
        });
    });

    if (!form) return;

    // Validasi form
    form.addEventListener('submit', function(e){
        const action = e.submitter?.value || form.querySelector('button[type="submit"][name="action"]:focus')?.value || 'save';

        // strip formatting to plain digits before submit
        rupiahInputs.forEach(function(input){
            input.value = input.value ? input.value.replace(/\./g, '') : '';
        });

        // Reset semua error state
        form.querySelectorAll('.required-field').forEach(field => {
            field.classList.remove('is-invalid');
        });

        // Jika action adalah draft, skip validasi
        if (action === 'draft') {
            return true;
        }

        // Validasi untuk action save/archive
        let isValid = true;
        const errors = [];

        // Validasi Nama Item
        const nama = form.querySelector('[name="nama_item"]')?.value.trim() || '';
        if (!nama) {
            form.querySelector('[name="nama_item"]').classList.add('is-invalid');
            isValid = false;
        }

        // Validasi Kategori
        const kategori = form.querySelector('[name="kategori_id"]')?.value || '';
        if (!kategori) {
            form.querySelector('[name="kategori_id"]').classList.add('is-invalid');
            isValid = false;
        }

        // Validasi Kode SKU
        const kodeSku = form.querySelector('[name="kode_sku"]')?.value.trim() || '';
        if (!kodeSku) {
            form.querySelector('[name="kode_sku"]').classList.add('is-invalid');
            isValid = false;
        }

        // Validasi Deskripsi Produk
        const deskripsi = form.querySelector('[name="deskripsi_produk"]')?.value.trim() || '';
        if (!deskripsi) {
            form.querySelector('[name="deskripsi_produk"]').classList.add('is-invalid');
            isValid = false;
        }

        // Validasi Harga Jual
        const hargaJual = form.querySelector('[name="harga_jual"]')?.value.trim() || '';
        if (!hargaJual || !/^\d+$/.test(hargaJual) || parseInt(hargaJual) <= 0) {
            form.querySelector('[name="harga_jual"]').classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll ke error pertama
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
    });

    // Real-time validation untuk required fields
    form.querySelectorAll('.required-field').forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        // Validasi khusus untuk harga jual
        if (field.name === 'harga_jual') {
            field.addEventListener('blur', function() {
                const value = this.value.replace(/\./g, '');
                if (!value || !/^\d+$/.test(value) || parseInt(value) <= 0) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });
});
</script>
@endpush
