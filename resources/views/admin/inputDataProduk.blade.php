@extends('layouts.admin')
@php $isEdit = isset($product); @endphp

@section('title', $isEdit ? 'Edit Data Produk' : 'Tambah Data Produk')

@section('content')

{{-- 1. HEADER HALAMAN: Judul di Kiri, Tombol Kembali di Kanan Atas --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">{{ $isEdit ? 'Edit Data Produk' : 'Tambah Data Produk' }}</h4>
    
    {{-- Tombol Kembali disini --}}
    <a href="{{ route('admin.products.index') }}" class="btn btn-light border shadow-sm fw-medium text-secondary px-4" style="border-radius: 8px;">
        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
    </a>
</div>

{{-- Error Alert --}}
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

<form action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        
        {{-- KOLOM KIRI: Informasi Utama & Media --}}
        <div class="col-lg-8">
            
            {{-- CARD 1: INFORMASI PRODUK --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-box-open me-2 text-primary"></i>Informasi Produk
                    </h6>
                </div>
                <div class="card-body p-4">
                    {{-- Nama Produk --}}
                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-medium">Nama Produk</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-tag"></i></span>
                            <input type="text" 
                                class="form-control border-start-0 ps-2 py-2 @error('nama_produk') is-invalid @enderror" 
                                name="nama_produk" 
                                value="{{ old('nama_produk', $isEdit ? $product->nama_produk : '') }}" 
                                placeholder="Contoh: Kamera Canon EOS 600D" required>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-0">
                        <label class="form-label text-secondary small fw-medium">Deskripsi Lengkap</label>
                        <textarea class="form-control py-2 @error('deskripsi_produk') is-invalid @enderror" 
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
                        <i class="fa-solid fa-images me-2 text-primary"></i>Media Produk
                    </h6>
                </div>
                <div class="card-body p-4">
                    <label class="form-label text-secondary small fw-medium">Upload Gambar</label>
                    <div class="input-group mb-2">
                        <input type="file" class="form-control py-2 @error('images') is-invalid @enderror" 
                            id="images" name="images[]" 
                            accept="image/*" multiple
                            {{ $isEdit ? '' : 'required' }}>
                        <label class="input-group-text" for="images">Browse</label>
                    </div>
                    <div class="form-text text-muted mb-3 small">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Format: JPG, JPEG, PNG. Maksimal 5MB per file. Gambar pertama akan dijadikan cover.
                    </div>
                    
                    {{-- Container Preview --}}
                    <div id="image-grid" class="d-flex flex-wrap gap-3 mt-3"></div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Inventaris & Harga --}}
        <div class="col-lg-4">
            
            {{-- CARD 3: KLASIFIKASI & STOK --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-list-check me-2 text-warning"></i>Klasifikasi & Stok
                    </h6>
                </div>
                <div class="card-body p-4">
                    {{-- Kategori --}}
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium">Kategori</label>
                        <select class="form-select py-2 text-secondary @error('id_kategori') is-invalid @enderror" name="id_kategori">
                            <option value="" disabled {{ old('id_kategori', $isEdit ? $product->id_kategori : '') ? '' : 'selected' }}>-- Pilih Kategori --</option>
                            @foreach ($semua_kategori as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('id_kategori', $isEdit ? $product->id_kategori : '') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SKU --}}
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium">Kode SKU</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-barcode"></i></span>
                            <input type="text" class="form-control border-start-0 ps-2 py-2 @error('kode_sku') is-invalid @enderror" 
                                name="kode_sku" 
                                value="{{ old('kode_sku', $isEdit ? $product->kode_sku : '') }}" 
                                placeholder="SKU-0000">
                        </div>
                    </div>

                    {{-- Stok --}}
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium">Stok</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-cubes"></i></span>
                            <input type="number" class="form-control border-start-0 ps-2 py-2 @error('stok_produk') is-invalid @enderror" 
                                name="stok_produk" 
                                value="{{ old('stok_produk', $isEdit ? $product->stok_produk : '') }}" 
                                placeholder="0" min="0">
                        </div>
                    </div>

                    {{-- Row Kondisi & Grade --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label text-secondary small fw-medium">Kondisi</label>
                            <select class="form-select py-2 text-secondary" name="status">
                                <option value="Second" {{ old('status', $isEdit ? $product->status : '') === 'Second' ? 'selected' : '' }}>Second</option>
                                <option value="Baru" {{ old('status', $isEdit ? $product->status : '') === 'Baru' ? 'selected' : '' }}>Baru</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small fw-medium">Grade</label>
                            <select class="form-select py-2 text-secondary" name="grade">
                                <option value="Unggulan" {{ old('grade', $isEdit ? $product->grade : '') === 'Unggulan' ? 'selected' : '' }}>Unggulan</option>
                                <option value="Standar" {{ old('grade', $isEdit ? $product->grade : '') === 'Standar' ? 'selected' : '' }}>Standar</option>
                                <option value="Minus" {{ old('grade', $isEdit ? $product->grade : '') === 'Minus' ? 'selected' : '' }}>Minus</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 4: HARGA --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-tags me-2 text-success"></i>Harga
                    </h6>
                </div>
                <div class="card-body p-4">
                    {{-- Harga Beli --}}
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium">Harga Beli (Modal)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                            <input type="number" class="form-control border-start-0 ps-2 py-2" 
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
                            <input type="number" class="form-control border-start-0 ps-2 py-2" 
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
                            <input type="number" class="form-control border-start-0 ps-2 py-2" 
                                name="harga_servis" 
                                value="{{ old('harga_servis', $isEdit ? $product->harga_servis : '') }}" 
                                placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER ACTION BAR: Tombol Simpan di Pojok Kanan Bawah --}}
        <div class="card-body d-flex justify-content-end align-items-center p-4">
            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                <i class="fa-solid fa-save me-2"></i>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk Baru' }}
            </button>
        </div>
</form

{{-- LOGIC JS GAMBAR (Tetap) --}}
@if($isEdit && isset($product->gambar) && count($product->gambar) > 0)
    @foreach($product->gambar as $img)
        <script>
            window.existingImages = window.existingImages || [];
            window.existingImages.push({
                id: "{{ $img->id }}",
                url: "{{ Storage::disk('r2')->url($img->path_gambar) }}"
            });
        </script>
        <input type="hidden" name="remove_images[]" value="" class="remove-input-{{ $img->id }}">
    @endforeach
@endif

@endsection

@push('styles')
<style>
    .upload-box {
        width: 150px;
        height: 150px;
        background: #f8f9fc;
        border: 2px dashed #ced4da;
        border-radius: 8px;
        cursor: pointer;
        overflow: hidden;
        transition: 0.3s;
        position: relative;
    }

    .upload-box:hover {
        border-color: #4e6bff;
        background: #eef3ff;
    }

    .upload-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-box .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 0, 0, 0.85);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .upload-box .main-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        background: rgba(0, 0, 0, 0.70);
        color: white;
        padding: 2px 6px;
        font-size: 11px;
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/productImages.js') }}"></script>
    @if($isEdit)
    @foreach ($product->gambar as $img)
        fetch("{{ Storage::disk('r2')->url($img->path_gambar) }}")
            .then(r => r.blob())
            .then(blob => {
                blob.name = "{{ $img->id }}.jpg";
                selectedFiles.push(blob);
                renderGrid();
                syncToForm();
            });
    @endforeach
    @endif
@endpush
