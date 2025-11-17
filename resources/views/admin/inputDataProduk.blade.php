@extends('layouts.admin')

@section('title', 'Tambah Data Produk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>DATA PRODUK</h5>
                            <hr style="border: 0.5px solid black; margin: 1.5rem 0;">
                            <label for="nama_produk" class="form-label">Nama Produk</label>
                            <input type="text"
                                   class="form-control"
                                   id="nama_produk"
                                   name="nama_produk"
                                   value="{{ old('nama_produk') }}"
                                   placeholder="Masukkan nama produk">
                        </div>
                        <div class="col-md-6">
                            <h5>HARGA</h5>
                            <hr style="border: 0.5px solid black; margin: 1.5rem 0;">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <input type="number"
                                   class="form-control"
                                   id="harga_jual"
                                   name="harga_jual"
                                   value="{{ old('harga_jual') }}"
                                   placeholder="Masukkan harga jual">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id_kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="id_kategori" name="id_kategori">
                                <option value="" disabled {{ old('id_kategori') ? '' : 'selected' }}>Pilih kategori</option>
                                @foreach ($semua_kategori as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('id_kategori') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="harga_beli" class="form-label">Harga Beli</label>
                            <input type="number"
                                   class="form-control"
                                   id="harga_beli"
                                   name="harga_beli"
                                   value="{{ old('harga_beli') }}"
                                   placeholder="Masukkan harga beli">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kode_sku" class="form-label">Kode SKU</label>
                            <input type="text"
                                   class="form-control"
                                   id="kode_sku"
                                   name="kode_sku"
                                   value="{{ old('kode_sku') }}"
                                   placeholder="Masukkan kode SKU">
                        </div>
                        <div class="col-md-6">
                            <label for="harga_servis" class="form-label">Harga Servis</label>
                            <input type="number"
                                   class="form-control"
                                   id="harga_servis"
                                   name="harga_servis"
                                   value="{{ old('harga_servis') }}"
                                   placeholder="Masukkan harga servis">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="stok_produk" class="form-label">Stok Produk</label>
                            <input type="number"
                                   class="form-control"
                                   id="stok_produk"
                                   name="stok_produk"
                                   value="{{ old('stok_produk') }}"
                                   placeholder="Masukkan stok produk">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="" disabled {{ old('status') ? '' : 'selected' }}>Pilih status</option>
                                <option value="Second" {{ old('status') === 'Second' ? 'selected' : '' }}>Second</option>
                                <option value="Baru" {{ old('status') === 'Baru' ? 'selected' : '' }}>Baru</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="grade" class="form-label">Grade</label>
                            <select class="form-select" id="grade" name="grade">
                                <option value="" disabled {{ old('grade') ? '' : 'selected' }}>Pilih grade</option>
                                <option value="Unggulan" {{ old('grade') === 'Unggulan' ? 'selected' : '' }}>Unggulan</option>
                                <option value="Standar" {{ old('grade') === 'Standar' ? 'selected' : '' }}>Standar</option>
                                <option value="Minus" {{ old('grade') === 'Minus' ? 'selected' : '' }}>Minus</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="deskripsi_produk" class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" id="deskripsi_produk" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi produk">{{ old('deskripsi_produk') }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="images" class="form-label">Gambar Produk</label>
                            <input type="file"
                                   class="form-control"
                                   id="images"
                                   name="images[]"
                                   accept="image/*"
                                   multiple>
                            <small class="text-muted">Gambar pertama akan menjadi gambar utama. Maksimal 5 MB per file.</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end justify-content-end">
                            <div class="text-end w-100">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-light me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
