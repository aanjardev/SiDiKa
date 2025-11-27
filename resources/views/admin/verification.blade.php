<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/form-validation.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #fff;
            overflow-x: hidden;
        }

        .verification-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .verification-card {
            width: 100%;
            max-width: 450px;
        }

        .verification-code-input {
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            text-align: center;
            font-weight: 600;
        }

        .activation-bg-side {
            min-height: 100vh;
            background-image: url('{{ asset('../mainIMG/Graphic Side.svg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .verification-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }

        .verification-body {
            padding: 2rem;
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
    </style>
</head>

<body>

    {{-- Include Alerts --}}
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 bg-white verification-wrapper">
                <div class="verification-card">
                    
                    <div class="verification-header">
                        <i class="fa-solid fa-envelope-circle-check fa-3x mb-3"></i>
                        <h3 class="fw-bold mb-2">Verifikasi Email</h3>
                        <p class="mb-0 opacity-90">Masukkan kode verifikasi yang telah dikirim ke {{ session('activation_email') }}</p>
                    </div>

                    <div class="verification-body bg-light">
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

            {{-- KOLOM KANAN (IMAGE) --}}
            <div class="col-lg-6 d-none d-lg-flex activation-bg-side"></div>
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
