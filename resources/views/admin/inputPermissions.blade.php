@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')

{{-- Route action seharusnya ke permissions.store/update, yang sudah benar di kedua branch. --}}
<form action="{{ isset($user) ? route('admin.permissions.update', $user->id) : route('admin.permissions.store') }}" method="POST" data-validate-form>
    @csrf
    @if(isset($user))
        @method('PUT')
    @endif

    <div class="row">
        {{-- KOLOM KIRI: Informasi Utama --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-user-shield me-2 text-primary"></i>
                        {{ isset($user) ? 'Informasi Akun Pengguna' : 'Identitas Pengguna Baru' }}
                    </h6>
                    <p class="text-muted small mt-1">Hubungkan akun dengan data karyawan dan tentukan email login.</p>
                </div>

                <div class="card-body p-4">
                    {{-- Nama Karyawan --}}
                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary small">Nama Karyawan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-user-tie"></i>
                            </span>
                            {{-- Atribut disabled dipertahankan agar tidak bisa diubah saat edit. Autofocus juga dipertahankan. --}}
                            <select name="karyawan_name" class="form-select border-start-0 ps-2 required-field" style="height: 45px;" required {{ isset($user) ? 'disabled' : '' }} data-error-message="Karyawan wajib dipilih" autofocus>
                                <option selected disabled value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawan_data as $k)
                                    <option value="{{ $k->id }}"
                                        {{ (old('karyawan_name') == $k->id || (isset($user) && $user->id == $k->id)) ? 'selected' : '' }}>
                                        {{ $k->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback">Karyawan wajib dipilih</div>
                        @if(isset($user))
                            {{-- Field hidden untuk mengirimkan ID karyawan saat form disubmit (karena select di atas disabled) --}}
                            <input type="hidden" name="karyawan_name" value="{{ $user->id }}">
                            <div class="form-text small text-muted mt-1">
                                <i class="fa-solid fa-circle-info me-1"></i>Nama karyawan terkunci pada mode edit.
                            </div>
                        @endif
                    </div>

                    {{-- Role atau Jabatan (Ditambahkan oleh teman Anda) --}}
                    <div class="mb-3">
                        <label for="role" class="form-label fw-medium text-secondary small">Role atau Jabatan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-user-tag"></i> {{-- Mengganti ikon ke yang lebih sesuai --}}
                            </span>
                            {{-- Menambahkan required-field dan old value --}}
                            <select name="role" id="role" class="form-select border-start-0 ps-2 required-field @error('role') is-invalid @enderror" style="height: 45px;" required {{ isset($user) ? 'disabled' : '' }} data-error-message="Role wajib dipilih">
                                <option selected disabled value="">-- Pilih Role --</option>
                                {{-- Saya asumsikan role yang valid adalah 'admin' dan 'manager', atau harusnya diambil dari database. 
                                    Menggunakan logic old/user role yang benar. --}}
                                <option value="admin" {{ (old('role') == 'admin' || (isset($user) && $user->role == 'admin')) ? 'selected' : '' }}>
                                    Admin 
                                </option>
                                <option value="manager" {{ (old('role') == 'manager' || (isset($user) && $user->role == 'manager')) ? 'selected' : '' }}>
                                    Manager 
                                </option>
                            </select>
                        </div>
                        <div class="invalid-feedback">Role wajib dipilih</div>
                        @error('role')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @if(isset($user))
                            {{-- Field hidden untuk mengirimkan ROLE saat form disubmit (karena select di atas disabled) --}}
                            <input type="hidden" name="role" value="{{ $user->role }}">
                        @endif
                    </div>
                    
                    {{-- Alamat Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium text-secondary small">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            {{-- Menggabungkan input type email dan validasi dari input-pembelian --}}
                            <input type="email" class="form-control border-start-0 ps-2 required-field @error('email') is-invalid @enderror"
                                id="email" name="email"
                                style="height: 45px;"
                                placeholder="contoh@email.com"
                                value="{{ old('email', isset($user) ? $user->email : '') }}"
                                required
                                data-error-message="Email wajib diisi"
                                data-validate="email"
                                {{ isset($user) ? 'readonly' : '' }}> {{-- Tambahkan readonly jika edit untuk email --}}
                        </div>
                        <div class="invalid-feedback">Email wajib diisi dengan format yang benar</div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Keamanan & Aksi --}}
        <div class="col-lg-4">

            {{-- Card Keamanan --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-lock me-2 text-warning"></i>Keamanan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fw-medium text-secondary small">Password Login</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password"
                            name="password"
                            id="password"
                            class="form-control border-start-0 border-end-0 ps-2 @error('password') is-invalid @enderror"
                            style="height: 45px;"
                            value="{{ old('password', isset($user) ? '' : 'admin123') }}"
                            placeholder="{{ isset($user) ? 'Biarkan kosong...' : 'Default: admin123' }}">

                        {{-- Tombol Mata (Show/Hide) --}}
                        <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePassword">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>

                    @error('password')
                        <div class="text-danger small mt-1 mb-2">{{ $message }}</div>
                    @enderror

                    <div class="form-text small text-muted" style="font-size: 0.8rem; line-height: 1.4;">
                        @if(isset($user))
                            <i class="fa-solid fa-circle-exclamation me-1 text-warning"></i> Kosongkan jika tidak ingin mengubah password user ini.
                        @else
                            <i class="fa-solid fa-check-circle me-1 text-success"></i> Password default untuk user baru adalah <strong>admin123</strong>.
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Aksi --}}
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-medium py-2">
                            <i class="fa-solid fa-save me-2"></i> {{ isset($user) ? 'Simpan Perubahan' : 'Simpan User Baru' }}
                        </button>
                        <a href="{{ route('admin.permissions') }}" class="btn btn-light border fw-medium text-secondary py-2">
                            <i class="fa-solid fa-arrow-left me-2"></i> Batal & Kembali
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        if(togglePassword && password && eyeIcon) {
            togglePassword.addEventListener('click', function (e) {
                // Toggle type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Toggle icon
                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        }
    });
</script>
@endpush