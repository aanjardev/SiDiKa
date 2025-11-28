@extends('layouts.admin')

@section('title', 'Lupa Password - SiDiKa')

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
                                    <i class="fas fa-key me-2 text-primary"></i>
                                    Lupa Password
                                </h4>
                                <p class="text-muted mb-0">
                                    Kami akan mengirimkan kode verifikasi ke email Anda
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

                            <form method="POST" action="{{ route('admin.profile.forgot-password') }}" class="needs-validation" novalidate>
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="forgot_email" class="form-label fw-medium">
                                        Email Terdaftar
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control" 
                                               id="forgot_email" 
                                               name="forgot_email" 
                                               value="{{ Auth::user()->email }}" 
                                               readonly
                                               required>
                                    </div>
                                    <div class="form-text text-muted">
                                        Email yang terdaftar di akun Anda
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Kirim Kode Verifikasi
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
                                        Panduan Reset Password
                                    </h5>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">Langkah-langkah:</h6>
                                        <ol class="mb-0">
                                            <li class="mb-2">Klik tombol "Kirim Kode Verifikasi"</li>
                                            <li class="mb-2">Periksa email Anda untuk mendapatkan kode 6 digit</li>
                                            <li class="mb-2">Masukkan kode di halaman verifikasi</li>
                                            <li class="mb-0">Buat password baru Anda</li>
                                        </ol>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">
                                            <i class="fas fa-shield-alt me-2 text-warning"></i>
                                            Informasi Keamanan:
                                        </h6>
                                        <ul class="mb-0">
                                            <li>Kode verifikasi berlaku selama <strong>30 menit</strong></li>
                                            <li>Jangan bagikan kode kepada siapa pun</li>
                                            <li>Periksa folder spam jika email tidak diterima</li>
                                        </ul>
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
    
    .form-control[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
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
