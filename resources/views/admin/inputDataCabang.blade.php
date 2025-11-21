@extends('layouts.admin')

@section('title', isset($branch) ? 'Edit Data Cabang' : 'Tambah Data Cabang')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            {{-- Card Header --}}
            <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-store me-2 text-primary"></i>
                    {{ isset($branch) ? 'Edit Informasi Cabang' : 'Tambah Cabang Baru' }}
                </h5>
                <p class="text-muted small mt-1">Lengkapi form di bawah untuk mengatur data cabang toko.</p>
            </div>

            <div class="card-body p-4">
                <form action="{{ isset($branch) ? route('admin.branches.update', $branch->id) : route('admin.branches.store') }}" method="POST">
                    @csrf
                    @if(isset($branch))
                        @method('PUT')
                    @endif

                    {{-- Nama Cabang --}}
                    <div class="mb-3">
                        <label for="namaCabang" class="form-label fw-medium text-secondary small">Nama Cabang</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-shop"></i>
                            </span>
                            <input type="text" 
                                class="form-control border-start-0 ps-2 @error('nama') is-invalid @enderror" 
                                id="namaCabang" 
                                name="nama" 
                                style="height: 45px;"
                                value="{{ old('nama', $branch->nama ?? '') }}" 
                                placeholder="Contoh: Dinoyo Kamera Pusat" required>
                        </div>
                        @error('nama')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat (Menggunakan Textarea) --}}
                    <div class="mb-3">
                        <label for="Alamat" class="form-label fw-medium text-secondary small">Alamat Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </span>
                            <textarea 
                                class="form-control border-start-0 ps-2 @error('alamat') is-invalid @enderror" 
                                id="Alamat" 
                                name="alamat" 
                                rows="3" 
                                placeholder="Masukkan alamat lengkap cabang...">{{ old('alamat', $branch->alamat ?? '') }}</textarea>
                        </div>
                        @error('alamat')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Link Maps --}}
                    <div class="mb-3">
                        <label for="LinkMaps" class="form-label fw-medium text-secondary small">Link Google Maps</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-link"></i>
                            </span>
                            <input type="text" 
                                class="form-control border-start-0 ps-2 @error('link_maps') is-invalid @enderror" 
                                id="LinkMaps" 
                                name="link_maps" 
                                style="height: 45px;"
                                value="{{ old('link_maps', $branch->link_maps ?? '') }}" 
                                placeholder="https://maps.google.com/...">
                        </div>
                        @error('link_maps')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nomor Telepon --}}
                    <div class="mb-4">
                        <label for="NomorTelepon" class="form-label fw-medium text-secondary small">Nomor Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input type="text" 
                                class="form-control border-start-0 ps-2 @error('nomor_telepon') is-invalid @enderror" 
                                id="NomorTelepon" 
                                name="nomor_telepon" 
                                style="height: 45px;"
                                value="{{ old('nomor_telepon', $branch->nomor_telepon ?? '') }}" 
                                placeholder="Contoh: 0812-xxxx-xxxx">
                        </div>
                        @error('nomor_telepon')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.branches.index') }}" class="btn btn-light border px-4 fw-medium text-secondary d-flex align-items-center gap-2">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-medium d-flex align-items-center gap-2">
                            <i class="fa-solid fa-save"></i> {{ isset($branch) ? 'Simpan Perubahan' : 'Simpan Cabang' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection