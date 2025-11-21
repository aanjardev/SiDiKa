@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            {{-- Card Header (Opsional, untuk judul yang jelas) --}}
            <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-user-shield me-2 text-primary"></i>
                    {{ isset($user) ? 'Edit Akun Pengguna' : 'Buat Akun Baru' }}
                </h5>
                <p class="text-muted small mt-1">Silakan isi formulir di bawah ini untuk mengelola akses pengguna.</p>
            </div>

            <div class="card-body p-4">
                <form action="{{ isset($user) ? route('admin.permissions.update', $user->id) : route('admin.permissions.store') }}" method="POST">
                    @csrf
                    @if(isset($user))
                        @method('PUT')
                    @endif

                    {{-- Nama Karyawan --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Nama Karyawan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-user-tie"></i>
                            </span>
                            <select name="karyawan_name" class="form-select border-start-0 ps-2" style="height: 45px;" required {{ isset($user) ? 'disabled' : '' }}>
                                <option selected disabled value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawan_data as $k)
                                    <option value="{{ $k->id }}" 
                                        {{ (old('karyawan_name') == $k->id || (isset($user) && $user->id == $k->id)) ? 'selected' : '' }}>
                                        {{ $k->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if(isset($user))
                            <input type="hidden" name="karyawan_name" value="{{ $user->id }}">
                            <div class="form-text small"><i class="fa-solid fa-circle-info me-1"></i>Nama karyawan tidak dapat diubah pada mode edit.</div>
                        @endif
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium text-secondary small">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control border-start-0 ps-2 @error('email') is-invalid @enderror" 
                                id="email" name="email"
                                style="height: 45px;"
                                placeholder="contoh@email.com"
                                value="{{ old('email', isset($user) ? $user->email : '') }}" required>
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password"
                                name="password"
                                id="password" 
                                class="form-control border-start-0 border-end-0 ps-2 @error('password') is-invalid @enderror" 
                                style="height: 45px;" 
                                value="{{ old('password', isset($user) ? '' : 'admin123') }}"
                                placeholder="{{ isset($user) ? 'Kosongkan jika tidak ingin mengganti' : 'Password default: admin123' }}">
                            
                            {{-- Tombol Mata (Show/Hide) --}}
                            <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePassword">
                                <i class="fa-solid fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @if(!isset($user))
                            <div class="form-text small text-muted">Default password untuk user baru adalah <strong>admin123</strong></div>
                        @endif
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Group Tombol Aksi --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.permissions') }}" class="btn btn-light border px-4 fw-medium text-secondary d-flex align-items-center gap-2">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-medium d-flex align-items-center gap-2">
                            <i class="fa-solid fa-save"></i> {{ isset($user) ? 'Simpan Perubahan' : 'Simpan User Baru' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

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
    });
</script>
@endpush