@extends('layouts.admin')
@php $isEdit = isset($product); @endphp

@section('title', $isEdit ? 'Edit Data Produk' : 'Tambah Data Produk')

@section('content')
<form action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        {{-- KOLOM KIRI: Informasi Dasar Produk --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-box-open me-2 text-primary"></i>Informasi Umum Produk</h6>
                </div>
                <div class="card-body">
                    
                    {{-- Nama Produk --}}
                    <div class="mb-4">
                        <label for="nama_produk" class="form-label fw-medium text-secondary small">Nama Produk</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-box"></i></span>
                            <input type="text" 
                                class="form-control border-start-0 ps-2 @error('nama_produk') is-invalid @enderror" 
                                id="nama_produk" name="nama_produk" 
                                style="height: 45px;"
                                value="{{ old('nama_produk', $isEdit ? $product->nama_produk : '') }}" 
                                placeholder="Contoh: Kamera Canon EOS 600D" required>
                        </div>
                        @error('nama_produk') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Baris: Kategori & Status --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_kategori" class="form-label fw-medium text-secondary small">Kategori</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-layer-group"></i></span>
                                <select class="form-select border-start-0 ps-2 @error('id_kategori') is-invalid @enderror" id="id_kategori" name="id_kategori" style="height: 45px;" required>
                                    <option value="" disabled {{ old('id_kategori', $isEdit ? $product->id_kategori : '') ? '' : 'selected' }}>-- Pilih Kategori --</option>
                                    @foreach ($semua_kategori as $kategori)
                                        <option value="{{ $kategori->id }}" {{ old('id_kategori', $isEdit ? $product->id_kategori : '') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('id_kategori') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-medium text-secondary small">Kondisi (Status)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-circle-half-stroke"></i></span>
                                <select class="form-select border-start-0 ps-2 @error('status') is-invalid @enderror" id="status" name="status" style="height: 45px;" required>
                                    <option value="" disabled {{ old('status', $isEdit ? $product->status : '') ? '' : 'selected' }}>-- Pilih Kondisi --</option>
                                    <option value="Baru" {{ old('status', $isEdit ? $product->status : '') === 'Baru' ? 'selected' : '' }}>Baru</option>
                                    <option value="Second" {{ old('status', $isEdit ? $product->status : '') === 'Second' ? 'selected' : '' }}>Second</option>
                                </select>
                            </div>
                            @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Baris: Grade (Jika Second) --}}
                    <div class="mb-4">
                        <label for="grade" class="form-label fw-medium text-secondary small">Grade Kualitas</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-star"></i></span>
                            <select class="form-select border-start-0 ps-2 @error('grade') is-invalid @enderror" id="grade" name="grade" style="height: 45px;" required>
                                <option value="" disabled {{ old('grade', $isEdit ? $product->grade : '') ? '' : 'selected' }}>-- Pilih Grade --</option>
                                <option value="Unggulan" {{ old('grade', $isEdit ? $product->grade : '') === 'Unggulan' ? 'selected' : '' }}>Unggulan (Mulus)</option>
                                <option value="Standar" {{ old('grade', $isEdit ? $product->grade : '') === 'Standar' ? 'selected' : '' }}>Standar (Normal)</option>
                                <option value="Minus" {{ old('grade', $isEdit ? $product->grade : '') === 'Minus' ? 'selected' : '' }}>Minus (Ada cacat)</option>
                            </select>
                        </div>
                        @error('grade') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label for="deskripsi_produk" class="form-label fw-medium text-secondary small">Deskripsi Lengkap</label>
                        <textarea class="form-control @error('deskripsi_produk') is-invalid @enderror" 
                            id="deskripsi_produk" name="deskripsi_produk" 
                            rows="6" 
                            placeholder="Tuliskan spesifikasi, kelengkapan, dan kondisi detail produk...">{{ old('deskripsi_produk', $isEdit ? $product->deskripsi_produk : '') }}</textarea>
                        @error('deskripsi_produk') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Inventaris & Harga --}}
        <div class="col-lg-4 mb-4">
            
            {{-- Card Inventaris --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-warehouse me-2 text-primary"></i>Inventaris</h6>
                </div>
                <div class="card-body">
                    {{-- Kode SKU --}}
                    <div class="mb-3">
                        <label for="kode_sku" class="form-label fw-medium text-secondary small">Kode SKU</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-barcode"></i></span>
                            <input type="text" class="form-control border-start-0 ps-2 @error('kode_sku') is-invalid @enderror" 
                                id="kode_sku" name="kode_sku" style="height: 45px;"
                                value="{{ old('kode_sku', $isEdit ? $product->kode_sku : '') }}" 
                                placeholder="SKU-0000">
                        </div>
                        @error('kode_sku') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Stok --}}
                    <div class="mb-0">
                        <label for="stok_produk" class="form-label fw-medium text-secondary small">Stok Saat Ini</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-boxes-stacked"></i></span>
                            <input type="number" class="form-control border-start-0 ps-2 @error('stok_produk') is-invalid @enderror" 
                                id="stok_produk" name="stok_produk" style="height: 45px;"
                                value="{{ old('stok_produk', $isEdit ? $product->stok_produk : '') }}" 
                                placeholder="0" min="0">
                        </div>
                        @error('stok_produk') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Card Harga --}}
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-tags me-2 text-primary"></i>Manajemen Harga</h6>
                </div>
                <div class="card-body">
                    {{-- Harga Beli --}}
                    <div class="mb-3">
                        <label for="harga_beli" class="form-label fw-medium text-secondary small">Harga Beli (Modal)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="number" class="form-control border-start-0 ps-2 @error('harga_beli') is-invalid @enderror" 
                                id="harga_beli" name="harga_beli" style="height: 45px;"
                                value="{{ old('harga_beli', $isEdit ? $product->harga_beli : '') }}" 
                                placeholder="0">
                        </div>
                    </div>

                    {{-- Harga Jual --}}
                    <div class="mb-3">
                        <label for="harga_jual" class="form-label fw-medium text-secondary small">Harga Jual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="number" class="form-control border-start-0 ps-2 @error('harga_jual') is-invalid @enderror" 
                                id="harga_jual" name="harga_jual" style="height: 45px;"
                                value="{{ old('harga_jual', $isEdit ? $product->harga_jual : '') }}" 
                                placeholder="0">
                        </div>
                    </div>

                    {{-- Harga Servis --}}
                    <div class="mb-0">
                        <label for="harga_servis" class="form-label fw-medium text-secondary small">Estimasi Harga Servis</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="number" class="form-control border-start-0 ps-2 @error('harga_servis') is-invalid @enderror" 
                                id="harga_servis" name="harga_servis" style="height: 45px;"
                                value="{{ old('harga_servis', $isEdit ? $product->harga_servis : '') }}" 
                                placeholder="0">
                        </div>
                        <div class="form-text small">Kosongkan jika tidak ada layanan servis.</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM BAWAH: Upload Gambar --}}
        <div class="col-12 mb-5">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-images me-2 text-primary"></i>Media Produk</h6>
                </div>
                <div class="card-body">
                    <label for="images" class="form-label fw-medium text-secondary small">Upload Gambar</label>
                    <div class="input-group">
                        <input type="file" 
                            class="form-control @error('images') is-invalid @enderror" 
                            id="images" name="images[]" 
                            accept="image/*" multiple
                            {{ $isEdit ? '' : 'required' }}>
                        <label class="input-group-text" for="images">Browse</label>
                    </div>
                    @error('images') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    <div class="form-text text-muted">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Format: JPG, JPEG, PNG. Maksimal 5MB per file. Gambar pertama akan dijadikan cover.
                    </div>

                    {{-- Preview Gambar Eksisting (Opsional: Hanya tampil jika mode Edit dan ada relasi gambar) --}}
                    @if($isEdit && isset($product->gambar) && count($product->gambar) > 0)
                        <div class="mt-4">
                            <h6 class="small fw-bold text-secondary mb-2">Gambar Saat Ini:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($product->gambar as $img)
                                    <div class="position-relative border rounded p-1" style="width: 100px; height: 100px;">
                                        <img src="{{ $img->url }}" class="w-100 h-100 object-fit-cover rounded" alt="Produk">
                                        {{-- Tambahkan tombol hapus gambar per item di sini jika diperlukan logika delete terpisah --}}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS (Floating / Bottom) --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.products.index') }}" class="btn btn-light border fw-medium text-secondary px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali
            </a>
            <button type="submit" class="btn btn-primary fw-medium px-5">
                <i class="fa-solid fa-save me-2"></i>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk Baru' }}
            </button>
        </div>
    </div>

</form>
@endsection