<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode | Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/form-validation.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --bs-primary: #3B8AFF;
            --bs-primary-rgb: 59, 138, 255;
            --bs-secondary: #0048B3;
            --bs-secondary-rgb: 0, 72, 179;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            overflow-x: hidden;
            height: 100vh;
        }

        /* KOLOM KIRI: FORM */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        /* Hope UI Card Styling */
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            padding: 2.5rem;
        }

        /* Hope UI Input Styling */
        .form-control {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 1.2rem;
            letter-spacing: 0.1em;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 138, 255, 0.15);
        }

        /* Hope UI Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-secondary) 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(59, 138, 255, 0.3);
        }

        /* KOLOM KANAN: BACKGROUND dengan SVG */
        .login-bg-side {
            min-height: 100vh;
            background: linear-gradient(135deg, #3B8AFF 0%, #0048B3 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Animated Background Elements */
        .bg-element {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            animation: float 6s ease-in-out infinite;
        }

        .bg-element-1 {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .bg-element-2 {
            width: 200px;
            height: 200px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .bg-element-3 {
            width: 150px;
            height: 150px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Content di tengah background */
        .bg-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: white;
            padding: 2rem;
        }

        /* Logo Perusahaan Styling */
        .brand-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 2rem;
        }

        .company-logo {
            flex-shrink: 0;
        }

        .logo-img {
            width: 200px;
            height: 200px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .brand-text {
            text-align: left;
        }

        .company-name {
            font-size: 5rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.9) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 10px;
        }

        .company-tagline {
            font-size: 1.5em;
            opacity: 0.8;
            margin-bottom: 40px;
            font-weight: 300;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .login-bg-side {
                display: none;
            }
            
            .login-wrapper {
                background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-secondary) 100%);
            }
            
            .auth-card {
                background: rgba(255, 255, 255, 0.98);
            }
        }

        /* Code Input Styling */
        .code-inputs {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .code-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
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

        /* Form Label Styling */
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Link Styling */
        .text-primary {
            color: var(--bs-primary) !important;
            text-decoration: none;
            font-weight: 500;
        }

        .text-primary:hover {
            text-decoration: underline;
        }

        /* Timer Styling */
        .timer {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .timer.warning {
            color: #dc3545;
        }
    </style>
</head>

<body>

    {{-- Include Alerts jika ada --}}
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 login-wrapper">
                <div class="login-card">
                    <div class="auth-card">
                        
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-dark mb-2">Verifikasi Kode</h2>
                            <p class="text-muted small">Masukkan kode 6 digit yang dikirim ke email Anda.</p>
                            @if(session('reset_email'))
                                <p class="text-muted small">
                                    <i class="fa-solid fa-envelope me-1"></i>
                                    {{ session('reset_email') }}
                                </p>
                            @endif
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

                        <form method="POST" action="{{ route('public.verify-reset-code.post') }}" id="verifyForm">
                            @csrf

                            {{-- Code Input --}}
                            <div class="mb-4">
                                <label for="verification_code" class="form-label text-center d-block">Kode Verifikasi <span class="text-danger">*</span></label>
                                <div class="code-inputs">
                                    <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required autofocus>
                                    <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                                </div>
                                <input type="hidden" name="verification_code" id="verification_code" required>
                                <div class="invalid-feedback">
                                    Kode verifikasi wajib diisi dengan 6 digit angka
                                </div>
                            </div>

                            {{-- Timer --}}
                            <div class="timer text-center mb-3" id="timer">
                                <i class="fa-solid fa-clock me-1"></i>
                                Kode berlaku selama <span id="countdown">30:00</span>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-check me-2"></i> Verifikasi Kode
                                </button>
                            </div>

                            {{-- Resend Code --}}
                            <div class="text-center">
                                <p class="text-muted small mb-0">
                                    Tidak menerima kode? 
                                    <button type="button" class="btn btn-link p-0 text-primary" id="resendBtn" disabled>
                                        Kirim Ulang
                                    </button>
                                </p>
                            </div>

                        </form>

                        <div class="mt-4 text-center">
                            <div class="border-top pt-3">
                                <p class="text-muted small mb-0">
                                    <a href="{{ route('password.request') }}" class="text-primary">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (BACKGROUND DESIGN) --}}
            <div class="col-lg-6 d-none d-lg-flex login-bg-side">
                {{-- Animated Background Elements --}}
                <div class="bg-element bg-element-1"></div>
                <div class="bg-element bg-element-2"></div>
                <div class="bg-element bg-element-3"></div>
                
                {{-- Content di tengah --}}
                <div class="bg-content">
                    <div class="brand-container">
                        {{-- Logo Perusahaan --}}
                        <div class="company-logo">
                            <img src="{{ asset('mainIMG/logoDK.png') }}" alt="Dinoyo Kamera" class="logo-img">
                        </div>
                        
                        {{-- Brand Text --}}
                        <div class="brand-text">
                            <h1 class="company-name">SiDiKa</h1>
                            <p class="company-tagline">Sistem Informasi Dinoyo Kamera</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Form Validation JS --}}
    <script src="{{ asset('js/form-validation.js') }}"></script>

    {{-- Logic Code Input & Timer --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const codeInputs = document.querySelectorAll('.code-input');
            const hiddenInput = document.getElementById('verification_code');
            const verifyForm = document.getElementById('verifyForm');
            const resendBtn = document.getElementById('resendBtn');
            const timerElement = document.getElementById('countdown');
            
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
            
            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 60) {
                    document.getElementById('timer').classList.add('warning');
                }
                
                if (timeLeft <= 0) {
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Kirim Ulang Kode';
                    document.getElementById('timer').innerHTML = '<i class="fa-solid fa-exclamation-triangle me-1"></i> Kode telah kadaluarsa';
                    return;
                }
                
                timeLeft--;
                setTimeout(updateTimer, 1000);
            }
            
            updateTimer();
            
            // Resend button
            resendBtn.addEventListener('click', function() {
                if (!this.disabled) {
                    window.location.href = "{{ route('public.resend-reset-code') }}";
                }
            });
            
            // Form validation
            if (verifyForm && window.FormValidator) {
                FormValidator.initForm(verifyForm);
            }
            
            // Add subtle animations
            const authCard = document.querySelector('.auth-card');
            if (authCard) {
                authCard.style.animation = 'slideInUp 0.6s ease-out';
            }
        });

        // Add slideInUp animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>
