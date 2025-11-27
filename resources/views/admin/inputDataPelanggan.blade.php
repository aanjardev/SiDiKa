@extends('layouts.admin')

@section('title', isset($readOnly) && $readOnly ? 'Detail Data Pelanggan' : 'Edit Data Pelanggan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                
                @if(isset($readOnly) && $readOnly)
                    {{-- Mode Read-Only untuk melihat detail --}}
                @else
                <form method="POST" action="{{ route('admin.customers.update', $pelanggan->id) }}">
                    @csrf
                    @method('PUT')
                @endif

                    {{-- Baris 1: Nama & Nomor Telepon --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Pelanggan</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $pelanggan->nama ?? '') }}" placeholder="Masukkan nama pelanggan" {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_telp" class="form-label">Nomor Telepon</label>
                                <input type="text"
                                       class="form-control @error('no_telp') is-invalid @enderror"
                                       id="no_telp"
                                       name="no_telp"
                                       value="{{ old('no_telp', $pelanggan->no_telp ?? '') }}"
                                       placeholder="Masukkan nomor telepon"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       maxlength="20"
                                       data-phone-validation
                                       data-max-digits="20"
                                       {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>
                                @error('no_telp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Jenis Kelamin & NIK --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" {{ isset($readOnly) && $readOnly ? 'disabled' : 'required' }}>
                                    <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin', $pelanggan->jenis_kelamin ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $pelanggan->jenis_kelamin ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="identitas" class="form-label">NIK</label>
                                <input type="text"
                                       class="form-control @error('identitas') is-invalid @enderror"
                                       id="identitas"
                                       name="identitas"
                                       value="{{ old('identitas', $pelanggan->identitas ?? '') }}"
                                       placeholder="Masukkan NIK (opsional)"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       maxlength="20"
                                       data-phone-validation
                                       data-max-digits="20"
                                       {{ isset($readOnly) && $readOnly ? 'readonly' : '' }}>
                                @error('identitas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2.5: Email --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <small class="text-muted">(untuk pengiriman nota)</small></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $pelanggan->email ?? '') }}" placeholder="contoh@email.com (opsional)" {{ isset($readOnly) && $readOnly ? 'readonly' : '' }}>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Email digunakan untuk mengirim nota pembelian secara otomatis.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 3: Alamat --}}
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap pelanggan" {{ isset($readOnly) && $readOnly ? 'readonly' : '' }}>{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Baris 4: Referensi & Keterangan --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="referensi" class="form-label">Referensi</label>
                                <input type="text" class="form-control @error('referensi') is-invalid @enderror" id="referensi" name="referensi" value="{{ old('referensi', $pelanggan->referensi ?? '') }}" placeholder="Masukkan referensi (opsional)" {{ isset($readOnly) && $readOnly ? 'readonly' : '' }}>
                                @error('referensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="2" placeholder="Masukkan keterangan tambahan (opsional)" {{ isset($readOnly) && $readOnly ? 'readonly' : '' }}>{{ old('keterangan', $pelanggan->keterangan ?? '') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if(isset($readOnly) && $readOnly)
                        {{-- Tampilkan Kode Customer jika dalam mode read-only --}}
                        <div class="mb-3">
                            <label class="form-label">Kode Customer</label>
                            <input type="text" class="form-control" value="{{ $pelanggan->kode_customer ?? '-' }}" readonly>
                        </div>
                    @endif

                    {{-- Tombol Aksi --}}
                    <div class="text-end mt-4">
                        @if(isset($readOnly) && $readOnly)
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-light me-2">Kembali</a>
                            <a href="{{ route('admin.customers.edit', $pelanggan->id) }}" class="btn btn-primary">Edit</a>
                        @else
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-light me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        @endif
                    </div>
                @if(!isset($readOnly) || !$readOnly)
                </form>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@if(isset($readOnly) && $readOnly)
<style>
    .form-control[readonly],
    .form-select:disabled {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
</style>
@endif
@endpush
@push('scripts')
<script src="{{ asset('js/phone-input-validation.js') }}"></script>
<script>

