{{-- PR: PILIH TANGGAL SESUAI HOPE UI --}}

@extends('layouts.admin')

@section('title', 'Tambah Data Karyawan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                
                {{-- Form ini hanya UI, belum berfungsi --}}
                <form method="POST" action="">
                    @csrf

                    {{-- Baris 1: Nama & NIK --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" placeholder="Masukkan 16 Digit NIK" required>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Jabatan & Email --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Misal: Staff Operasional" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Contoh: @dinoyokamera.com" required>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 3: Nomor Telepon & Tanggal Masuk --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_telp" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="no_telp" name="no_telp" placeholder="Masukkan nomor telepon aktif" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control flatpickr_humandate" id="tgl_masuk" name="tgl_masuk" placeholder="Pilih Tanggal..." required>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 4: Gaji Pokok & Status --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                                <input type="text" class="form-control" id="gaji_pokok" name="gaji_pokok" placeholder="Masukkan nominal gaji (misal: 5000000)" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="" selected disabled>Pilih Status</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="non-aktif">Non Aktif</option>
                                    <option value="magang">Magang</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 5: Alamat --}}
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap karyawan" required></textarea>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="text-end mt-4">
                        <a href="{{ url('admin/data-karyawan') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

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
