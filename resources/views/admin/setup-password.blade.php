<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Password | Dinoyo Kamera</title>
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

        .setup-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .setup-card {
            width: 100%;
            max-width: 450px;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: 0;
            color: #6c757d;
        }

        .form-control {
            background-color: #fff;
            border-left: 0;
            padding: 0.7rem 1rem;
        }

        .input-group:focus-within .input-group-text {
            border-color: #86b7fe;
            background-color: #fff;
            color: #0d6efd;
        }

        .input-group:focus-within .form-control {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-toggle-password {
            border-left: 0;
            border-color: #dee2e6;
            background-color: #fff;
            color: #6c757d;
            z-index: 10;
        }

        .btn-toggle-password:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
            border-color: #dee2e6;
        }

        .btn-toggle-password:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }

        .activation-bg-side {
            min-height: 100vh;
            background-image: url('{{ asset('../mainIMG/Graphic Side.svg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }

        .setup-body {
            padding: 2rem;
        }

        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }

        .strength-weak { background-color: #dc3545; width: 33%; }
        .strength-medium { background-color: #ffc107; width: 66%; }
        .strength-strong { background-color: #28a745; width: 100%; }
    </style>
</head>

<body>

    {{-- Include Alerts --}}
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 bg-white setup-wrapper">
                <div class="setup-card">
                    
                    <div class="setup-header">
                        <i class="fa-solid fa-lock fa-3x mb-3"></i>
                        <h3 class="fw-bold mb-2">Buat Password</h3>
                        <p class="mb-0 opacity-90">Buat password untuk akun <strong>{{ $user->email }}</strong></p>
                    </div>

                    <div class="setup-body bg-light">
                        <form method="POST" action="{{ route('activation.setup-password', $user->activation_token) }}" id="setupForm">
                            @csrf

                            {{-- Password Input --}}
                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium text-secondary small">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control border-start-0 border-end-0 required-field @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="Masukkan password"
                                        required
                                        data-error-message="Password wajib diisi"
                                        minlength="6">
                                    <button class="btn btn-outline-secondary btn-toggle-password border-start-0" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    @error('password')
                                        {{ $message }}
                                    @else
                                        Password wajib diisi minimal 6 karakter
                                    @enderror
                                </div>
                                <div class="password-strength" id="passwordStrength"></div>
                                <div class="form-text small text-muted mt-2">
                                    Password minimal 6 karakter. Gunakan kombinasi huruf dan angka untuk keamanan.
                                </div>
                            </div>

                            {{-- Password Confirmation --}}
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-medium text-secondary small">
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control border-start-0 border-end-0 required-field @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="Ulangi password"
                                        required
                                        data-error-message="Konfirmasi password wajib diisi">
                                    <button class="btn btn-outline-secondary btn-toggle-password border-start-0" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    @error('password_confirmation')
                                        {{ $message }}
                                    @else
                                        Konfirmasi password harus sama dengan password
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                    <i class="fa-solid fa-check-circle me-2"></i> Aktifkan Akun & Login
                                </button>
                            </div>

                            {{-- Info --}}
                            <div class="alert alert-info small">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Setelah aktivasi, Anda akan langsung login ke sistem.
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
            const setupForm = document.getElementById('setupForm');
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const passwordStrength = document.getElementById('passwordStrength');
            const togglePassword = document.getElementById('togglePassword');
            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');

            if (setupForm && window.FormValidator) {
                FormValidator.initForm(setupForm);
            }

            // Toggle password visibility
            function setupTogglePassword(toggleBtn, inputField) {
                if (toggleBtn && inputField) {
                    toggleBtn.addEventListener('click', function() {
                        const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
                        inputField.setAttribute('type', type);
                        
                        const icon = toggleBtn.querySelector('i');
                        if (type === 'password') {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    });
                }
            }

            setupTogglePassword(togglePassword, passwordInput);
            setupTogglePassword(togglePasswordConfirm, passwordConfirmInput);

            // Password strength indicator
            if (passwordInput && passwordStrength) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length >= 6) strength++;
                    if (password.length >= 10) strength++;
                    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                    if (/[0-9]/.test(password)) strength++;
                    if (/[^a-zA-Z0-9]/.test(password)) strength++;

                    passwordStrength.className = 'password-strength';
                    
                    if (password.length === 0) {
                        passwordStrength.style.display = 'none';
                    } else {
                        passwordStrength.style.display = 'block';
                        if (strength <= 2) {
                            passwordStrength.classList.add('strength-weak');
                        } else if (strength <= 4) {
                            passwordStrength.classList.add('strength-medium');
                        } else {
                            passwordStrength.classList.add('strength-strong');
                        }
                    }
                });
            }

            // Real-time password confirmation validation
            if (passwordConfirmInput && passwordInput) {
                passwordConfirmInput.addEventListener('input', function() {
                    if (this.value !== passwordInput.value) {
                        this.setCustomValidity('Password tidak sama');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        });
    </script>
</body>

</html>
