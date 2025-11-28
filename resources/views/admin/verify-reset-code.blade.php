@extends('layouts.admin')

@section('title', 'Verifikasi Kode Reset Password - SiDiKa')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Left Column - Form -->
                        <div class="col-lg-6 p-4">
                            <div class="mb-4">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-shield-alt me-2 text-primary"></i>
                                    Verifikasi Kode
                                </h4>
                                <p class="text-muted mb-0">
                                    Masukkan kode 6 digit yang dikirim ke email Anda
                                </p>
                            </div>

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="fas fa-envelope fa-2x text-primary"></i>
                                </div>
                                <p class="text-muted small mb-0">
                                    Kode dikirim ke: <strong>{{ session('reset_email') }}</strong>
                                </p>
                            </div>

                            <form method="POST" action="{{ route('admin.profile.verify-reset-code.post') }}" class="needs-validation" novalidate>
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="verification_code" class="form-label fw-medium">
                                        Kode Verifikasi
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control text-center fw-bold" 
                                               id="verification_code" 
                                               name="verification_code" 
                                               placeholder="123456" 
                                               maxlength="6" 
                                               pattern="[0-9]{6}" 
                                               required 
                                               autocomplete="off"
                                               style="letter-spacing: 3px; font-size: 1.25rem;">
                                    </div>
                                    <div class="form-text text-muted">
                                        Masukkan 6 digit angka dari email Anda
                                    </div>
                                    <div class="invalid-feedback">
                                        Kode verifikasi harus 6 digit angka
                                    </div>
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Verifikasi Kode
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Right Column - Instructions -->
                        <div class="col-lg-6 bg-light p-4">
                            <div class="h-100 d-flex flex-column">
                                <div class="mb-4">
                                    <h5 class="fw-bold mb-3">
                                        <i class="fas fa-info-circle me-2 text-primary"></i>
                                        Panduan Verifikasi
                                    </h5>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">Cara verifikasi:</h6>
                                        <ol class="mb-0">
                                            <li class="mb-2">Buka email Anda</li>
                                            <li class="mb-2">Cari email dengan subjek "Reset Password"</li>
                                            <li class="mb-2">Salin kode 6 digit dari email</li>
                                            <li class="mb-0">Masukkan kode di form kiri</li>
                                        </ol>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">
                                            <i class="fas fa-clock me-2 text-warning"></i>
                                            Informasi Penting:
                                        </h6>
                                        <ul class="mb-0">
                                            <li>Kode berlaku selama <strong>30 menit</strong></li>
                                            <li>Periksa folder spam jika tidak ditemukan</li>
                                            <li>Kode hanya bisa digunakan sekali</li>
                                        </ul>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-muted mb-2">Tidak menerima kode?</p>
                                        <form method="POST" action="{{ route('admin.profile.resend-reset-code') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-redo me-1"></i>
                                                Kirim Ulang Kode
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="border-top pt-3">
                                    <a href="{{ route('admin.profile') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Kembali ke Profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }
    
    .input-group-text {
        border-right: none;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
    }
    
    .form-control.is-valid {
        border-color: #198754;
        background-color: #f0fff4;
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }
    
    @media (max-width: 991px) {
        .col-lg-6 {
            border-bottom: 1px solid #e9ecef;
        }
        
        .col-lg-6:last-child {
            border-bottom: none;
        }
    }
</style>

<script>
// Auto-format verification code input
document.getElementById('verification_code').addEventListener('input', function(e) {
    // Remove any non-digit characters
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Auto-focus to next input if using multiple inputs (not needed for single input)
    if (this.value.length === 6) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    } else {
        this.classList.remove('is-valid');
    }
});

// Prevent paste of non-digit characters
document.getElementById('verification_code').addEventListener('paste', function(e) {
    e.preventDefault();
    let pastedData = e.clipboardData.getData('text');
    pastedData = pastedData.replace(/[^0-9]/g, '').substring(0, 6);
    this.value = pastedData;
    
    if (this.value.length === 6) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    }
});

// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
@endsection
