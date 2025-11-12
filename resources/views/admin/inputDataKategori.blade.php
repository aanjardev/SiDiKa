@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Data Kategori' : 'Tambah Data Kategori')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ isset($category)
                ? route('admin.categories.update', $category->id)
                : route('admin.categories.store') }}" method="POST">
                    @csrf
                    @if(isset($category))
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" placeholder="Masukkan nama kategori" value="{{ old('nama_kategori', isset($category) ? $category->nama_kategori : '') }}">
                        @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Perbarui' : 'Simpan'}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
