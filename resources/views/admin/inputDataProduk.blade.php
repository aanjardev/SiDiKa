@extends('layouts.admin')

@section('title', 'Tambah Data Produk')

@section('content')
<div class="bd-example">
    <div class="form-container-lg">      
        <form>
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>DATA PRODUK DAN INVENTARIS</h5>
                    <hr style="border: 0.5px solid black; margin: 1.5rem 0;">
                    <label for="namaCabang" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="namaCabang" placeholder="Masukkan nama produk">
                </div>
                <div class="col-md-6">
                    <h5>INFORMASI HARGA</h5>
                    <hr style="border: 0.5px solid black; margin: 1.5rem 0;">   
                    <label for="NomorTelepon" class="form-label">Harga Jual</label>
                    <input type="text" class="form-control" id="NomorTelepon" placeholder="Masukkan harga jual">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Alamat" class="form-label">Kategori</label>
                    <select type="text" class="form-control" id="Alamat">
                        <option value="" disabled selected hidden>Pilih kategori</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="LinkMaps" class="form-label">Harga Beli</label>
                    <input type="text" class="form-control" id="LinkMaps" placeholder="Masukkan harga beli">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="namaCabang" class="form-label">Kode SKU</label>
                    <input type="text" class="form-control" id="namaCabang" placeholder="Masukkan kode sku">
                </div>
                <div class="col-md-6">
                    <label for="NomorTelepon" class="form-label">Harga Servis</label>
                    <input type="text" class="form-control" id="NomorTelepon" placeholder="Masukkan harga servis">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Alamat" class="form-label">Stok Produk</label>
                    <input type="text" class="form-control" id="Alamat" placeholder="Masukkan stok produk">
                </div>
                <div class="col-md-6">
                    <label for="Alamat" class="form-label">Status</label>
                    <select type="text" class="form-control" id="Alamat">
                        <option value="" disabled selected hidden>Pilih status</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
            </div>

            <hr style="border: 0.5px solid black; margin: 1.5rem 0;">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Alamat" class="form-label">Deskripsi Produk</label>
                    <input type="text" class="form-control" id="Alamat" placeholder="Masukkan deskripsi produk"style="height:107px;">
                </div>
                <div class="col-md-6">
                    <label for="LinkMaps" class="form-label">Serial Number</label>
                    <input type="text" class="form-control mb-4" id="LinkMaps" placeholder="Masukkan serial number (body)">
                    <input type="text" class="form-control" id="LinkMaps" placeholder="Masukkan serial number lensa (jika ada)">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Alamat" class="form-label">Kelengkapan Produk</label>
                    <input type="text" class="form-control" id="Alamat" placeholder="Masukkan kelengkapan produk"style="height:107px;">
                </div>
                <div class="col-md-6">
                    <label for="LinkMaps" class="form-label">Gambar Produk</label>
                    <input type="file" class="form-control" id="LinkMaps"> 
                    <div class="mt-4">
                        <button type="submit" class="btn btn-gray me-4">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
