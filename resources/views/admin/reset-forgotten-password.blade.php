@extends('layouts.admin')

@section('title', 'Reset Password - SiDiKa')

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
                                    <i class="fas fa-unlock-alt me-2 text-primary"></i>
                                    Buat Password Baru
                                </h4>
                                <p class="text-muted mb-0">
                                    Verifikasi berhasil! Silakan buat password baru
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

                            <form method="POST" action="{{ route('admin.profile.reset-forgotten-password.post') }}" class="needs-validation" novalidate>
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label fw-medium">
                                        Password Baru
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control" 
                                               id="new_password" 
                                               name="new_password" 
                                               placeholder="Masukkan password baru" 
                                               required 
                                               minlength="6"
                                               autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text text-muted">
                                        Minimal 6 karakter
                                    </div>
                                    <div class="invalid-feedback">
                                        Password minimal 6 karakter
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label fw-medium">
                                        Konfirmasi Password Baru
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control" 
                                               id="new_password_confirmation" 
                                               name="new_password_confirmation" 
                                               placeholder="Ulangi password baru" 
                                               required 
                                               minlength="6"
                                               autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">
                                        Konfirmasi password harus sama
                                    </div>
                                </div>

                                <!-- Password Strength Indicator -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted">Kekuatan Password:</span>
                                        <span id="strengthText" class="small fw-bold">-</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        Simpan Password Baru
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
                                        Panduan Password Aman
                                    </h5>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">Tips password yang kuat:</h6>
                                        <ul class="mb-0">
                                            <li class="mb-2">Gunakan minimal <strong>6 karakter</strong></li>
                                            <li class="mb-2">Kombinasikan huruf besar dan kecil</li>
                                            <li class="mb-2">Tambahkan angka dan simbol khusus</li>
                                            <li class="mb-2">Hindari informasi pribadi (nama, tanggal lahir)</li>
                                            <li class="mb-0">Jangan gunakan password yang sama dengan akun lain</li>
                                        </ul>
                                    </div>


                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">
                                            <i class="fas fa-lightbulb me-2 text-info"></i>
                                            Contoh Password Baik:
                                        </h6>
                                        <div class="alert alert-info small mb-0">
                                            <code>Kucing123!</code> atau <code>M3s!n2024#</code>
                                        </div>
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
    
    .progress-bar {
        transition: width 0.3s ease, background-color 0.3s ease;
    }
    
    .progress-bar.bg-danger { background-color: #dc3545 !important; }
    .progress-bar.bg-warning { background-color: #ffc107 !important; }
    .progress-bar.bg-info { background-color: #17a2b8 !important; }
    .progress-bar.bg-success { background-color: #28a745 !important; }
    
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
// Toggle password visibility
document.getElementById('toggleNewPassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('new_password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('new_password_confirmation');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = '';
    
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    // Remove all color classes
    strengthBar.className = 'progress-bar';
    
    switch(strength) {
        case 0:
        case 1:
            strengthBar.style.width = '20%';
            strengthBar.classList.add('bg-danger');
            strengthText.textContent = 'Sangat Lemah';
            strengthText.className = 'small fw-bold text-danger';
            break;
        case 2:
            strengthBar.style.width = '40%';
            strengthBar.classList.add('bg-warning');
            strengthText.textContent = 'Lemah';
            strengthText.className = 'small fw-bold text-warning';
            break;
        case 3:
            strengthBar.style.width = '60%';
            strengthBar.classList.add('bg-info');
            strengthText.textContent = 'Sedang';
            strengthText.className = 'small fw-bold text-info';
            break;
        case 4:
            strengthBar.style.width = '80%';
            strengthBar.classList.add('bg-success');
            strengthText.textContent = 'Kuat';
            strengthText.className = 'small fw-bold text-success';
            break;
        case 5:
            strengthBar.style.width = '100%';
            strengthBar.classList.add('bg-success');
            strengthText.textContent = 'Sangat Kuat';
            strengthText.className = 'small fw-bold text-success';
            break;
    }
}

document.getElementById('new_password').addEventListener('input', function() {
    checkPasswordStrength(this.value);
});

// Password confirmation validation
document.getElementById('new_password_confirmation').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword === newPassword && confirmPassword.length > 0) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    } else if (confirmPassword.length > 0) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else {
        this.classList.remove('is-valid', 'is-invalid');
    }
});

// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                // Check password confirmation
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('new_password_confirmation').value;
                
                if (newPassword !== confirmPassword) {
                    event.preventDefault();
                    event.stopPropagation();
                    document.getElementById('new_password_confirmation').classList.add('is-invalid');
                }
                
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
