@extends('layouts.admin')

@section('title', 'Tambah Data Cabang')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="namaCabang" class="form-label">Nama Cabang</label>
                        <input type="text" class="form-control" id="namaCabang" aria-describedby="emailHelp" placeholder="Masukkan nama cabang">
                    </div>
                    <div class="mb-3">
                        <label for="Alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="Alamat" aria-describedby="emailHelp" placeholder="Masukkan alamat cabang" style="height:100px;">
                    </div>
                    <div class="mb-3">
                        <label for="LinkMaps" class="form-label">Link Maps</label>
                        <input type="text" class="form-control" id="LinkMaps" aria-describedby="emailHelp" placeholder="Masukkan link Google Maps">
                    </div>
                    <div class="mb-5">
                        <label for="NomorTelepon" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="NomorTelepon" aria-describedby="emailHelp" placeholder="Masukkan nomor telepon">
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ url('admin/branches ') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection