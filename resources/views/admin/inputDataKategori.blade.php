@extends('layouts.admin')

@section('title', 'Tambah Data Kategori')

@section('content')
<div class="bd-example">
    <div class="form-container">      
        <form>
            <div class="mb-3">
                <label for="namaCabang" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="namaCabang" aria-describedby="emailHelp" placeholder="Masukkan nama kategori">
            </div>
            <button type="submit" class="btn btn-gray me-4">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
