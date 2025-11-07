@extends('layouts.admin')

@section('title', 'Tambah Data Kategori')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="namaCabang" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="namaCabang" aria-describedby="emailHelp" placeholder="Masukkan nama kategori">
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ url('admin/categories') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection