@extends('layouts.admin')

@section('title','Tambah User')

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
                        <label for="nama_kategori" class="form-label">Nama Karyawan</label>
                        <div class="mb-3">
                            <select class="form-select form-select-sm" style="height: 40px;" aria-label=".form-select-sm example">
                                <option selected disabled value="">Pilih karyawan</option>
                                <option value="1">Aan</option>
                                <option value="2">Anjar</option>
                                <option value="3">Setyawati</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ route('admin.permissions') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Perbarui' : 'Simpan'}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
