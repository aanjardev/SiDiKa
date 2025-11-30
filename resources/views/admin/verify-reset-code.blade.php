@extends('layouts.admin')

@section('title', 'Verifikasi Kode - SiDiKa')

@push('page-actions')
    <a href="{{ route('admin.profile.forgot-password') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left me-1"></i>
        <span>Kembali</span>
    </a>
    <button type="submit" form="verifyForm" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-check me-1"></i>
        <span>Verifikasi Kode</span>
    </button>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle p-3 me-3">
                            <i class="fas fa-shield-alt text-white fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Verifikasi Kode Reset</h5>
                            <p class="text-muted mb-0">Masukkan kode 6 digit yang dikirim ke email Anda</p>
                        </div>
                    </div>

                    <!-- @if(session('error'))
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
                    @endif -->

                    <form method="POST" action="{{ route('admin.profile.verify-reset-code.post') }}" id="verifyForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4 d-none">
                                    <label class="form-label fw-medium">Email Terdaftar</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control border-start-0" 
                                               value="{{ session('reset_email') }}"
                                               readonly>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="verification_code" class="form-label fw-medium">Kode Verifikasi <span class="text-danger">*</span></label>
                                    <div class="code-inputs d-flex gap-2 mb-3">
                                        <input type="text" class="form-control text-center code-input" maxlength="1" pattern="[0-9]" required>
                                        <input type="text" class="form-control text-center code-input" maxlength="1" pattern="[0-9]" required>
                                        <input type="text" class="form-control text-center code-input" maxlength="1" pattern="[0-9]" required>
                                        <input type="text" class="form-control text-center code-input" maxlength="1" pattern="[0-9]" required>
                                        <input type="text" class="form-control text-center code-input" maxlength="1" pattern="[0-9]" required>
                                        <input type="text" class="form-control text-center code-input" maxlength="1" pattern="[0-9]" required>
                                    </div>
                                    <input type="hidden" name="verification_code" id="verification_code" required>
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Masukkan 6 digit kode yang dikirim ke email Anda
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi Kode
                            </h6>
                            <ul class="mb-0 small">
                                <li>Kode berlaku selama 30 menit</li>
                                <li>Periksa folder spam jika tidak menerima email</li>
                                <li>Jangan bagikan kode kepada siapa pun</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <p class="text-muted small mb-0">
                                Tidak menerima kode? 
                                <button type="button" class="btn btn-link p-0 text-primary" id="resendBtn">
                                    Kirim Ulang
                                </button>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-clock me-2 text-warning"></i>
                        Status Verifikasi
                    </h6>
                    
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle p-3 d-inline-block mb-2">
                            <i class="fas fa-hourglass-half text-primary fs-3"></i>
                        </div>
                        <p class="small text-muted mb-0">
                            <span id="timer">30:00</span> tersisa
                        </p>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-question-circle me-2 text-info"></i>
                            Bantuan
                        </h6>
                        
                        <div class="small">
                            <div class="d-flex align-items-start mb-2">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong>Email Valid:</strong> Pastikan email terdaftar aktif
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-2">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong>Cek Spam:</strong> Folder spam/junk email
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong>Hubungi Admin:</strong> Jika tidak menerima kode
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Code input handling
    const codeInputs = document.querySelectorAll('.code-input');
    const hiddenInput = document.getElementById('verification_code');
    
    // Auto-focus next input
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d$/.test(value)) {
                e.target.value = '';
                return;
            }
            
            // Add filled class
            e.target.classList.add('filled');
            
            // Move to next input
            if (value && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
            
            // Update hidden input
            updateHiddenInput();
        });
        
        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                codeInputs[index - 1].focus();
                codeInputs[index - 1].value = '';
                codeInputs[index - 1].classList.remove('filled');
                updateHiddenInput();
            }
        });
        
        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            
            for (let i = 0; i < pastedData.length; i++) {
                if (codeInputs[index + i]) {
                    codeInputs[index + i].value = pastedData[i];
                    codeInputs[index + i].classList.add('filled');
                }
            }
            
            // Focus next empty input or last
            const nextEmpty = Array.from(codeInputs).find(inp => !inp.value);
            if (nextEmpty) {
                nextEmpty.focus();
            } else {
                codeInputs[codeInputs.length - 1].focus();
            }
            
            updateHiddenInput();
        });
    });
    
    function updateHiddenInput() {
        const code = Array.from(codeInputs).map(input => input.value).join('');
        hiddenInput.value = code;
    }
    
    // Timer countdown
    let timeLeft = 30 * 60; // 30 minutes in seconds
    const timerElement = document.getElementById('timer');
    
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 60) {
            timerElement.parentElement.classList.add('text-danger');
        }
        
        if (timeLeft <= 0) {
            document.getElementById('resendBtn').disabled = false;
            document.getElementById('resendBtn').textContent = 'Kirim Ulang Kode';
            timerElement.textContent = 'Kadaluarsa';
            return;
        }
        
        timeLeft--;
        setTimeout(updateTimer, 1000);
    }
    
    updateTimer();
    
    // Resend button
    document.getElementById('resendBtn').addEventListener('click', function() {
        if (!this.disabled) {
            window.location.href = "{{ route('admin.profile.resend-reset-code') }}";
        }
    });
    
    // Form validation
    const verifyForm = document.getElementById('verifyForm');
    if (verifyForm && window.FormValidator) {
        FormValidator.initForm(verifyForm);
    }
});
</script>

<style>
.code-input {
    width: 45px;
    height: 45px;
    font-size: 1.2rem;
    font-weight: 600;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.code-input:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(59, 138, 255, 0.15);
}

.code-input.filled {
    border-color: var(--bs-primary);
    background-color: rgba(59, 138, 255, 0.1);
}
</style>
@endpush
