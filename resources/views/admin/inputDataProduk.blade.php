@extends('layouts.admin')

@php
    $isEdit = isset($product);
@endphp

@section('title', $isEdit ? 'Edit Data Produk' : 'Tambah Data Produk')

@push('page-actions')
    @php
        $backRoute = route('admin.products.index');
    @endphp

    <a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnKembali">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
@endpush

@section('content')

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0"
             role="alert"
             style="border-left: 5px solid #dc3545 !important;">
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

    <form action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="row">

            {{-- KOLOM KIRI: Informasi Utama & Media --}}
            <div class="col-lg-8">

                {{-- CARD 1: INFORMASI PRODUK --}}
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-box-open me-2 text-primary"></i>
                            Informasi Produk
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Nama Produk --}}
                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-medium">Nama Produk</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-tag"></i>
                                </span>
                                <input
                                    type="text"
                                    class="form-control border-start-0 ps-2 py-2 @error('nama_produk') is-invalid @enderror"
                                    name="nama_produk"
                                    value="{{ old('nama_produk', $isEdit ? $product->nama_produk : '') }}"
                                    placeholder="Contoh: Kamera Canon EOS 600D"
                                    required>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-0">
                            <label class="form-label text-secondary small fw-medium">Deskripsi Lengkap</label>
                            <textarea
                                class="form-control py-2 @error('deskripsi_produk') is-invalid @enderror"
                                name="deskripsi_produk"
                                rows="5"
                                placeholder="Tuliskan spesifikasi, kelengkapan, dan kondisi detail produk...">{{ old('deskripsi_produk', $isEdit ? $product->deskripsi_produk : '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: MEDIA PRODUK --}}
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-images me-2 text-primary"></i>
                            Media Produk
                        </h6>
                    </div>

                    <div class="card-body p-4">

                        <div class="small text-muted mb-3">
                            <i class="fa-solid fa-circle-info me-1 text-primary"></i>
                            Maksimal <strong>10 gambar</strong> (Max 5MB/file). Klik kotak di bawah untuk memilih gambar.
                        </div>

                        {{-- HIDDEN INPUT (diisi otomatis oleh JS) --}}
                        <input type="file" id="image-input-hidden" name="images[]" multiple class="d-none">

                        {{-- Upload grid modern --}}
                        <div id="upload-grid" class="d-flex flex-wrap gap-3">
                            {{-- JS akan generate upload-box --}}
                        </div>

                        {{-- Status upload --}}
                        <div id="upload-status" class="mt-3"></div>

                        {{-- Gambar Saat Ini --}}
                        @if ($isEdit && isset($product) && $product->photos && $product->photos->count())
                            <hr class="my-5 opacity-25">
                            <h6 class="fw-bold text-dark mb-3">Gambar Saat Ini</h6>

                            <div id="current-images" class="d-flex flex-wrap gap-3">
                                @foreach ($product->photos as $photo)
                                    <div class="card border position-relative"
                                         style="width: 160px;"
                                         data-image-id="{{ $photo->id }}">
                                        <img
                                            src="{{ asset('storage/' . $photo->path) }}"
                                            class="card-img-top"
                                            style="height: 160px; object-fit: cover;"
                                            alt="Gambar produk">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if ($photo->is_main)
                                                        <span class="badge bg-primary">Utama</span>
                                                    @else
                                                        <span class="badge bg-secondary">Tambahan</span>
                                                    @endif
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-info btn-set-main"
                                                        data-image-id="{{ $photo->id }}"
                                                        title="Set sebagai Gambar Utama">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger remove-image"
                                                        data-image-id="{{ $photo->id }}"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: Inventaris & Harga --}}
            <div class="col-lg-4">

                {{-- CARD 3: KLASIFIKASI & STOK --}}
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-list-check me-2 text-warning"></i>
                            Klasifikasi & Stok
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Kategori --}}
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-medium">Kategori</label>
                            <select
                                class="form-select py-2 text-secondary @error('id_kategori') is-invalid @enderror"
                                name="id_kategori">
                                <option value="" disabled
                                    {{ old('id_kategori', $isEdit ? $product->id_kategori : '') ? '' : 'selected' }}>
                                    -- Pilih Kategori --
                                </option>
                                @foreach ($semua_kategori as $kategori)
                                    <option
                                        value="{{ $kategori->id }}"
                                        {{ old('id_kategori', $isEdit ? $product->id_kategori : '') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SKU --}}
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-medium">Kode SKU</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-barcode"></i>
                                </span>
                                <input
                                    type="text"
                                    class="form-control border-start-0 ps-2 py-2 @error('kode_sku') is-invalid @enderror"
                                    name="kode_sku"
                                    value="{{ old('kode_sku', $isEdit ? $product->kode_sku : '') }}"
                                    placeholder="SKU-0000">
                            </div>
                        </div>

                        {{-- Stok --}}
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-medium">Stok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-cubes"></i>
                                </span>
                                <input
                                    type="number"
                                    class="form-control border-start-0 ps-2 py-2 @error('stok_produk') is-invalid @enderror"
                                    name="stok_produk"
                                    value="{{ old('stok_produk', $isEdit ? $product->stok_produk : '') }}"
                                    placeholder="0"
                                    min="0">
                            </div>
                        </div>

                        {{-- Kondisi & Grade --}}
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label text-secondary small fw-medium">Kondisi</label>
                                <select class="form-select py-2 text-secondary" name="status">
                                    <option
                                        value="Second"
                                        {{ old('status', $isEdit ? $product->status : '') === 'Second' ? 'selected' : '' }}>
                                        Second
                                    </option>
                                    <option
                                        value="Baru"
                                        {{ old('status', $isEdit ? $product->status : '') === 'Baru' ? 'selected' : '' }}>
                                        Baru
                                    </option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-secondary small fw-medium">Grade</label>
                                <select class="form-select py-2 text-secondary" name="grade">
                                    <option
                                        value="Unggulan"
                                        {{ old('grade', $isEdit ? $product->grade : '') === 'Unggulan' ? 'selected' : '' }}>
                                        Unggulan
                                    </option>
                                    <option
                                        value="Standar"
                                        {{ old('grade', $isEdit ? $product->grade : '') === 'Standar' ? 'selected' : '' }}>
                                        Standar
                                    </option>
                                    <option
                                        value="Minus"
                                        {{ old('grade', $isEdit ? $product->grade : '') === 'Minus' ? 'selected' : '' }}>
                                        Minus
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- CARD 4: HARGA --}}
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-tags me-2 text-success"></i>
                            Harga
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Harga Beli --}}
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-medium">Harga Beli (Modal)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                                <input
                                    type="number"
                                    class="form-control border-start-0 ps-2 py-2"
                                    name="harga_beli"
                                    value="{{ old('harga_beli', $isEdit ? $product->harga_beli : '') }}"
                                    placeholder="0">
                            </div>
                        </div>

                        {{-- Harga Jual --}}
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-medium">Harga Jual</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                                <input
                                    type="number"
                                    class="form-control border-start-0 ps-2 py-2"
                                    name="harga_jual"
                                    value="{{ old('harga_jual', $isEdit ? $product->harga_jual : '') }}"
                                    placeholder="0">
                            </div>
                        </div>

                        {{-- Harga Servis --}}
                        <div class="mb-0">
                            <label class="form-label text-secondary small fw-medium">Estimasi Servis</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                                <input
                                    type="number"
                                    class="form-control border-start-0 ps-2 py-2"
                                    name="harga_servis"
                                    value="{{ old('harga_servis', $isEdit ? $product->harga_servis : '') }}"
                                    placeholder="0">
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        {{-- FOOTER ACTION BAR: Tombol Simpan --}}
        <div class="d-flex justify-content-end align-items-center p-4">
            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                <i class="fa-solid fa-save me-2"></i>
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk Baru' }}
            </button>
        </div>

    </form>

    {{-- LOGIC HIDDEN INPUT UNTUK REMOVE IMAGES (Jika dipakai di JS) --}}
    @if ($isEdit && isset($product) && $product->photos && $product->photos->count())
        @foreach ($product->photos as $img)
            <input
                type="hidden"
                name="remove_images[]"
                value=""
                class="remove-input-{{ $img->id }}">
        @endforeach
    @endif

