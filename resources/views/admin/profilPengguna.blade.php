@extends('layouts.admin')

@section('title', 'Profil Pengguna')

@push('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left me-1"></i>
        <span>Kembali ke Dashboard</span>
    </a>
@endpush

@section('content')

<div class="row">
    
    {{-- KOLOM KIRI: Kartu Identitas & Aksi Utama --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 text-center h-100" style="border-radius: 10px;">
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                {{-- Avatar Placeholder --}}
                <div class="mb-3 position-relative">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary" 
                         style="width: 120px; height: 120px; font-size: 3rem;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name ?? 'Nama Pengguna' }}</h5>
                <p class="text-muted small mb-4">{{ ucfirst(Auth::user()->role ?? 'Staff') }}</p>

                <div class="d-grid w-100 gap-2">
                    {{-- Tombol Ganti Password (Modal Trigger) --}}
                    <button type="button" class="btn btn-outline-primary fw-medium" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fa-solid fa-key me-2"></i> Ganti Password
                    </button>

                    {{-- Tombol Logout (Low Profile) --}}
                    <button type="button" class="btn btn-outline-secondary fw-medium" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Detail Informasi --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-circle-info me-2 text-primary"></i>Informasi Akun
                </h6>
            </div>
            <div class="card-body p-4">
                <form>
                    {{-- Baris 1 --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 ps-2 bg-light" 
                                    value="{{ Auth::user()->name ?? 'Nama Lengkap' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-2 bg-light" 
                                    value="{{ Auth::user()->email ?? 'email@contoh.com' }}" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2 --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Jabatan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-briefcase"></i></span>
                                <input type="text" class="form-control border-start-0 ps-2 bg-light" 
                                    value="{{ ucfirst(Auth::user()->role ?? 'Staff') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Nomor Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" class="form-control border-start-0 ps-2 bg-light" 
                                    value="{{ Auth::user()->no_telp ?? '-' }}" readonly placeholder="Tidak ada nomor telepon">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ganti Password --}}
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="changePasswordModalLabel">
                    <i class="fa-solid fa-key me-2 text-primary"></i>Ganti Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.profile.resetPassword') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-medium">Password Saat Ini</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control border-start-0" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary border-start-0" type="button" id="toggleCurrentPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-medium">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-key"></i></span>
                            <input type="password" class="form-control border-start-0" id="new_password" name="new_password" required minlength="6">
                            <button class="btn btn-outline-secondary border-start-0" type="button" id="toggleNewPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text small text-muted">Minimal 6 karakter</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label fw-medium">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control border-start-0" id="new_password_confirmation" name="new_password_confirmation" required>
                            <button class="btn btn-outline-secondary border-start-0" type="button" id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password_confirmation')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i>Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Logout --}}
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="logoutModalLabel">
                    <i class="fa-solid fa-right-from-bracket me-2 text-warning"></i>Konfirmasi Logout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center text-warning mx-auto mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-right-from-bracket fa-2x"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Apakah Anda yakin ingin logout?</h6>
                    <p class="text-muted small mb-0">
                        Anda akan keluar dari sistem dan perlu login kembali untuk mengakses halaman admin.
                    </p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>Batal
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>Ya, Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    
    if (toggleCurrentPassword) {
        toggleCurrentPassword.addEventListener('click', function() {
            const passwordField = document.getElementById('current_password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
    
    if (toggleNewPassword) {
        toggleNewPassword.addEventListener('click', function() {
            const passwordField = document.getElementById('new_password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
    
    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function() {
            const passwordField = document.getElementById('new_password_confirmation');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
});
</script>
@endpush