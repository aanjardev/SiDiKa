@extends('layouts.admin')

@section('title', isset($branch) ? (isset($isShow) && $isShow ? 'Detail Data Cabang' : 'Edit Data Cabang') : 'Tambah Data Cabang')

@section('content')

<form action="{{ isset($branch) && (!isset($isShow) || !$isShow) ? route('admin.branches.update', $branch->id) : '#' }}" method="POST">
    @csrf
    @if(isset($branch) && (!isset($isShow) || !$isShow))
        @method('PUT')
    @endif

    <div class="row">
        {{-- KOLOM KIRI: Informasi Utama --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-store me-2 text-primary"></i>
                        {{ isset($isShow) && $isShow ? 'Detail Cabang' : (isset($branch) ? 'Informasi Cabang' : 'Identitas Cabang Baru') }}
                    </h6>
                    <p class="text-muted small mt-1">
                        {{ isset($isShow) && $isShow ? 'Informasi detail cabang (mode tampil)' : 'Masukkan detail nama dan alamat fisik cabang.' }}
                    </p>
                </div>

                <div class="card-body p-4">
                    {{-- Nama Cabang --}}
                    <div class="mb-4">
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
                                placeholder="Contoh: Dinoyo Kamera Pusat"
                                {{ isset($isShow) && $isShow ? 'readonly' : 'required' }} autofocus>
                        </div>
                        @error('nama')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat --}}
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
                                rows="5" 
                                placeholder="Masukkan alamat lengkap cabang..."
                                {{ isset($isShow) && $isShow ? 'readonly' : '' }}>{{ old('alamat', $branch->alamat ?? '') }}</textarea>
                        </div>
                        @error('alamat')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Kontak & Aksi --}}
        <div class="col-lg-4">
            
            {{-- Card Kontak --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-address-book me-2 text-warning"></i>Kontak & Lokasi
                    </h6>
                </div>
                <div class="card-body p-4">
                    {{-- Nomor Telepon --}}
                    <div class="mb-3">
                        <label for="NomorTelepon" class="form-label fw-medium text-secondary small">Nomor Telepon / WA</label>
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
                                placeholder="0812-xxxx-xxxx"
                                {{ isset($isShow) && $isShow ? 'readonly' : '' }}>
                        </div>
                        @error('nomor_telepon')
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
                                placeholder="https://maps.google.com/..."
                                {{ isset($isShow) && $isShow ? 'readonly' : '' }}>
                        </div>
                        @if(isset($isShow) && $isShow)
                            <div class="form-text small">
                                <a href="{{ $branch->link_maps ?? '#' }}" target="_blank" class="text-primary">
                                    <i class="fa-solid fa-external-link-alt me-1"></i>Buka di Google Maps
                                </a>
                            </div>
                        @else
                            <div class="form-text small text-muted">Salin link lokasi dari Google Maps.</div>
                        @endif
                        @error('link_maps')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jam Buka --}}
                    <div class="mb-3">
                        <label for="JamBuka" class="form-label fw-medium text-secondary small">Jam Buka</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-clock"></i>
                            </span>
                            <input type="text" 
                                class="form-control border-start-0 ps-2 @error('jam_buka') is-invalid @enderror" 
                                id="JamBuka" 
                                name="jam_buka" 
                                placeholder="HH:MM" 
                                value="{{ old('jam_buka', $branch->jam_buka ?? '') }}"
                                {{ isset($isShow) && $isShow ? 'readonly' : 'required' }}>
                        </div>
                        @error('jam_buka')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jam Tutup --}}
                    <div class="mb-3">
                        <label for="JamTutup" class="form-label fw-medium text-secondary small">Jam Tutup</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-clock"></i>
                            </span>
                            <input type="text" 
                                class="form-control border-start-0 ps-2 @error('jam_tutup') is-invalid @enderror" 
                                id="JamTutup" 
                                name="jam_tutup" 
                                placeholder="HH:MM" 
                                value="{{ old('jam_tutup', $branch->jam_tutup ?? '') }}"
                                {{ isset($isShow) && $isShow ? 'readonly' : 'required' }}>
                        </div>
                        @error('jam_tutup')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Card Aksi --}}
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        @if(!isset($isShow) || !$isShow)
                            <button type="submit" class="btn btn-primary fw-medium py-2">
                                <i class="fa-solid fa-save me-2"></i> {{ isset($branch) ? 'Simpan Perubahan' : 'Simpan Cabang' }}
                            </button>
                        @endif
                        <a href="{{ route('admin.branches.index') }}" class="btn btn-light border fw-medium text-secondary py-2">
                            <i class="fa-solid fa-arrow-left me-2"></i> {{ isset($isShow) && $isShow ? 'Kembali' : 'Batal & Kembali' }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection