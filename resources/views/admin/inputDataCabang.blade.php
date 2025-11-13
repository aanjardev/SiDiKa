@extends('layouts.admin')

@section('title', isset($branch) ? 'Edit Data Cabang' : 'Tambah Data Cabang')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- Tentukan action & method form --}}
                <form action="{{ isset($branch) 
                    ? route('admin.branches.update', $branch->id) 
                    : route('admin.branches.store') }}" 
                    method="POST">

                    @csrf
                    @if(isset($branch))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="namaCabang" class="form-label">Nama Cabang</label>
                        <input type="text" 
                            class="form-control @error('nama') is-invalid @enderror" 
                            id="namaCabang" 
                            name="nama" 
                            value="{{ old('nama', $branch->nama ?? '') }}" 
                            placeholder="Masukkan nama cabang">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="Alamat" class="form-label">Alamat</label>
                        <input type="text" 
                            class="form-control @error('alamat') is-invalid @enderror" 
                            id="Alamat" 
                            name="alamat" 
                            value="{{ old('alamat', $branch->alamat ?? '') }}" 
                            placeholder="Masukkan alamat cabang" 
                            style="height:100px;">
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="LinkMaps" class="form-label">Link Maps</label>
                        <input type="text" 
                            class="form-control @error('link_maps') is-invalid @enderror" 
                            id="LinkMaps" 
                            name="link_maps" 
                            value="{{ old('link_maps', $branch->link_maps ?? '') }}" 
                            placeholder="Masukkan link Google Maps">
                        @error('link_maps')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="NomorTelepon" class="form-label">Nomor Telepon</label>
                        <input type="text" 
                            class="form-control @error('nomor_telepon') is-invalid @enderror" 
                            id="NomorTelepon" 
                            name="nomor_telepon" 
                            value="{{ old('nomor_telepon', $branch->nomor_telepon ?? '') }}" 
                            placeholder="Masukkan nomor telepon">
                        @error('nomor_telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('admin.branches') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($branch) ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