@endsection

@push('styles')
<style>
    .upload-box {
        width: 160px;
        height: 160px;
        background: #ffffff;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.2s ease-in-out;
        position: relative;
    }

    .upload-box:hover {
        border-color: #0d6efd;
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    .upload-box.has-image {
        border: 2px solid #0d6efd;
        background: #fff;
    }

    .upload-box .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #adb5bd;
        transition: color 0.2s;
    }

    .upload-box:hover .empty-state {
        color: #0d6efd;
    }

    .upload-box .preview {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .upload-box .preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-box .controls {
        position: absolute;
        top: 6px;
        right: 6px;
        z-index: 10;
    }

    .upload-box .btn-remove-queue {
        border-radius: 50%;
        width: 24px;
        height: 24px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .upload-box .main-choice {
        position: absolute;
        bottom: 6px;
        left: 6px;
        z-index: 10;
        background: rgba(255, 255, 255, 0.95);
        padding: 4px 8px;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid #eee;
    }

    .upload-box .main-choice label {
        margin: 0;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        color: #495057;
    }

    .upload-status {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        animation: fadeIn 0.3s ease;
    }

    .upload-status.success {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }

    .upload-status.error {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #current-images .card {
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    #current-images .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('js/productImages.js') }}"></script>
@endpush
