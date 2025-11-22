@extends('layouts.admin')

@section('title', 'Proses QC - ' . ($item->nama_item ?? 'Item'))

@push('page-actions')
    <a href="{{ route('admin.quality-control.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
@endpush

@section('content')

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

<form action="{{ route('admin.quality-control.update', $item->id) }}" method="POST" id="qcForm">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0" style="color:black;">Detail Item</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Item <span class="text-danger">*</span></label>
                        <input type="text" name="nama_item" class="form-control required-field" value="{{ old('nama_item', $item->nama_item) }}">
                        <div class="invalid-feedback" id="nama_item_error">
                            Nama Item wajib diisi
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" class="form-select required-field">
                                <option value="">Pilih Kategori</option>
                                @foreach($semua_kategori as $kat)
                                    <option value="{{ $kat->id }}" {{ (old('kategori_id', $item->kategori_id) == $kat->id) ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="kategori_id_error">
                                Kategori wajib dipilih
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode SKU <span class="text-danger">*</span></label>
                            <input type="text" name="kode_sku" class="form-control required-field" value="{{ old('kode_sku', $item->kode_sku) }}">
                            <div class="invalid-feedback" id="kode_sku_error">
                                Kode SKU wajib diisi
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $item->serial_number) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Lensa</label>
                            <input type="text" name="serial_lens" class="form-control" value="{{ old('serial_lens', $item->serial_lens) }}">
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Kondisi & Kelengkapan</h6>

                    <div class="accordion" id="qcConditionAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Kondisi Fisik Unit
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#qcConditionAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Fisik</label>
                                            <input type="text" name="kondisi_fisik" class="form-control" value="{{ old('kondisi_fisik', $item->kondisi_fisik) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Baut</label>
                                            <input type="text" name="kondisi_baut" class="form-control" value="{{ old('kondisi_baut', $item->kondisi_baut) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Tutup USB</label>
                                            <input type="text" name="kondisi_tutup_usb" class="form-control" value="{{ old('kondisi_tutup_usb', $item->kondisi_tutup_usb) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Grip</label>
                                            <input type="text" name="kondisi_grip" class="form-control" value="{{ old('kondisi_grip', $item->kondisi_grip) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Kondisi Lensa & Sensor
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#qcConditionAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Jamur Lensa</label>
                                            <input type="text" name="kondisi_jamur_lensa" class="form-control" value="{{ old('kondisi_jamur_lensa', $item->kondisi_jamur_lensa) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Jamur Sensor</label>
                                            <input type="text" name="kondisi_jamur_sensor" class="form-control" value="{{ old('kondisi_jamur_sensor', $item->kondisi_jamur_sensor) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi AF Lensa</label>
                                            <input type="text" name="kondisi_af_lensa" class="form-control" value="{{ old('kondisi_af_lensa', $item->kondisi_af_lensa) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Diafragma Lensa</label>
                                            <input type="text" name="kondisi_diafragma_lensa" class="form-control" value="{{ old('kondisi_diafragma_lensa', $item->kondisi_diafragma_lensa) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Zoom Lensa</label>
                                            <input type="text" name="kondisi_zoom_lensa" class="form-control" value="{{ old('kondisi_zoom_lensa', $item->kondisi_zoom_lensa) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi View Finder</label>
                                            <input type="text" name="kondisi_view_finder" class="form-control" value="{{ old('kondisi_view_finder', $item->kondisi_view_finder) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Kondisi Fungsi Lain & Kelengkapan
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#qcConditionAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Mounting</label>
                                            <input type="text" name="kondisi_mounting" class="form-control" value="{{ old('kondisi_mounting', $item->kondisi_mounting) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Slot Memori</label>
                                            <input type="text" name="kondisi_slot_memori" class="form-control" value="{{ old('kondisi_slot_memori', $item->kondisi_slot_memori) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi LCD</label>
                                            <input type="text" name="kondisi_lcd" class="form-control" value="{{ old('kondisi_lcd', $item->kondisi_lcd) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Tombol</label>
                                            <input type="text" name="kondisi_tombol" class="form-control" value="{{ old('kondisi_tombol', $item->kondisi_tombol) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Flash</label>
                                            <input type="text" name="kondisi_flash" class="form-control" value="{{ old('kondisi_flash', $item->kondisi_flash) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kondisi Sound / Mic</label>
                                            <input type="text" name="kondisi_sound_mic" class="form-control" value="{{ old('kondisi_sound_mic', $item->kondisi_sound_mic) }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kondisi Lain-lain</label>
                                        <input type="text" name="kondisi_lain_lain" class="form-control" value="{{ old('kondisi_lain_lain', $item->kondisi_lain_lain) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kelengkapan (Box, Baterai, Charger...)</label>
                                        <input type="text" name="kelengkapan" class="form-control" value="{{ old('kelengkapan', $item->kelengkapan) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Deskripsi Produk <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_produk" class="form-control required-field" rows="4">{{ old('deskripsi_produk', $item->deskripsi_produk) }}</textarea>
                        <div class="invalid-feedback" id="deskripsi_produk_error">
                            Deskripsi Produk wajib diisi
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 " style="color:black;">Harga & Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Harga Modal</label>
                        <input type="text" name="harga_beli" id="harga_beli" class="form-control rupiah-mask" value="{{ old('harga_beli', $item->harga_beli) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Servis</label>
                        <input type="text" name="harga_servis" id="harga_servis" class="form-control rupiah-mask" value="{{ old('harga_servis', $item->harga_servis) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                        <input type="text" name="harga_jual" id="harga_jual" class="form-control rupiah-mask required-field" value="{{ old('harga_jual', $item->harga_jual) }}">
                        <div class="invalid-feedback" id="harga_jual_error">
                            Harga Jual wajib diisi dan harus berupa angka lebih dari 0
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Qty</label>
                        <input type="number" name="qty" class="form-control" value="{{ old('qty', $item->qty) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <select name="grade" class="form-select">
                            <option value="Unggulan" {{ (old('grade', $item->grade) == 'Unggulan') ? 'selected' : '' }}>Unggulan</option>
                            <option value="Standar" {{ (old('grade', $item->grade) == 'Standar') ? 'selected' : '' }}>Standar</option>
                            <option value="Minus" {{ (old('grade', $item->grade) == 'Minus') ? 'selected' : '' }}>Minus</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Barang</label>
                        <select name="status" class="form-select">
                            <option value="Second" {{ (old('status', $item->status) == 'Second') ? 'selected' : '' }}>Second</option>
                            <option value="Baru" {{ (old('status', $item->status) == 'Baru') ? 'selected' : '' }}>Baru</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status QC</label>
                        <select name="status_qc" class="form-select">
                            <option value="menunggu_qc" {{ (old('status_qc', $item->status_qc) == 'menunggu_qc') ? 'selected' : '' }}>Menunggu QC</option>
                            <option value="lolos_qc" {{ (old('status_qc', $item->status_qc) == 'lolos_qc') ? 'selected' : '' }}>Lolos QC</option>
                            <option value="gagal_qc" {{ (old('status_qc', $item->status_qc) == 'gagal_qc') ? 'selected' : '' }}>Gagal QC</option>
                            <option value="diarsipkan" {{ (old('status_qc', $item->status_qc) == 'diarsipkan') ? 'selected' : '' }}>Diarsipkan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan QC</label>
                        <textarea name="catatan_qc" class="form-control" rows="3">{{ old('catatan_qc', $item->catatan_qc) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary w-100 btn-slim">Draft</button>
                        <button type="submit" name="action" value="save" class="btn btn-primary w-100 btn-slim">Simpan</button>
                        <button type="submit" name="action" value="archive" class="btn btn-danger w-100 btn-slim">Arsipkan</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</form>

@push('styles')
<style>
    .btn-slim {
        height: 40px;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-header.bg-light {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6;
    }

    .required-field:invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: none;
    }

    .required-field:invalid + .invalid-feedback {
        display: block;
    }

    .text-danger {
        color: #dc3545 !important;
    }
</style>
@endpush

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
                const confirmation = confirm("Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?");
                if (confirmation) {
                    isFormDirty = false; // Reset flag sebelum redirect
                    window.location.href = btnKembali.href;
                }
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
                    const confirmation = confirm("Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?");
                    if (confirmation) {
                        isFormDirty = false; // Reset flag sebelum redirect
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

@endsection
