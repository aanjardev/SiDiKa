{{-- PR: PILIH TANGGAL SESUAI HOPE UI --}}

@extends('layouts.admin')

@section('title', isset($readOnly) && $readOnly ? 'Detail Data Karyawan' : (isset($employee) ? 'Edit Data Karyawan' : 'Tambah Data Karyawan'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                
                @if(isset($readOnly) && $readOnly)
                    {{-- Mode Read-Only untuk melihat detail --}}
                @else
                <form method="POST" action="{{ isset($employee) ? route('admin.employees.update', $employee->id) : route('admin.employees.store') }}">
                    @csrf
                    @if(isset($employee))
                        @method('PUT')
                    @endif
                @endif

                    {{-- Baris 1: Nama & NIK --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $employee->nama_lengkap ?? '') }}" placeholder="Masukkan nama lengkap" {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', $employee->nik ?? '') }}" placeholder="Masukkan 16 Digit NIK" {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Jabatan & Email --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <select class="form-select @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" {{ isset($readOnly) && $readOnly ? 'disabled' : 'required' }}>
                                    <option value="" selected disabled>Pilih Jabatan</option>
                                    <option value="Manager" {{ old('jabatan', $employee->jabatan ?? '') === 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Staff Operasional" {{ old('jabatan', $employee->jabatan ?? '') === 'Staff Operasional' ? 'selected' : '' }}>Staff Operasional</option>
                                </select>
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->user->email ?? '') }}" placeholder="Contoh: @dinoyokamera.com" {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 3: Nomor Telepon & Tanggal Masuk --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nomor_telepon" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control @error('nomor_telepon') is-invalid @enderror" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $employee->nomor_telepon ?? '') }}" placeholder="Masukkan nomor telepon aktif" {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>
                                @error('nomor_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control flatpickr_humandate @error('tanggal_masuk') is-invalid @enderror" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', isset($employee) && $employee->tanggal_masuk ? $employee->tanggal_masuk->format('Y-m-d') : '') }}" placeholder="Pilih Tanggal..." {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>
                                @error('tanggal_masuk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 4: Gaji Pokok & Status --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gaji" class="form-label">Gaji Pokok</label>
                                <input type="number" class="form-control @error('gaji') is-invalid @enderror" id="gaji" name="gaji" value="{{ old('gaji', $employee->gaji ?? '') }}" placeholder="Masukkan nominal gaji (misal: 5000000)" min="0" {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>
                                @error('gaji')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" {{ isset($readOnly) && $readOnly ? 'disabled' : 'required' }}>
                                    <option value="" selected disabled>Pilih Status</option>
                                    <option value="aktif" {{ old('status', $employee->status ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="non-aktif" {{ old('status', $employee->status ?? '') === 'non-aktif' ? 'selected' : '' }}>Non Aktif</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 5: Alamat --}}
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap karyawan" {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}>{{ old('alamat', $employee->alamat ?? '') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="text-end mt-4">
                        @if(isset($readOnly) && $readOnly)
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-light me-2">Kembali</a>
                            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-primary">Edit</a>
                        @else
                            <a href="{{ url('admin/employees') }}" class="btn btn-light me-2">Batal</a>
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
<script>
    // Jika kamu menggunakan Flatpickr bawaan Hope UI:
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr(".flatpickr_humandate", {
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d"
            });
        }
    });
</script>
@endpush
