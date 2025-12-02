<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    {{-- Fonts & Icons --}}
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
        .verification-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .verification-card {
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
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 138, 255, 0.15);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .input-group:focus-within .input-group-text {
            background-color: #fff;
            border-color: var(--bs-primary);
            color: var(--bs-primary);
        }

        .input-group:focus-within .form-control {
            background-color: #fff;
            border-color: var(--bs-primary);
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

        /* Verification Specific Styles */
        .verification-code-input {
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            text-align: center;
            font-weight: 600;
        }

        .code-display {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            font-family: monospace;
            font-size: 1.2rem;
            color: #495057;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .login-bg-side {
                display: none;
            }
            
            .verification-wrapper {
                background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-secondary) 100%);
            }
            
            .auth-card {
                background: rgba(255, 255, 255, 0.98);
            }
        }

        /* Logo Styling */
        .auth-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }
    </style>
</head>

<body>

    {{-- Include Alerts --}}
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 verification-wrapper">
                <div class="verification-card">
                    <div class="auth-card">
                        {{-- Logo --}}
                        <div class="auth-logo">
                            <i class="fa-solid fa-envelope-circle-check"></i>
                        </div>

                        {{-- Header --}}
                        <div class="text-center mb-4">
                            <h3 class="fw-bold mb-2">Verifikasi Email</h3>
                            <p class="text-muted mb-0">Masukkan kode verifikasi yang telah dikirim ke {{ session('activation_email') }}</p>
                        </div>

                        {{-- Token Expiry Info --}}
                        @if(session('expiry'))
                        <div class="alert alert-warning mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-clock me-2"></i>
                                <div>
                                    <small><strong>{{ session('expiry') }}</strong></small>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Form --}}
                        <form method="POST" action="{{ route('activation.verify-code') }}" id="verificationForm">
                            @csrf

                            {{-- Verification Code Input --}}
                            <div class="mb-4">
                                <label for="verification_code" class="form-label fw-medium text-secondary small">
                                    Kode Verifikasi <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control verification-code-input required-field @error('verification_code') is-invalid @enderror"
                                        id="verification_code"
                                        name="verification_code"
                                        maxlength="6"
                                        placeholder="000000"
                                        required
                                        autofocus
                                        data-error-message="Kode verifikasi wajib diisi">
                                </div>
                                <div class="invalid-feedback">
                                    @error('verification_code')
                                        {{ $message }}
                                    @else
                                        Kode verifikasi wajib diisi (6 digit)
                                    @enderror
                                </div>
                                <div class="form-text small text-muted mt-2">
                                    Masukkan 6 digit kode verifikasi yang dikirim ke email Anda.
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                    <i class="fa-solid fa-check-circle me-2"></i> Verifikasi Kode
                                </button>
                            </div>

                            {{-- Resend Code --}}
                            <div class="text-center">
                                <form method="POST" action="{{ route('activation.resend-code') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-primary text-decoration-none p-0 small">
                                        <i class="fa-solid fa-redo me-1"></i> Kirim ulang kode
                                    </button>
                                </form>
                                <br>
                                <small class="text-muted">
                                    <a href="{{ route('activation.form') }}" class="text-decoration-none">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                                    </a>
                                </small>
                            </div>
                        </form>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const verificationForm = document.getElementById('verificationForm');
            const codeInput = document.getElementById('verification_code');
            
            if (verificationForm && window.FormValidator) {
                FormValidator.initForm(verificationForm);
            }

            // Auto-format verification code input
            if (codeInput) {
                codeInput.addEventListener('input', function(e) {
                    // Remove any non-digit characters
                    let value = e.target.value.replace(/\D/g, '');
                    // Limit to 6 characters
                    if (value.length > 6) {
                        value = value.slice(0, 6);
                    }
                    e.target.value = value;
                });
            }
        });
    </script>
</body>

</html>
